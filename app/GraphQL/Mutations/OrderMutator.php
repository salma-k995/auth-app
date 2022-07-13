<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use App\Exports\OrdersExport;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Product;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\DB;
use \Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;

final class OrderMutator
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function createOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $order =  $user->orders()->create($args);

        foreach ($args['products'] as $product) {

            $prod = Product::where('id', $product)->FirstOrFail();
            $reductions = $prod->reductions;

            if ($product['quantity'] <= $prod->quantity) {
                foreach ($reductions as $reduction) {
                    $reductionClientsIds = $reduction->clients()->pluck('clients.id')->toArray();

                    if (in_array($args['client_id'], $reductionClientsIds)) {
                        if ($reduction->percent_value != null) {
                            $order->products()->attach($product['id'], [
                                'quantity' => $product['quantity'],
                                'total_price' => $product['quantity'] * Product::find($product['id'])->price - ($product['quantity'] * (Product::find($product['id'])->price * $reduction->percent_value / 100))
                            ]);
                        } else $order->products()->attach($product['id'], [
                            'quantity' => $product['quantity'],
                            'total_price' => $product['quantity'] * Product::find($product['id'])->price - $reduction->amount_value
                        ]);
                    } else  $order->products()->attach($product['id'], [
                        'quantity' => $product['quantity'],
                        'total_price' => $product['quantity'] * Product::find($product['id'])->price
                    ]);
                }
                $product = $prod->update([
                    'quantity' => $prod->quantity - $product['quantity']
                ]);
            } else  throw new GraphQLException("can not buy product is alerady deleted.", "error");
        }
        return $order;
    }

    public function updateOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $order = $user->orders()->where('id', $args['id'])->firstOrFail();

        $orderProductsIds = $order->products()->pluck('products.id')->toArray();

        $updatedOrderProductsIds = array_column($args['products'], 'id');

        $productsToBeRemoved = $order->products()->whereNotIn('products.id', $updatedOrderProductsIds)->pluck('products.id')->toArray();

        $order->products()->detach($productsToBeRemoved);

        foreach ($args['products'] as $product) {

            $product_availability = $order->products()->find($product['id']);
            $prod = Product::where('id', $product)->FirstOrFail();

            $reductions = $prod->reductions;
            if ($product['quantity'] <= $prod->quantity) {
                error_log('rrrrrrrrrrr');
                if (empty($product_availability)) {

                    if (count($reductions) != 0) {
                        foreach ($reductions as $reduction) {

                            $reductionClientsIds = $reductions->clients()->pluck('clients.id')->toArray();

                            if (in_array($order->id, $reductionClientsIds)) {

                                if ($reductions->percent_value != null) {
                                    $order->products()->attach($product['id'], [
                                        'quantity' => $product['quantity'],
                                        'total_price' => $product['quantity'] * Product::find($product['id'])->price - ($product['quantity'] * (Product::find($product['id'])->price * $reduction->percent_value / 100))
                                    ]);
                                } else $order->products()->attach($product['id'], [
                                    'quantity' => $product['quantity'],
                                    'total_price' => $product['quantity'] * Product::find($product['id'])->price - $reductions->amount_value
                                ]);
                            }
                        }
                    } else  $order->products()->attach($product['id'], [
                        'quantity' => $product['quantity'],
                        'total_price' => $product['quantity'] * Product::find($product['id'])->price
                    ]);
                } else {
                    if (count($reductions) != 0) {
                        foreach ($reductions as $reduction) {
                            if ($reduction->percent_value != null) {
                                $order->products()
                                    ->updateExistingPivot(
                                        $product['id'],
                                        [
                                            'quantity' => $product['quantity'],
                                            'total_price' => $product_availability->price * $product['quantity'] - ($product['quantity'] * ($product_availability->price * $reduction->percent_value / 100))
                                        ]
                                    );
                            } else  $order->products()
                                ->updateExistingPivot(
                                    $product['id'],
                                    [
                                        'quantity' => $product['quantity'],
                                        'total_price' => $product_availability->price * $product['quantity'] - $reduction->amount_value
                                    ]
                                );
                        }
                    } else   $order->products()->attach($product['id'], [
                        'quantity' => $product['quantity'],
                        'total_price' => $product['quantity'] * Product::find($product['id'])->price
                    ]);
                }
                $product = $prod->update([
                    'quantity' => $prod->quantity - $product['quantity']
                ]);
            }
        }

        return $order;
    }

    public function deleteOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        try {

            DB::beginTransaction();

            foreach ($args['ordersIds'] as $order) {

                $order = $user->orders->where('id', $order)->firstOrFail();
                $order->products()->detach();
                $order->delete();
            }

            DB::commit();

            return 'All order are deleted successfuly';
        } catch (\Exception $e) {

            DB::rollback();

            throw new GraphQLException("can not delete this order is alerady deleted.", "error");
        }
    }

    public function updateStatus($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $order = $user->orders()->where('id', $args['id'])->firstOrFail();

        OrderHistory::create([
            'status' => $order->status,
            'order_id'  => $order->id
        ]);

        $order->update([
            'status' => $args['status']
        ]);
        $order->save();

        return 'Order status is updated successfuly';
    }

    public function exportOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (Storage::disk('public')->exists('orders.xlsx')) {

            Storage::disk('public')->delete('orders.xlsx');
        }

        if (array_key_exists('ids', $args)) {
            Excel::store(new OrdersExport($args['ids']), 'orders.xlsx', 'public');
        } else Excel::store(new OrdersExport(), 'orders.xlsx', 'public');

        return env('APP_URL') . "/storage/" . 'orders.xlsx';
    }

    public function searchOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $orders = Order::where('reference', 'LIKE', '%' . $args['terme'] . '%')->orWhere('satus',   'LIKE', '%' . $args['terme'] . '%');

        return $orders;
    }


    public function createorderProductPDF($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {

        $user = $context->request()->user();
        $pages = [];
        if (array_key_exists('ids', $args)) {
            foreach ($args['ids'] as $key => $order_id) {
                $order = $user->orders()->where('id', $order_id)->firstOrFail();
                $pages[] = view('pdf.orderProductPDF')->with(compact('order'));
            }
        } else {
            $orders = $user->orders()->get();
            error_log($orders);
            foreach ($orders as $key => $order) {
                $order = $user->orders()->where('id', $order->id)->first();
                $pages[] = view('pdf.orderProductPDF')->with(compact('order'));
            }
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf = $pdf->loadView('pdf.index', ['pages' => $pages]);

        Storage::put('public/pdf/orderProducts.pdf', $pdf->output());

        return env('APP_URL') . "/storage/pdf/" . 'orderProducts.pdf';
    }
}

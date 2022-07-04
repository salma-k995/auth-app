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
use Illuminate\Support\Facades\Log;

use function Safe\error_log;

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

        foreach ($args['objects'] as $object) {

            $product = Product::find($object);

            $order->products()->attach(
                $object,
                [
                    'quantity' => $args['quantity'],
                    'total_price' => $args['quantity'] * $product->price
                ]
            );
        }

        return $order;
    }

    public function updateOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $order = Order::where('id', $args['id'])->firstOrFail();

        $orderProductsIds = $order->products()->pluck('products.id')->toArray();

        $updatedOrderProductsIds = array_column($args['products'], 'id');

        $productsToBeRemoved = $order->products()->whereNotIn('products.id', $updatedOrderProductsIds)->pluck('products.id')->toArray();

        $order->products()->detach($productsToBeRemoved);

        foreach ($args['products'] as $product) {

            $product_availability = $order->products()->find($product['id']);

            if (empty($product_availability)) {

                $order->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'total_price' => $product['quantity'] * Product::find($product['id'])->price
                ]);
            } else {
                $order->products()
                    ->updateExistingPivot(
                        $product['id'],
                        [
                            'quantity' => $product['quantity'],
                            'total_price' => $product_availability->price * $product['quantity']
                        ]
                    );
            }
        }
        return $order;
    }

    public function deleteOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        try {

            DB::beginTransaction();

            foreach ($args['object'] as $order) {
                $order = $user->orders->where('id', $order)->firstOrFail();

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
        $order = Order::where('id', $args['id'])->firstOrFail();

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
}

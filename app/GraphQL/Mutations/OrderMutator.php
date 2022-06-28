<?php

namespace App\GraphQL\Mutations;

use App\Exports\OrdersExport;
use App\Models\Order;
use App\Models\Product;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

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
        error_log($order);

        foreach ($args['objects'] as $object) {

            $product = Product::find($object);
            $order_products = $order->products()->attach(
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

        $order_products = $order->products()->detach();

        foreach ($args['objects'] as $object) {

            $product = Product::find($object);
            $order_products = $order->products()->attach(
                $object,
                [
                    'quantity' => $args['quantity'],
                    'total_price' => $args['quantity'] * $product->price
                ]
            );
        }

        $order->update($args);
        $order->save();

        return $order;
    }

    public function deleteOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $order =  Order::where('id', $args['id'])->firstOrFail();

        $order_products = $order->products()->detach($args['objects']);

        return 'Order is deleted';
    }

    public function deleteOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        Order::whereIn('id', $args['object'])->delete();

        return 'Orders are deleted successfuly';
    }

    public function updateStatus($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $order = Order::where('id', $args['id'])->firstOrFail();

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
        }

       else Excel::store(new OrdersExport(), 'orders.xlsx', 'public');

        return env('APP_URL') . "/storage/" . 'orders.xlsx';
    }
}

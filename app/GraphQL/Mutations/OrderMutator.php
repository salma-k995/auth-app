<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\Product;
use GraphQL\Type\Definition\ResolveInfo;
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
        $client = $context->request->user();

        $order =  $client->orders()->create($args);

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

        $order=$order->update($args['status']);
       // error_log($order);
        return 'Order status is updated successfuly';
    }
}

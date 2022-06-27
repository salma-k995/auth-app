<?php

namespace App\GraphQL\Queries;

use App\Models\Client;
use App\Models\Order;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class OrderQueries
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function orders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $client = Client::where('id', $args['id'])->firstOrFail();

        $client_orders= $client->orders;

        return $client_orders;
    }



    public function orderProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $order = Order::where('id', $args['id'])->firstOrFail();

        $order_products= $order->products;

        return $order_products;
    }
}

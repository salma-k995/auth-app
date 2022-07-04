<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

use function Safe\error_log;

final class OrderHistoryQueries
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }


    public function orderHistories($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $order = Order::findOrFail($args['id']);
        
        $histories = $order->orderHistories;

        return $histories;
    }
}

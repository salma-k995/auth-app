<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

use function Safe\error_log;

final class UserQueries
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function userAllClients($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $clients = $user->clients();

        return $clients;
    }

    public function userAllProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {

        $user = $context->request->user();

        $products = $user->products();

        return $products;
    }

    public function userAllOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $orders = $user->orders();

        return $orders;
    }
}

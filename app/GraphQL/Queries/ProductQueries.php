<?php

namespace App\GraphQL\Queries;

use App\Models\Product;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ProductQueries
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function getAllProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        if (!empty($args['name']))
            $products = $user->products()->where('name', 'LIKE', '%' . $args['name'] . '%')->get();

        else $products = $user->products;

        return $products;
    }

    public function showProduct($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $product = $user->products->where('id', $args['id'])->firstOrFail();

        return $product;
    }
}

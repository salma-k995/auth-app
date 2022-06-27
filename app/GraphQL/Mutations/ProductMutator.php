<?php

namespace App\GraphQL\Mutations;

use App\Exports\ProductsExport;
use App\Models\Product;
use GraphQL\Type\Definition\ResolveInfo;
use Maatwebsite\Excel\Facades\Excel;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ProductMutator
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function createProduct($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $file = $args['url'];

        $product =  $user->products()->create($args);
        $fileName = $file->storePublicly('products', 'public');
        $product->image()->create([
            'url' => $fileName
        ]);

        return $product;
    }

    public function updateProduct($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $product = Product::where('id', $args['id'])->firstOrFail();
    
        $product =  $product->update($args);

        return 'the product us updated successfuly';
    }

    public function deleteProduct($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $product = Product::where('id', $args['id'])->firstOrFail();

        $product->delete();

        return 'the product is deleted successufuly';
    }

    public function deleteProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        Product::whereIn('id', $args['object'])->delete();

        return 'All product are deleted successfuly';
    }

    public function exportProduct($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        Excel::store(new ProductsExport(2018), 'products.xlsx');
        return "goood";
    }
}

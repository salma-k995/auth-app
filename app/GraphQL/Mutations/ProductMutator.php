<?php

namespace App\GraphQL\Mutations;

use App\Exports\ProductsExport;
use App\Models\Product;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Storage;
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

    public function exportProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (Storage::disk('public')->exists('products.xlsx')) {

            Storage::disk('public')->delete('products.xlsx');
        }

        if (array_key_exists('ids', $args)) {
            Excel::store(new ProductsExport($args['ids']), 'products.xlsx', 'public');
        }
        else Excel::store(new ProductsExport(), 'products.xlsx', 'public');


        return env('APP_URL') . "/storage/" . 'products.xlsx';
    }
}

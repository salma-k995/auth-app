<?php

namespace App\GraphQL\Mutations;

use App\Exports\ProductsExport;
use App\Models\Product;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Exceptions\GraphQLException;
use Illuminate\Support\Facades\DB;


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
        $user = $context->request->user();

        $product = $user->products->where('id', $args['id'])->firstOrFail();

        if (array_key_exists('url', $args)) {
            $file = $args['url'];
            $fileName = $file->storePublicly('products', 'public');
            $product->image()->update([
                'url' => $fileName
            ]);
        }

        $product =  $product->update($args);

        return 'the product us updated successfuly';
    }

    public function deleteProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        try {
            DB::beginTransaction();

            foreach ($args['productsIds'] as $product) {

                $product = $user->products->where('id', $product)->firstOrFail();
                $product->orders()->delete();
                $product->reductions()->delete();
                $product->delete();
            }
            DB::commit();

            return 'All product are deleted successfuly';
        } catch (\Exception $e) {

            DB::rollback();
            throw new GraphQLException("can not delete this product is alerady deleted.", "error");
        }
    }

    public function exportProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (Storage::disk('public')->exists('products.xlsx')) {

            Storage::disk('public')->delete('products.xlsx');
        }

        if (array_key_exists('ids', $args)) {
            Excel::store(new ProductsExport($args['ids']), 'products.xlsx', 'public');
        } else Excel::store(new ProductsExport(), 'products.xlsx', 'public');

        return env('APP_URL') . "/storage/" . 'products.xlsx';
    }

    public function searchProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $products = Product::where('name', 'LIKE', '%' . $args['terme'] . '%')->orWhere('description',   'LIKE', '%' . $args['terme'] . '%')
            ->orWhere('price', 'LIKE', '%' . $args['terme'] . '%');

        return $products;
    }
}

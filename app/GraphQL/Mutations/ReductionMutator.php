<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use App\Models\Product;
use App\Models\Reduction;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ReductionMutator
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function createReduction($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $product = $user->products()->where('id', $args['id'])->firstOrFail();

        $reduction =  $product->reductions()->create($args);

        $reduction->clients()->attach($args['clients']);

        return $reduction;
    }

    public function updateReduction($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $reduction = Reduction::where('id', $args['id'])->firstOrFail();

        $reduction = $reduction->update($args);

        return 'reduction is updated successfuly';
    }

    public function deleteReductions($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {

            DB::beginTransaction();

            foreach ($args['reductionsids'] as $reduction) {

                $reduction = Reduction::where('id', $reduction)->firstOrFail();

                $reduction->delete();
            }

            DB::commit();

            return 'All reductions are deleted successfuly';
        } catch (\Exception $e) {

            DB::rollback();

            throw new GraphQLException("can not delete this reduction is alerady deleted.", "error");
        }
    }
}

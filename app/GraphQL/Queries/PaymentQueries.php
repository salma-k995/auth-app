<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class PaymentQueries
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function showPayment($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $payment = $user->payments->where('id', $args['id'])->firstOrFail();

        return $payment;
    }

    public function showClientPayments($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $client = $user->clients()->where('id', $args['client_id'])->FirstOrFail();

        $payments = $client->payments;

        return $payments;
    }
}

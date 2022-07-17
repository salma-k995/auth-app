<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class PaymentMutator
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function createPayment($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $order= $user->orders()->where('id', $args['order_id'])->FirstOrFail();

        $products = $order->products;
        $sum_tot_Price=0;
        foreach($products as $product){
            $sum_tot_Price += $product['pivot']['total_price'];
        }
        $payment = $order->payments()->create([
            'amount'=> $sum_tot_Price,
            'payment_method'=>$args['payment_method'],
            'user_id'=> $user->id,
            'client_id'=>$order->client_id,
            'order_id'=>$order->id
        ]);

        return $payment;
    }

    public function updatePayment($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $order= $user->orders()->where('id', $args['order_id'])->FirstOrFail();

        $payment= $user->payments()->where('id', $args['order_id'])->FirstOrFail();

        $products = $order->products;

        $sum_tot_Price=0;

        foreach($products as $product){
            $sum_tot_Price += $product['pivot']['total_price'];
        }

        $new_amount = $sum_tot_Price - $payment->amount;
        
        $payment = $payment->update([
            'amount'=> $new_amount,
            'payment_method'=>$args['payment_method'],

        ]);

        return $payment ;

    }

}

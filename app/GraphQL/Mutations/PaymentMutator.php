<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

use function Safe\error_log;

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
        //user can pay order when order is completed

        $user = $context->request->user();

        $order = $user->orders()->where('id', $args['order_id'])->FirstOrFail();

        // if ($order->status == "COMPLETED") {
        $products = $order->products;
        $sum_tot_Price = 0;
        foreach ($products as $product) {
            $sum_tot_Price += $product['pivot']['total_price'];
        }
        $payment = $order->payments()->create([
            'amount' => $sum_tot_Price,
            'payment_method' => $args['payment_method'],
            'user_id' => $user->id,
            'client_id' => $order->client_id,
            'order_id' => $order->id
        ]);
        return $payment;
        //   }
        throw new GraphQLException("can not pay this order is not completed yet.", "error");
    }

    
    public function updatePayment($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $payment = $user->payments()->where('id', $args['id'])->FirstOrFail();

        $order = $payment->order;

        $products = $order->products;

        $sum_tot_Price = 0;

        foreach ($products as $product) {
            $sum_tot_Price += $product['pivot']['total_price'];
        }
        //   if ($order->status != "COMPLETED") {
        if ($sum_tot_Price > $payment->amount) {
            $new_amount = $sum_tot_Price - $payment->amount;
            $payment = $payment->update([
                'amount' => $new_amount,
                'payment_method' => $args['payment_method'],
            ]);
        } else if ($sum_tot_Price < $payment->amount) {

            $new_amount = $payment->amount - $sum_tot_Price;
            $payment = $payment->update([
                'amount' => $sum_tot_Price,
                'payment_method' => $args['payment_method'],
            ]);
        } else {
            $payment = $payment->update([
                'payment_method' => $args['payment_method'],
            ]);
        }
        //   }
        return $payment;
    }
}

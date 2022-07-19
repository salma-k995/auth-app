<?php

namespace App\GraphQL\Queries;

use App\Models\Client;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

use function Safe\error_log;

final class ClientQueries
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }


    public function getAllClients($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        if (!empty($args['first_name']))
            $clients = $user->clients()->where('first_name', 'LIKE', '%' . $args['first_name'] . '%')->get();

        else $clients = $user->clients;

        return $clients;
    }
    public function showClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $client = $user->clients->where('id', $args['id'])->firstOrFail();

        return $client;
    }
}

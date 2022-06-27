<?php

namespace App\GraphQL\Queries;

use App\Models\Client;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

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

    public function showClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $client = Client::where('id', $args['id'])->firstOrFail();

        return $client;
    }
}

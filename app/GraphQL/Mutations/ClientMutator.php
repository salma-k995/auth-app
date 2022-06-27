<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use App\Models\Client;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ClientMutator
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function registerClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();
        // error_log($user );
        $client = $user->clients()->create($args);

        return $client;
    }

    function loginClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $client = Client::where('email', $args['email'])->firstOrFail();

        if (!Hash::check($args['password'], $client->password))
            throw new GraphQLException("invalid credentiels", "Invalid Input");

        $token = $client->createToken('auth_token')->plainTextToken;

        return ['token' => $token, 'client' => $client];
    }


    public function updateClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $client = Client::where('id', $args['id'])->firstOrFail();
        // error_log($client);
        $client = $client->update($args);

        return 'Client is updated successfuly';
    }


    public function deleteClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $client = Client::where('id', $args['id'])->firstOrFail();

        $client->delete();

        return 'The client is deleted successfuly';
    }

    public function deleteClients($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        //  Client::truncate();
        Client::whereIn('id', $args['object'])->delete();

        return 'Clients are deleted successfuly';
    }
}

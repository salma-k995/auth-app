<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use App\Exports\ClientsExport;
use App\Models\Client;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
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
        Client::whereIn('id', $args['object'])->delete();

        return 'Clients are deleted successfuly';
    }

    public function exportClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (Storage::disk('public')->exists('products.xlsx')) {

            Storage::disk('public')->delete('products.xlsx');
        }

        Excel::store(new ClientsExport(2018), 'clients.xlsx');

        return env('APP_URL') . "/storage/" . 'clients.xlsx';
    }
}

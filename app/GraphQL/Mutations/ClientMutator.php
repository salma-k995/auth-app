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
        $user = $context->request->user();

        $client = Client::where('id', $args['id'])->where('user_id',$user->id )->firstOrFail();

        $client = $client->update($args);

        return 'Client is updated successfuly';
    }

    public function deleteClients($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        Client::whereIn('id', $args['object'])->where('user_id',$user->id)->delete();

        return 'Clients are deleted successfuly';
    }

    public function exportClients($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (Storage::disk('public')->exists('clients.xlsx')) {

            Storage::disk('public')->delete('clients.xlsx');
        }

        if (array_key_exists('ids', $args)) {
            Excel::store(new ClientsExport($args['ids']), 'clients.xlsx', 'public');
        }

       else Excel::store(new ClientsExport(), 'clients.xlsx', 'public');

        return env('APP_URL') . "/storage/" . 'clients.xlsx';
    }
}

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
use Illuminate\Support\Facades\DB;

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

    public function createClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $client = $user->clients()->create($args);

        return $client;
    }

    public function updateClient($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $client = Client::where('id', $args['id'])->where('user_id', $user->id)->firstOrFail();

        $client = $client->update($args);

        return 'Client is updated successfuly';
    }

    public function deleteClients($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        try {

            DB::beginTransaction();

            foreach ($args['object'] as $client) {

                $client = $user->clients->where('id', $client)->firstOrFail();

                $client->delete();
            }

            DB::commit();

            return 'All clients are deleted successfuly';
        } catch (\Exception $e) {

            DB::rollback();

            throw new GraphQLException("can not delete this client is alerady deleted.", "error");
        }
    }

    public function exportClients($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (Storage::disk('public')->exists('clients.xlsx')) {

            Storage::disk('public')->delete('clients.xlsx');
        }

        if (array_key_exists('ids', $args)) {
            Excel::store(new ClientsExport($args['ids']), 'clients.xlsx', 'public');
        } else Excel::store(new ClientsExport(), 'clients.xlsx', 'public');

        return env('APP_URL') . "/storage/" . 'clients.xlsx';
    }

    public function searchClients($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $clients = Client::where('first_name', 'LIKE', '%' . $args['terme'] . '%')->orWhere('last_name',   'LIKE', '%' . $args['terme'] . '%')
            ->orWhere('phone', 'LIKE', '%' . $args['terme'] . '%')->orWhere('email', 'LIKE', '%' . $args['terme'] . '%');

        return $clients;
    }
}

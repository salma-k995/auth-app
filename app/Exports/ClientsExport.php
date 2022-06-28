<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClientsExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */

    private $ids;

    public function __construct($ids = null)
    {
        $this->ids = $ids;
    }
    public function collection()
    {
        if ($this->ids)
            $clients = Client::whereIn('id', $this->ids)->get();

        else $clients = Client::all();

        return $clients;
    }
}

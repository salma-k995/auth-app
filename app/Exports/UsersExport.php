<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
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
        // return User::all();
        if ($this->ids)
            $orders = User::whereIn('id', $this->ids)->get();

        else $orders = User::all();

        return $orders;
    }
}

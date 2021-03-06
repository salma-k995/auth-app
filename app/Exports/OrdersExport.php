<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrdersExport implements FromCollection
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
        // return Order::all();

        if ($this->ids)
            $orders = Order::whereIn('id', $this->ids)->get();

        else $orders = Order::all();

        return $orders;
    }
}

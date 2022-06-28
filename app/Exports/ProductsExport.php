<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements FromCollection
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
        //  return Product::all();
        if ($this->ids)
            $orders = Product::whereIn('id', $this->ids)->get();

        else $orders = Product::all();

        return $orders;
    }
}

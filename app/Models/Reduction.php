<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Reduction extends Model
{
    use HasFactory;


    protected $fillable = [
        'percent',
        'amount',
        'percent_value',
        'amount_value',
        'product_id',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class)->withPivot(['reduction_id', 'client_id']);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $cascadeDeletes = ['users'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'user_id',
    ];

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $value
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable')->orderBy('created_at', 'desc');
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(

            get: fn () => !empty($this->image) ? env('APP_URL') . "/storage/" . $this->image->url  : null
        );
    }


    public function reductions(): HasMany
    {
        return $this->hasMany(Reduction::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\hasMany;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable')->orderBy('created_at', 'desc');
    }

    public function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            //    get: fn () => !empty($this->image) ? $this->image->url  : null
            get: fn () => !empty($this->image) ? env('APP_URL') . "/storage/" . $this->image->url  : null
        );
    }

    public function socialLogins(): hasMany
    {
        return $this->hasMany(Sociallogin::class);
    }

    public function clients(): hasMany
    {
        return $this->hasMany(Client::class);
    }

    public function products(): hasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): hasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): hasMany
    {
        return $this->hasMany(Paymentt::class);
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable=['amount', 'payment_method','user_id' ,'client_id','order_id'];

    public function users(){
        return $this->belongsTo(User::class);
    }
    public function clients(){
        return $this->belongsTo(Client::class);
    }
    public function orders(){
        return $this->belongsTo(Order::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentAccountInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_account_info';

    protected $fillable = [
        'key',
        'value',
    ];
}
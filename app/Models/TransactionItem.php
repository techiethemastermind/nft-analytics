<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    public $fillable = ['transaction_id', 'address', 'status', 'timestamp', 'type', 'nft', 'price', 'amount1', 'amount2'];
}

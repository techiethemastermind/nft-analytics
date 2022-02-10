<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class Token extends Model
{
    use HasFactory;

    public $fillable = [
        'name', 'address', 'link'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

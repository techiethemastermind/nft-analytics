<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    public $fillable = ['token_id', 'address', 'link', 'method', 'from', 'to', 'token', 'time', 'status', 'error'];
}

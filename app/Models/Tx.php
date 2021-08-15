<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tx extends Model
{
    use HasFactory;

    protected $table = 'txs';

    protected $fillable = [
        'email_user',
        'tx',
        'wallet',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email_user');
    } 
    
}

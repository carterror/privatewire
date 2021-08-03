<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dns',
        'server_id',
        'user_id',
    ];

    public function server()
    {
        return $this->hasOne(Server::class, 'id', 'server_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}



<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['Customer', 'User'];

    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}

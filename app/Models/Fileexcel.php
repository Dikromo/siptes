<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fileexcel extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['User', 'Customer'];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function Customer()
    {
        return $this->hasMany(Customer::class);
    }
}

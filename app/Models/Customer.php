<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function Fileexcel()
    {
        return $this->belongsTo(Fileexcel::class);
    }
    public function Distribusi()
    {
        return $this->hasMany(Distribusi::class);
    }
}

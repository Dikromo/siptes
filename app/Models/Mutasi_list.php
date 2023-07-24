<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi_list extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function Mutasi()
    {
        return $this->hasMany(Mutasi::class);
    }
}

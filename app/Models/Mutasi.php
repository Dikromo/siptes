<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function Mutasi_list()
    {
        return $this->hasMany(Mutasi_list::class);
    }
}

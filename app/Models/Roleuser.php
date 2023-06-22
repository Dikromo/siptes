<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roleuser extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $with = ['User'];
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}

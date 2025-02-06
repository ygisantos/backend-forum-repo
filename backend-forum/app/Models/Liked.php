<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Liked extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['user_id', 'forum_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function forum() {
        return $this->belongsTo(Forum::class);
    }
}

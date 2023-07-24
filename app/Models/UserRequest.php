<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'status', 'message', 'comment', 'name',
        'email', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

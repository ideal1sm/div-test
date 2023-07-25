<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="Role"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="title", type="string", readOnly="true", example="Пользователь"),
 * @OA\Property(property="slug", type="string", readOnly="true", example="user"),
 * )
 *
 * Class Role
 *
 */

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

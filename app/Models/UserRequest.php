<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @OA\Schema(
 * required={"password"},
 * @OA\Xml(name="UserRequest"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", readOnly="true", description="User name", example="Test user"),
 * @OA\Property(property="email", type="string", readOnly="true", format="email", description="User unique email address", example="user@gmail.com"),
 * @OA\Property(property="status", type="string", readOnly="true", description="User request status", example="Active"),
 * @OA\Property(property="comment", type="string", description="Comment of manager. Not null only if status = Resolved", example="Test commnet of manager"),
 * @OA\Property(property="user", type="object", description="Related user object", ref="#/components/schemas/User"),
 * @OA\Property(property="created_at", ref="#/components/schemas/BaseModel/properties/created_at"),
 * @OA\Property(property="updated_at", ref="#/components/schemas/BaseModel/properties/updated_at"),
 * )
 *
 * Class UserRequest
 *
 */

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

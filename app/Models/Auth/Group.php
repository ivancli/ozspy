<?php

namespace OzSpy\Models\Auth;


use Illuminate\Database\Eloquent\SoftDeletes;
use OzSpy\Models\Model;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_groups', 'group_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_groups', 'group_id', 'role_id');
    }
}

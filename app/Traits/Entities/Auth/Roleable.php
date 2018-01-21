<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 2:00 PM
 */

namespace OzSpy\Traits\Entities\Auth;

use OzSpy\Exceptions\Models\RoleRelationshipNotFound;

/**
 * Trait Roleable
 * @package OzSpy\Traits\Entities\Auth
 */
trait Roleable
{
    protected $staffRoles = ['super_admin', 'admin'];

    /**
     * Roleable constructor.
     * @throws RoleRelationshipNotFound
     */
    public function __construct()
    {
        if (!method_exists(self::class, 'roles')) {
            throw new RoleRelationshipNotFound;
        }
    }

    /**
     * @param $role_name
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rolesByName($role_name)
    {
        return $this->roles()->where('name', $role_name);
    }

    /**
     * @param array $role_names
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rolesInNames(array $role_names)
    {
        return $this->roles()->whereIn('name', $role_names);
    }

    /**
     * @param $role_names
     * @return bool
     */
    public function is($role_names)
    {
        if (is_array($role_names)) {
            return $this->rolesInNames($role_names)->exists();
        } else {
            return $this->rolesByName($role_names)->exists();
        }
    }

    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->is('super_admin');
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is('admin');
    }

    /**
     * @return bool
     */
    public function isStaff()
    {
        return $this->is($this->staffRoles);
    }
}
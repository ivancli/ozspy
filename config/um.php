<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/02/2017
 * Time: 5:44 PM
 */


return [
    /*
    |--------------------------------------------------------------------------
    | UM Role Model
    |--------------------------------------------------------------------------
    |
    | This is the Role model used by UM to create correct relations.  Update
    | the role if it is in a different namespace.
    |
    */
    'role' => 'App\Models\Role',
    /*
    |--------------------------------------------------------------------------
    | UM Roles Table
    |--------------------------------------------------------------------------
    |
    | This is the roles table used by UM to save roles to the database.
    |
    */
    'roles_table' => 'roles',
    /*
    |--------------------------------------------------------------------------
    | Application User Model
    |--------------------------------------------------------------------------
    |
    | This is the User model used by UM to create correct relations.
    | Update the User if it is in a different namespace.
    |
    */
    'user' => 'App\Models\User',
    /*
    |--------------------------------------------------------------------------
    | Application Users Table
    |--------------------------------------------------------------------------
    |
    | This is the users table used by the application to save users to the
    | database.
    |
    */
    'users_table' => 'users',
    /*
    |--------------------------------------------------------------------------
    | UM Permission Model
    |--------------------------------------------------------------------------
    |
    | This is the Permission model used by UM to create correct relations.
    | Update the permission if it is in a different namespace.
    |
    */
    'permission' => 'App\Models\Permission',
    /*
    |--------------------------------------------------------------------------
    | UM Permissions Table
    |--------------------------------------------------------------------------
    |
    | This is the permissions table used by UM to save permissions to the
    | database.
    |
    */
    'permissions_table' => 'permissions',
    /*
    |--------------------------------------------------------------------------
    | UM permission_role Table
    |--------------------------------------------------------------------------
    |
    | This is the permission_role table used by UM to save relationship
    | between permissions and roles to the database.
    |
    */
    'permission_role_table' => 'permission_role',
    /*
    |--------------------------------------------------------------------------
    | UM role_user Table
    |--------------------------------------------------------------------------
    |
    | This is the role_user table used by UM to save assigned roles to the
    | database.
    |
    */
    'role_user_table' => 'role_user',
    /*
    |--------------------------------------------------------------------------
    | UM Group Model
    |--------------------------------------------------------------------------
    |
    | This is the Group model used by UM to create correct relations.
    | Update the group if it is in a different namespace.
    |
    */
    'group' => 'App\Models\Group',
    /*
    |--------------------------------------------------------------------------
    | UM Groups Table
    |--------------------------------------------------------------------------
    |
    | This is the groups table used by UM to save groups to the
    | database.
    |
    */
    'groups_table' => 'groups',
    /*
    |--------------------------------------------------------------------------
    | UM group_user Table
    |--------------------------------------------------------------------------
    |
    | This is the group_user table used by UM to save assigned groups to the
    | database.
    |
    */
    'group_user_table' => 'group_user',
];
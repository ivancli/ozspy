<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    protected $roleModel;

    public function __construct(\App\Models\Role $roleModel)
    {
        $this->roleModel = $roleModel;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = $this->roleModel->create([
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'Role with all permissions'
        ]);

        $client = $this->roleModel->create([
            'name' => 'client',
            'display_name' => 'client',
            'description' => 'Client with normal user permissions'
        ]);
    }
}

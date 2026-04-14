<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

/**
 * Chạy sau migration role_permissions: php artisan db:seed --class=RolePermissionSeeder
 * (An toàn khi chạy lại — cập nhật quyền mặc định cho admin/manager/editor.)
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::query()->where('name', 'admin')->first();
        if (! $admin) {
            return;
        }

        $this->apply($admin->id, [
            'tours' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
            'users' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
            'bookings' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
            'reports' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
        ]);

        $manager = Role::query()->firstOrCreate(
            ['name' => 'manager'],
            ['description' => 'Quản lý Tour & Đơn hàng']
        );
        $this->apply($manager->id, [
            'tours' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
            'users' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
            'bookings' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
            'reports' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
        ]);

        $editor = Role::query()->firstOrCreate(
            ['name' => 'editor'],
            ['description' => 'Cập nhật nội dung Tour']
        );
        $this->apply($editor->id, [
            'tours' => ['view' => true, 'create' => false, 'edit' => true, 'delete' => false],
            'users' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
            'bookings' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
            'reports' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
        ]);
    }

    /**
     * @param  array<string, array{view: bool, create: bool, edit: bool, delete: bool}>  $matrix
     */
    private function apply(int $roleId, array $matrix): void
    {
        foreach ($matrix as $moduleKey => $flags) {
            RolePermission::query()->updateOrCreate(
                [
                    'role_id' => $roleId,
                    'module_key' => $moduleKey,
                ],
                [
                    'can_view' => $flags['view'],
                    'can_create' => $flags['create'],
                    'can_edit' => $flags['edit'],
                    'can_delete' => $flags['delete'],
                ]
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRoleController extends Controller
{
    /** @var list<array{key: string, label: string}> */
    private const MODULES = [
        ['key' => 'tours', 'label' => 'Quản lý Tour'],
        ['key' => 'users', 'label' => 'Người dùng'],
        ['key' => 'bookings', 'label' => 'Đơn đặt chỗ'],
        ['key' => 'reports', 'label' => 'Báo cáo & Thống kê'],
    ];

    public function index(): JsonResponse
    {
        $roles = Role::query()
            ->with('rolePermissions')
            ->withCount('users')
            ->orderBy('id')
            ->get();

        $payloadRoles = $roles->map(fn (Role $role) => $this->serializeRole($role));

        return response()->json([
            'data' => [
                'modules' => self::MODULES,
                'roles' => $payloadRoles,
                'activities' => [],
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
        ]);

        $role = DB::transaction(function () use ($data, $request) {
            $role = Role::query()->create([
                'name' => strtolower($data['name']),
                'description' => $data['description'] ?? null,
            ]);
            $this->syncPermissions($role, $request->input('permissions', []));

            return $role->fresh(['rolePermissions']);
        });

        return response()->json(['data' => $this->serializeRole($role->loadCount('users'))], 201);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:roles,name,'.$role->id],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
        ]);

        if (($data['name'] ?? null) === 'admin' && $role->name !== 'admin') {
            return response()->json(['message' => 'Không thể đổi tên thành admin'], 422);
        }

        if (isset($data['name']) && $role->name === 'admin' && $data['name'] !== 'admin') {
            return response()->json(['message' => 'Không thể đổi tên vai trò hệ thống admin'], 422);
        }

        DB::transaction(function () use ($role, $data, $request) {
            $fill = array_intersect_key($data, array_flip(['name', 'description']));
            if (isset($fill['name'])) {
                $fill['name'] = strtolower($fill['name']);
            }
            if ($fill !== []) {
                $role->fill($fill)->save();
            }
            if ($request->has('permissions')) {
                $this->syncPermissions($role->fresh(), $request->input('permissions', []));
            }
        });

        $role->refresh()->load(['rolePermissions']);
        $role->loadCount('users');

        return response()->json(['data' => $this->serializeRole($role)]);
    }

    public function destroy(Role $role): JsonResponse
    {
        if ($role->name === 'admin') {
            return response()->json(['message' => 'Không thể xóa vai trò admin'], 422);
        }

        if ($role->users()->count() > 0) {
            return response()->json(['message' => 'Vẫn còn tài khoản gắn vai trò này'], 422);
        }

        $role->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * @param  array<string, array<string, bool>>  $permissions
     */
    private function syncPermissions(Role $role, array $permissions): void
    {
        $allowedKeys = collect(self::MODULES)->pluck('key')->all();

        foreach ($allowedKeys as $key) {
            $p = $permissions[$key] ?? [];
            RolePermission::query()->updateOrCreate(
                [
                    'role_id' => $role->id,
                    'module_key' => $key,
                ],
                [
                    'can_view' => (bool) ($p['view'] ?? false),
                    'can_create' => (bool) ($p['create'] ?? false),
                    'can_edit' => (bool) ($p['edit'] ?? false),
                    'can_delete' => (bool) ($p['delete'] ?? false),
                ]
            );
        }
    }

    private function serializeRole(Role $role): array
    {
        $perms = [];
        foreach (self::MODULES as $mod) {
            $key = $mod['key'];
            $rp = $role->rolePermissions->firstWhere('module_key', $key);
            $perms[$key] = [
                'view' => $rp ? $rp->can_view : false,
                'create' => $rp ? $rp->can_create : false,
                'edit' => $rp ? $rp->can_edit : false,
                'delete' => $rp ? $rp->can_delete : false,
            ];
        }

        return [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'users_count' => (int) ($role->users_count ?? $role->users()->count()),
            'is_system' => $role->name === 'admin',
            'permissions' => $perms,
        ];
    }
}

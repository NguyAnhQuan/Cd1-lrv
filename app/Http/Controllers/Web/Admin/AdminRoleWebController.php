<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Api\Admin\AdminRoleController;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRoleWebController extends Controller
{
    public function index(): View
    {
        $json = app(AdminRoleController::class)->index();
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json->getContent(), true) ?? [];
        $data = $decoded['data'] ?? [];

        return view('admin.roles.index', [
            'modules' => $data['modules'] ?? [],
            'roles' => $data['roles'] ?? [],
        ]);
    }

    public function create(): View
    {
        $json = app(AdminRoleController::class)->index();
        $decoded = json_decode($json->getContent(), true) ?? [];
        $data = $decoded['data'] ?? [];

        return view('admin.roles.form', [
            'role' => null,
            'modules' => $data['modules'] ?? [],
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        $request->merge(['permissions' => $this->permissionArrayFromRequest($request)]);

        $json = app(AdminRoleController::class)->store($request);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return back()->withErrors(['message' => $body['message'] ?? 'Lỗi'])->withInput();
        }

        return redirect()->route('admin.roles.index')->with('success', 'Đã tạo vai trò.');
    }

    public function edit(Role $role): View
    {
        $json = app(AdminRoleController::class)->index();
        $decoded = json_decode($json->getContent(), true) ?? [];
        $data = $decoded['data'] ?? [];
        $roles = $data['roles'] ?? [];
        $current = null;
        foreach ($roles as $r) {
            if (is_array($r) && (int) ($r['id'] ?? 0) === $role->id) {
                $current = $r;
                break;
            }
        }
        if ($current === null) {
            abort(404);
        }

        return view('admin.roles.form', [
            'role' => $current,
            'modules' => $data['modules'] ?? [],
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:roles,name,'.$role->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        $request->merge(['permissions' => $this->permissionArrayFromRequest($request)]);

        $json = app(AdminRoleController::class)->update($request, $role);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return back()->withErrors(['message' => $body['message'] ?? 'Lỗi'])->withInput();
        }

        return redirect()->route('admin.roles.index')->with('success', 'Đã cập nhật vai trò.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $json = app(AdminRoleController::class)->destroy($role);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return redirect()->route('admin.roles.index')->with('error', $body['message'] ?? 'Không xóa được.');
        }

        return redirect()->route('admin.roles.index')->with('success', 'Đã xóa vai trò.');
    }

    /**
     * @return array<string, array<string, bool>>
     */
    private function permissionArrayFromRequest(Request $request): array
    {
        $modules = ['tours', 'users', 'bookings', 'reports'];
        $permissions = [];
        foreach ($modules as $key) {
            $permissions[$key] = [
                'view' => $request->boolean("perm_{$key}_view"),
                'create' => $request->boolean("perm_{$key}_create"),
                'edit' => $request->boolean("perm_{$key}_edit"),
                'delete' => $request->boolean("perm_{$key}_delete"),
            ];
        }

        return $permissions;
    }
}

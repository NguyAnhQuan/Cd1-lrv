<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminUserWebController extends Controller
{
    public function index(): View
    {
        $json = app(AdminUserController::class)->index();
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json->getContent(), true) ?? [];
        /** @var list<array<string, mixed>> $users */
        $users = $decoded['data'] ?? [];

        return view('admin.users.index', ['users' => $users]);
    }

    public function edit(User $user): View
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.edit', [
            'editUser' => $user->load('roles'),
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->setUserResolver(fn () => Auth::user());
        $json = app(AdminUserController::class)->update($request, $user);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return back()->withErrors(['message' => $body['message'] ?? 'Cập nhật thất bại'])->withInput();
        }

        return redirect()->route('admin.users.index')->with('success', 'Đã cập nhật người dùng.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $request->setUserResolver(fn () => Auth::user());
        $json = app(AdminUserController::class)->destroy($request, $user);
        $body = json_decode($json->getContent(), true);
        if ($json->getStatusCode() >= 400) {
            return back()->with('error', $body['message'] ?? 'Không xóa được.');
        }

        return redirect()->route('admin.users.index')->with('success', 'Đã xóa người dùng.');
    }
}

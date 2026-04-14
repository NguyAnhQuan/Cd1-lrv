<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileWebController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return view('web.profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $u */
        $u = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($u->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'], $u->password ?? '')) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])->withInput();
            }
            $u->password = $data['password'];
        }

        $u->name = $data['name'];
        $u->email = $data['email'];
        $u->phone = $data['phone'] ?? null;
        $u->save();

        return back()->with('success', 'Đã cập nhật hồ sơ.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'password' => ['required', 'string'],
        ]);

        /** @var \App\Models\User $u */
        $u = Auth::user();

        if (! Hash::check($data['password'], $u->password ?? '')) {
            return back()->withErrors(['password' => 'Mật khẩu không đúng.']);
        }

        $u->deleteCompletely();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Tài khoản đã được xóa.');
    }
}

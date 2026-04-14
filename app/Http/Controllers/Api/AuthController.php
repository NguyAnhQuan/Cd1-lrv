<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'status' => 'active',
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        /** @var User|null $user */
        $user = User::query()->where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password ?? '')) {
            return response()->json(['message' => 'Email hoặc mật khẩu không đúng'], 422);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->userPayload($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $u */
        $u = $request->user();

        return response()->json([
            'user' => $this->userPayload($u),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $u */
        $u = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($u->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'], $u->password ?? '')) {
                return response()->json(['message' => 'Mật khẩu hiện tại không đúng'], 422);
            }
            $u->password = $data['password'];
        }

        $u->name = $data['name'];
        $u->email = $data['email'];
        $u->phone = $data['phone'] ?? null;
        $u->save();

        return response()->json([
            'user' => $this->userPayload($u->fresh()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['ok' => true]);
    }

    public function destroyAccount(Request $request): JsonResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string'],
        ]);

        /** @var User $u */
        $u = $request->user();

        if (! Hash::check($data['password'], $u->password ?? '')) {
            return response()->json(['message' => 'Mật khẩu không đúng'], 422);
        }

        $u->deleteCompletely();

        return response()->json(['ok' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_admin' => $user->isAdmin(),
            'loyalty_points' => (int) ($user->loyalty_points ?? 0),
        ];
    }
}


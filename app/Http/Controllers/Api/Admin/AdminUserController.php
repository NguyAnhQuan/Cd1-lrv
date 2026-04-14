<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()
            ->with('roles')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $ids = $users->pluck('id');
        $stats = collect();
        if ($ids->isNotEmpty()) {
            $stats = Booking::query()
                ->selectRaw('user_id, COALESCE(SUM(total_price), 0) as total_spend, COUNT(*) as trips_count')
                ->whereIn('user_id', $ids)
                ->where(function ($q) {
                    $q->whereNull('status')->orWhere('status', '!=', 'cancelled');
                })
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');
        }

        return response()->json([
            'data' => $users->map(fn (User $u) => $this->serializeUser($u, $stats[$u->id] ?? null)),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'string', 'max:2048'],
            'role_names' => ['nullable', 'array'],
            'role_names.*' => ['string', 'exists:roles,name'],
        ]);

        $roleNames = $data['role_names'] ?? null;
        unset($data['role_names']);

        $wasAdmin = $user->roles()->where('name', 'admin')->exists();

        $user->fill($data)->save();

        if ($roleNames !== null) {
            if ($request->user()->id === $user->id && $wasAdmin && ! in_array('admin', $roleNames, true)) {
                $roleNames[] = 'admin';
            }
            $ids = Role::query()->whereIn('name', $roleNames)->pluck('id');
            $user->roles()->sync($ids);
        }

        $fresh = $user->fresh(['roles']);
        $statsRow = $this->statsForUser($user->id);

        return response()->json(['data' => $this->serializeUser($fresh, $statsRow)]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'Không thể xóa chính tài khoản đang đăng nhập.'], Response::HTTP_FORBIDDEN);
        }

        $user->deleteCompletely();

        return response()->json(['ok' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeUser(User $u, ?object $statsRow = null): array
    {
        $spend = $statsRow ? (float) $statsRow->total_spend : 0.0;
        $trips = $statsRow ? (int) $statsRow->trips_count : 0;

        return [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'phone' => $u->phone,
            'avatar' => $u->avatar,
            'status' => $u->status,
            'created_at' => $u->created_at,
            'roles' => $u->roles?->pluck('name')->values()->all() ?? [],
            'total_spend' => $spend,
            'trips_count' => $trips,
        ];
    }

    private function statsForUser(int $userId): ?object
    {
        return Booking::query()
            ->selectRaw('COALESCE(SUM(total_price), 0) as total_spend, COUNT(*) as trips_count')
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'cancelled');
            })
            ->first();
    }
}


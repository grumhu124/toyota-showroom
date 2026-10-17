<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSalesRequest;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Models\User;
use App\Services\Account\UpdateUserStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::enum(UserRole::class)],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $query = User::query();
        $search = trim($filters['q'] ?? '');
        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }
        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        return view('admin.users.index', [
            'users' => $query->orderByDesc('id')->paginate(15)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('admin.users.create');
    }

    public function store(StoreSalesRequest $request): RedirectResponse
    {
        $user = new User($request->safe()->only(['name', 'email', 'phone', 'password']));
        $user->forceFill(['role' => UserRole::Sales, 'is_active' => true])->save();

        return redirect()->route('admin.users.index')
            ->with('status', 'Đã tạo tài khoản nhân viên.');
    }

    public function updateStatus(UpdateUserStatusRequest $request, User $user, UpdateUserStatus $service): RedirectResponse
    {
        $service->handle($request->user(), $user, $request->boolean('is_active'));

        return redirect()->route('admin.users.index')
            ->with('status', 'Đã cập nhật trạng thái tài khoản.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['businessInstance.businessType', 'instanceRole', 'sucursal'])
            ->when($request->business_instance_id, fn ($q) => $q->where('business_instance_id', $request->business_instance_id))
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->online, fn ($q) => $request->online ? $q->whereNotNull('last_seen_at')->whereRaw('last_seen_at > NOW() - INTERVAL 5 MINUTE') : $q->whereNull('last_seen_at'));

        return UserResource::collection($query->orderBy('created_at', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin,supervisor',
            'business_instance_id' => 'nullable|exists:business_instances,id',
            'instance_role_id' => 'nullable|exists:instance_roles,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'business_type_id' => 'nullable|exists:business_types,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'business_instance_id' => $validated['business_instance_id'],
            'instance_role_id' => $validated['instance_role_id'],
            'sucursal_id' => $validated['sucursal_id'],
            'business_type_id' => $validated['business_type_id'],
        ]);

        return new UserResource($user->load(['businessInstance.businessType', 'instanceRole', 'sucursal']));
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['businessInstance.businessType', 'instanceRole', 'sucursal']));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:user,admin,supervisor',
            'business_instance_id' => 'sometimes|exists:business_instances,id',
            'instance_role_id' => 'sometimes|exists:instance_roles,id',
            'sucursal_id' => 'sometimes|exists:sucursales,id',
            'business_type_id' => 'sometimes|exists:business_types,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return new UserResource($user->load(['businessInstance.businessType', 'instanceRole', 'sucursal']));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted.']);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user()->load(['businessInstance.businessType', 'instanceRole', 'sucursal']));
    }
}

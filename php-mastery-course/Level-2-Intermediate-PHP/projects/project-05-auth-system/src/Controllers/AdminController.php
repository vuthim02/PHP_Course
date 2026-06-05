<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Core\Database;

class AdminController
{
    public function __construct()
    {
        if (\App\Core\Session::get('user_role') !== 'admin') {
            \App\Core\Session::flash('error', 'Admin access required.');
            \App\Core\View::redirect('/');
        }
    }

    public function dashboard(): void
    {
        $userCount = count(User::all());
        $roleCount = count(Role::all());
        $permissionCount = count(Permission::all());
        View::render('admin/dashboard', compact('userCount', 'roleCount', 'permissionCount'));
    }

    public function users(): void
    {
        $users = User::all();
        $roles = Role::all();
        View::render('admin/users', compact('users', 'roles'));
    }

    public function updateUserRole(int $id): void
    {
        $user = User::find($id);
        if (!$user) { View::redirect('/admin/users'); }

        $user->role = Request::post('role', 'user');
        $user->save();

        Session::flash('success', 'User role updated.');
        View::redirect('/admin/users');
    }

    public function toggleUserStatus(int $id): void
    {
        $user = User::find($id);
        if ($user && $user->id !== (int) Session::get('user_id')) {
            $user->status = $user->status === 'active' ? 'suspended' : 'active';
            $user->save();
        }
        View::redirect('/admin/users');
    }

    public function deleteUser(int $id): void
    {
        $user = User::find($id);
        if ($user && $user->id !== (int) Session::get('user_id')) {
            Database::getInstance()->delete('users', 'id = ?', [$id]);
        }
        View::redirect('/admin/users');
    }

    public function roles(): void
    {
        $roles = Role::all();
        $permissions = Permission::all();
        View::render('admin/roles', compact('roles', 'permissions'));
    }

    public function storeRole(): void
    {
        $role = new Role();
        $role->name = Request::post('name', '');
        $role->description = Request::post('description', '');
        $role->save();

        Session::flash('success', 'Role created.');
        View::redirect('/admin/roles');
    }

    public function deleteRole(int $id): void
    {
        $role = Role::find($id);
        if ($role) $role->delete();
        View::redirect('/admin/roles');
    }

    public function assignPermission(int $id): void
    {
        $role = Role::find($id);
        $permissionId = (int) Request::post('permission_id');

        if ($role && $permissionId) {
            $role->assignPermission($permissionId);
            Session::flash('success', 'Permission assigned.');
        }

        View::redirect('/admin/roles');
    }

    public function removePermission(int $roleId, int $permissionId): void
    {
        $role = Role::find($roleId);
        if ($role) $role->removePermission($permissionId);
        View::redirect('/admin/roles');
    }

    public function permissions(): void
    {
        $permissions = Permission::all();
        View::render('admin/permissions', compact('permissions'));
    }

    public function storePermission(): void
    {
        $perm = new Permission();
        $perm->name = Request::post('name', '');
        $perm->description = Request::post('description', '');
        $perm->save();

        Session::flash('success', 'Permission created.');
        View::redirect('/admin/permissions');
    }

    public function deletePermission(int $id): void
    {
        $perm = Permission::find($id);
        if ($perm) $perm->delete();
        View::redirect('/admin/permissions');
    }
}

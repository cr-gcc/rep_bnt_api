<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Campaign;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\RolesAndPermissions\RolPermissionsCampaignsRequest;
use App\Http\Resources\RoleAccessControlResource;
use Illuminate\Support\Facades\Auth;

class RolesAndPermissionsController extends Controller
{
	public function roles()
	{
		$levels = [];
		if (Auth::user()->hasRole('Super-Admin')) {
			$levels = [];
		} else if (Auth::user()->can('nivel-2')) {
			$levels = ['nivel-1', 'nivel-2'];
		} else if (Auth::user()->can('nivel-3')) {
			$levels = ['nivel-1', 'nivel-2', 'nivel-3'];
		} else if (Auth::user()->can('nivel-4')) {
			$levels = ['nivel-1', 'nivel-2', 'nivel-3', 'nivel-4'];
		} else {
			$levels = [];
		}
		
		$query = Role::with('permissions');

		if (!empty($levels)) {
			$query->whereDoesntHave('permissions', function ($q) use ($levels) {
				$q->whereIn('name', $levels);
			});
		}

		$roles = $query->orderBy('name')->get();

		return response()->json($roles, 200);
	}

	public function roleAccess($role_id)
	{
		$role = Role::find($role_id);
		return new RoleAccessControlResource($role);
	}

	public function permissions()
	{
		$permissions = Permission::all();
		return response()->json($permissions, 200);
	}

	public function permissionsByGroup()
	{
		$permissions = Permission::orderBy('group')
			->get()
			->groupBy('group');
		return response()->json($permissions, 200);
	}

	public function accessControl(RolPermissionsCampaignsRequest $request)
	{
		$request->validated();
		$role_id = $request->input('role_id');
		$permissions = $request->input('permissions_id');
		$campaigns = $request->input('campaigns_id');
		try {
			$role = Role::find($role_id);
			// syncPermissions will add new permissions AND remove old ones (complete replacement)
			$role->syncPermissions($permissions);
			// sync will add new campaigns AND remove old ones (complete replacement)
			$role->campaigns()->sync($campaigns);
			$role->load('permissions', 'campaigns');
			return new RoleAccessControlResource($role);
		} catch (\Exception $e) {
			return response()->json($e, 500);
		}
	}
}

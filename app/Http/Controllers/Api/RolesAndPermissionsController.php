<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Campaign;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\RolesAndPermissions\RolPermissionsCampaignsRequest;
use App\Http\Resources\RoleAccessControlResource;

class RolesAndPermissionsController extends Controller
{
	public function roles()
	{
		$roles = Role::with('permissions')->get();
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
		$permissions = Permission::all()->groupBy('group');
		return response()->json($permissions, 200);
	}

	public function accessControl(RolPermissionsCampaignsRequest $request)
	{
		$request->validated();
		$role_id = $request->input('role_id');
		$permissions = $request->input('permissions_id');
		$campaigns = $request->input('campaigns_id');
		\Log::info($role_id);
		\Log::info($permissions);
		\Log::info($campaigns);	
		try {
			\Log::info('entro al try');
			
			$role = Role::find($role_id);
			// syncPermissions will add new permissions AND remove old ones (complete replacement)
			$role->syncPermissions($permissions);
			// sync will add new campaigns AND remove old ones (complete replacement)
			$role->campaigns()->sync($campaigns);
			$role->load('permissions', 'campaigns');
			return new RoleAccessControlResource($role);
		} catch (\Exception $e) {
			\Log::info($e);
			return response()->json($e, 500);
		}
	}
}

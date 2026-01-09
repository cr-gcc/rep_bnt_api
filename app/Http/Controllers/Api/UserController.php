<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$users = User::with('roles')->get();
		return response()->json($users, 200);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreUserRequest $request)
	{
		$data  = [];
		$status = 400;
		//	Usuario a crear
		try {
			$password = config('app.default_password');
			$user_data = [
				'name' => $request->name,
				'last_name' => $request->last_name,
				'email' => $request->email,
				'user' => $request->user,
				'password' => Hash::make($password),
				'birthday' => $request->birthday,
			];
			$user = User::create($user_data);
			$role_id = $request->role_id;
			$role = Role::find($role_id);
			//	Actualiza roles y permisos
			$user->changeRole($role);
			$data = [
				'data' => $user,
				'message' => 'Usuario creado correctamente',
			];
			$status = 200;
		} catch (\Exception $e) {
			$data = [
				'data' => [],
				'message' => 'Error al crear el usuario.'
			];
			$status = 500;
		}

		return response()->json($data, $status);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(string $id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateUserRequest $request, string $id)
	{
		$data  = [];
		$status = 400;
		//	Usuario a actualizar
		try {
			$user = User::findOrFail($id);
			$user->update($request->validated());
			$role_id = $request->role_id;
			$role = Role::find($role_id);
			//	Actualiza roles y permisos
			$user->changeRole($role);
			$data = [
				'data' => $user,
				'message' => 'Usuario actualizado correctamente',
			];
			$status = 200;
		} catch (\Exception $e) {
			$data = [
				'data' => [],
				'message' => 'Error al actualizar el usuario.'
			];
			$status = 500;
		}

		return response()->json($data, $status);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
		//
	}
}

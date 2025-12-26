<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\User\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
	function version()
	{
		return response()->json([
			'message' => "Reportería Banorte v0.1",
		], 200);
	}
	// Login y asignar token en cookie (acepta email o RFC)
	public function login(Request $request)
	{
		// Obtener el identificador (puede ser email o RFC/user)
		$identifier = $request->input('email'); // Mantener compatibilidad con frontend
		$password = $request->input('password');

		// Intentar autenticación primero con email
		$credentials = ['email' => $identifier, 'password' => $password];
		
		if (!Auth::attempt($credentials)) {
			// Si falla, intentar con el campo 'user' (RFC)
			$credentials = ['user' => $identifier, 'password' => $password];
			
			if (!Auth::attempt($credentials)) {
				return response()->json(['message' => 'Credenciales inválidas'], 401);
			}
		}

		$user = Auth::user();
		$token = $user->createToken('Vue3App')->accessToken;

		$cookie = cookie(
			'access_token',
			$token,
			60 * 24,       // 1 día
			'/',
			null,
			true,        // Secure (HTTPS)
			true,        // HttpOnly
			false,
			'Strict'     // SameSite
		);

		return response()->json(['user' => $user])->withCookie($cookie);
	}
	//
	public function logout()
	{
		$cookie = cookie()->forget('access_token');
		return response()->json(['message' => 'Logout exitoso'])->withCookie($cookie);
	}
	//
	public function me(Request $request)
	{
		return response()->json($request->user());
	}
	//
	public function resetPassword($user_id)
	{
		$status = 400;
		$data = [];
		$user = User::findOrFail($user_id);
		if ($user) {
			try {
				$default_password = config('app.default_password');
				$user->password = Hash::make($default_password);
				$user->change_password = 0;
				$user->save();
				$data = [
					'message' => 'Contraseña cambiada exitosamente',
				];
				$status = 200;
			} catch (\Exception $e) {
				$data = [
					'error' => 'Error al cambiar la contraseña',
				];
				$status = 500;
			}
		}
		else {
			$data = [
				'error' => 'Usuario no encontrado',
			];
			$status = 404;
		}
		return response()->json($data, $status);
	}

	public function changePassword(ChangePasswordRequest $request)
	{
		$status = 400;
		$data = [];
		$user_id = Auth::user()->id;
		$user = User::findOrFail($user_id);
		if ($user) {
			try {
				$password = $request->password;
				$user->password = Hash::make($password);
				$user->change_password = 1;
				$user->save();
				$data = [
					'data' => $user,
					'message' => 'Contraseña cambiada exitosamente',
				];
				$status = 200;
			} catch (\Exception $e) {
				$data = [
					'data' => [],
					'error' => 'Error al cambiar la contraseña',
				];
				$status = 500;
			}
		}
		else {
			$data = [
				'data' => [],
				'error' => 'Usuario no encontrado',
			];
			$status = 404;
		}
		return response()->json($data, $status);
	}
	
}

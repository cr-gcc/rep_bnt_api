<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
}

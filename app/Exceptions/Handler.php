<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<Throwable>>
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed to the session.
	 *
	 * @var array<int, string>
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Handle unauthenticated exceptions for API (Sanctum).
	 */
	protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
	{
		return response()->json([
			'message' => 'Unauthenticated.'
		], 401);
	}
}

<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\GetDatesRequest;
use Illuminate\Http\Request;

class SaleController extends Controller
{
	public function getGeneralCounts(GetDatesRequest $request)
	{
		$validated = $request->validated();
	}
}

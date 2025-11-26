<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
	public function index()
	{
		$campaigns = Campaign::where('active', true)->get();
		return response()->json($campaigns, 200);
	}
}

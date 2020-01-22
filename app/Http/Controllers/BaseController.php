<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
	protected $data = null;

	/**
	 * @param bool $error
	 * @param int $responseCode
	 * @param array $message
	 * @param null $data
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseJson($error = true, $responseCode = 200, $message = [], $data = null)
	{
		return response()->json([
			'error'         =>  $error,
			'response_code' => $responseCode,
			'message'       => $message,
			'data'          =>  $data
		]);
	}
}

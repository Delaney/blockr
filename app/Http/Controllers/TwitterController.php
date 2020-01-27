<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\GuzzleException;
use App\Http\Controllers\BaseController;

use App\Client\Server\Twitter;

class TwitterController extends BaseController
{
	protected $server;

	public function __construct()
	{
		$this->middleware('guest');
	}

	public function start()
	{
		session_start();
		
		$callback = env('APP_URL') . ':8000/cb';
		$identifier = env('TWITTER_API_KEY');
		$key = env('TWITTER_API_SECRET');

		$server = new Twitter(array(
			'identifier' => $identifier,
			'secret' => $key,
			'callback_uri' => $callback,
		));

		$temporaryCredentials = $server->getTemporaryCredentials();
		
		$_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
		session_write_close();
		
		$server->authorize($temporaryCredentials);
		// var_dump($temporaryCredentials);
	}

	public function token(Request $request)
	{
		session_start();
		
		$callback = env('APP_URL') . ':8000/cb';
		$identifier = env('TWITTER_API_KEY');
		$key = env('TWITTER_API_SECRET');

		$server = new Twitter(array(
			'identifier' => $identifier,
			'secret' => $key,
			'callback_uri' => $callback,
		));

		if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
			$temporaryCredentials = unserialize($_SESSION['temporary_credentials']);

			$token = $server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);

			$user = $server->getUserDetails($token);

			var_dump($user);
		}
	}
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Http\Controllers\BaseController;

class UserController extends BaseController
{
	public function __construct()
	{
		$this->middleware('guest');
	}

	public function start()
	{
		$client = new Client();
		$callback = env('APP_URL') . ':8000/cb';
		$key = env('TWITTER_API_KEY');
		$url = 'https://api.twitter.com/oauth/request_token';

		// return $key;

		$request = $client->post($url, [
			'form_params' => [
				'oauth_callback' => $callback,
				'oauth_consumer_key' => $key
			]
		]);

		return $request;

	}

	protected function temporaryCredentialsProtocolHeader($uri)
	{
		$callback = env('APP_URL') . ':8000/cb';
		$params = array_merge($this->baseProtocolParameters(), array('oauth_callback' => $callback));

		$params['oauth_signature'] = $this->signature->sign($uri, $params, 'POST');
	}

	protected function baseProtocolParameters()
	{
		$dateTime = new \DateTime();

		return array(
			'oauth_consumer_key' => env('TWITTER_API_KEY'),
			'oauth_nonce' => ,
			'oauth_signature_method' => $this->nonce(),
			'oauth_timestamp' => $dateTime->format('U'),
			'oauth_version' => '1.0',
		);
	}

	protected function nonce($length = 32)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}
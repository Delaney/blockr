<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\GuzzleException;
use App\Http\Controllers\BaseController;
use App\Models\User;

use App\Client\Server\Twitter;
use App\Client\Credentials\TokenCredentials;

use App\Client\Blockr\Block;

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
			$exists = User::where('uid', '=', $user->uid)->first();

			if (empty($exists)) {
				$user->identifier = $token->getIdentifier();
				$user->secret = $token->getSecret();
				$user->save();
			} else {
				$user = $exists;
			}
		}

	}

	public function index()
	{
		// if (!Auth::check()) {
		// 	return view('welcome');
		// } else {
		// 	$user = Auth::user();

		// 	return view('admin.index');
		// }

		return view('admin.index');
	}
	
	public function botcheck(Request $request)
	{
		$token = new TokenCredentials;
		$token->setIdentifier(env('BOT_IDENTIFIER'));
		$token->setSecret(env('BOT_SECRET'));

		// var_dump($token);

		$callback = env('APP_URL') . ':8000/cb';

		$server = new Twitter(array(
			'identifier' => env('TWITTER_API_KEY'),
			'secret' => env('TWITTER_API_SECRET'),
			'callback_uri' => $callback,
		));

		$messages = $server->getBotMessages($token);
		$jsn = json_encode($messages, true);
		$arr = json_decode($jsn, true);

		$messages = $arr["events"];

		// var_dump($arr);
		// print_r($arr["events"]);
		// echo count($arr["events"]);
		// return json_encode($json, true));

		// foreach($arr['events'] as $key => $value) {
		// 	echo nl2br("$key\n" . $value["message_create"]["sender_id"] . "\n\n");
		// }

		// $sender0_id = $arr['events'][1]["message_create"]["sender_id"];

		$sender0 = new Block($messages[1]);
		echo $sender0->getSenderHandle();

		// $this->printArray($arr);

		// var_dump($arr['events'][1]);
	}

	public function botMessages(Request $request)
	{
		return view('admin.pages.messages');
	}

	public function printArray($arr) {
		foreach($arr as $key => $value) {
			if (gettype($value) != "array") {
				echo nl2br("$key => $value\n");
			} else {
				$this->printArray($value);
			}
		}
	}
}
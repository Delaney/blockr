<?php

namespace App\Client\Server;

use App\Client\Credentials\TokenCredentials;
use App\Models\User;

class Twitter extends Server
{
	public function urlTemporaryCredentials()
	{
		return 'https://api.twitter.com/oauth/request_token';
	}

	public function urlAuthorization()
	{
		return 'https://api.twitter.com/oauth/authenticate';
	}

	public function urlTokenCredentials()
	{
		return 'https://api.twitter.com/oauth/access_token';
	}

	public function urlUserDetails()
	{
		return 'https://api.twitter.com/1.1/account/verify_credentials.json?';
	}

	public function userDetails($dataRaw, TokenCredentials $tokenCredentials)
	{
		$user = new User();

		$data = array_key_first($dataRaw);
		$data .= '""}}}';
		$data = json_decode($data, true);

		$user->uid = $data['id_str'];
        $user->handle = $data['screen_name'];
        $user->name = $data['name'];
        $user->email = null;
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        // $used = array('id', 'screen_name', 'name', 'email');

        // foreach ($data as $key => $value) {
        //     if (strpos($key, 'url') !== false) {
        //         if (!in_array($key, $used)) {
        //             $used[] = $key;
        //         }

        //         $user->urls[$key] = $value;
        //     }
        // }

        // Save all extra data
        // $user->extra = array_diff_key($data, array_flip($used));

		return $user;
	}

	public function userUid($data, TokenCredentials $tokenCredentials)
	{
		return $data['id'];
	}

	public function userEmail($data, TokenCredentials $tokenCredentials)
	{
		return;
	}

	public function userScreenName($data, TokenCredentials $tokenCredentials)
	{
		return $data['name'];
	}

	public function userHandle($data, TokenCredentials $tokenCredentials)
	{
		return $data['screen_name'];
	}
}
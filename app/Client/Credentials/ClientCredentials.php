<?php

namespace App\Client\Credentials;

class ClientCredentials extends Credentials implements ClientCredentialsInterface
{
	protected $callbackUri;

	public function getCallbackUri()
	{
		return $this->callbackUri;
	}

	public function setCallbackUri($callbackUri)
	{
		$this->callbackUri = $callbackUri;
	}
}
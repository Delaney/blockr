<?php

namespace App\Client\Credentials;

interface ClientCredentialsInterface extends CredentialsInterface
{
	public function getCallbackUri();

	public function setCallbackUri($callbackUri);

}
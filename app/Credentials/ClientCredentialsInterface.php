<?php

namespace App\Credentials;

interface ClientCredentialsInterface extends CredentialsInterface
{
	public function getCallbackUri();

	public function setCallbackUri();

}
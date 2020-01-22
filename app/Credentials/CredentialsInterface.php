<?php

namespace App\Credentials;

interface CredentialsInterface
{

	public function getIdentifier();

	public function setIdentifier($identifier);

	public function getSecret();

	public function setSecret($secret);

	public function getCallbackUri();

	public function setCallbackUri($callbackUri);

}
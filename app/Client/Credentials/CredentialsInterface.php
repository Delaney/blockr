<?php

namespace App\Client\Credentials;

interface CredentialsInterface
{

	public function getIdentifier();

	public function setIdentifier($identifier);

	public function getSecret();

	public function setSecret($secret);

}
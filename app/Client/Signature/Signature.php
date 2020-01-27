<?php

namespace App\Client\Signature;

use App\Client\Credentials\CredentialsInterface;
use App\Client\Credentials\ClientCredentialsInterface;

abstract class Signature implements SignatureInterface
{
	protected $clientCredentials;
	protected $credentials;

	public function __construct(ClientCredentialsInterface $clientCredentials)
	{
		$this->clientCredentials = $clientCredentials;
	}

	public function setCredentials(CredentialsInterface $credentials)
	{
		$this->credentials = $credentials;
	}

	protected function key()
	{
		$key = rawurlencode($this->clientCredentials->getSecret()) . '&';

		if ($this->credentials !== null){
			$key .= rawurlencode($this->credentials->getSecret());
		}

		return $key;
	}
}
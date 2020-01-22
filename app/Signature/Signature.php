<?php

namespace App\Signature;

use App\Credentials\CredentialsInterface;
use App\Credentials\ClientCredentialsInterface;

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
		$key = rawurlencode($this->clentCredentials->getSecret()) . '&';

		if ($this->credentials !== null){
			$key .= rawurlencode($this->credentials->getSecret());
		}

		return $key;
	}
}
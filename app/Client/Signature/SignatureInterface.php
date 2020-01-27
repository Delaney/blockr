<?php

namespace App\Client\Signature;

use App\Client\Credentials\ClientCredentialsInterface;
use App\Client\Credentials\CredentialsInterface;

interface SignatureInterface
{

	public function __construct(ClientCredentialsInterface $clientCredentials);

	public function setCredentials(CredentialsInterface $credentials);

	public function method();

	public function sign($uri, array $params = array(), $method = 'POST');
}
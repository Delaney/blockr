<?php

namespace App\Signature;

use App\Credentials\ClientCredentialsInterface;
use App\Credentials\CredentialsInterface;

interface SignatureInterface
{

	public function __construct(ClientCredentialsInterface $clientCredentials);

	public function setCredentials(CredentialsInterface $credentials);

	public function method();

	public function sign($uri, array $params = array(), $method = 'POST');
}
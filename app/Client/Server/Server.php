<?php

namespace App\Client\Server;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\BadResponseException;
use App\Client\Credentials\ClientCredentialsInterface;
use App\Client\Credentials\ClientCredentials;
use App\Client\Credentials\CredentialsInterface;
use App\Client\Credentials\CredentialsException;
use App\Client\Credentials\RsaClientCredentials;
use App\Client\Credentials\TemporaryCredentials;
use App\Client\Credentials\TokenCredentials;
use App\Client\Signature\HmacSha1Signature;
use App\Client\Signature\RsaSha1Signature;
use App\Client\Signature\SignatureInterface;

abstract class Server
{
	protected$clientCredentials;

	protected $signature;

	protected $responseType = 'json';

	protected $cacheUserDetailsResponse;

	protected $userAgent;

	public function __construct($clientCredentials, SignatureInterface $signature = null)
	{
		if (is_array($clientCredentials)) {
			$clientCredentials = $this->createClientCredentials($clientCredentials);
		} elseif (!$clientCredentials instanceof ClientCredentialsInterface) {
			throw new \InvalidArgumentException('Client credentials must be an array or valid object.');
		}
	
		$this->clientCredentials = $clientCredentials;
	
		if (!$signature && $clientCredentials instanceof RsaClientCredentials) {
			$signature = new RsaSha1Signature($clientCredentials);
		}
	
		$this->signature = $signature ?: new HmacSha1Signature($clientCredentials);
	}

	public function getTemporaryCredentials()
	{
		$uri = $this->urlTemporaryCredentials();

		$client = $this->createHttpClient();

		$header = $this->temporaryCredentialsProtocolHeader($uri);
		$authorizationHeader = array('Authorization' => $header);
		$headers = $this->buildHttpClientHeaders($authorizationHeader);

		try {
			$response = $client->post($uri, [
				'headers' => $headers,
			]);
		} catch (BadResponseException $e) {
			return $this->handleTemporaryCredentialsBadResponse($e);
		}

		return $this->createTemporaryCredentials((string) $response->getBody());
	}

	public function getAuthorizationUrl($temporaryIdentifier)
	{
		if ($temporaryIdentifier instanceof TemporaryCredentials) {
			$temporaryIdentifier = $temporaryIdentifier->getIdentifier();
		}

		$params = array('oauth_token' => $temporaryIdentifier);

		$url = $this->urlAuthorization();
		$queryString = http_build_query($params);

		return $this->buildUrl($url, $queryString);
	}

	public function authorize($temporaryIdentifier)
	{
		$url = $this->getAuthorizationUrl($temporaryIdentifier);

		header('Location: '.$url);
		die();
	}

	public function getTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verifier)
	{
		if ($temporaryIdentifier !== $temporaryCredentials->getIdentifier()) {
			throw new \InvalidArgumentException(
				'Temporary identifier passed back by server does not match that of stored temporary credentials.
				Potential man-in-the-middle.'
			);
		}

		$uri = $this->urlTokenCredentials();
		$bodyParams = array('oauth_verifier' => $verifier);

		$client = $this->createHttpClient();

		$headers = $this->getHeaders($temporaryCredentials, 'POST', $uri, $bodyParams);

		try {
			$response = $client->post($uri, [
				'headers' => $headers,
				'form_params' => $bodyParams,
			]);
		} catch (BadResponseException $e) {
			return $this->handleTokenCredentialsBadResponse($e);
		}

		return $this->handleTokenCredentials((string) $response->getBody());
	}

	public function getUserDetails(TokenCredentials $tokenCredentials, $force = false)
	{
		$data = $this->fetchUserDetails($tokenCredentials, $force);

		return $this->userDetails($data, $tokenCredentials);
	}

	public function getUserUid(TokenCredentials $tokenCredentials, $force = false)
	{
		$data = $this->fetchUserDetails($tokenCredentials, $force);

		return $this->userUid($data, $tokenCredentials);
	}

	public function getUserEmail(TokenCredentials $tokenCredentials, $force = false)
	{
		$data = $this->fetchUserDetails($tokenCredentials, $force);

		return $this->userEmail($data, $tokenCredentials);
	}

	public function getUserScreenName(TokenCredentials $tokenCredentials, $force = false)
	{
		$data = $this->fetchUserDetails($tokenCredentials, $force);

		return $this->userScreenName($data, $tokenCredentials);
	}

	protected function fetchUserDetails(TokenCredentials $tokenCredentials, $force = false)
	{
		if (!$this->cachedUserDetailsResponse || $force) {
			$url = $this->urlUserDetails();

			$client = $this->createHttpClient();

			$headers = $this->getHeaders($tokenCredentials, 'GET', $url);

			try {
				$response = $client->get($url, [
					'headers' => $headers,
				]);
			} catch (BadResponseException $e) {
				$response = $e->getResponse();
				$body = $response->getBody();
				$statusCode = $reponse->getStatusCode();

				throw new \Exception(
					"Received error [$body] with status code [$statusCode] when retrieving token credentials."
				);
			}

			switch ($this->responseType) {
				case 'json':
					$this->cachedUserDetailsResponse = json_decode((string) $response->getBody(), true);

				case 'xml':
					$this->cachedUserDetailsResponse = simplexml_load_string((string) $response->getBody());
					break;

				case 'string':
					parse_str((string) $response->getBody(), $this->cachedUserDetailsResponse);
					break;

				default:
					throw new \InvalidArgumentException("Invalid responsetype [{$this->responseType}].");
			}
			
			return $this->cachedUserDetailsResponse;
		}
	}

	public function getClientCredentials()
	{
		return $this->clientCredentials;
	}
	
	public function getSignature()
	{
		return $this->signature;
	}
	
	public function createHttpClient()
	{
		return new GuzzleHttpClient();
	}
	
	public function setUserAgent($userAgent = null)
	{
		$this->userAgent = $userAgent;

		return $this;
	}
	
	public function getHeaders(CredentialsInterface $credentials, $method, $url, array $bodyParams = array())
	{
		$header = $this->protocolHeader(strtoupper($method), $url, $credentials, $bodyParams);
		$authorizationHeader = array('Authorization' => $header);
		$headers = $this->buildHttpClientHeaders($authorizationHeader);

		return $headers;
	}

	protected function getHttpClientDefaultHeaders()
	{
		$defaultHeaders = array();
		if (!empty($this->userAgent)) {
			$defaultHeaders['User-Agent'] = $this->userAgent;
		}

		return $defaultHeaders;
	}
	
	protected function buildHttpClientHeaders($headers = array())
	{
		$defaultHeaders = $this->getHttpClientDefaultHeaders();

		return array_merge($headers, $defaultHeaders);
	}
	
	protected function createClientCredentials(array $clientCredentials)
	{
		$keys = array('identifier', 'secret');

		foreach ($keys as $key) {
			if (!isset($clientCredentials[$key])) {
				throw new \InvalidArgumentException("Missing client credentials key [$key] from options.");
			}
		}

		if (isset($clientCredentials['rsa_private_key']) && isset($clientCredentials['rsa_public_key'])) {
			$_clientCredentials = new RsaClientCredentials();
			$_clientCredentials->setRsaPrivateKey($clientCredentials['rsa_private_key']);
			$_clientCredentials->setRsaPublicKey($clientCredentials['rsa_public_key']);
		} else {
			$_clientCredentials = new ClientCredentials();
		}

		$_clientCredentials->setIdentifier($clientCredentials['identifier']);
		$_clientCredentials->setSecret($clientCredentials['secret']);

		if (isset($clientCredentials['callback_uri'])) {
			$_clientCredentials->setCallbackUri($clientCredentials['callback_uri']);
		}

		return $_clientCredentials;
	}

	protected function handleTemporaryCredentialsBadResponse(BadResponseException $e)
	{
		$response = $e->getResponse();
		$body = $response->getBody();
		$statusCode = $response->getStatusCode();

		throw new CredentialsException(
			"Received HTTP status code [$statusCode] with message \"$body\" when getting temporary credentials."
		);
	}
	
	protected function createTemporaryCredentials($body)
	{
		parse_str($body, $data);

		if (!$data || !is_array($data)) {
			throw new CredentialsException('Unable to parse temporary credentials response.');
		}

		if (!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] != 'true') {
			throw new CredentialsException('Error in retrieving temporary credentials.');
		}

		$temporaryCredentials = new TemporaryCredentials();
		$temporaryCredentials->setIdentifier($data['oauth_token']);
		$temporaryCredentials->setSecret($data['oauth_token_secret']);

		return $temporaryCredentials;
	}
	
	protected function handleTokenCredentialsBadResponse(BadResponseException $e)
	{
		$response = $e->getResponse();
		$body = $response->getBody();
		$statusCode = $response->getStatusCode();

		throw new CredentialsException(
			"Received HTTP status code [$statusCode] with message \"$body\" when getting token credentials."
		);
	}

	protected function createTokenCredentials($body)
	{
		parse_str($body, $data);

		if (!$data || !is_array($data)) {
			throw new CredentialsException('Unable to parse token credentials response.');
		}

		if (isset($data['error'])) {
			throw new CredentialsException("Error [{$data['error']}] in retrieving token credentials.");
		}

		$tokenCredentials = new TokenCredentials();
		$tokenCredentials->setIdentifier($data['oauth_token']);
		$tokenCredentials->setSecret($data['oauth_token_secret']);

		return $tokenCredentials;
	}
	
	protected function baseProtocolParameters()
	{
		$dateTime = new \DateTime();

		return array(
			'oauth_consumer_key' => $this->clientCredentials->getIdentifier(),
			'oauth_nonce' => $this->nonce(),
			'oauth_signature_method' => $this->signature->method(),
			'oauth_timestamp' => $dateTime->format('U'),
			'oauth_version' => '1.0',
		);
	}
	
	protected function additionalProtocolParameters()
	{
		return array();
	}

	protected function temporaryCredentialsProtocolHeader($uri)
	{
		$parameters = array_merge($this->baseProtocolParameters(), array(
			'oauth_callback' => $this->clientCredentials->getCallbackUri(),
		));

		$parameters['oauth_signature'] = $this->signature->sign($uri, $parameters, 'POST');

		return $this->normalizeProtocolParameters($parameters);
	}
	
	protected function protocolHeader($method, $uri, CredentialsInterface $credentials, array $bodyParameters = array())
	{
		$parameters = array_merge(
			$this->baseProtocolParameters(),
			$this->additionalProtocolParameters(),
			array(
				'oauth_token' => $credentials->getIdentifier(),
			)
		);

		$this->signature->setCredentials($credentials);

		$parameters['oauth_signature'] = $this->signature->sign(
			$uri,
			array_merge($parameters, $bodyParameters),
			$method
		);

		return $this->normalizeProtocolParameters($parameters);
	}
	
	protected function normalizeProtocolParameters(array $parameters)
	{
		array_walk($parameters, function (&$value, $key) {
			$value = rawurlencode($key).'="'.rawurlencode($value).'"';
		});

		return 'OAuth '.implode(', ', $parameters);
	}
	
	protected function nonce($length = 32)
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
	}
	
	protected function buildUrl($host, $queryString)
	{
		return $host.(strpos($host, '?') !== false ? '&' : '?').$queryString;
	}
	
	abstract public function urlTemporaryCredentials();

	abstract public function urlAuthorization();

	abstract public function urlTokenCredentials();

	abstract public function urlUserDetails();

	abstract public function userDetails($data, TokenCredentials $tokenCredentials);

	abstract public function userUid($data, TokenCredentials $tokenCredentials);

	abstract public function userEmail($data, TokenCredentials $tokenCredentials);
	
	abstract public function userScreenName($data, TokenCredentials $tokenCredentials);
}

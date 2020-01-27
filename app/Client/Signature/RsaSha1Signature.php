<?php

namespace App\Client\Signature;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Uri;
use App\Client\Signature\Signature;
use App\Client\Signature\SignatureInterface;

class RsaSha1Signature extends Signature implements SignatureInterface{
	public function method()
	{
		return 'RSA-SHA1';
	}

	public function sign($uri, array $params = array(), $method = 'POST')
	{
		$url = $this->createUrl($uri);
		$baseString = $this->baseString($url, $method, $params);

		$privateKey = $this->clientCredentials->getRsaPrivateKey();

		openssl_sign($baseString, $signature, $privateKey);

		return base64_encode($signature);
	}

	protected function createUrl($uri)
	{
		return PS7\uri_for($uri);
	}

	protected function baseString(Uri $url, $method = 'POST', array $params = array())
	{
		$baseString = rawurlencode($method).'&';

		$schemeHostPath = Uri::fromParts(array(
			'scheme'	=>	$url->getScheme(),
			'host'		=>	$url->getHost(),
			'path'		=>	$url->getPath(),
		));

		$baseString .= rawurlencode($schemeHostPath).'&';

		$data = array();
		parse_str($url->getQuery(), $query);
		$data = array_merge($query, $params);

		array_walk_recursive($data, function (&$key, &$value) {
			$key = rawurlencode(rawurldecode($key));
			$value = rawurlencode(rawurldecode($value));
		});
		ksort($data);

		$baseString .= $this->queryStringFromData($data);

		return $baseString;
	}

	protected function queryStringFromData($data, $queryParams = false, $prevKey = '')
	{
		if ($initial = (false === $queryParams)) {
			$queryParams = array();
		}

		foreach ($data as $key => $value) {
			if ($prevKey) {
				$key = $prevKey.'['.$key.']';
			}
			
			if (is_array($value)) {
				$queryParams = $this->queryStringFromData($value, $queryParams, $key);
			} else {
				$queryParams[] = rawurlencode($key.'='.$value);
			}
		}

		if ($initial) {
			return implode('%26', $queryParams);
		}

		return $queryParams;
	}
}
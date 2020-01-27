<?php

namespace App\Client\Credentials;

class RsaClientCredentials extends ClientCredentials
{
	protected $rsaPublicKeyFile;

	protected $rsaPrivateKeyFile;

	protected $rsaPublicKey;

	protected $rsaPrivateKey;

	public function setRsaPublicKey($filename)
	{
		$this->rsaPublicKeyFile = $filename;
		$this->rsaPublicKey = null;

		return $this;
	}

	public function setRsaPrivateKey($filename)
	{
		$this->rsaPrivateKeyFile = $filename;
		$this->rsaPrivateKey = null;

		return $this;
	}

	public function getRsaPublicKey()
	{
		if ($this->rsaPublicKey) {
			return $this->rsaPublicKey;
		}

		if (!file_exists($this->rsaPublicKeyFile)) {
			throw new CredentialsException('Could not read the public key file.');
		}

		$this->rsaPublicKey = openssl_get_publickey(file_get_contents($this->rsaPublicKeyFile));

		if (!$this->rsaPublicKey) {
			throw new CredentialsException('Cannot access public key for signing');
		}

		return $this->rsaPublicKey;
	}

	public function getRsaPrivateKey()
	{
		if ($this->rsaPrivateKey) {
			return $this->rsaPrivateKey;
		}

		if (!file_exists($this->rsaPrivateKeyFile)) {
			throw new CredentialsException('Could not read the private key file.');
		}

		$this->rsaPrivateKey = openssl_pkey_get_private(file_get_contents($this->rsaPrivateKeyFile));

		if (!$this->rsaPrivateKey) {
			throw new CredentialsException('Cannot access private key for signing');
		}

		return $this->rsaPrivateKey;
	}

	public function __destruct()
	{
		if ($this->rsaPublicKey) {
			openssl_free_key($this->rsaPublicKey);
		}

		if ($this->rsaPrivateKey) {
			openssl_free_key($this->rsaPrivateKey);
		}
	}
}
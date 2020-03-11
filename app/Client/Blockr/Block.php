<?php

namespace App\Client\Blockr;

use App\Models\User;

class Block {

	protected $message;
	protected $sender_id;
	protected $sender;

	public function __construct($message) {
		$this->sender_id = $message["message_create"]["sender_id"];
		$this->sender = User::where('uid', '=', $this->sender_id)->first();
	}

	public function getSenderHandle() {
		if (empty($this->sender)) {
			return false;
		} else {
			return $this->sender->handle;
		}
	}

}
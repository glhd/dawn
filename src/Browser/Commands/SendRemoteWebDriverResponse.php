<?php

namespace Glhd\Dawn\Browser\Commands;

use Glhd\Dawn\IO\Command;

class SendRemoteWebDriverResponse extends Command
{
	public function __construct(
		public string $request_id,
		public mixed $response = null,
	) {
	}
	
	public function execute(\Glhd\Dawn\Browser\RemoteWebDriverBroker $broker)
	{
		$broker->addToResponseQueue($this->request_id, $this->response);
	}
}

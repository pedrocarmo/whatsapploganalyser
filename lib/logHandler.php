<?php

namespace lib;


class logHandler
{

	private $queue = [];
	private $dbh;

	public function __construct (dbHandler $dbh)
	{
		$this->dbh = $dbh;
	}

	public function parseLog($location)
	{
		$fh = fopen($location, 'r');
		while ($line = fgets($fh)) {
			preg_match ("/^(.+) - ([\w]+).*: (.+)\\n$/", $line, $matches);
			if ($matches[3] == '<Media omitted>')
			{
				$matches[3] = "";
			}
			$this->queueMessage($matches[2], \DateTime::createFromFormat("M j, H:i", $matches[1]), $matches[3]);
		}
		fclose($fh);

		return $this->dbh->insertMessages($this->queue);
	}

	private function queueMessage($sender, $time, $message)
	{
		$this->queue[] = [
			'sender'  => $sender,
			'tstamp'  => $time,
			'message' => $message
		];
	}
} 
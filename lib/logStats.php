<?php

namespace lib;


class logStats
{
	private $dbh;

	public function __construct (dbHandler $dbh)
	{
		$this->dbh = $dbh;
	}

	public function messagesHourAvg()
	{
		$messages = $this->dbh->getMessagesByHour();
		$days = $this->dbh->getNumOfDays();

		$result = [];
		$senders = [];
		foreach ($messages as $message)
		{
			$m = $message['m'] == 1 ? 0.5 : 0;
			$time = $message['h'] + $m;
			$time = (string) $time;

			$result['messages'][$time][$message['sender']] = number_format($message['c'] / $days, 2);
			$senders[$message['sender']] = 0;
		}

		$result['senders'] = array_keys($senders);

		return $result;
	}

	public function messagesWeekday()
	{
		$messages = $this->dbh->getMessagesByWeekday();

		$result = [];
		$senders = [];
		foreach ($messages as $message)
		{
			$result['messages'][$message['w']][$message['sender']] = $message['c'];
			$senders[$message['sender']] = 0;
		}

		$result['senders'] = array_keys($senders);

		return $result;
	}

	public function messagesDay()
	{
		$messages = $this->dbh->getMessagesByDay();

		$result = [];
		foreach ($messages as $message)
		{
			$date = new \DateTime($message['d']);
			$result['messages'][$message['d']] = [
				'y' => $date->format('Y'),
				'm' => $date->format('n') - 1,
				'd' => $date->format('d'),
				'c' => $message['c']
			];
		}

		return $result;
	}

	public function firstMsgOfDay()
	{
		$messages = $this->dbh->getFirstMsgOfDayBySender();

		$firstSender = current($messages)['sender'];
		$result = [];
		foreach ($messages as $message)
		{
			$date = new \DateTime($message['d']);
			$result['messages'][$message['d']] = [
				'sender' => $message['sender'],
				'y'      => $date->format('Y'),
				'm'      => $date->format('n') - 1,
				'd'      => $date->format('d'),
				'p'      => $firstSender == $message['sender'] ? 1 : -1
			];
		}

		unset($messages);

		return $result;
	}

	public function wordCloud($count, $minLength)
	{
		$output = $this->dbh->getWordsPerSender($minLength);

		// lets tone down the number of results to the top X words
		$result = [];
		foreach ($output as $sender => $words)
		{
			arsort($words);
			$result[$sender] = array_slice($words, 0, $count);
		}

		//clear memory
		unset ($output);

		return $result;
	}
}
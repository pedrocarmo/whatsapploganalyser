<?php

namespace lib;


class dbHandler {

	private $pdo;

	public function __construct($host, $db, $user, $pass)
	{
		$dsn = "mysql:dbname={$db};host={$host}";

		try {
			$this->pdo = new \PDO($dsn, $user, $pass);
		} catch (\PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}

	public function insertMessages($messages)
	{
		$count = count($messages);

		if ($count == 0)
		{
			return "No messages to insert!";
		}

		$this->pdo->exec("TRUNCATE messages");
		$this->pdo->exec("SET names utf8");

		$sth = $this->pdo->prepare("
			INSERT INTO messages
				(`sender`, `tstamp`, `message`)
			VALUES " . trim(str_repeat("(?,?,?),",$count), ","));

		$params = [];
		foreach ($messages as $message)
		{
			$params[] = $message['sender'];
			$params[] = $message['tstamp']->format('Y-m-d H:i:s');
			$params[] = $message['message'];
		}

		$sth->execute($params);

		return "{$count} messages inserted!";
	}

	public function getMessagesByHour ()
	{
		$sth = $this->pdo->prepare("
			SELECT `sender`,
				HOUR( `tstamp` ) AS h ,
				FLOOR(MINUTE(`tstamp` )/30) AS m,
				COUNT( `id` ) AS c
			FROM  `messages`
			GROUP BY h, FLOOR(MINUTE(`tstamp` )/30), sender
		");

		$sth->execute();

		return $sth->fetchAll();
	}

	public function getMessagesByWeekday ()
	{
		$sth = $this->pdo->prepare("
			SELECT `sender`,
				DAYOFWEEK(`tstamp`) AS w,
				COUNT( `id` ) AS c
			FROM  `messages`
			GROUP BY w, sender
		");

		$sth->execute();

		return $sth->fetchAll();
	}

	public function getWordsPerSender($minLenght = 4)
	{
		$sth = $this->pdo->prepare("
			SELECT `sender`, `message`
			FROM  `messages`
		", [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL]);

		$sth->execute();

		$result = [];
		while ($row = $sth->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {

			// normalize
			$tmpWords = strtolower($row['message']);
			$tmpWords = preg_replace("/\pP+/", '', $tmpWords);
			$words = explode(" ", $tmpWords);

			foreach ($words as $w)
			{
				if (strlen($w) < $minLenght)
				{
					continue;
				}

				if (!empty($result[$row['sender']][$w]))
				{
					$result[$row['sender']][$w] = $result[$row['sender']][$w] + 1;
				}
				else
				{
					$result[$row['sender']][$w] = 1;
				}
			}
		}

		return $result;

	}

	public function getNumOfDays()
	{
		static $count = null;

		if ($count != null)
		{
			return $count;
		}

		$sth = $this->pdo->prepare("
			SELECT `tstamp`
			FROM `messages`
			ORDER BY `tstamp` ASC
			LIMIT 1
		");

		$sth->execute();

		$firstDay = new \DateTime($sth->fetch()['tstamp']);

		$sth = $this->pdo->prepare("
			SELECT `tstamp`
			FROM `messages`
			ORDER BY `tstamp` DESC
			LIMIT 1
		");

		$sth->execute();

		$lastDay = new \DateTime($sth->fetch()['tstamp']);

		$countObj = $firstDay->diff($lastDay);

		$count = $countObj->days;

		return $count;
	}
} 
<?php

require_once (__DIR__ . "/include.php");

$logStats = new \lib\logStats($dbh);

?>

<html>
<head>
	<title>
		Whatsapp stats analyser
	</title>

	<script src="js/wordcloud2.js/src/wordcloud2.js"></script>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>

	<script type="text/javascript">
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChartHour);
		function drawChartHour() {
			var data = google.visualization.arrayToDataTable([
				<?php
					$maxValue = 0;
					$messagesHour = $logStats->messagesHourAvg();

					$headerTmp = "['Hours', ";
					foreach ($messagesHour['senders'] as $sender)
					{
						$headerTmp .= "'{$sender}', ";
					}
					$header = trim($headerTmp, ", ") . "],\n";

					echo $header;
					foreach ($messagesHour['messages'] as $time => $message)
					{
						$lineTmp = "[{$time}, ";
						foreach ($messagesHour['senders'] as $sender)
						{
							$maxValue = !empty($message[$sender]) && $message[$sender] > $maxValue ? $message[$sender] : $maxValue;
							$lineTmp .= !empty($message[$sender]) ? "{$message[$sender]}, " : "0, ";
						}
						$line = trim($lineTmp, ", ") . "],\n";
						echo $line;
					}

					//clear memory
					unset($messagesHour);
				?>
			]);

			var options = {
				title: 'Messages per hour',
				curveType: 'function',
				hAxis: {minValue: 0, maxValue: 24},
				vAxis: {minValue: 0, maxValue: <?=$maxValue;?>},
				legend: 'none',
				series: {
					0: { color: 'pink' },
					1: { color: 'blue'}
				}
			};

			var chart = new google.visualization.LineChart(document.getElementById('hour_chart_div'));

			chart.draw(data, options);
		}

		google.setOnLoadCallback(drawChartWeekDay);
		function drawChartWeekDay() {

		var data = google.visualization.arrayToDataTable([
			<?php
				$maxValue = 0;
				$messagesWeek = $logStats->messagesWeekday();

				$headerTmp = "['Weekeday', ";
				foreach ($messagesWeek['senders'] as $sender)
				{
					$headerTmp .= "'{$sender}', ";
				}
				$header = trim($headerTmp, ", ") . "],\n";

				echo $header;
				foreach ($messagesWeek['messages'] as $weekdayNum => $message)
				{
					switch ($weekdayNum)
					{
						case 1: $weekday = "Sunday"; break;
						case 2: $weekday = "Monday"; break;
						case 3: $weekday = "Tuesday"; break;
						case 4: $weekday = "Wednesday"; break;
						case 5: $weekday = "Thursday"; break;
						case 6: $weekday = "Friday"; break;
						case 7: $weekday = "Saturday"; break;
					}


					$lineTmp = "['{$weekday}', ";
					foreach ($messagesWeek['senders'] as $sender)
					{
						$maxValue = !empty($message[$sender]) && $message[$sender] > $maxValue ? $message[$sender] : $maxValue;
						$lineTmp .= !empty($message[$sender]) ? "{$message[$sender]}, " : "0, ";
					}
					$line = trim($lineTmp, ", ") . "],\n";
					echo $line;
				}

				//clear memory
				unset($messagesWeek);
		?>
		]);

		var options = {
			title: 'Messages By Weekday',
			vAxis: {minValue: 0, maxValue: <?=$maxValue;?>},
			series: {
				0: { color: 'pink' },
				1: { color: 'blue'}
			}
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('weekday_chart_div'));

		chart.draw(data, options);

	}
</script>
</head>

<body>
<div id="hour_chart_div" style="width: 1300px; height: 700px;"></div>
<div id="weekday_chart_div" style="width: 1300px; height: 700px;"></div>

<h1>Most used words</h1>

	<?php
		$wordsPerUser = $logStats->wordCloud(100, 5);
		foreach ($wordsPerUser as $sender => $words)
		{
			$list = "";
			foreach ($words as $word => $count)
			{
				$list .= "['$word', $count],";
			}
			$list = trim ($list, ",");

			echo "<h2>{$sender}</h2>
			<canvas id='{$sender}Canvas' width='700' height='400'></canvas>
			<script>
				WordCloud(document.getElementById('{$sender}Canvas'),
					{ list : [ {$list} ],
					  click: function(item) {
							alert(item[0] + ': ' + item[1]);
						  }
					}
				);
			</script>";
		}
	?>

</body>
</html>
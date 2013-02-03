<?php

	$start_time = microtime(true);
	$curr_s = "";
	$file_path = "input.txt";

	$p = getopt("f:o:s");

	$fp = NULL;

	if (isset($p["s"])) {
		$fp = fopen("php://stdin", "r");
	} elseif (isset($p["f"])) {
		$fp = fopen($p["f"], "r") or die("can`t open input file!");
	} else {
		usage();
		die();
	}

	function get_group($fp, & $buffer) {
		$buffer = [];
		$cs = "";

		while($cs = fgets($fp)) {
			$buffer[] = $cs;

			if (strpos($cs, "SUB-TOTAL") !== FALSE) {
				break;
			}
		}

		if (!$cs) {
			$buffer = [];
			return $cs;
		}

		return $buffer;
	}

	function get_items(& $buffer) {
		$result = [];

		$bl = sizeof($buffer);
		if ($bl < 9) {
			return [false, false];
		}
		$i = 2;
		$ras = false;

		while (true) {
			$cs = $buffer[$i];

			if (strpos($cs, "REMOTE AREA SERVICE") !== FALSE) {
				$ras = true;
			}

			if (strpos($cs, "SENDER") !== FALSE && strpos($cs, "RECEIVER") !== FALSE) {
				$result[] = array_slice($buffer, $i - ($ras ? 5 : 4), 9 + ($ras ? 1 : 0));
				$i += 5;
				$ras = false;
			} else {
				$i ++;
			}

			if ($i >= $bl) {
				break;
			}
		}

		$ls = $buffer[$bl - 1];
		$cs = strpos($ls, "*") + 2;
		$ce = strpos($ls, "   ", $cs);

		$country = substr($ls, $cs, $ce - $cs);

		return array($country, $result);
	}

	function get_item_data(& $data) {
		$result = [];
		$hawb_s = substr($data[0], 12);
		$result["hawb_no"] = substr($hawb_s, 0, strpos($hawb_s, " "));
		$hawb_s = "";

		$info_data = array_slice($data, -5);

		$pos = strpos($info_data[0], "RECEIVER") + 8;
		foreach ($info_data as $key => $value) {
			$result["info"][] = trim(substr($value, $pos));
		}

		return $result;
	}

	$lines = [];
	$result = [];

	while(get_group($fp, $lines)) {
		list($c, $i) = get_items($lines);

		if (!$c) {
			continue;
		}

		foreach ($i as $item) {
			$r = get_item_data($item);
			$r["info"][] = $c;
			$result[] = $r;
		}
	}

	if (isset($p["o"])) {
		file_put_contents($p["o"], json_encode($result));
	} else {
		print json_encode($result);
	}

	fclose($fp);

?>

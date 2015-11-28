<?php

	include '../src/parser/src/Data.php';
	include '../src/parser/data/browsers-chrome.php';


	$stable = [
		'desktop' => [],
		'mobile'  => []
	];


	$omaha = explode("\n", file_get_contents("http://omahaproxy.appspot.com/history"));
	foreach ($omaha as $i => $line) {
		$items = explode(",", $line);

		if ($items[0] == 'mac' && $items[1] == 'stable') {
			$stable['desktop'][] = implode('.', array_slice(explode('.', $items[2]), 0, 3));
		}

		if ($items[0] == 'android' && $items[1] == 'stable') {
			$stable['mobile'][] = implode('.', array_slice(explode('.', $items[2]), 0, 3));
		}
	}

	$stable['desktop'] = array_unique($stable['desktop']);
	$stable['mobile'] = array_unique($stable['mobile']);

	sort($stable['desktop']);
	sort($stable['mobile']);


	foreach ($stable['desktop'] as $i => $version) {
		if (!isset(WhichBrowser\Data\Chrome::$DESKTOP[$version])) {
			WhichBrowser\Data\Chrome::$DESKTOP[$version] = 'stable';
		}
	}

	foreach ($stable['mobile'] as $i => $version) {
		if (!isset(WhichBrowser\Data\Chrome::$MOBILE[$version])) {
			WhichBrowser\Data\Chrome::$MOBILE[$version] = 'stable';
		}
	}


	$result  = "";
	$result .= "<?php\n\n";
	$result .= "\t\tnamespace WhichBrowser\Data;\n\n";
	$result .= "\t\tChrome::\$DESKTOP = [\n";
	foreach (WhichBrowser\Data\Chrome::$DESKTOP as $version => $channel) $result .= "\t\t\t'{$version}' => '{$channel}',\n";
	$result .= "\t\t];\n\n";
	$result .= "\t\tChrome::\$MOBILE = [\n";
	foreach (WhichBrowser\Data\Chrome::$MOBILE as $version => $channel) $result .= "\t\t\t'{$version}' => '{$channel}',\n";
	$result .= "\t\t];\n\n";


	file_put_contents('../src/parser/data/browsers-chrome.php', $result);

	echo `git diff ../src/parser/data/browsers-chrome.php`;
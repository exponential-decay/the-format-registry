<?php

	include_once ("private/parsedown/Parsedown.php");

	function html_surround($mdtext)
	{
$head = <<<'EOD'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>the-fr.org: The Format Registry</title>
<link rel="stylesheet" href="/public/css/main.css" type="text/css" />
</head>
<body>
EOD;

		$tail = "</body></html>";

		return $head . $mdtext . $tail; 
	}

	function generate_markdown($markdown)
	{
		$mdtext = Parsedown::instance()->parse($markdown);
		return html_surround($mdtext);
	}

?>

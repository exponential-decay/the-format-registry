<?php

	include_once ("parsedown/Parsedown.php");
	include_once ("response-handler-class.php");

	function format_tfr_xml($xml, $uritype)
	{
		$xsl_loc = "private/xsl/tfr-rdf-generic.xsl";
		
		if ($uritype == ResponseHandler::DOC)
		{
			$xsl_loc = "private/xsl/tfr-rdf-generic.xsl";
		}
		elseif ($uritype == ResponseHandler::DEF)
		{
			$xsl_loc = "private/xsl/tfr-rdf-generic-prop.xsl";
		}
		elseif ($uritype == ResponseHandler::PROP)
		{
			$xsl_loc = "private/xsl/tfr-rdf-generic-prop.xsl";
		}
		else
		{
			$xsl_loc = "private/xsl/tfr-rdf-generic-prop.xsl";
		}

		$doc = new DOMDocument();
		$xsl = new XSLTProcessor();

		$doc->load($xsl_loc);
		$xsl->importStyleSheet($doc);

		$doc->loadXML($xml);
		$xslresult = trim(preg_replace('/\t+/', '', $xsl->transformToXML($doc)));

		return $xslresult;
	}

	function html_surround($mdtext)
	{
$head = <<<'EOD'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
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

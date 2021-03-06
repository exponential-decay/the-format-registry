<?php
	#Primary structures and locations for TFR to look at...

	#urls-agents
	define("EXPONENTIALDKWEBAGENT", "exponentialDK-PRONOM-Export-web/0.0.0");
	define("PRONOMBASEURL", "http://apps.nationalarchives.gov.uk/PRONOM/");

	#folders
	define('DATADIR', "../private/pronom/pronom-data/");
	define('LATESTDIR', "../private/pronom/pronom-latest/");
	define('ARCHIVEDIR', "../public/pronom/pronom-archive/");
	define('FMT', "fmt");
	define('XFMT', "x-fmt");

	#files
	define('INIFILE', "../private/ini/pronom.ini");

	#triple locations
	define('TRIPLOC', '../public/tfr/tfr-triples/');
	define('TRIPFILE', TRIPLOC . "tfr-format-triples.nt");

    #containers
	define('LATEST_CONTAINER', "../private/pronom/pronom-latest/container/container-signature.xml");

    #non-containers
	define('LATEST_NON_CONTAINER', "../private/pronom/pronom-latest/non-container/non-container-signature.xml");

?>

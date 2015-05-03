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
	define('TRIPFILE', TRIPLOC . "tfr-triples.nt");	
?>

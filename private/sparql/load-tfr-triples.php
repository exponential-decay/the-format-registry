<?php
	header("Content-type: text/plain");

	/* ARC2 static class inclusion */
	include_once("arc2access.php"); 
	include_once("arc/ARC2.php");

	/* MySQL and endpoint configuration */ 
	$config = array(
		/* db */
		'db_host' => 'localhost', /* optional, default is localhost */
		'db_name' => 'arc_db',
		'db_user' => ARC2USER,
		'db_pwd' => ARC2PASS,

		/* store name */
		'store_name' => 'tfr_db',

		/* stop after 100 errors */
		'max_errors' => 100,
	);

	/* instantiation */
	$ep = ARC2::getStore($config);
	if (!$ep->isSetUp()) {
		$ep->setUp(); /* create MySQL tables */
	}

	$ep->reset();	# always loading a fresh set of triples

	$load_triples = $ep->query('LOAD <../../public/tfr/tfr-triples/tfr-format-triples.nt>');
	$load_ontology = $ep->query('LOAD <../../public/tfr/tfr-triples/tfr-ontology-triples.nt>');	

	print "load triples result:" . "\r\n\r\n";	
	print_r($load_triples);

	print "\r\n\r\n";

	print "load triples result:" . "\r\n\r\n";	
	print_r($load_ontology);
?>

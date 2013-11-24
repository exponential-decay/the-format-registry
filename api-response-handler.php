<?php

	include_once ("private/sparqllib/sparqllib.php");
	include_once ("response-handler-class.php");
	include_once ("private/md-from-xml.php");

	# /api/id/puid/fmt/
	# /api/id/puid/x-fmt/
	# /api/id/puid/x-fmt/
	# /api/id/udfr/

	function connect_to_sparql()
	{
		$endpoint = 'http://localhost/public/sparql/endpoint.php';
		$db = sparql_connect( $endpoint );

		if(!$db) 
		{
			print $db->errno() . ": " . $db->error(). "\n"; 
			exit; 
		}

		return $db;
	}

	function ask_triplestore_object($db, $predicate, $object)
	{
		$tfr_ask_query = "ask where { ?s " . $predicate . " '" . $object . "' }";
		$db->outputfmt(ARC2PLAIN);
		$tfr_ask_result = $db->query($tfr_ask_query, True);
		return $tfr_ask_result;
	}

	function select_triplestore_object($db, $predicate, $object)
	{
		$tfr_select_query = "SELECT ?record WHERE { ?record " . $predicate . " '" . $object . "'. } LIMIT 1";
		$db->outputfmt(ARC2RDFXML);
		$tfr_ask_result = $db->query($tfr_select_query, True);
		return $tfr_ask_result;
	}

	function handle_request($db)
	{
		#$slugs = new ResponseHandler();
		$slugs = array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])));

		if(sizeof($slugs) >= 5)
		{
			if ($slugs[ResponseHandler::APITYPE] == 'id')
			{
				print_r ($slugs);

				if(strcmp($slugs[ResponseHandler::APIEXTERNALSCHEME], PUID) == 0)
				{

					#print ask_triplestore_object($db, "<http://the-fr.org/prop/format-registry/puid>", "fmt/15");
					print select_triplestore_object($db, "<http://the-fr.org/prop/format-registry/puid>", "fmt/15");
					#print $slugs[ResponseHandler::APIPUIDTYPE];
					#print $slugs[ResponseHandler::APIPUIDVALUE];
	
				}
			}
		}
	}

	$db = connect_to_sparql();
	handle_request($db);

?>

<?php

	include_once ("private/sparqllib/sparqllib.php");
	include_once ("private/sparqllib/sparqllib-beta-functions.php");
	include_once ("response-handler-class.php");
	include_once ("private/md-from-xml.php");

	# /api/id/puid/fmt/
	# /api/id/puid/x-fmt/
	# /api/id/puid/x-fmt/
	# /api/id/udfr/

	function connect_to_sparql()
	{
		$endpoint = "http://" . $_SERVER[HTTP_HOST] . "/public/sparql/endpoint.php";
		$db = sparql_connect( $endpoint );

		if(!$db) 
		{
			print $db->errno() . ": " . $db->error(). "\n"; 
			exit; 
		}

		return $db;
	}

	function handle_request($db)
	{
		#$slugs = new ResponseHandler();
		$slugs = array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])));

		if(sizeof($slugs) >= 5)
		{
			if ($slugs[ResponseHandler::APITYPE] == 'id')
			{
				if(strcmp($slugs[ResponseHandler::APIEXTERNALSCHEME], PUID) == 0)
				{
					$puid_type = $slugs[ResponseHandler::APIPUIDTYPE];

					if ($puid_type == 'xfmt')	# search is more flexible
						$puid_type = 'x-fmt';

					$puid_string = $puid_type . "/" . $slugs[ResponseHandler::APIPUIDVALUE];

					if(ask_triplestore_object($db, "<http://the-fr.org/prop/format-registry/puid>", $puid_string) == 'true')
					{					
						$data = select_triplestore_object($db, "<http://the-fr.org/prop/format-registry/puid>", $puid_string);

						$xml = simplexml_load_string($data);		

						$subject_uri = $xml->results->result->binding->uri;

						header('Location: ' . $subject_uri) ;
					}
					else
					{
						print generate_markdown("## No data for requested puid: " . $puid_string);
					}
				}
				else
				{
					print generate_markdown("## API functionality not yet implemented.");	#not yet invoked
				}		
			}
			else
			{
				print generate_markdown("## API functionality not yet implemented.");		#not yet invoked
			}
		}
	}

	$db = connect_to_sparql();
	handle_request($db);

?>

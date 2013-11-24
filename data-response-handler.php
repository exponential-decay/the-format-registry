<?php

	include_once ("private/output-md-page.php");
	include_once ("private/sparqllib/sparqllib.php");
	include_once ("response-handler-class.php");

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

	function get_slugs($delimeter, $request_uri)
	{
		define("EXTPOS", 2);	#position of expected file request, IDed by extension

		$slugs = array_values(array_filter(explode($delimeter, $request_uri)));
		
		# split out a format extension if we have one, to return something useful
		$format = (array_filter(explode('.', $slugs[EXTPOS])));

		# remove concatenated portion, then merge		
		unset($slugs[EXTPOS]);	

		#return
		return array_merge($slugs, $format);
	}

	function ask_triplestore($db, $subject_uri)
	{
		$tfr_ask_query = "ask where { " . $subject_uri . " ?p ?o . }";		# TODO: extract to function
		$db->outputfmt(ARC2PLAIN);
		$tfr_ask_result = $db->query($tfr_ask_query, True);
		return $tfr_ask_result;
	}

	function set_subject_uri($slugs)
	{
		$subject_uri = "<http://the-fr.org/id/". $slugs->slugs_arr[ResponseHandler::URICLASS]. "/" . $slugs->slugs_arr[ResponseHandler::URIVALUE] . ">";
		return $subject_uri;	
	}

	function handle_request($db)
	{
		$slugs = new ResponseHandler();

		if($slugs->slugsize >= 3 && $slugs->slugsize < 5)
		{			
			if(strcmp($slugs->slugs_arr[ResponseHandler::URITYPE], DOC) == 0 || 
					strcmp($slugs->slugs_arr[ResponseHandler::URITYPE], DEF) == 0 || 
					strcmp($slugs->slugs_arr[ResponseHandler::URITYPE], PROP) == 0 && 
					strcmp($slugs->slugs_arr[ResponseHandler::URICLASS], DATACLASS) == 0)
			{
				$subject_uri = set_subject_uri($slugs);

				if (ask_triplestore($db, $subject_uri) == 'true')
				{
					$tfr_describe_query = "describe " . $subject_uri;
					$db->outputfmt(ARC2XML);
					$tfr_describe_result = $db->query($tfr_describe_query, True);
					$xslMDresult = format_tfr_xml($tfr_describe_result);
					#$xslMDresult = format_prop_xml($tfr_describe_result);
					print generate_markdown($xslMDresult);
				}
				else
				{
					print generate_markdown("## No data for requested uri (DOC): " . $_SERVER['REQUEST_URI']);
				}
			}
			elseif (strcmp($slugs->slugs_arr[ResponseHandler::URITYPE], DATA) == 0 && strcmp($slugs->slugs_arr[ResponseHandler::URICLASS], DATACLASS) == 0)
			{
				$subject_uri = set_subject_uri($slugs);

				if (ask_triplestore($db, $subject_uri) == 'true')
				{
					$tfr_describe_query = "describe " . $subject_uri;

					$db->outputfmt($slugs->handle_return_format($slugs->slugs_arr[ResponseHandler::RETURNFORMAT]));

					$tfr_describe_result = $db->query($tfr_describe_query, True);

					$filename = 'Content-disposition: attachment; filename=' . $slugs->slugs_arr[ResponseHandler::URIVALUE] . "." . $slugs->slugs_arr[ResponseHandler::RETURNFORMAT];
					header($filename);
					header ("Content-Type: " . $slugs->content_type);
					print $tfr_describe_result;
				}
				else
				{
					print generate_markdown("## No data for requested uri (DATA): " . $_SERVER['REQUEST_URI']);
				}
			}
		}
		else
		{
			print generate_markdown("## No results... potentially invalid uri");
		}
	}

	$db = connect_to_sparql();
	handle_request($db);
?>

<?php

	include_once ("private/md-from-xml.php");
	include_once ("response-handler-class.php");
	include_once ("private/sparqllib/sparqllib.php");
	include_once ("private/sparqllib/sparqllib-beta-functions.php");

	function connect_to_sparql()
	{
		$endpoint = "http://" . $_SERVER['HTTP_HOST'] . "/public/sparql/endpoint.php";
		$db = sparql_connect( $endpoint );

		if(!$db) 
		{
			print $db->errno() . ": " . $db->error(). "\n"; 
			exit; 
		}

		return $db;
	}

	function set_subject_uri($slugs, $uritype)
	{
		$uri_type_string = $slugs->slugs_arr[ResponseHandler::URITYPE];

		if($uritype == ResponseHandler::DOC)
		{
			$uri_type_string = 'id';
		}
	
		$subject_uri = "<http://the-fr.org/" . 
			$uri_type_string . "/". 
				$slugs->slugs_arr[ResponseHandler::URICLASS]. "/" . 
					$slugs->slugs_arr[ResponseHandler::URIVALUE] . ">";	

		return $subject_uri;	
	}

	function handle_request($db)
	{
		$slugs = new ResponseHandler();

		if($slugs->slugsize >= 3 && $slugs->slugsize < 5)
		{			
			if (($slugs->uri_type == ResponseHandler::DOC && 
					strcmp($slugs->slugs_arr[ResponseHandler::URICLASS], DATACLASS) == 0) ||
				 		($slugs->uri_type == ResponseHandler::DEF || 
				 			$slugs->uri_type == ResponseHandler::PROP) &&
					 			strcmp($slugs->slugs_arr[ResponseHandler::URICLASS], FORMATREG) == 0)
			{			
				$subject_uri = set_subject_uri($slugs, $slugs->uri_type);

				if (ask_triplestore($db, $subject_uri) == 'true')
				{
					$tfr_describe_result = describe_triplestore_subject($db, $subject_uri, ARC2XML);
					$xslMDresult = format_tfr_xml($tfr_describe_result, $slugs->uri_type);
					print generate_markdown($xslMDresult);
				}
				else
				{
					print generate_markdown("## No data for requested uri: " . $_SERVER['REQUEST_URI']);
				}
			}
			elseif (strcmp($slugs->slugs_arr[ResponseHandler::URITYPE], DATA) == 0 && strcmp($slugs->slugs_arr[ResponseHandler::URICLASS], DATACLASS) == 0)
			{
				$subject_uri = set_subject_uri($slugs);

				if (ask_triplestore($db, $subject_uri) == 'true')
				{
					$tfr_describe_result = describe_triplestore_subject($db, $subject_uri, $slugs->handle_return_format($slugs->slugs_arr[ResponseHandler::RETURNFORMAT]));

					$filename = 'Content-disposition: attachment; filename=' . $slugs->slugs_arr[ResponseHandler::URIVALUE] . "." . $slugs->slugs_arr[ResponseHandler::RETURNFORMAT];
					
					header ($filename);
					header ("Content-Type: " . $slugs->content_type);
					
					print $tfr_describe_result;
				}
				else
				{
					print generate_markdown("## No data for requested uri (DATA): " . $_SERVER['REQUEST_URI']);
				}
			}
			else
			{
				print generate_markdown("## No results... potentially invalid uri: " . $_SERVER['REQUEST_URI']);
			}
		}
		else
		{
			print generate_markdown("## No results... potentially invalid uri: " . $_SERVER['REQUEST_URI']);
		}
	}

	$db = connect_to_sparql();
	handle_request($db);
?>

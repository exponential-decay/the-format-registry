<?php

	include_once ("private/output-md-page.php");
	include_once ("private/sparqllib/sparqllib.php");
	include_once ("response-handler-class.php");
	include_once ("private/sparqllib/sparqllib-arc2-outputformats.php");

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

	function return_format($extension)
	{
		$format = ARC2XML;
		$extension = strtolower($extension);		

		if($extension == "xml")
		{
			$format = ARC2XML;
		}
		elseif($extension == "json")
		{
			$format = ARC2JSON;
		}
		elseif($extension == "php")
		{
			$format = ARC2PHP;
		}
		elseif($extension == "ttl")
		{
			$format = ARC2TTL;
		}
		elseif($extension == "rdf")
		{
			$format = ARC2RDFXML;
		}
		elseif($extension == "tsv")
		{
			$format = ARC2TSV;
		}

		return $format;
	}

	function handle_request($db)
	{
		#Sample request: http://the-fr.org/id/file-format/1

		$slugs = new ResponseHandler();


		if($slugs->slugsize >= 3 && $slugs->slugsize < 5)
		{
			$typeslugptr   = 0;
			$classslugptr  = 1;
			$idslugptr		= 2; 
			$fmtslugptr    = 3;	
			
			define("DOC", 'doc');
			define("DATA", 'data');
			define("PROP", 'prop');
			define("DEF", 'def');

			define("API", "api");

			define("PUID", "puid");
			define("FMTPUID", "fmt");
			define("XFMTPUID", "x-fmt");
			define("XFMTPUIDALT", "xfmt");

			define("DATACLASS", "file-format");
			define("FORMATREG", "format-registry");

			if(strcmp($slugs->slugs_arr[$typeslugptr], DOC) == 0 && strcmp($slugs->slugs_arr[$classslugptr], DATACLASS) == 0)
			{
				$subject_uri = "<http://the-fr.org/id/". $slugs->slugs_arr[$classslugptr]. "/" . $slugs->slugs_arr[$idslugptr] . ">";
				$tfr_ask_result = ask_triplestore($db, $subject_uri);

				if ($tfr_ask_result == 'true')
				{
					$tfr_describe_query = "describe " . $subject_uri;
					$db->outputfmt(ARC2XML);
					$tfr_describe_result = $db->query($tfr_describe_query, True);
					$xslMDresult = format_tfr_xml($tfr_describe_result);
					print generate_markdown($xslMDresult);
				}
				else
				{
					print generate_markdown("## No data for requested uri (DOC): " . $_SERVER['REQUEST_URI']);
				}
			}
			elseif (strcmp($slugs->slugs_arr[$typeslugptr], DATA) == 0 && strcmp($slugs->slugs_arr[$classslugptr], DATACLASS) == 0)
			{
				$subject_uri = "<http://the-fr.org/id/". $slugs->slugs_arr[$classslugptr]. "/" . $slugs->slugs_arr[$idslugptr] . ">";
				$tfr_ask_result = ask_triplestore($db, $subject_uri);

				if ($tfr_ask_result == 'true')
				{
					$tfr_describe_query = "describe " . $subject_uri;

					$db->outputfmt(return_format($slugs->slugs_arr[$fmtslugptr]));

					$tfr_describe_result = $db->query($tfr_describe_query, True);

					$filename = 'Content-disposition: attachment; filename=' . $slugs->slugs_arr[$idslugptr] . "." . $slugs->slugs_arr[$fmtslugptr];
					header($filename);
					header ('Content-Type: text/xml');
					print $tfr_describe_result;
				}
				else
				{
					print generate_markdown("## No data for requested uri (DATA): " . $_SERVER['REQUEST_URI']);
				}
			}
			elseif (strcmp($slugs->slugs_arr[$typeslugptr], DEF) == 0 && strcmp($slugs->slugs_arr[$classslugptr], FORMATREG) == 0)
			{
				$subject_uri = "<http://the-fr.org/def/". $slugs->slugs_arr[$classslugptr]. "/" . $slugs->slugs_arr[$idslugptr] . ">";
				$tfr_ask_result = ask_triplestore($db, $subject_uri);

				if ($tfr_ask_result == 'true')
				{
					$tfr_describe_query = "describe " . $subject_uri;
					$db->outputfmt(ARC2XML);
					$tfr_describe_result = $db->query($tfr_describe_query, True);
					$xslMDresult = format_prop_xml($tfr_describe_result);
					print generate_markdown($xslMDresult);
				}
				else
				{
					print generate_markdown("## No data for requested uri (DEF): " . $_SERVER['REQUEST_URI']);
				}
			}
			elseif (strcmp($slugs->slugs_arr[$typeslugptr], PROP) == 0 && strcmp($slugs->slugs_arr[$classslugptr], FORMATREG) == 0)
			{
				$subject_uri = "<http://the-fr.org/prop/". $slugs->slugs_arr[$classslugptr]. "/" . $slugs->slugs_arr[$idslugptr] . ">";
				$tfr_ask_result = ask_triplestore($db, $subject_uri);

				if ($tfr_ask_result == 'true')
				{
					$tfr_describe_query = "describe " . $subject_uri;
					$db->outputfmt(ARC2XML);
					$tfr_describe_result = $db->query($tfr_describe_query, True);
					$xslMDresult = format_prop_xml($tfr_describe_result);
					print generate_markdown($xslMDresult);
				}
				else
				{
					print generate_markdown("## No data for requested uri (PROP): " . $_SERVER['REQUEST_URI']);
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

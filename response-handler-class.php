<?php
	
	include_once ("private/sparqllib/sparqllib-arc2-outputformats.php");

	# Standard data URI components
	define("DOC", 'doc');		# document e.g. HTML return
	define("DATA", 'data');		# data e.g. rdf, json, ttl
	define("PROP", 'prop');		# property
	define("DEF", 'def');		# definition

	# Registry extension
	define("API", "api");		# api functionality

	# PRONOM API compatibility
	define("PUID", "puid");
	define("FMTPUID", "fmt");
	define("XFMTPUID", "x-fmt");
	define("ALTXFMTPUID", "xfmt");

	# the-fr.org primary data objects
	define("DATACLASS", "file-format");
	define("FORMATREG", "format-registry");

	#Sample request: http://the-fr.org/id/file-format/1
	#Sample request: http://the-fr.org/doc/file-format/1
	#Sample request: http://the-fr.org/data/file-format/1
	#Sample request: http://the-fr.org/api/id/puid/fmt/1

	class ResponseHandler
	{
		const DOC 		= 0;
		const DEF 		= 1;
		const PROP 	= 2;
		const DATA  	= 3;
		const API 		= 4;
		const UNKNOWN = 9999;

		const URITYPE  		= 0;
		const URICLASS  		= 1;
		const URIVALUE			= 2; 
		const RETURNFORMAT  	= 3;	

		const APITYPE 				= 1;
		const APIEXTERNALSCHEME = 2;
		const APIPUIDTYPE 		= 3; 
		const APIPUIDVALUE 		= 4;

		public $slugs_arr = '';
		public $slugsize = '';

		public $uri_type = '';	# doc, def, prop, data and api

		public $content_type = '';

		public function __construct()
		{
			$this->slugs_arr = $this->get_slugs('/', $_SERVER['REQUEST_URI']);
			$this->slugsize = sizeof($this->slugs_arr);
			$this->uri_type = $this->set_uri_type($this->slugs_arr[self::URITYPE]);
		}

		private function get_slugs($delimeter, $request_uri)
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

		private function set_uri_type($uri_type_from_arr)
		{
			$uritype = self::UNKNOWN;

			if (strcmp($uri_type_from_arr, DOC) == 0)
			{
				$uritype = self::DOC;
			}  
			elseif (strcmp($uri_type_from_arr, DEF) == 0) 
			{
				$uritype = self::DEF;
			}	
			elseif (strcmp($uri_type_from_arr, PROP) == 0) 
			{
				$uritype = self::PROP;
			}
			elseif (strcmp($uri_type_from_arr, DATACLASS) == 0)
			{
				$uritype = self::DATA;
			}
			elseif (strcmp($uri_type_from_arr, API) == 0)
			{
				$uritype = self::API;
			}

			return $uritype;
		}

		public function handle_return_format($extension)
		{
			$format = ARC2XML;
			$extension = strtolower($extension);		

			if($extension == "xml")
			{
				$format = ARC2XML;
				$this->content_type = "application/sparql-results+xml";
			}
			elseif($extension == "json")
			{
				$format = ARC2JSON;
				$this->content_type = "application/json";
			}
			elseif($extension == "php")
			{
				$format = ARC2PHP;
				$this->content_type = "application/vnd.php.serialized";
			}
			elseif($extension == "ttl")
			{
				$format = ARC2TTL;
				$this->content_type = "text/turtle";
			}
			elseif($extension == "rdf")
			{
				$format = ARC2RDFXML;
				$this->content_type = "application/rdf+xml";
			}
			elseif($extension == "tsv")
			{
				$format = ARC2TSV;
				$this->content_type = "text/tab-separated-values";
			}

			return $format;
		}
	}

?>

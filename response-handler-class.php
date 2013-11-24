<?php

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
		const URITYPE  		= 0;
		const URICLASS  		= 1;
		const URIVALUE			= 2; 
		const RETURNFORMAT  	= 3;	

		public $slugs_arr = '';
		public $slugsize = '';

		function __construct()
		{
			$this->slugs_arr = get_slugs('/', $_SERVER['REQUEST_URI']);
			$this->slugsize = sizeof($this->slugs_arr);
		}
	}

?>

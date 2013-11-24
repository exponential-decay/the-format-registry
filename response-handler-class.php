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
	define("XFMTPUIDALT", "xfmt");

	# the-fr.org primary data objects
	define("DATACLASS", "file-format");
	define("FORMATREG", "format-registry");

	class ResponseHandler
	{
		public $slugs_arr = '';
		public $slugsize = '';

		function __construct()
		{
			print $_SERVER['REQUEST_URI'];

			$this->slugs_arr = get_slugs('/', $_SERVER['REQUEST_URI']);
			$this->slugsize = sizeof($this->slugs_arr);
		}
	}

?>

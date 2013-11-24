<?php

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

	class ResponseHandler
	{
		public $slugs_arr = '';
		public $slugsize = '';

		function __construct()
		{
			$root = $_SERVER['DOCUMENT_ROOT'];
			$this->slugs_arr = get_slugs('/', $_SERVER['REQUEST_URI']);
			$this->slugsize = sizeof($this->slugs_arr);
		}
	}

?>

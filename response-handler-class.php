<?php

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

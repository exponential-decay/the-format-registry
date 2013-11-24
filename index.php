<?php

	include_once ("output-index-md.php");

	function handle_request()
	{
		$md = file_get_contents("README.md");
		$md = generate_markdown($md);
		print $md;
		
	}

	handle_request();

?>

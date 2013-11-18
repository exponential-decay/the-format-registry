<?php

	include_once ("output-md-page.php");

	function handle_request()
	{
		$md = file_get_contents("README.md");
		$md = generate_markdown($md);
		print $md;
		
	}

	handle_request();

?>

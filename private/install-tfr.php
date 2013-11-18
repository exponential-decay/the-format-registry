<?php
	
	include("tfr-structure-locations.php");

	function tfr_init()
	{
		if(!file_exists(DATADIR))
		{
			mkdir(DATADIR);
		}

		if(!file_exists(ARCHIVEDIR))
		{
			mkdir(ARCHIVEDIR);
		}

		if(!file_exists(LATESTDIR))
		{
			mkdir(LATESTDIR);
			mkdir(LATESTDIR . "/fmt");
			mkdir(LATESTDIR . "/x-fmt");
		}
	}

?>

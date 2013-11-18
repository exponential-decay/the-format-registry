<?php

	include ("tfr-structure-locations.php");
	include ("pronom-to-rdf-mapping.php");

	header("Content-type: text/plain; charset=utf-8"); 

	function init_nt_file()
	{
		if(!file_exists(TRIPLOC))
		{
			mkdir(TRIPLOC);
		}

		return fopen(TRIPFILE, 'w');
	}

	function close_nt_file($ntfile)
	{
		fclose($ntfile);		# TODO: check if open
	}

	function create_tfr_triples($ntfile, $data)
	{
		pronom_to_rdf_map($ntfile, $data);
	}

	function pronom_data_to_triples()
	{
		$ntfile = init_nt_file();

		$type_arr = array(XFMT, FMT);

		#for($x = 0; $x < 1; $x++)
		for($x = 0; $x < sizeof($type_arr); $x++)
		{
			$basedir = LATESTDIR . "/" . $type_arr[$x];
			$files = scandir($basedir);
			foreach($files as $file)
			{
				$srcfile = $basedir . "/" . $file;

				if(strcmp(substr($srcfile, -4, 4), ".xml") == 0)	# TODO: Better check
				{
					create_tfr_triples($ntfile, file_get_contents($basedir . "/" . $file));
				}			
			}
		}

		close_nt_file($ntfile);
	}

	pronom_data_to_triples();

?>

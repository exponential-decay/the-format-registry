<?php

	include ("tfr-structure-locations.php");
	include ("pronom-to-rdf-mapping.php");
   include ("non-container-relationship-mapping.php");

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

	function create_tfr_triples($ntfile, $data, $puid, $containermagic=False, $priority_list=False)
	{
		pronom_to_rdf_map($ntfile, $data, $puid, $containermagic, $priority_list);
	}

	function pronom_data_to_triples($containermagic=False, $priority_list=False)
	{
		$ntfile = init_nt_file();

		$type_arr = array(XFMT, FMT);

		#for($x = 0; $x < 1; $x++)
		for($x = 0; $x < sizeof($type_arr); $x++)
		{
         $no_array = array();

			$basedir = LATESTDIR . "/" . $type_arr[$x];
			$files = scandir($basedir);

         //sort array consistently by number to set URI values 
         //in concrete. xfmt1 becomes uri1 xfmt10 becomes uri10 
         //not uri2, alphabetical ascending sort...
			foreach($files as $file)
			{
				if(strcmp(substr($file, -4, 4), ".xml") == 0)	# TODO: Better check
				{
               $no = str_replace(".xml", "", $file);
               $no = str_replace($type_arr[$x], "", $no);
	            array_push($no_array, $no);
            }
			}

         asort($no_array);

         foreach ($no_array as $nor)
         {   
				$srcfile = $basedir . "/" . $type_arr[$x] . $nor . ".xml";
				create_tfr_triples($ntfile, file_get_contents($srcfile), $srcfile, $containermagic);
         }
		}

      //given a list of resource, create priority relationships between each...
      if ($priority_list != False && sizeof($priority_list) > 0)
      {
         create_priorities($ntfile, $priority_list);
      }

		close_nt_file($ntfile);
	}

   function read_container_magic()
   {
      $containermagic = array();

      $data = file_get_contents(LATEST_CONTAINER);
      $xml = simplexml_load_string($data);
      foreach ($xml->FileFormatMappings->FileFormatMapping as $FormatMapping)
      {
         $puid = $FormatMapping['Puid'];
         array_push($containermagic, (string)$puid);
      }
      return $containermagic;
   }

   //get magic from container signature file...
   $containermagic = read_container_magic();

   //get information from non-container signature file
   $format_ids = read_signature_priorities();            //format ids from signature file
   $priority_list = get_priority_list($format_ids);      //get priority list from signature file

   //convert pronom data to triples...
	pronom_data_to_triples($containermagic, $priority_list);

?>

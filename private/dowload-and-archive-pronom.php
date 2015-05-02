<?php

	include("tfr-structure-locations.php");

	header("Content-type: text/plain; charset=utf-8"); 

   class PronomData
   {
      public $fmt = '';
      public $xfmt;

      public $sigfileno;
      public $sigfiledate;

      private $pronom_ini_array;

      public $pronom_release_note = "http://www.nationalarchives.gov.uk/aboutapps/pronom/release-notes.xml";

      function __construct()
      {
		   $this->pronom_ini_array = parse_ini_file(INIFILE, true);
         self::load_ini_data();
         self::load_ini_dates();
      }

      function load_ini_data()
      {
         $this->fmt = $this->pronom_ini_array['puids']['fmt'];
		   $this->xfmt = $this->pronom_ini_array['puids']['x-fmt'];	
      }

      function load_ini_dates()
      {
         $this->sigfiledate =  $this->pronom_ini_array['last update']['date'];
         $this->sigfileno =  $this->pronom_ini_array['last update']['fileno'];
      }

      function write_new_ini_data($pronomdata)
      {
         #learn how to write back to an ini file...
         print "Updating ini data with new  values \n";
         print "Latest puid: " + $pronomdata->fmt;
      }
   }



	function normalize_date($date)
	{
		$date = strtolower(str_replace(",", "", $date));
		$date = str_replace(" ", "-", $date);
		$date = str_replace(":", "-", $date);
		return $date;
	}

	function build_download_folders()
	{
		$built = false;

		$dummy_date = "Wed, 12 Nov 2013 10:18:17";	#n.b. write substring or full string...	
		$dummy_file_no = 71;

		$folder_name = "signature-file-v" . $dummy_file_no . "-" . normalize_date($dummy_date);

		if(!file_exists(DATADIR . $folder_name))
		{
			mkdir(DATADIR . $folder_name);
			mkdir(DATADIR . $folder_name . "/fmt");
			$built = mkdir(DATADIR . $folder_name . "/x-fmt");
		}
		else
		{
			$built = true;		#may not always be sufficient
		}

		return DATADIR . $folder_name; 
	}

	function get_data($url) 
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, EXPONENTIALDKWEBAGENT);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	function check_for_data($test)
	{
		$gotdata = false;		
		$PRONOM_HTML_FAIL_CHECK_STR = "<!DOCTYPE html PUBLIC";

		if (strcmp(substr($test, 0, strlen($PRONOM_HTML_FAIL_CHECK_STR)), $PRONOM_HTML_FAIL_CHECK_STR) != 0)
		{
			$gotdata = true;
		}

		return $gotdata;
	}

   function getlastupdateinfo($pronomdata)
   {
		$newdata = false;

      #TODO: Unneded, but might use for testing...
		$dummy_date = "Wed, 12 Nov 2013 10:18:17";	#n.b. write substring or full string...	
		$dummy_file_no = 71;
		
		$release_notes_headers = get_headers($pronomdata->pronom_release_note, 1);

      #Do comparison if date last-modified has changed...
      $olddate = substr($dummy_date, 0, 16);

      $lastmodified = $release_notes_headers['Last-Modified'];

      $newdate = substr($lastmodified, 0, 16);

		if(strcmp($pronomdata->sigfiledate, $newdate) != 0)
      {
         $newdata = true;

         #Update class information, rewrite in destructor...
         $pronomdata->sigfiledate = $newdate;
      }

      return $newdata;
   }

   function loadreleasenotes($pronomdata)
   {
      $file_contents = file_get_contents($pronomdata->pronom_release_note); #, FILE_TEXT, NULL, 234, 3);
      $xml = simplexml_load_string($file_contents) or die("Error: Cannot create object");
      return $xml;
   }

   function getlastsigfileno($pronomdata, $releasenotexml)
   {
      $newfle = false;
      $signaturefilename = $releasenotexml->release_note[0]->signature_filename;
      $signaturefileno = rtrim(ltrim($signaturefilename, 'DROID_SignatureFile_V'), '.xml');
      if ($signaturefileno > $pronomdata->sigfileno)
      {
         $newfile = true;
         $pronomdata->sigfileno = $signaturefileno;
      }
      return $newfile;
   }

   function latest_puid_numbers($pronomdata, $releasenotexml)
   {
      $newdata = false;
      $formats = $releasenotexml->release_note[0]->release_outline[0]->format;

      #number of new formats in release note
      $newformats = sizeof($formats) - 1; 
      
      #update latest fmt/number for scraping PRONOM...
      $newpuid = $formats[$newformats]->puid[0];

      if ($newpuid > $pronomdata->fmt)
      {
         $newdata = true;
         $pronomdata->fmt = $newpuid;
      }

      return $newdata;
   }

	function new_pronom_data_check($pronomdata)
	{
      $newdata = getlastupdateinfo($pronomdata);

      if ($newdata == true)
		{  
         $newdata = false;

         $xml = loadreleasenotes($pronomdata);
         $newdata = getlastsigfileno($pronomdata, $xml);

         if ($newdata == true)
         {
            $newdata = false;
            $newdata = latest_puid_numbers($pronomdata, $xml);
         }

         #release variable once we don't need it... 
         #hope for garbage collection to occur... 
         $xml = null;
		}

		return $newdata;	
	}

	function get_pronom_record($built)
	{
		#sample record url: http://www.nationalarchives.gov.uk/PRONOM/fmt/588.xml
	
		$type_arr = array("fmt", "x-fmt");

		for($x = 0; $x < sizeof($type_arr); $x++)
		{
			#for ($y = 1; $y <= $fmt; $y++)
			for ($y = 1; $y <= 2; $y++)
			{
				$filename = $y . ".xml";
				$url = PRONOMBASEURL . $type_arr[$x] . '/' . $filename;
				$data = get_data($url);
				if(check_for_data($data)) 
				{ 
					file_put_contents($built . '/' . $type_arr[$x] . '/' . $filename, $data);
					file_put_contents(LATESTDIR . $type_arr[$x] . '/' . $filename, $data);
				}
			}
		}
	}

	function archive_pronom_record($built)
	{
		$zipFile = ARCHIVEDIR . str_replace(DATADIR, "", $built) . ".zip";
		$zipArchive = new ZipArchive();

		print $zipFile;

		if(!$zipArchive->open($zipFile, ZIPARCHIVE::OVERWRITE))
			die("Failed to create archive\n");

		$zipArchive->addGlob($built . "/*/*");
		if (!$zipArchive->status == ZIPARCHIVE::ER_OK)
			echo "Failed to write files to zip\n";

		$zipArchive->close();
	}

   #entry point
   $pronomdata = new PronomData();

	if (new_pronom_data_check($pronomdata))
	{
		$built = build_download_folders();
		if($built)
		{
			#get_pronom_record($built);
		   #archive_pronom_record($built);
		}

      $pronomdata->write_new_ini_data($pronomdata);
	}

?>



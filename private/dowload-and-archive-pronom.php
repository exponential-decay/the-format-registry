<?php

	include("tfr-structure-locations.php");

	header("Content-type: text/plain; charset=utf-8"); 

   class PronomData
   {
      public $fmt;
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
         #Example data to write out...
         /*[last update] 
         date=Wed, 12 Nov 2013 10:18:17 GMT
         fileno=71

         [puids]
         ; + 1 actual figure for scraping...
         fmt=610
         x-fmt=456*/

         #TODO: Find a library to handle this
         $lastwritedata = "[last update]\ndate=" . $pronomdata->sigfiledate . "\nfileno=" . $pronomdata->sigfileno;
         $fmtwritedata = "[puids]\nfmt=" . $pronomdata->fmt . "\nx-fmt=" . $pronomdata->xfmt; 

         $inidata = $lastwritedata . "\n\n" . $fmtwritedata;

         $newini = fopen(INIFILE, 'w');
         fwrite($newini, $inidata);
         fclose($newini);
      }
   }



	function normalize_date($date)
	{
		$date = strtolower(str_replace(",", "", $date));
		$date = str_replace(" ", "-", $date);
		$date = str_replace(":", "-", $date);
		return $date;
	}

	function build_download_folders($pronomdata)
	{
		$built = false;

      $folder_name = "signature-file-v" . $pronomdata->sigfileno . "-" . normalize_date($pronomdata->sigfiledate);

      if(!file_exists(DATADIR))
      {
         mkdir(DATADIR);
      }

      $newdir = DATADIR . $folder_name;

		if(!file_exists($newdir))
		{
			mkdir($newdir);

         mkdir(LATESTDIR);
			mkdir(LATESTDIR . "/fmt");
			mkdir(LATESTDIR . "/x-fmt");
  
			mkdir($newdir . "/fmt");
			$built = mkdir($newdir . "/x-fmt");
		}
		else
		{
			$built = true;		#may not always be sufficient
		}

		return $newdir; 
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
         $pronomdata->sigfiledate = $lastmodified;
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
      $newfile = false;
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
         $pronomdata->fmt = (string)$newpuid;   #xfmts never go up
      }

      return $pronomdata;
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
            #$newdata = false;    #dont need this here...
            latest_puid_numbers($pronomdata, $xml);
         }

         #release variable once we don't need it... 
         #hope for garbage collection to occur... 
         $xml = null;
		}

		return $newdata;	
	}

	function get_pronom_record($built, $pronomdata)
	{
		#sample record url: http://app.nationalarchives.gov.uk/PRONOM/fmt/588.xml
	
		$type_arr = array("fmt", "x-fmt");
      $type_ar = array("fmt", "xfmt");

      #delete after testing
      #$pronomdata->fmt = 10;
      #$pronomdata->xfmt = 10;

		for($x = 0; $x < sizeof($type_arr); $x++)
		{
			for ($y = 1; $y <= $pronomdata->$type_ar[$x]; $y++)
			{
				$filename = $y . ".xml";
				$url = PRONOMBASEURL . $type_arr[$x] . '/' . $filename;
				$data = get_data($url);

				if(check_for_data($data)) 
				{ 
               #command to help debug folder ownership...
               #echo exec('whoami');

               #TODO: Latest, and built directories as variables... 
					file_put_contents($built . '/' . $type_arr[$x] . '/' . $filename, $data);
               
					copy ($built . '/' . $type_arr[$x] . '/' . $filename, LATESTDIR . $type_arr[$x] . '/' . $filename);
				}
			}
		}
	}

	function archive_pronom_record($built)
	{
      if (!file_exists(ARCHIVEDIR)) {
         mkdir(ARCHIVEDIR);
      }

		$zipFile = ARCHIVEDIR . str_replace(DATADIR, "", $built) . ".zip";
		$zipArchive = new ZipArchive();

      #seems to open in memory, not be contingent on disk write access.
		if(!$zipArchive->open($zipFile, ZIPARCHIVE::OVERWRITE))
      {
			die("Failed to create archive\n");
      }

      #can't recurse, glob pattern for each subfolder
      #consider rearranging zip another time, when it works.
		$zipArchive->addGlob($built . "/fmt/*");
		$zipArchive->addGlob($built . "/x-fmt/*");

		if (!$zipArchive->status == ZIPARCHIVE::ER_OK)
			error_log("Failed to write files to zip\n");

		$zipArchive->close();
	}

   #entry point
   $pronomdata = new PronomData();

	if (new_pronom_data_check($pronomdata))
	{
		$built = build_download_folders($pronomdata);
		if($built)
		{
			get_pronom_record($built, $pronomdata);
		   archive_pronom_record($built);
		}

      $pronomdata->write_new_ini_data($pronomdata);
      error_log("New PRONOM data available, v" . $pronomdata->sigfileno . " " . $pronomdata->sigfiledate);
	}
   else
   {
      error_log("There is no new PRONOM signature, v" . $pronomdata->sigfileno . " " . $pronomdata->sigfiledate);
   }  

?>



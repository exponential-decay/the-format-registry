<?php
    include "tfr-predicates.php";
    include "tfr-uri-constants.php";
    include "wikidigi/get_wikidigi.php";

   //modifier may be used to add language to literals, e.g. "@en"
    function write_triple($subject, $predicate, $object, $modifier="", $literal=True)
    {
        $spo = "";
        if($literal)
            $object = '"' . $object . '"' . $modifier;
        $spo = $spo . $subject . " " . $predicate . " " . $object . " ." . "\r\n";
        return $spo;
    }

    function extract_class($ntfile, $subject)
    {
        $class = write_triple($subject, CLASS_PREDICATE, FORMAT_CLASS_TYPE, "", false);
        fwrite($ntfile, $class);
    }

   // pronom xml uri
   function make_xml_uri($identifier)
   {
      //e.g. http://www.nationalarchives.gov.uk/PRONOM/fmt/1.xml
      return "<http://www.nationalarchives.gov.uk/PRONOM/" . $identifier . ".xml>";
   }

   // pronom standard uri
   function make_std_uri($identifier)
   {
      //e.g. http://www.nationalarchives.gov.uk/PRONOM/fmt/1
      return "<http://www.nationalarchives.gov.uk/PRONOM/" . $identifier . ">";
   }

   function add_to_uri_list($puid, $subject, &$puid_resource_arr)
   {
      $uri_pair = array();
      $uri_pair['puid'] = (string)$puid;
      $uri_pair['uri'] = (string)$subject;
      array_push($puid_resource_arr, $uri_pair);      //pass by argument...
   }

    function extract_identifiers($ntfile, $subject, $xml, &$puid_resource_arr)
    {
      $puid = false;
        $predicatevalue = "";

        $FormatIdentifier = $xml->FileFormatIdentifier;
        foreach($FormatIdentifier as $Identifier)
        {
            if (strcmp($Identifier->IdentifierType, 'MIME') == 0)
            {
                $predicatevalue = $predicatevalue . write_triple($subject, MEDIATYPE_PREDICATE, $Identifier->Identifier);
            }
            elseif (strcmp($Identifier->IdentifierType, 'PUID') == 0)
            {
            $puid = $Identifier->Identifier;
                $predicatevalue = $predicatevalue . write_triple($subject, PUID_PREDICATE, $puid);
            $predicatevalue = $predicatevalue . write_triple($subject, SAMEAS_PREDICATE, make_std_uri($puid), "", false);
            }
        }

        fwrite($ntfile, $predicatevalue);
      add_to_uri_list($puid, $subject, $puid_resource_arr);
      return $puid;
    }

    function extract_name_version($ntfile, $subject, $xml)
    {
        $name_version = "";
        $name_version = $name_version . write_triple($subject, NAME_PREDICATE, $xml->FormatName, "@en");
        if (strlen(trim($xml->FormatVersion)) > 0)
        {
            $name_version = $name_version . write_triple($subject, VERSION_PREDICATE, $xml->FormatVersion);
        }
        fwrite($ntfile, $name_version);
    }

    function extract_description($ntfile, $subject, $xml)
    {
        fwrite($ntfile, write_triple($subject, DESCRIPTION_PREDICATE, addslashes($xml->FormatDescription), "@en"));
      if (strpos(strtolower($xml->FormatDescription), 'deprecated') !== false)
      {
         fwrite($ntfile, write_triple($subject, DEPRECATED_PREDICATE, "true", "^^<http://www.w3.org/2001/XMLSchema#boolean>"));
      }
      else
      {
         fwrite($ntfile, write_triple($subject, DEPRECATED_PREDICATE, "false", "^^<http://www.w3.org/2001/XMLSchema#boolean>"));
      }
    }

    function extract_type($ntfile, $subject, $xml)
    {
        $typetrip = "";

        $types = str_getcsv($xml->FormatTypes);
        foreach($types as $type)
        {
            if (strcmp(trim($type), "Audio") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, AUDIO_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Aggregate") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, AGGREGATE_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Database") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, DATABASE_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Dataset") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, DATASET_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Email") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, EMAIL_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "GIS") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, GIS_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Image (Raster)") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, RASTER_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Image (Vector)") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, VECTOR_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Page Description") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, PAGEDESC_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Presentation") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, PREZI_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Spreadsheet") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, SPREADSHEET_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Text (Mark-up)") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, MARKUP_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Text (Structured)") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, STRUCTUREDTXT_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Text (Unstructured)") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, UNSTRUCTUREDTXT_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Video") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, VIDEO_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Word Processor") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, WPTXT_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Model") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, MODEL_CLASS_TYPE, "", false);
            }
            elseif (strcmp(trim($type), "Font") == 0)
            {
                $typetrip = $typetrip . write_triple($subject, TYPE_PREDICATE, FONT_CLASS_TYPE, "", false);
            }
         elseif (strcmp(trim($type), "") == 0)
         {
            //do nothing for empty type, XML returns ""
         }
         else
         {
            error_log("Unknown type in PRONOM data: " . trim($type));
         }
        }

        fwrite($ntfile, $typetrip);
    }

    function extract_extension($ntfile, $subject, $xml)
    {
        $exttxt = "";

        foreach($xml->ExternalSignature as $ext)
        {
            if(strcmp($ext->SignatureType, "File extension") == 0)
            {
                $exttxt = $exttxt . write_triple($subject, EXTENSION_PREDICATE, $ext->Signature);
            }
        }

        fwrite($ntfile, $exttxt);
    }

    function extract_alias($ntfile, $subject, $xml)
    {
        $aliastxt = "";

        $aliases = str_getcsv($xml->FormatAliases);
        foreach($aliases as $alias)
        {
            if(strlen(trim($alias)) > 0)
            {
                $aliastxt = $aliastxt . write_triple($subject, ALIAS_PREDICATE, $alias);
            }
        }

        fwrite($ntfile, $aliastxt);
    }

   function extract_magic($ntfile, $subject, $xml, $containermagic=false, $puid=false)
   {
      $container_magic = false;

      if ($puid != false)
      {
         if (array_search($puid, $containermagic) > 0)
         {
            #fmt/681 provides a good example, container, no standard signature
            $container_magic = true;
         }
      }

      $binary_magic = false;
      if (sizeof($xml->InternalSignature) > 0)
      {
         $binary_magic = true;
      }

      $magictext = "";
      $containertext = "";
      $binarytext = "";

      //e.g. <http://example.com/#someBool> <http://www.example.com/2003/01/bool/test#test> "true"^^<http://www.w3.org/2001/XMLSchema#boolean> .
      if ($container_magic == true or $binary_magic == true)
      {
         $magictext = $magictext . write_triple($subject, MAGIC_PREDICATE, "true", "^^<http://www.w3.org/2001/XMLSchema#boolean>");
      }
      else
      {
         $magictext = $magictext . write_triple($subject, MAGIC_PREDICATE, "false", "^^<http://www.w3.org/2001/XMLSchema#boolean>");
      }

      if ($container_magic == true)
      {
         $containertext = $containertext . write_triple($subject, CONTAINER_PREDICATE, "true", "^^<http://www.w3.org/2001/XMLSchema#boolean>");
      }
      else
      {
         $containertext = $containertext . write_triple($subject, CONTAINER_PREDICATE, "false", "^^<http://www.w3.org/2001/XMLSchema#boolean>");
      }

      if ($binary_magic == true)
      {
         $binarytext = $binarytext . write_triple($subject, BINARY_PREDICATE, "true", "^^<http://www.w3.org/2001/XMLSchema#boolean>");
      }
      else
      {
         $binarytext = $binarytext . write_triple($subject, BINARY_PREDICATE, "false", "^^<http://www.w3.org/2001/XMLSchema#boolean>");
      }

      fwrite($ntfile, $magictext);
      fwrite($ntfile, $containertext);
      fwrite($ntfile, $binarytext);
   }

    function add_wikidigi($ntfile, $subject, $puid=false)
    {
        if ($puid != "x-fmt/toggle") {
            error_log("Creating wikidigi triple for " . $puid);
            $wd = puid_to_wikidigi($puid);
            if ($wd) {
                $wd_triple = write_triple($subject, SEEALSO_PREDICATE, $wd, "", False);
                fwrite($ntfile, $wd_triple);
            }
            $wd = puid_to_wididigi_sfw($puid);
            if (is_array($wd) || is_object($wd)) {
                foreach($wd as $uri => $label){
                    $wd_triple = write_triple($subject, WIKIDIGI_SOFTWARE_PREDICATE, $uri, "", False);
                    $wd_label = write_triple($subject, WIKIDIGI_SOFTWARE_LABEL, $label, "", True);
                    fwrite($ntfile, $wd_triple);
                    # fwrite($ntfile, $wd_label);
                }
            }
            $wd = puid_to_fdd($puid);
            if ($wd) {
                $wd_triple = write_triple($subject, SEEALSO_PREDICATE, $wd, "", False);
                fwrite($ntfile, $wd_triple);
                  error_log("Creating wikidigi triple for " . $wd_triple);;
            }
        }
    }

   function create_priorities($ntfile, $priority_list, $puid_resource_arr)
   {
      foreach($priority_list as $fmt)
      {
         $f1 = False;
         $f2 = False;
         $uri1 = '';
         $uri2 = '';
         foreach($puid_resource_arr as $uri)
         {
            if ($fmt['HASPRIORITY'] == $uri['puid'])
            {
               $f1 = True;
               $uri1 = (string)$uri['uri'];
            }
            if ($fmt['OVER'] == $uri['puid'])
            {
               $f2 = True;
               $uri2 = (string)$uri['uri'];
            }

            //we've the uris for the formats we are seeking priorities for
            //translate to a triple string and exit early.
            if ($f1 == True && $f2 == True)
            {
               $priority_text = write_triple($uri1, HASPRIORITY_PREDICATE, $uri2, "", False);
               fwrite($ntfile, $priority_text);
               break;
            }
         }
      }
   }

    function triple_mapper($ntfile, $subject, $formatXML, $containermagic, &$puid_resource_arr)
    {
        extract_class($ntfile, $subject);
        $puid = extract_identifiers($ntfile, $subject, $formatXML, $puid_resource_arr);
        extract_name_version($ntfile, $subject, $formatXML);
        extract_alias($ntfile, $subject, $formatXML);
        extract_description($ntfile, $subject, $formatXML);
        extract_type($ntfile, $subject, $formatXML);
        extract_extension($ntfile, $subject, $formatXML);
        extract_magic($ntfile, $subject, $formatXML, $containermagic, $puid);
        add_wikidigi($ntfile, $subject, $puid);
    }

    function mint_subject()
    {
        static $no = 1;
        $subject = "<" . BASEURI_SUBJECT . $no . ">";
        $no++;
        return $subject;
    }

   function pronom_to_rdf_map($ntfile, $data, $puid, $containermagic, &$puid_resource_arr)
   {
      $xml = simplexml_load_string($data);

      if (substr($data, 0, 1) == '')
      {
         error_log("Problem with file " . $puid . " may be empty.");
      }
      else
      {
         $formatXML = $xml->report_format_detail->FileFormat;
         triple_mapper($ntfile, mint_subject(), $formatXML, $containermagic, $puid_resource_arr);
      }
    }
?>

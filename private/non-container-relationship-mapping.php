<?php

	include_once ("tfr-structure-locations.php");

   define("DROID_PUID", 'PUID');
   define("DROID_ID", 'ID');
   define("DROID_PRIORITY", 'PRIORITY');
   define("SIG_HAS_PRIORITY", 'HASPRIORITY');
   define("SIG_OVER", 'OVER');

   /* Parts of the structure that we're interested in...
   <FFSignatureFile DateCreated="2016-07-27T12:13:11" Version="86" xmlns="http://www.nationalarchives.gov.uk/pronom/SignatureFile">
    <FileFormatCollection>
     <FileFormat ID="1509" MIMEType="audio/x-wav"
         Name="Broadcast WAVE" PUID="fmt/710" Version="1 WAVEFORMATEXTENSIBLE Encoding">
         <InternalSignatureID>1007</InternalSignatureID>
         <InternalSignatureID>1036</InternalSignatureID>
         <Extension>rf64</Extension>
         <Extension>wav</Extension>
         <HasPriorityOverFileFormatID>654</HasPriorityOverFileFormatID>
         <HasPriorityOverFileFormatID>656</HasPriorityOverFileFormatID>
         <HasPriorityOverFileFormatID>786</HasPriorityOverFileFormatID>
     </FileFormat>
    </FileFormatCollection>
   </FFSignatureFile>
   */

   function read_signature_priorities()
   {
      $puids = array();

      $data = file_get_contents(LATEST_NON_CONTAINER);
      $xml = simplexml_load_string($data);
      foreach ($xml->FileFormatCollection->FileFormat as $FileFormat)
      {
         $ff = array();

         $puid = $FileFormat[DROID_PUID];
         $id = $FileFormat[DROID_ID];

         $ff[DROID_PUID] = (string)$puid;
         $ff[DROID_ID] = (string)$id;
         $id_arr = 0;

         if ($FileFormat->HasPriorityOverFileFormatID != null)
         {
            if (sizeof($FileFormat->HasPriorityOverFileFormatID) > 0)
            {
               $id_arr = (array)$FileFormat->HasPriorityOverFileFormatID;
            }
         }

         if (is_array($id_arr)) {
            if (sizeof($id_arr) > 0)
            {
               $ff[DROID_PRIORITY] = $id_arr;
            }
         } else {
            $ff[DROID_PRIORITY] = null;
         }

         array_push($puids, $ff);
      }
      return $puids;
   }

   function get_priority_list($all_ids)
   {
      $results = array();
      $priorities = $all_ids;
      foreach ($priorities as $hasPriority)
      {
         if ($hasPriority[DROID_PRIORITY] != null)
         {
            foreach($hasPriority[DROID_PRIORITY] as $id)
            {
               foreach ($all_ids as $over)
               {
                  if ($id == $over[DROID_ID])
                  {
                     $pri = array();
                     $pri[SIG_HAS_PRIORITY] = $hasPriority[DROID_PUID];
                     $pri[SIG_OVER] = $over[DROID_PUID];
                     array_push($results, $pri);
                  }
               }
            }
         }
      }
      return $results;
   }
?>

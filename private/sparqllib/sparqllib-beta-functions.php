<?php
	
	# functions that might be useful for sparqllib eventually

	function describe_triplestore_subject($db, $subject_uri, $output_format)
	{
		$tfr_describe_query = "describe " . $subject_uri;
		$db->outputfmt = $output_format;
		return $db->query($tfr_describe_query, True);
	}

	function ask_triplestore($db, $subject_uri)
	{
		$tfr_ask_query = "ask where { " . $subject_uri . " ?p ?o . }";
		$db->outputfmt(ARC2PLAIN);
		$tfr_ask_result = $db->query($tfr_ask_query, True);
		return $tfr_ask_result;
	}

	function ask_triplestore_object($db, $predicate, $object)
	{
		$tfr_ask_query = "ask where { ?s " . $predicate . " '" . $object . "' }";
		$db->outputfmt(ARC2PLAIN);
		$tfr_ask_result = $db->query($tfr_ask_query, True);
		return $tfr_ask_result;
	}

	function select_triplestore_object($db, $predicate, $object)
	{
		$tfr_select_query = "SELECT ?record WHERE { ?record " . $predicate . " '" . $object . "'. } LIMIT 1";
		$db->outputfmt(ARC2RDFXML);
		$tfr_ask_result = $db->query($tfr_select_query, True);
		return $tfr_ask_result;
	}

?>

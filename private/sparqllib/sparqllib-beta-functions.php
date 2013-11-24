<?php
	
	# functions that might be useful for sparqllib eventually

	function ask_triplestore($db, $subject_uri)
	{
		$tfr_ask_query = "ask where { " . $subject_uri . " ?p ?o . }";
		$db->outputfmt(ARC2PLAIN);
		$tfr_ask_result = $db->query($tfr_ask_query, True);
		return $tfr_ask_result;
	}
?>

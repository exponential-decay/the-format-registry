<?php

###############################
# Christopher Gutteridge 2010
#  cjg@ecs.soton.ac.uk
#  cc-by License 
#  http://graphite.ecs.soton.ac.uk/sparqllib/
###############################

function sparql_connect( $endpoint ) { return new sparql_connection( $endpoint ); }

function sparql_ns( $short, $long, $db = null ) { return _sparql_a_connection( $db )->ns( $short, $long ); }
function sparql_query( $sparql, $db = null ) { return _sparql_a_connection( $db )->query( $sparql ); }
function sparql_errno( $db = null ) { return _sparql_a_connection( $db )->errno(); }
function sparql_error( $db = null ) { return _sparql_a_connection( $db )->error(); }

function sparql_fetch_array( $result ) { return $result->fetch_array(); }
function sparql_num_rows( $result ) { return $result->num_rows(); }
function sparql_field_array( $result ) { return $result->field_array(); }
function sparql_field_name( $result, $i ) { return $result->field_name( $i ); }

function sparql_fetch_all( $result ) { return $result->fetch_all(); }

function sparql_get( $endpoint, $sparql ) 
{ 
	$db = sparql_connect( $endpoint );
	if( !$db ) { return; }
	$result = $db->query( $sparql );
	if( !$result ) { return; }
	return $result->fetch_all(); 
}

function _sparql_a_connection( $db )
{
	global $sparql_last_connection;
	if( !isset( $db ) )
	{
		if( !isset( $sparql_last_connection ) )
		{
			print( "No currect SPARQL connection (connection) in play!" );
			return;
		}
		$db = $sparql_last_connection;
	}
	return $db;
}
		

#	$timeout = 20;
#	$old = ini_set('default_socket_timeout', $timeout);
#	ini_set('default_socket_timeout', $old);
class sparql_connection
{
	var $db;
	var $debug = false;
	var $errno = null;
	var $error = null;
	var $ns = array();
	
	var $offset = null;
	var $limit = null;
	
	var $outputfmt = null;

	function __construct( $endpoint )
	{
		$this->endpoint = $endpoint;
		global $sparql_last_connection;
		$sparql_last_connection = $this;
	}

	private function setCURLParameters($url)
	{
		if( $this->debug )
		{
			error_log($url);
		}
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url); 			// Set url to talk to, could also set on init
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);		// Fail silently if HTTP code greater than 400 	 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 	// Return into a string variable 
		curl_setopt($ch, CURLOPT_TIMEOUT, 3); 			// Time seconds to allow CURL to execute
		curl_setopt($ch, CURLOPT_HEADER, 0);			// TRUE to include HEADER in output (TODO: Debug mode?)...
		
		return $ch;
	}

	function ns( $short, $long )
	{
		$this->ns[$short] = $long;
	}

	function offset( $offset )
	{
		$this->offset = $offset;
	}

	function limit( $limit )
	{
		$this->limit = $limit;
	}

	function outputfmt( $outputfmt )
	{
		$this->outputfmt = $outputfmt;
	}

	function errno() { return $this->errno; }
	function error() { return $this->error; }

	private function construct_query( $query )
	{
		$prefixes = "";
		foreach( $this->ns as $k=>$v )
		{
			$prefixes .= "PREFIX $k: <$v>\n";
		}
		
		$entire_query = $prefixes.$query;
		
		if( $this->offset !== null )
		{
			$offset = " OFFSET " . $this->offset;
			$entire_query = $entire_query . $offset;
		}

		if( $this->limit !== null )
		{
			$limit = " LIMIT " . $this->limit;
			$entire_query = $entire_query . $limit;
		}

		if( $this->debug ) 
		{
			print "<div class='debug'><a href='".htmlspecialchars($url)."'>".htmlspecialchars($prefixes.$query)."</a></div>\n"; 
		}

		return $entire_query;
	}

	private function handle_curl_error($ch, $output)
	{
		$info = curl_getinfo($ch);

		if(curl_errno($ch))
		{
			$this->errno = curl_errno( $ch );
			$this->error = 'Curl error: ' . curl_error($ch);
			return false;
		}
		if( $output === '' )
		{
			$this->errno = "-1";
			$this->error = 'URL returned no data';
			return false;
		}
		if( $info['http_code'] != 200) 
		{
			$this->errno = $info['http_code'];
			$this->error = 'Bad response, '.$info['http_code'].': '.$output;
			return false;
		}
		
		return true;
	}

	function encode_query($query)
	{
		$url = $this->endpoint."?query=".urlencode( $this->construct_query($query) );
		if ($this->outputfmt != null)
		{
			$url = $url."&output=".urlencode($this->outputfmt);
		}
		return $url;
	}

	//RS: 	query_all() Function to query the entire triple store. Use with care!
	//Args: 	$query: sparql query
	//		  	$limit: sparql query limit
	//			$offset: result offset
	//			$query_iterations: limit the number of times we query endpoint
 
	function query_all( $query, $limit = 20, $offset = 0, $query_iterations = null )
	{
		$this->errno = null;
		$this->error = null;
		
		$this->offset($offset);
		$this->limit($limit);
		
		$bData = true;
		$query_count = 0;

		$output = array();

		while($bData === true)
		{
			//create a new url with new offset parameters...
			$url = $this->encode_query($query);

			// Retrieve CURL session handle...
			$ch = $this->setCURLParameters($url);

			$tmp_output = curl_exec($ch);
			if(!$this->handle_curl_error($ch, $tmp_output)) { return; }

			// Close CURL session...
			curl_close($ch);

			//array to store each sparql result
			$output[] = $tmp_output;
	
			//if output does not contain '<result>' element $bData = false
			if(strstr($tmp_output, '<result>') === false)
				$bData = false;
			
			$offset+=$limit;
			$this->offset($offset);
			
			$query_count++;
			
			//exit loop when we've reached user set number of queries
			if($query_count === $query_iterations)
				$bData = false;
		}

		//For each sparql result in our array merge rows into single array
		for($i = 0; $i < sizeof($output); $i++)
		{
			// Handle CURL output here...
			$parser = new xx_xml($output[$i], 'contents');
			
			if ( $i === 0 ) 
				$all_rows = $parser->rows;		//initial pass, generate and populate array 
			else
				$all_rows = array_merge($all_rows, $parser->rows);
		}
	
		return new sparql_result( $this, $all_rows, $parser->fields );
	}

	function query( $query, $plain=False )
	{	
		$this->errno = null;
		$this->error = null;
		
			//create a new url with new offset parameters...
			$url = $this->encode_query($query);

		// Retrieve CURL session handle...
		$ch = $this->setCURLParameters($url);
		
		$output = curl_exec($ch);

		if(!$this->handle_curl_error($ch, $output)) { return; }
		
		// Close CURL session...
		curl_close($ch);

		if(!$plain)			# continue using SPARQLLIB parsing functions
		{
			// Handle CURL output here...
			$parser = new xx_xml($output, 'contents');
			return new sparql_result( $this, $parser->rows, $parser->fields );
		}
		else { return $output; }	# return content as formatted by endpoint
	}
}

class sparql_result
{
	var $rows;
	var $fields;
	var $db;
	var $i = 0;
	
	function __construct( $db, $rows, $fields )
	{
		$this->rows = $rows;
		$this->fields = $fields;
		$this->db = $db;
	}

	function fetch_array()
	{
		if( !@$this->rows[$this->i] ) { return; }
		$r = array();
		foreach( $this->rows[$this->i++]  as $k=>$v )
		{
			$r[$k] = $v["value"];
		}
		return $r;
	}

	function fetch_all()
	{
		$r = new sparql_results();
		$r->fields = $this->fields;
		foreach( $this->rows as $i=>$row )
		{
			$r []= $this->fetch_array();
		}
		return $r;
	}

	function num_rows()
	{
		return sizeof( $this->rows );
	}

	function field_array()
	{
		return $this->fields;
	}

	function field_name($i)
	{
		return $this->fields[$i];
	}
	
	function rewind()
	{
		$this->i = 0;
	}
}
//End of sparql_result Class


# class xx_xml adapted code found at http://php.net/manual/en/function.xml-parse.php
# class is cc-by 
# hello at rootsy dot co dot uk / 24-May-2008 09:30
class xx_xml 
{

	// XML parser variables
	var $parser;
	var $name;
	var $attr;
	var $data  = array();
	var $stack = array();
	var $keys;
	var $path;
  
	// either you pass url atau contents.
	// Use 'url' or 'contents' for the parameter
	var $type;

	// function with the default parameter value
	function xx_xml($url='http://www.opocot.com', $type='url') 
	{
		$this->type = $type;
		$this->url  = $url;
		$this->parse();
	}
  
	// parse XML data
	function parse()
	{
		$this->rows = array();
		$this->fields = array();

		$data = '';

		$this->parser = xml_parser_create ("UTF-8");

		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'startXML', 'endXML');
		xml_set_character_data_handler($this->parser, 'charXML');

		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);

		if ($this->type == 'url') 
		{
			// if use type = 'url' now we open the XML with fopen

			if (!($fp = fopen($this->url, 'rb')))
			{
				$this->error("Cannot open {$this->url}");
			}

			while (($data = fread($fp, 8192))) 
			{
				if (!xml_parse($this->parser, $data, feof($fp))) 
				{
					$this->error(sprintf('XML error at line %d column %d',
					xml_get_current_line_number($this->parser),
					xml_get_current_column_number($this->parser)));
				}
			}
		} 
		
		//handle XML 'contents' directly instead of passing a URL
		else if ($this->type == 'contents') 
		{
			// Now we can pass the contents, maybe if you want
			// to use CURL, SOCK or other method.
			$lines = explode("\n",$this->url);

			foreach ($lines as $val) 
			{		
				$data = $val . "\n";
				if (!xml_parse($this->parser, $data)) 
				{
					echo $data.'<br />';
					$this->error(sprintf('XML error at line %d column %d',
					xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser)));
				}
			}
		}
	}

	function startXML($parser, $name, $attr)	
	{
		//Handle result data in result, i.e. results 
		//section of SPARQL XML...
		if( $name == "result" )
		{
			$this->result = array();
		}
		if( $name == "binding" )
		{
			$this->part = $attr["name"];
		}
		if( $name == "uri" || $name == "bnode" )
		{
			$this->part_type = "uri";
			$this->chars = "";
		}
		if( $name == "literal" )
		{
			$this->part_type = "literal";
			if( isset( $attr["datatype"] ) )
			{
				$this->part_datatype = $attr["datatype"];
			}
			if( isset( $attr["xml:lang"] ) )
			{
				$this->part_lang = $attr["xml:lang"];
			}
			$this->chars = "";
		}
		
		//Handle field data in result, e.g. ?s ?p ?o .
		if( $name == "variable" )
		{
			$this->fields[] = $attr["name"];
		}
	}

	function endXML($parser, $name)	
	{
		if( $name == "result" )
		{
			$this->rows[] = $this->result;
			$this->result = array();
		}
		if( $name == "uri" || $name == "bnode" || $name == "literal" )
		{
			$this->result[$this->part] = array( "type"=>$name, "value"=>$this->chars );
			if( isset( $this->part_lang ) )
			{
				$this->result[$this->part]["lang"] = $this->part_lang;
			}
			if( isset( $this->part_datatype ) )
			{
				$this->result[$this->part]["datatype"] = $this->part_datatype;
			}
			$this->part_datatype = null;
			$this->part_lang = null;
		}
	}

	function charXML($parser, $data)	
	{
		@$this->chars .= $data;
	}

	function error($msg)	
	{
		echo "<div align=\"center\">
			<font color=\"red\"><b>Error: $msg</b></font>
			</div>";
		exit();
	}
}
//End of xx_xml Class


class sparql_results extends ArrayIterator
{
	var $fields;
	function fields() { return $this->fields; }

	function render_table()
	{
		$html = "<table class='sparql-results'><tr>";
		foreach( $this->fields as $i=>$field )
		{
			$html .= "<th>?$field</th>";
		}
		$html .= "</tr>";
		foreach( $this as $row )
		{
			$html.="<tr>";
			foreach( $row as $cell )
			{
				$html .= "<td>".htmlspecialchars( $cell )."</td>";
			}
			$html.="</tr>";
		}
		$html.="</table>
<style>
table.sparql-results { border-collapse: collapse; }
table.sparql-results tr td { border: solid 1px #000 ; padding:4px;vertical-align:top}
table.sparql-results tr th { border: solid 1px #000 ; padding:4px;vertical-align:top ; background-color:#eee;}
</style>
";
		return $html;exit;
	}

}
//End of sparql_results Class

?>

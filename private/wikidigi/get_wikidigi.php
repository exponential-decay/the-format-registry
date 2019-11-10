<?php

class SPARQLQueryDispatcher
{
    private $endpointUrl;

    public function __construct(string $endpointUrl)
    {
        $this->endpointUrl = $endpointUrl;
    }

    public function query(string $sparqlQuery): array
    {
        // User-agent policy: https://meta.wikimedia.org/wiki/User-Agent_policy
        $agent = 'User-Agent: the-fr.org/0.1 (https://the-fr.org; allalongthewatchtower2001+thefrorg@gmail.com) fr.org/0.1';
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Accept: application/sparql-results+json',
                    $agent
                ],
            ],
        ];
        $context = stream_context_create($opts);
        $url = $this->endpointUrl . '?query=' . urlencode($sparqlQuery);
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true);
    }
}

$wikiDigiPageSPARQLString = <<< 'SPARQL'
SELECT ?format ?pronom ?formatLabel ?software ?softwareLabel WHERE {
  ?format wdt:P2748 "{{ PUID }}".
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
}
SPARQL;

$wikiDigiSoftwareSPARQL = <<< 'SPARQL'
SELECT ?format ?pronom ?formatLabel ?software ?softwareLabel WHERE {
  ?format wdt:P2748 "{{ PUID }}".
  ?software wdt:P1072 ?format.
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
}
SPARQL;

$locFDDSparql = <<< 'SPARQL'
SELECT ?item ?fdd WHERE
{
  ?format wdt:P2748 "{{ PUID }}".
  ?format wdt:P3266 ?fdd .
}
SPARQL;

// Get Wikidata record for a PUID if it exists.
function puid_to_wikidigi($puid) {
    global $wikiDigiPageSPARQLString;
    $search = str_replace("{{ PUID }}", $puid, $wikiDigiPageSPARQLString);
    $endpointUrl = 'https://query.wikidata.org/sparql';
    $queryDispatcher = new SPARQLQueryDispatcher($endpointUrl);
    $queryResult = $queryDispatcher->query($search);
    if ($queryResult["results"]["bindings"]) {
        if ($queryResult["results"]["bindings"][0]["format"]["type"])
        {
            return "<" . $queryResult["results"]["bindings"][0]["format"]["value"] . ">";
        }
    }
    return False;
}

// Get software that can handle the PUID via Wikidata.
function puid_to_wididigi_sfw($puid) {

    global $wikiDigiSoftwareSPARQL;
    $search = str_replace("{{ PUID }}", $puid, $wikiDigiSoftwareSPARQL);
    $endpointUrl = 'https://query.wikidata.org/sparql';
    $queryDispatcher = new SPARQLQueryDispatcher($endpointUrl);
    $queryResult = $queryDispatcher->query($search);
    $uris = array();
    if ($queryResult["results"]["bindings"])
    {
        foreach($queryResult["results"]["bindings"] as $uri) {
            if ($uri["software"]["type"] == "uri") {
                $uris["<" . $uri["software"]["value"] . ">"] = $uri["softwareLabel"]["value"];
            }
        }
        return $uris;
    }
    return False;
}

// Return Library of Congress references via Wikidata lookup also.
function puid_to_fdd($puid) {
    $loc = "https://www.loc.gov/preservation/digital/formats/fdd/";
    global $locFDDSparql;
    $search = str_replace("{{ PUID }}", $puid, $locFDDSparql);
    $endpointUrl = 'https://query.wikidata.org/sparql';
    $queryDispatcher = new SPARQLQueryDispatcher($endpointUrl);
    $queryResult = $queryDispatcher->query($search);
    if ($queryResult["results"]["bindings"]) {
        if ($queryResult["results"]["bindings"][0]["fdd"]["type"])
        {

            return "<" . $loc . $queryResult["results"]["bindings"][0]["fdd"]["value"] . ".shtml" . ">";
        }
    }
    return False;
}

?>


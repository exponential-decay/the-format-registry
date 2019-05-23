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

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Accept: application/sparql-results+json'
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

?>


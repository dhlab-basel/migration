<?php
/**
 * Created by PhpStorm.
 * User: rosenth
 * Date: 21.08.18
 * Time: 17:26
 */

$GLOBALS['server'] = 'http://0.0.0.0:3333';
$GLOBALS['username'] = 'root@example.com';
$GLOBALS['password'] = 'test';

function die_on_api_error($result, $line) {
    if (isset($result->error)) {
        echo 'ERROR in "knora API" at line: ', $line, PHP_EOL, $result->error, PHP_EOL;
        die(-1);
    }
}
//=============================================================================


function knora_get($apiurl, $iri) {
    echo 'GET ', $apiurl, '/', $iri, PHP_EOL;

    $cid = curl_init($apiurl . '/' . urlencode($iri));
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_USERPWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    curl_setopt($cid, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));
    $jsonstr = curl_exec($cid);
    curl_close($cid);
    if (($result = json_decode($jsonstr)) === null) {
        echo 'JSON ERROR CODE = ', json_last_error(), PHP_EOL;
        echo 'RESULT = ', $jsonstr, PHP_EOL;
        $result = new stdClass();
        $result->error = $jsonstr;
    }

    return $result;
}
//=============================================================================


function knora_post_data($apiurl, $data) {
    $datastr = json_encode($data);

    echo 'POST ', $apiurl, PHP_EOL;
    $cid = curl_init($apiurl);
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_POST, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_USERPWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    curl_setopt($cid, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));
    curl_setopt($cid, CURLOPT_POSTFIELDS, $datastr);
    $jsonstr = curl_exec($cid);

    curl_close($cid);
    if (($result = json_decode($jsonstr)) === null) {
        echo 'JSON ERROR CODE = ', json_last_error(), PHP_EOL;
        echo 'RESULT = ', $jsonstr, PHP_EOL;
        $result = new stdClass();
        $result->error = $jsonstr;
    }

    return $result;
}
//=============================================================================


function knora_delete($apiurl, $iri, array $options = NULL) {

    $debug_url = $apiurl. '/' . $iri;
    $url = $apiurl . '/' . urlencode($iri);
    if (!is_null($options)) {
        $sep = '?';
        foreach ($options as $key => $val) {
            $debug_url .= $sep . $key . '=' . $val;
            $url .= $sep . $key . '=' . urlencode($val);
            $sep = '&';
        }
    }

    echo 'DELETE ', $debug_url, PHP_EOL;
    echo $url, PHP_EOL;
    echo 'IRI=', $iri, PHP_EOL;

    $cid = curl_init($url);
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_USERPWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    curl_setopt($cid, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));
    $jsonstr = curl_exec($cid);
    curl_close($cid);
    if (($result = json_decode($jsonstr)) === null) {
        echo 'JSON ERROR CODE = ', json_last_error(), PHP_EOL;
        echo 'RESULT = ', $jsonstr, PHP_EOL;
        $result = new stdClass();
        $result->success = FALSE;
    }

    return $result;
}
//=============================================================================


function create_ontology_struct ($name, $project_iri) {
    $ontology = (object) array(
        'knora-api:ontologyName' => $name,
        'knora-api:attachedToProject' => (object) array(
            '@id' => $project_iri,
        ),
        'rdfs:label' => $name,
        '@context' => (object) array(
            'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
            'knora-api' => 'http://api.knora.org/ontology/knora-api/v2#'
        )
    );
    return $ontology;
}
//=============================================================================

$ontology = create_ontology_struct ('gaga', "http://rdfh.ch/projects/0001");
$result = knora_post_data($GLOBALS['server'] . '/v2/ontologies', $ontology);
die_on_api_error($result, __LINE__);

$result = knora_get($GLOBALS['server'] . '/v2/ontologies/metadata', "http://rdfh.ch/projects/0001");

foreach ($result->{'@graph'} as $res) {
    if ($res->{'rdfs:label'} == 'gaga') {
        print_r($res);
        $ontology_iri = $res->{'@id'};
        $ontology_moddate = $res->{'knora-api:lastModificationDate'};
        echo 'Ontology already exists:', $ontology_iri, PHP_EOL;

        $result = knora_delete(
            $GLOBALS['server'] . '/v2/ontologies',
            $ontology_iri,
            array('lastModificationDate' => $ontology_moddate)
        );
        die_on_api_error($result, __LINE__);
    }
}



?>
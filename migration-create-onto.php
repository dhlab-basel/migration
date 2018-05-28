<?php
/**
 * Created by PhpStorm.
 * User: rosenth
 * Date: 25.05.18
 * Time: 12:12
 */


$GLOBALS['username'] = 'root@example.com';
$GLOBALS['password'] = 'test';

/*
 * Properties in knora-base:
 *
 * - :hasValue
 * - :hasColor
 * - :hasComment
 * - :hasGeometry
 * - :hasLinkTo
 * - :isPartOf
 * - :isRegionOf
 * - :isAnnotationOf
 * - :seqnum
 *
 * Classes in knora-base:
 *
 * - :Resource
 * - :StillImageRepresentation
 * - :TextRepresentation
 * - :AudioRepresentation
 * - :DDDRepresentation
 * - :DocumentRepresentation
 * - :MovingImageRepresentation
 * - :Annotation -> :hasComment, :isAnnotationOf, :isAnnotationOfValue
 * - :LinkObj -> :hasComment, :hasLinkTo, :hasLinkToValue
 * - :LinkValue [reification node]
 * - :Region -> :hasColor, :isRegionOf, :hasGeometry, :isRegionOfValue, :hasComment
 *
 * For lists:
 *
 * - :ListNode -> :hasSubListNode, :listNodePosition, :listNodeName, :isRootNode, :hasRootNode, :attachedToProject
 *
 * Values in knora-base:
 *
 * - :Value
 * - :TextValue
 * - :ColorValue
 * - :DateValue
 * - :DecimalValue
 * - :GeomValue
 * - :GeonameValue
 * - :IntValue
 * - :BooleanValue
 * - :UriValue
 * - :IntervalValue
 * - :ListValue
 *
 * GUI elements
 *
 * - :Colorpicker
 * - :Date
 * - :Geometry
 * - :Geonames
 * - :Interval
 * - :List
 * - :Pulldown
 * - :Radio
 * - :Richtext
 * - :Searchbox
 * - :SimpleText
 * - :Slider
 * - :Spinbox
 * - :Textarea
 * - :Checkbox
 * - :Fileupload
 *
 */
function process_attributes($node) {
    $element = array();
    $attr_node = $node->attributes;
    for ($i = 0; $i < $attr_node->length; $i++) {
        $item = $attr_node->item($i);
        $element[$item->nodeName] = $item->nodeValue;
    }
    return $element;
}
//=============================================================================================


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


function knora_put_data($apiurl, $iri, $data) {
    $datastr = json_encode($data);

    echo 'PUT ', $apiurl, PHP_EOL;
    $cid = curl_init($apiurl . '/' . urlencode($iri));
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_CUSTOMREQUEST, 'PUT');
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

function knora_delete($apiurl, $iri, array $options = NULL) {
    echo 'DELETE ', $apiurl, '/', $iri, PHP_EOL;

    $url = $apiurl . '/' . urlencode($iri);
    if (!is_null($options)) {
        $sep = '?';
        foreach ($options as $key => $val) {
            $url .= $sep . $key . '=' . urlencode($val);
            $sep = '&';
        }
    }

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



function class_struct($ontology_iri,
                      $onto_name,
                      $last_onto_date,
                      $class_name,
                      $super_class,
                      array $labels,
                      array $comments

) {
    $tmp_labels = array();
    foreach ($labels as $lang => $label) {
        $tmp_labels[] = (object) array (
            '@language' => $lang,
            '@value' => $label
        );
    }
    $labels = $tmp_labels;

    $tmp_comments = array();
    foreach ($comments as $lang => $comment) {
        $tmp_comments[] = (object) array (
            '@language' => $lang,
            '@value' => $comment
        );
    }
    $comments = $tmp_comments;

    $class = (object) array (
        'knora-api:hasOntologies' => (object) array (
            '@id' => $ontology_iri,
            '@type' => 'owl:Ontology',
            'knora-api:hasClasses' => array(
                $onto_name . ':' . $class_name => array(
                    '@id' => $onto_name . ':' . $class_name,
                    '@type' => 'owl:Class',
                    'rdfs:label' => $labels,
                    'rdfs:comment' => $comments,
                    'rdfs:subClassOf' => array(
                        '@id' => $super_class
                    )
                )
            ),
            'knora-api:lastModificationDate' => $last_onto_date,
        ),
        '@context' => array(
            "rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
            "knora-api" => "http://api.knora.org/ontology/knora-api/v2#",
            "owl" => "http://www.w3.org/2002/07/owl#",
            "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
            "xsd" => "http://www.w3.org/2001/XMLSchema#",
            $onto_name => $ontology_iri . '#'
        )
    );

    return $class;
}
//=============================================================================

function property_struct($ontology_iri,
                         $onto_name,
                         $last_onto_date,
                         $prop_name,
                         array $super_props,
                         $subject,
                         $object,
                         array $labels,
                         array $comments,
                         $gui_element,
                         array $gui_attributes

) {
    $tmp_labels = array();
    foreach ($labels as $lang => $label) {
        $tmp_labels[] = (object) array (
            '@language' => $lang,
            '@value' => $label
        );
    }
    $labels = $tmp_labels;

    $tmp_comments = array();
    foreach ($comments as $lang => $comment) {
        $tmp_comments[] = (object) array (
            '@language' => $lang,
            '@value' => $comment
        );
    }
    $comments = $tmp_comments;

    $tmp_super_props = array();
    foreach ($super_props as $super_prop) {
        $tmp_super_props[] = (object) array (
            '@id' => $super_prop
        )
    }
    $super_props = $tmp_super_props;

    $property = (object) array(
        'knora-api:hasOntologies' => (object) array (
            '@id' => $ontology_iri,
            '@type' => 'owl:Ontology',
            'knora-api:hasProperties' => (object) array (
                $onto_name . ':' . $prop_name => (object) array(
                    '@id' => $onto_name . ':' . $prop_name,
                    'knora-api:subjectType' => (object) array (
                        '@id' => $subject
                    ),
                    'knora-api:objectType' => (object) array (
                        '@id' => $object
                    ),
                    'rdfs:comment' => $comments,
                    'rdfs:label' => $labels,
                    'rdfs:subPropertyOf' => $super_props,
                    'salsah-gui:guiElement' => (object) array (
                        '@id' => $gui_element
                    ),
                    'salsah-gui:guiAttribute' => $gui_attributes
                )
            ),
            'knora-api:lastModificationDate' => $last_onto_date,
        ),
        '@context' => (object) array (
            'knora-api' => 'http://api.knora.org/ontology/knora-api/v2#',
            'salsah-gui' => 'http://api.knora.org/ontology/salsah-gui/v2#',
            'owl' => 'http://www.w3.org/2002/07/owl#',
            'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
            'xsd' => 'http://www.w3.org/2001/XMLSchema#',
             $onto_name => $ontology_iri . '#'
       )
    );

    return $property;
}
//=============================================================================


function process_restype_node($project_iri, DOMnode $node) {

}
//=============================================================================


function process_vocabulary_node($project_iri, DOMnode $node) {
    $restype_nodes = array();
    $attributes = process_attributes($node);

    $result = knora_get($GLOBALS['server'] . '/v2/ontologies/metadata', $project_iri);
    if ($result->{'knora-api:hasOntologies'}->{'rdfs:label'} == $attributes['name']) {
        $ontology_iri = $result->{'knora-api:hasOntologies'}->{'@id'};
        $ontology_moddate = $result->{'knora-api:hasOntologies'}->{'knora-api:lastModificationDate'};
        echo 'Ontology already exists:', $ontology_iri, PHP_EOL;
        $result = knora_delete($GLOBALS['server'] . '/v2/ontologies',
            $ontology_iri,
            array('lastModificationDate' => $ontology_moddate));
        print_r($result);
    }
    $result = knora_get($GLOBALS['server'] . '/v2/ontologies/metadata', $project_iri);
    print_r($result);

    $ontology = array();
    $ontology['knora-api:ontologyName'] = $attributes['name'];
    $p = array(
        '@id' => $project_iri
    );
    $ontology['knora-api:attachedToProject'] = $p;
    $ontology['rdfs:label'] = $attributes['name'];
    $jsonld = array(
        'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
        'knora-api' => 'http://api.knora.org/ontology/knora-api/v2#'
    );
    $ontology['@context'] = $jsonld;

    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'restype': {
                array_push($restype_nodes, $subnode); break;
            }
        }
    }

    $result = knora_post_data($GLOBALS['server'] . '/v2/ontologies', $ontology);

    $ontology_info = new stdClass();
    $ontology_info->iri = $result->{'knora-api:hasOntologies'}->{'@id'};
    $ontology_info->mod_date = $result->{'knora-api:hasOntologies'}->{'knora-api:lastModificationDate'};

    foreach ($restype_nodes as $restype_node) {
        process_restype($project_iri, $ontology_info, $restype_node);
    }
}
//=============================================================================

function process_project_node(DOMnode $node) {
    $vocabulary_nodes = array();
    $attributes = process_attributes($node);

    $project_iri = 'http://rdfh.ch/projects/' . sprintf('%04x', $attributes['id']);

    $project = new stdClass();
    $project->shortname = $attributes['name'];
    $project->shortcode = sprintf('%04x', $attributes['id']);
    $project->keywords = array();
    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'longname': $project->longname = $subnode->nodeValue; break;
            case 'description': {
                $project->description = array();
                $desc = new stdClass();
                $desc->value = $subnode->nodeValue;
                $desc->language = 'de';
                $project->description[] = $desc;
                break;
            }
            case 'keywords': $project->keywords[] = $subnode->nodeValue; break;
            case 'iconsrc' : $project->logo = $subnode->nodeValue; break;
            case 'vocabulary': array_push($vocabulary_nodes, $subnode); break;
        }
    }
    $project->status = TRUE;
    $project->selfjoin = FALSE;

    $result = knora_get($GLOBALS['server'] . '/admin/projects', $project_iri);
    if (isset($result->error)) {
        $result = knora_post_data($GLOBALS['server'] . '/admin/projects', $project);
        $project_iri = $result->project->id;
    }
    else {
        unset($project->shortcode); // we don't need this for PUT
        $result = knora_put_data($GLOBALS['server'] . '/admin/projects', $project_iri, $project);
        $project_iri = $result->project->id;
    }

    //
    // now process all vocabularies
    //
    foreach ($vocabulary_nodes as $vocabulary_node) {
        process_vocabulary_node($project_iri, $vocabulary_node);
    }

}
//=============================================================================================


$infile = NULL;

function print_usage() {
    echo 'usage: ', $_SERVER['argv'][0], ' [-server url] xml-dump-file', PHP_EOL, PHP_EOL;
    echo '  -server server: server where the ontology should be created!.', PHP_EOL;
    echo PHP_EOL;
}
//=============================================================================================

$GLOBALS['server'] = 'http://0.0.0.0:3333';
for ($i = 1; $i < $_SERVER['argc']; $i++) {
    switch ($_SERVER['argv'][$i]) {
        case '-server': {
            $i++;
            $GLOBALS['server'] = $_SERVER['argv'][$i];
            break;
        }
        case '-list': {
            break;
        }
        default: {
            $infile = $_SERVER['argv'][$i];
        }
    }
}

if (is_null($infile)) {
    print_usage();
    die();
}

//
// Parsing the XML-Document
//
$doc = new DOMDocument();
$doc->load($infile);
if ($doc->childNodes->length == 1) {
    $root = $doc->childNodes->item(0);
    if ($root->nodeName == 'salsah') {
        $node = $root;
        for ($i = 0; $i < $node->childNodes->length; $i++) {
            $project_node = $node->childNodes->item($i);
            if ($project_node->nodeName == '#text') continue;
            if ($project_node->nodeName == '#comment') continue;
            process_project_node($project_node);
        }
    }
}
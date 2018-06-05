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


function die_on_api_error($result, $line) {
    if (isset($result->error)) {
        echo 'ERROR in "knora API" at line: ', $line, PHP_EOL, $result->error, PHP_EOL;
        die(-1);
    }
}
//=============================================================================


function create_property_struct (
    $ontology_iri,
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
        );
    }
    $super_props = $tmp_super_props;

    $property = (object) array(
        '@id' => $ontology_iri,
        '@type' => 'owl:Ontology',
        'knora-api:lastModificationDate' => $last_onto_date,
        '@graph' => array(
            (object) array(
                '@id' => $onto_name . ':' . $prop_name,
                '@type'=> 'owl:ObjectProperty',
                'knora-api:subjectType'=> (object) array(
                    '@id' => $subject
                ),
                'knora-api:objectType' => (object) array(
                    '@id' => $object
                ),
                'rdfs:comment' => $comments,
                'rdfs:label' => $labels,
                'rdfs:subPropertyOf' => $super_props,
                'salsah-gui:guiElement' => (object) array(
                    '@id' => $gui_element
                ),
                'salsah-gui:guiAttribute' => $gui_attributes
            )
        ),
        '@context' => (object) array (
            'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
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


function create_resclass_struct (
    $ontology_iri,
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
        '@id' => $ontology_iri,
        '@type' => 'owl:Ontology',
        'knora-api:lastModificationDate' => $last_onto_date,
        '@graph' => array(
            (object) array(
                '@id' => $onto_name . ':' . $class_name,
                '@type' => 'owl:Class',
                'rdfs:label' => $labels,
                'rdfs:comment' => $comments,
                'rdfs:subClassOf' => (object) array(
                    '@id' => $super_class
                )
            )
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


function create_ontology_struct ($name, $project_iri) {
    /* Docu from scala test
    {
        "knora-api:ontologyName": "foo",
        "knora-api:attachedToProject": {
            "@id": "$imagesProjectIri"
        },
        "rdfs:label": "$label",
        "@context": {
            "rdfs": "${OntologyConstants.Rdfs.RdfsPrefixExpansion}",
            "knora-api": "${OntologyConstants.KnoraApiV2WithValueObjects.KnoraApiV2PrefixExpansion}"
        }
    }
    */
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


function create_project_struct($shortcode, $shortname, $longname, array $descriptions, array $keywords, $logo) {
    $tmp_descriptions = array();
    foreach ($descriptions as $description) {
        $tmp_descriptions[] = (object) array (
            'language' => $description->language,
            'value' => $description->value
        );
    }
    $descriptions = $tmp_descriptions;

    //$project_iri = 'http://rdfh.ch/projects/' . sprintf('%04x', $id);

    $project = (object) array (
        'shortname' => $shortname,
        'shortcode' => $shortcode,
        'longname' => $longname,
        'description' => $descriptions,
        'keywords' => $keywords,
        'logo' => $logo,
        'status' => TRUE,
        'selfjoin' => FALSE
    );
    return $project;
}
//=============================================================================


function process_property_node(
    $ontology_iri,
    $onto_name,
    $last_onto_date,
    $subject_name,
    DOMnode $node
) {
    $attributes = process_attributes($node);

    $labels = array();
    $valtype = NULL;
    $occurrence = NULL;
    $attrs = array();
    $gui_element = NULL;
    $gui_attrs = array();

    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'label': {
                $subattributes = process_attributes($subnode);
                $labels[$subattributes['lang']] = $subnode->nodeValue;
                break;
            }
            case 'valtype' : {
                $valtype = $subnode->nodeValue;
                break;
            }
            case 'occurrence': {
                $occurrence = $subnode->nodeValue;
                break;
            }
            case 'attributes': {
                $attrs = explode(';', $subnode->nodeValue);
                break;
            }
            case 'gui_element': {
                $gui_element = $subnode->nodeValue;
                break;
            }
            case 'gui_attributes': {
                $gui_attrs = explode(';', $subnode->nodeValue);
                break;
            }
        }
    }

    switch ($valtype) {
        case 'VALTYPE_TEXT': {
            $super_props[] = 'knora-api:hasValue';
            $object = 'knora-api:TextValue';
            break;
        }
        case 'VALTYPE_INTEGER': {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        case 'VALTYPE_FLOAT': {
            $super_props[] = 'knora-api:hasValue';
            $object = 'knora-base:IntValue';
            break;
        }
        case 'VALTYPE_DATE': {
            $super_props[] = 'knora-api:hasValue';
            $object = 'knora-base:DateValue';
            break;
        }
        case 'VALTYPE_PERIOD': {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        case 'VALTYPE_RESPTR': {
            $super_props[] = 'knora-api:hasLinkToValue';
            $object = 'knora-base:LinkValue';
            break;
        }
        case 'VALTYPE_SELECTION' : {
            $super_props[] = 'knora-api:hasValue';
            $object = 'knora-base:ListValue';
            break;
        }
        case 'VALTYPE_TIME' : {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        case 'VALTYPE_INTERVAL' : {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        case 'VALTYPE_GEOMETRY' : {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        case 'VALTYPE_COLOR' : {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        case 'VALTYPE_HLIST' : {
            $super_props[] = 'knora-api:hasValue';
            $object = 'knora-base:ListValue';
            break;
        }
        case 'VALTYPE_ICONCLASS' : {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        case 'VALTYPE_RICHTEXT' : {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        case 'VALTYPE_GEONAME' : {
            $super_props[] = 'knora-api:hasValue';
            break;
        }
        default: {
            $super_props[] = 'knora-api:hasValue';
        }
    }

    $subject = $onto_name . ':' . $subject_name;
    create_property_struct (
        $ontology_iri,
        $onto_name,
        $last_onto_date,
        $attributes['name'],
        $super_props,
        $subject,
        $object,
        $labels,
        $comments,
        $gui_element,
        $gui_attributes
    );

    return $last_onto_date;

}
//=============================================================================


function process_resclass_node(
    $ontology_iri,
    $onto_name,
    $last_onto_date,
    DOMnode $node
) {
    $attributes = process_attributes($node);

    $properties = array();
    $labels = array();
    $comments = array();
    $class_name = $attributes['name'];
    $superclass = NULL;

    switch ($attributes['type']) {
        case 'object': $super_class = 'knora-api:Resource'; break;
        case 'textobject': $super_class = 'knora-api:TextRepresentation'; break;
        case 'image' : $super_class = 'knora-api:StillImageRepresentation'; break;
        case 'sound': $super_class = 'knora-api:AudioRepresentation'; break;
        case 'movie': $super_class = 'knora-api:MovingImageRepresentation'; break;
        case 'region'; $super_class = 'knora-api:Region'; break;
        default: $super_class = 'knora-api:Resource';
    }


    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'label': {
                $subattributes = process_attributes($subnode);
                $labels[$subattributes['lang']] = $subnode->nodeValue;
                break;
            }
            case 'description': {
                $subattributes = process_attributes($subnode);
                $comments[$subattributes['lang']] = $subnode->nodeValue;
                break;
            }
            case 'iconsrc': break; // does not exist in API???
            case 'property': array_push($properties, $subnode); break;
        }
    }

    $resclass = create_resclass_struct (
        $ontology_iri,
        $onto_name,
        $last_onto_date,
        $class_name,
        $super_class,
        $labels,
        $comments
    );

    $result = knora_post_data($GLOBALS['server'] . '/v2/ontologies/classes', $resclass);
    echo '----------------------------------------------------------', PHP_EOL;
    print_r($result);
    echo '==========================================================', PHP_EOL;
    die_on_api_error($result, __LINE__);
    $last_onto_date = $result->{'knora-api:lastModificationDate'};

    foreach ($properties as $property_node) {
        $last_onto_date = process_property_node(
            $ontology_iri,
            $onto_name,
            $last_onto_date,
            $class_name,
            $property_node
        );
    }

    return $last_onto_date;
}
//=============================================================================


function process_ontology_node($project_iri, DOMnode $node) {
    $restype_nodes = array();
    $attributes = process_attributes($node);
    $onto_name = $attributes['name'];

    $result = knora_get($GLOBALS['server'] . '/v2/ontologies/metadata', $project_iri);

    if ($result->{'rdfs:label'} == $attributes['name']) {
        $ontology_iri = $result->{'@id'};
        $ontology_moddate = $result->{'knora-api:lastModificationDate'};
        echo 'Ontology already exists:', $ontology_iri, PHP_EOL;

        $result = knora_delete(
            $GLOBALS['server'] . '/v2/ontologies',
            $ontology_iri,
            array('lastModificationDate' => $ontology_moddate)
        );
        die_on_api_error($result, __LINE__);
    }

    $ontology = create_ontology_struct ($onto_name, $project_iri);

    $result = knora_post_data($GLOBALS['server'] . '/v2/ontologies', $ontology);
    die_on_api_error($result, __LINE__);

    $ontology_iri = $result->{'@id'};
    $ontology_moddate = $result->{'knora-api:lastModificationDate'};


    $restype_nodes = array();

    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'longname': break; // we do nothing with the long vocabulary/ontology name
            case 'uri': break; // we ignore this "fake" uri...
            case 'restype': array_push($restype_nodes, $subnode); break;
            default: ;
        }
    }

    foreach ($restype_nodes as $restype_node) {
        $ontology_moddate = process_resclass_node(
            $ontology_iri,
            $onto_name,
            $ontology_moddate,
            $restype_node
        );
    }
}
//=============================================================================

function process_project_node(DOMnode $node) {
    $attributes = process_attributes($node);
    $shortname = $attributes['shortname'];
    $shortcode = sprintf('%04x', $attributes['id']);
    $shortcode = '1010';

    $vocabulary_nodes = array();
    $project_iri = 'http://rdfh.ch/projects/' . $shortcode;


    $descriptions = array();
    $keywords = array();
    $logo = NULL;
    $longname = NULL;

    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'longname': $longname = $subnode->nodeValue; break;
            case 'description': {
                $subattributes = process_attributes($subnode);
                $desc = new stdClass();
                $desc->value = $subnode->nodeValue;
                $desc->language = $subattributes['lang'];
                $descriptions[] = $desc;
                break;
            }
            case 'keywords': $keywords[] = trim($subnode->nodeValue); break;
            case 'iconsrc' : $logo = $subnode->nodeValue; break;
            case 'vocabulary': array_push($vocabulary_nodes, $subnode); break;
        }
    }

    $project = create_project_struct($shortcode, $shortname, $longname, $descriptions, $keywords, $logo);

    $result = knora_get($GLOBALS['server'] . '/admin/projects', $project_iri);
    if (isset($result->error)) {
        $result = knora_post_data($GLOBALS['server'] . '/admin/projects', $project);
        die_on_api_error($result, __LINE__);
        $project_iri = $result->project->id;
    }
    else {
        unset($project->shortcode); // we don't need this for PUT
        $result = knora_put_data($GLOBALS['server'] . '/admin/projects', $project_iri, $project);
        die_on_api_error($result, __LINE__);
        $project_iri = $result->project->id;
    }

    //
    // now process all vocabularies
    //
    foreach ($vocabulary_nodes as $vocabulary_node) {
        process_ontology_node($project_iri, $vocabulary_node);
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
<?php
/**
 * Created by PhpStorm.
 * User: rosenth
 * Date: 25.05.18
 * Time: 12:12
 */


$GLOBALS['username'] = 'root@example.com';
$GLOBALS['password'] = 'test';

$GLOBALS['selections'] = array();
$GLOBALS['hlists'] = array();
$GLOBALS['res_ids'] = array();
$GLOBALS['properties'] = array();

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


function die_on_api_error($result, $line, $data = NULL) {
    if (isset($result->error)) {
        echo 'ERROR in "knora API" at line: ', $line, PHP_EOL, $result->error, PHP_EOL;
        if (isset($data)) print_r($data);
        die(-1);
    }
}
//=============================================================================


function create_property_struct (
    string $ontology_iri,
    string $onto_name,
    string $last_onto_date,
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
    if (count($comments) == 0) $comments['en'] = 'none';
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

    $propdata = new stdClass;
    $propdata->{'@id'} = $onto_name . ':' . $prop_name;
    $propdata->{'@type'} = 'owl:ObjectProperty';
    if (!is_null($subject)) {
        $propdata->{'knora-api:subjectType'} = (object) array(
            '@id' => $subject
        );

    }
    $propdata->{'knora-api:objectType'} = (object) array(
        '@id' => $object
    );
    if (count($comments) > 0) {
        $propdata->{'rdfs:comment'} = $comments;
    }
    $propdata->{'rdfs:label'} = $labels;
    $propdata->{'rdfs:subPropertyOf'} = $super_props;
    $propdata->{'salsah-gui:guiElement'} = (object) array(
        '@id' => $gui_element
    );
    if (count($gui_attributes) > 0) {
        $propdata->{'salsah-gui:guiAttribute'} = $gui_attributes;
    }


    $property = (object) array(
        '@id' => $ontology_iri,
        '@type' => 'owl:Ontology',
        'knora-api:lastModificationDate' => $last_onto_date,
        '@graph' => array(
            $propdata
        ),
        '@context' => (object) array (
            'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
            'knora-api' => 'http://api.knora.org/ontology/knora-api/v2#',
            'salsah-gui' => 'http://api.knora.org/ontology/salsah-gui/v2#',
            'owl' => 'http://www.w3.org/2002/07/owl#',
            'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
            'xsd' => 'http://www.w3.org/2001/XMLSchema#',
            'dcterms' => 'http://purl.org/dc/terms/',
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
    if (count($comments) == 0) $comments['en'] = 'none';
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

function create_list_struct($name, $project_iri, array $labels, array $comments) {
    $tmp_labels = array();
    foreach ($labels as $label) {
        $tmp_labels[] = (object) array (
            'language' => $label->language,
            'value' => $label->value
        );
    }
    $labels = $tmp_labels;

    $tmp_comments = array();
    foreach ($comments as $comment) {
        $tmp_comments[] = (object) array (
            'language' => $comment->language,
            'value' => $comment->value
        );
    }
    $comments = $tmp_comments;

    $list = (object) array(
        'projectIri' => $project_iri,
        'labels' => $labels,
        'comments' => $comments
    );

    return $list;

}
//=============================================================================


function create_cardinality_struct(
    $ontology_iri,
    $onto_name,
    $class_iri,
    $property_iri,
    $occurence,
    $last_mod_date
) {
    switch ($occurence) {
        case '1': $cc1 = 'owl:cardinality'; $cc2 = 1; break;
        case '0-1': $cc1 = 'owl:maxCardinality'; $cc2 = 1; break;
        case '0-n': $cc1 = 'owl:minCardinality'; $cc2 = 0; break;
        case '1-n': $cc1 = 'owl:minCardinality'; $cc2 = 1; break;
        default: die('FATAL ERROR: Unknown cardinality: ' . $occurence);
    }
    $cardinality = (object) array(
        '@id' => $ontology_iri,
        '@type' => 'owl:Ontology',
        'knora-api:lastModificationDate' => $last_mod_date,
        '@graph' => array(
            (object) array(
                '@id' => $class_iri,
                '@type' => 'owl:Class',
                'rdfs:subClassOf' => (object) array(
                    '@type' => 'owl:Restriction',
                    $cc1 => $cc2,
                    'owl:onProperty' => (object) array(
                        '@id' => $property_iri
                    )
                )
            )
        ),
        '@context' => (object) array(
            'rdf' => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
            'knora-api' => 'http://api.knora.org/ontology/knora-api/v2#',
            'owl' => 'http://www.w3.org/2002/07/owl#',
            'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
            'xsd' => 'http://www.w3.org/2001/XMLSchema#',
            $onto_name => $ontology_iri . '#'
        )
    );

    return $cardinality;
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

    $prop_voc = $attributes['vocabulary'];
    $prop_name = $attributes['name'];

    if ($prop_name == '__location__') return $last_onto_date;

    $labels = array();
    $valtype = NULL;
    $occurrence = NULL;
    $super_props = array();
    $attrs = array();
    $gui_element = NULL;
    $gui_attrs = array();
    $comments = array();
    $resptr = NULL;
    $res_id = NULL;
    $used_by_res = array();

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
            case 'id' : {
                $res_id = $subnode->nodeValue;
            }
            case 'occurrence': {
                $occurrence = $subnode->nodeValue;
                break;
            }
            case 'used_by_res': {
                array_push($used_by_res, $subnode->nodeValue);
                break;
            }
            case 'attributes': {
                $tmp_attrs = explode(';', $subnode->nodeValue);
                foreach ($tmp_attrs as $attr) {
                    if (!empty($attr)) {
                        $attrs[] = $attr;
                    }
                }
                break;
            }
            case 'gui_element': {
                $gui_element = $subnode->nodeValue;
                switch ($subnode->nodeValue) {
                    case 'text': $gui_element = 'salsah-gui:SimpleText'; break;
                    case 'textarea': $gui_element = 'salsah-gui:Textarea'; break;
                    case 'pulldown': $gui_element = 'salsah-gui:Pulldown'; break;
                    case 'slider': $gui_element = 'salsah-gui:Slider'; break;
                    case 'spinbox': $gui_element = 'salsah-gui:Spinbox'; break;
                    case 'searchbox': $gui_element = 'salsah-gui:Searchbox'; break;
                    case 'date': $gui_element = 'salsah-gui:Date'; break;
                    case 'geometry': $gui_element = 'salsah-gui:Geometry'; break;
                    case 'colorpicker': $gui_element = 'salsah-gui:Colorpicker'; break;
                    case 'hlist': $gui_element = 'salsah-gui:List'; break;
                    case 'radio': $gui_element = 'salsah-gui:Radio'; break;
                    case 'richtext': $gui_element = 'salsah-gui:Richtext'; break;
                    //case 'time': $gui_element = 'salsah-gui:'; break;
                    case 'interval': $gui_element = 'salsah-gui:Interval'; break;
                    case 'geoname': $gui_element = 'salsah-gui:Geonames'; break;
                    default: echo '??????: ', $subnode->nodeValue, PHP_EOL; die();
                }
                break;
            }
            case 'gui_attributes': {
                // we don't need these here
                // $gui_attrs = explode(';', $subnode->nodeValue);
                break;
            }
            case 'resptr': {
                $resptr = $subnode->nodeValue;
                break;
            }
        }
    }


    $object = NULL;
    if ($prop_voc == 'salsah') {
        switch ($prop_name) {
            case 'lastname':
            case 'firstname':
            case 'institution':
            case 'address':
            case 'city':
            case 'zipcode':
            case 'phone':
            case 'fax':
            case 'email':
            case 'origname':
            case 'institution':
            case 'keyword':
            case 'label':
                {
                    //
                    // all these are just a sub-property of a TextValue;
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'uri':
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:UriValue';
                    break;
                }
            case 'comment':
            case 'comment_rt':
                {
                    $super_props[] = 'knora-api:hasComment';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'part_of':
                {
                    $super_props[] = 'knora-api:isPartOf';
                    $object = is_null($resptr) ? 'knora-api:Resource' : $resptr; // $resptr may be NULL !!
                    break;
                }
            case 'region_of':
                {
                    $super_props[] = 'knora-api:isRegionOf';
                    $object = is_null($resptr) ? 'knora-api:Representation' : $resptr; // $resptr may be NULL !!
                    break;
                }
            case 'seqnum':
                {
                    $super_props[] = 'knora-api:seqnum';
                    $object = 'knora-api:IntValue';
                    break;
                }
            case 'transcription':
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'canton':
                {
                    // TODO: !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    break;
                }
            case 'color':
                {
                    $super_props[] = 'knora-api:hasColor';
                    $object = 'knora-api:ColorValue';
                    break;
                }
            case 'external_id':
                {
                    // TODO: !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    break;
                }
            case 'external_provider':
                {
                    // TODO: !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    break;
                }
            case 'geography':
                {
                    // TODO: !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    break;
                }
            case 'geometry':
                {
                    $super_props[] = 'knora-api:hasGeometry';
                    $object = 'knora-api:GeometryValue';
                }
            default:
                {
                    die('FATAL ERROR: Unknown property "' . $prop_name . '" in salsah system vocabulary!' . PHP_EOL);
                }
        }
    }
    else if ($prop_voc == 'dc') {
        switch ($prop_name) {
            case 'author':
                {
                    $super_props[] = 'dcterms:author';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'contributor':
                {
                    $super_props[] = 'dcterms:contributor';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'coverage':
                {
                    $super_props[] = 'dcterms:coverage';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'creator':
                {
                    $super_props[] = 'dcterms:creator';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'date':
                {
                    $super_props[] = 'dcterms:date';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:DateValue';
                    break;
                }
            case 'description':
            case 'description_rt':
                {
                    $super_props[] = 'dcterms:creator';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'format':
                {
                    $super_props[] = 'dcterms:format';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'identifier':
                {
                    $super_props[] = 'dcterms:identifier';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'language':
                {
                    $super_props[] = 'dcterms:language';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'publisher':
                {
                    $super_props[] = 'dcterms:publisher';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'relation':
                {
                    $super_props[] = 'dcterms:relation';
                    $super_props[] = 'knora-api:hasValue';
                    $object = is_null($resptr) ? 'knora-api:Resource' : $resptr; // $resptr may be NULL !!
                    break;
                }
            case 'rights':
                {
                    $super_props[] = 'dcterms:rights';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'source':
            case 'source_rt':
                {
                    $super_props[] = 'dcterms:source';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'subject':
                {
                    $super_props[] = 'dcterms:subject';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'title':
                {
                    $super_props[] = 'dcterms:title';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            case 'type':
                {
                    $super_props[] = 'dcterms:type';
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    break;
                }
            default:
                {
                    // TODO: ERROR MESSAGE !!!!!!!!!!!!
                }
        }
    }
    else if ($prop_voc == $onto_name) {
        switch ($valtype) {
            case 'VALTYPE_TEXT':
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    $gui_attrs = $attrs;
                    break;
                }
            case 'VALTYPE_INTEGER':
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:IntValue';
                    $gui_attrs = $attrs;
                    break;
                }
            case 'VALTYPE_FLOAT':
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:DecimalValue';
                    $gui_attrs = $attrs;
                    break;
                }
            case 'VALTYPE_DATE':
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:DateValue';
                    $gui_attrs = $attrs;
                    break;
                }
            case 'VALTYPE_PERIOD':
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:DateValue';
                    $gui_attrs = $attrs;
                    break;
                }
            case 'VALTYPE_RESPTR':
                {
                    $super_props[] = 'knora-api:hasLinkTo';
                    $object = 'knora-api:Resource';
                    $tmp_attrs = array();
                    foreach ($attrs as $i => $attr) {
                        if (strncmp('restypeid=', $attr, 10) == 0) {
                            list($dummy, $res_id) = explode('=', $attr);
                            if (array_key_exists($res_id, $GLOBALS['res_ids'])) {
                                $object = $GLOBALS['res_ids'][$res_id];
                            } else {
                                echo '==> PROBLEM!!!!!!: resourcetype_id = ', $res_id, ' not known!', PHP_EOL;
                                print_r($GLOBALS['res_ids']);
                                die();
                            }
                        } else {
                            array_push($tmp_attrs, $attr);
                        }
                    }
                    break;
                }
            case 'VALTYPE_SELECTION' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:ListValue';

                    $tmp_attrs = array();
                    foreach ($attrs as $i => $attr) {
                        if (strncmp('selection=', $attr, 10) == 0) {
                            list($dummy, $sel_id) = explode('=', $attr);
                            $sel_iri = $GLOBALS['selections'][$sel_id];
                            array_push($tmp_attrs, 'hlist=<' . $sel_iri . '>');
                        } else {
                            array_push($tmp_attrs, $attr);
                        }
                    }

                    $gui_attrs = $tmp_attrs;
                    break;
                }
            case 'VALTYPE_TIME' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    break;
                }
            case 'VALTYPE_INTERVAL' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    $super_props[] = 'knora-api:IntervalValue';
                    break;
                }
            case 'VALTYPE_GEOMETRY' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    break;

                }
            case 'VALTYPE_COLOR' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    break;
                }
            case 'VALTYPE_HLIST' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:ListValue';

                    $tmp_attrs = array();
                    foreach ($attrs as $i => $attr) {
                        if (strncmp('hlist=', $attr, 6) == 0) {
                            list($dummy, $sel_id) = explode('=', $attr);
                            $sel_iri = $GLOBALS['hlists'][$sel_id];
                            array_push($tmp_attrs, 'hlist=<' . $sel_iri . '>');
                        } else {
                            array_push($tmp_attrs, $attr);
                        }
                    }
                    $gui_attrs = $tmp_attrs;
                    break;
                }
            case 'VALTYPE_ICONCLASS' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    break;
                }
            case 'VALTYPE_RICHTEXT' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:TextValue';
                    $gui_attrs = $attrs;
                    break;
                }
            case 'VALTYPE_GEONAME' :
                {
                    $super_props[] = 'knora-api:hasValue';
                    $object = 'knora-api:GeonameValue';
                    $gui_attrs = array();
                    break;
                }
            default:
                {
                    $super_props[] = 'knora-api:hasValue';
                }
        }
    }
    else {
        die('FATAL ERROR: Unknown vocabulary "' . $prop_voc . '" for property "' . $prop_name . '"!' . PHP_EOL);
    }


    //if (($prop_voc == $onto_name) and (count($used_by_res) > 1)) {
    if (count($used_by_res) > 1) {
        $subject = 'knora-api:Resource';
    }
    else {
        $subject = $onto_name . ':' . $subject_name;
    }


    $property = create_property_struct (
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
        $gui_attrs
    );

    if (!array_key_exists($prop_voc . ':' . $prop_name,  $GLOBALS['properties'])) {
        echo 'INFO: Adding ', $onto_name, ':', $attributes['name'], '...';
        $result = knora_post_data($GLOBALS['server'] . '/v2/ontologies/properties', $property);
        if (isset($result->error)) print_r($GLOBALS['properties'][$prop_voc . ':' . $prop_name]);
        die_on_api_error($result, __LINE__, $property);
        echo 'done!', PHP_EOL;
        $last_onto_date = $result->{'knora-api:lastModificationDate'};
        $GLOBALS['properties'][$prop_voc . ':' . $prop_name] = $result;
    }

    $property_iri = $onto_name . ':' . $attributes['name'];

    echo 'INFO: Adding cardinality: ', $property_iri, PHP_EOL;

    $class_iri = $onto_name . ':' . $subject_name;
    $cardinality = create_cardinality_struct(
        $ontology_iri,
        $onto_name,
        $class_iri,
        $property_iri,
        $occurrence,
        $last_onto_date
    );


    $result = knora_post_data($GLOBALS['server'] . '/v2/ontologies/cardinalities', $cardinality);
    die_on_api_error($result, __LINE__, $property);

    $last_onto_date = $result->{'knora-api:lastModificationDate'};

    return $last_onto_date;
}
//=============================================================================


function get_resclass_ids($onto_name, DOMnode $node) {
    $attributes = process_attributes($node);
    $class_name = $attributes['name'];
    $res_id = $attributes['id'];
    $GLOBALS['res_ids'][$res_id] = $onto_name . ':' . $class_name;
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
    die_on_api_error($result, __LINE__, $resclass);
    $last_onto_date = $result->{'knora-api:lastModificationDate'};
    $class_iri = $result->{'@graph'}[0]->{'@id'};

    return array($class_name, $properties, $last_onto_date);
}
//=============================================================================


function process_ontology_node($project_iri, DOMnode $node) {
    $lists = array();
    $restype_nodes = array();
    $attributes = process_attributes($node);
    $onto_name = $attributes['name'];

    $result = knora_get($GLOBALS['server'] . '/v2/ontologies/metadata', $project_iri);

    if (isset($result->{'@graph'})) {
        foreach ($result->{'@graph'} as $res) {
            if ($res->{'rdfs:label'} == $attributes['name']) {
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
    }
    else {
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
    }

    $ontology = create_ontology_struct ($onto_name, $project_iri);

    $result = knora_post_data($GLOBALS['server'] . '/v2/ontologies', $ontology);
    die_on_api_error($result, __LINE__, $ontology);

    $ontology_iri = $result->{'@id'};
    $ontology_moddate = $result->{'knora-api:lastModificationDate'};


    $restype_nodes = array();
    $selections = array();
    $hlists = array();

    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'longname': break; // we do nothing with the long vocabulary/ontology name
            case 'uri': break; // we ignore this "fake" uri...
            case 'selection': array_push($selections, $subnode); break;
            case 'hlist': array_push($hlists, $subnode); break;
            case 'restype': array_push($restype_nodes, $subnode); break;
            default: ;
        }
    }

    foreach ($selections as $selection) {
        $selinfo = process_selection_nodes($project_iri, $selection);
    }


    foreach ($hlists as $hlist) {
        $hlistsinfo = process_hlist_nodes($project_iri, $hlist);
    }

    foreach ($restype_nodes as $restype_node) {
        get_resclass_ids($onto_name, $restype_node);
    }

    $class_properties = array();
    foreach ($restype_nodes as $restype_node) {
        list ($class_name, $properties, $ontology_moddate) = process_resclass_node(
            $ontology_iri,
            $onto_name,
            $ontology_moddate,
            $restype_node
        );
        $class_properties[$class_name] = $properties;
    }

    foreach ($class_properties as $class_name => $properties) {
        foreach ($properties as $property_node) {
            $ontology_moddate = process_property_node(
                $ontology_iri,
                $onto_name,
                $ontology_moddate,
                $class_name,
                $property_node
            );
        }
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
        die_on_api_error($result, __LINE__, $project);
        $project_iri = $result->project->id;
    }
    else {
        unset($project->shortcode); // we don't need this for PUT
        $result = knora_put_data($GLOBALS['server'] . '/admin/projects', $project_iri, $project);
        die_on_api_error($result, __LINE__, $project);
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

function process_selection_nodes($project_iri, DOMnode $node) {
    $attributes = process_attributes($node);
    $selection_name = $attributes['name'];
    $selection_id = $attributes['id'];

    $comments = array();
    $labels = array();
    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'label': {
                $subattributes = process_attributes($subnode);
                $label = new stdClass();
                $label->value = $subnode->nodeValue;
                $label->language = $subattributes['lang'];
                $labels[] = $label;
                break;
            }
            case 'description': {
                $subattributes = process_attributes($subnode);
                $comment = new stdClass();
                $comment->value = $subnode->nodeValue;
                $comment->language = $subattributes['lang'];
                $comments[] = $comment;
                break;
            }
            case 'nodes': break;
            default:
        }
    }
    $list = create_list_struct($selection_name, $project_iri, $labels, $comments);
    print_r($list); die();
    $result = knora_post_data($GLOBALS['server'] . '/admin/lists', $list);
    die_on_api_error($result, __LINE__, $list);

    $GLOBALS['selections'][$selection_id] = $result->list->listinfo->id;

    return $result->list->listinfo;
}
//=============================================================================================

function process_hlist_nodes($project_iri, DOMnode $node) {
    $attributes = process_attributes($node);
    $hlist_name = $attributes['name'];
    $hlist_id = $attributes['id'];

    $comments = array();
    $labels = array();
    for ($i = 0; $i < $node->childNodes->length; $i++) {
        $subnode = $node->childNodes->item($i);
        switch($subnode->nodeName) {
            case 'label': {
                $subattributes = process_attributes($subnode);
                $label = new stdClass();
                $label->value = $subnode->nodeValue;
                $label->language = $subattributes['lang'];
                $labels[] = $label;
                break;
            }
            case 'description': {
                $subattributes = process_attributes($subnode);
                $comment = new stdClass();
                $comment->value = $subnode->nodeValue;
                $comment->language = $subattributes['lang'];
                $comments[] = $comment;
                break;
            }
            case 'nodes': break;
            default:
        }
    }
    $hlist = create_list_struct($hlist_name, $project_iri, $labels, $comments);
    $result = knora_post_data($GLOBALS['server'] . '/admin/lists', $hlist);
    die_on_api_error($result, __LINE__, $hlist);
    echo 'INFO: added hlist with id=', $hlist_id, PHP_EOL;

    $GLOBALS['hlists'][$hlist_id] = $result->list->listinfo->id;

    return $result->list->listinfo;
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
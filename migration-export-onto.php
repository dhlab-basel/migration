<?php
/**
 * Created by PhpStorm.
 * User: rosenth
 * Date: 25.05.18
 * Time: 12:12
 */

$GLOBALS['username'] = 'root';
$GLOBALS['password'] = 'SieuPfa15';

$outfile = 'text.xml';

function get_projects() {
    $cid = curl_init('http://data.dasch.swiss/api/projects');
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed for property "'.$restype.'"!'.PHP_EOL);
    }
    curl_close($cid);
    $retdata = json_decode($jsonstr);
    if ($retdata->status != 0) {
        print_r(retdata);
        die('curl_exec failed for property "'.$restype.'"!'.PHP_EOL);
    }
    return $retdata->properties;
}
//=============================================================================

function get_project($shortname) {
    $cid = curl_init('http://data.dasch.swiss/api/projects/' . $shortname . '?lang=all');
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed for property get_project() !' . PHP_EOL);
    }
    curl_close($cid);
    $retdata = json_decode($jsonstr);
    if ($retdata->status != 0) {
        print_r(retdata);
        die('curl_exec failed for property get_project() !' . PHP_EOL);
    }
    return $retdata->project_info;
}
//=============================================================================

function get_vocabularies_of_project($project_id) {
    $cid = curl_init('http://data.dasch.swiss/api/vocabularies/' . $project_id);
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed in get_vocabularies_of_project() !'.PHP_EOL);
    }
    curl_close($cid);
    $data = json_decode($jsonstr);
    if ($data->status != 0) {
        print_r($data);
        die('curl_exec failed in get_vocabularies_of_project() !'.PHP_EOL);
    }
    $retdata = array();
    foreach ($data->vocabularies as $vocabulary) {
        if ($vocabulary->project_id == $project_id) {
            $retdata[] = $vocabulary;
        }
    }

    return $retdata;
}
//=============================================================================

function get_resourcetypes_of_vocabulary($vocabulary_id) {
    $cid = curl_init('http://data.dasch.swiss/api/resourcetypes?vocabulary=' . $vocabulary_id);
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed in get_resourcetypes_of_vocabulary() !'.PHP_EOL);
    }
    curl_close($cid);
    $data = json_decode($jsonstr);

    return $data->resourcetypes;
}
//=============================================================================

function get_selections_of_vocabulary($vocabulary_id) {
    $cid = curl_init('http://data.dasch.swiss/api/selections?vocabulary=' . $vocabulary_id . '&lang=all');
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed in get_selections_of_vocabulary() !'.PHP_EOL);
    }
    curl_close($cid);
    $data = json_decode($jsonstr);

    return $data->selections;
}
//=============================================================================

function get_selection_by_id($selection_id) {
    $cid = curl_init('http://data.dasch.swiss/api/selections/' . $selection_id . '?lang=all');
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed in get_selections_by_id() !'.PHP_EOL);
    }
    curl_close($cid);

    $data = json_decode($jsonstr);

    return $data->selection;
}
//=============================================================================

function get_hlists_of_vocabulary($vocabulary_id) {
    $cid = curl_init('http://data.dasch.swiss/api/hlists?vocabulary=' . $vocabulary_id . '&lang=all');
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed in get_hlists_of_vocabulary() !'.PHP_EOL);
    }
    curl_close($cid);
    $data = json_decode($jsonstr);

    return $data->hlists;
}
//=============================================================================

function get_hlist_by_id($selection_id) {
    $cid = curl_init('http://data.dasch.swiss/api/hlists/' . $selection_id . '?lang=all');
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed in get_hlists_by_id() !'.PHP_EOL);
    }
    curl_close($cid);

    $data = json_decode($jsonstr);

    return $data->hlist;
}
//=============================================================================

function get_resourcetype($restype_id) {
    $cid = curl_init('http://data.dasch.swiss/api/resourcetypes/' . $restype_id . '?lang=all');
    curl_setopt($cid, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cid, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($cid, CURLOPT_KEYPASSWD, $GLOBALS['username'].':'.$GLOBALS['password']);
    if (($jsonstr = curl_exec($cid)) === false) {
        die('curl_exec failed in get_resourcetypes_of_vocabulary() !'.PHP_EOL);
    }
    curl_close($cid);
    $data = json_decode($jsonstr);

    return $data->restype_info;
}
//=============================================================================


function write_comment(XMLWriter $xml, $string) {
    $xml->startComment();
    $xml->setIndent(FALSE);
    $xml->text(PHP_EOL);
    if (is_array($string)) {
        foreach ($string as $str) {
            $xml->text($str . PHP_EOL);
        }
    }
    else {
        $xml->text($string . PHP_EOL);
    }
    $xml->setIndent(TRUE);
    $xml->endComment();
}
//=============================================================================


$project_name = NULL;
$outfile = NULL;

function print_usage() {
    echo 'usage: ', $_SERVER['argv'][0], ' -project project-name xml-dump-file', PHP_EOL, PHP_EOL;
    echo '  -project project-name: dump data from given project.', PHP_EOL;
    echo PHP_EOL;
}
//=============================================================================


function write_hlist_level(&$xml, $nodelist) {
    foreach ($nodelist as $node) {
        $xml->startElement('node');
        $xml->writeAttribute('id', $node->id);
        $xml->writeAttribute('name', $node->name);
        if (isset($node->label) and is_array($node->label)) {
            foreach ((array) $node->label as $label) {
                $xml->startElement('label');
                $xml->writeAttribute('lang', $label->shortname);
                $xml->text($label->label);
                $xml->endElement(); // label
            }
        }
        if (isset($node->children) and is_array($node->children)) {
            $xml->startElement('subnodes');
            write_hlist_level($xml, $node->children);
            $xml->endElement(); // subnodes
        }
        $xml->endElement(); // node
    }

}
//=============================================================================


for ($i = 1; $i < $_SERVER['argc']; $i++) {
    switch ($_SERVER['argv'][$i]) {
       case '-project': {
            $i++;
            $project_name = $_SERVER['argv'][$i];
            break;
        }
        case '-list': {
            break;
        }
        default: {
            $outfile = $_SERVER['argv'][$i];
        }
    }
}

if (is_null($project_name) || is_null($outfile)) {
    print_usage();
    die();
}


$xml = new XMLWriter();
$xml->openURI($outfile);
$xml->setIndent(TRUE);
$xml->setIndentString('   ');
$xml->startDocument('1.0', 'UTF-8');

$xml->startElement('salsah');
$xml->writeAttribute('version', '0.9');

$xml->startComment();
$xml->text(PHP_EOL);
$xml->text('Migration data from SALSAH' . PHP_EOL);
$xml->text('Date: ' . date('D d. F Y / H:i:s') . PHP_EOL);
$xml->text('Hostname: ' . gethostname() . PHP_EOL);
$xml->endComment();

//
// Getting project info!
//
$project = get_project($project_name);
$xml->startElement('project');
$xml->writeAttribute('id', $project->id);
$xml->writeAttribute('shortname', $project->shortname);
$xml->writeElement('longname', $project->longname);

//
// write project description(s) [in all languages]
//
if (isset($project->description)) {
    if (is_array($project->description)) {
        foreach ($project->description as $descobj) {
            $xml->startElement('description');
            $xml->writeAttribute('lang', $descobj->shortname);
            $xml->text($descobj->description);
            $xml->endElement(); // description
        }
    }
    else {
        $xml->writeElement('description', $project->description);
    }
}
if (isset($project->url) and !empty($project->url)) {
    $xml->writeElement('url', $project->url);
}
$xml->writeElement('language', $project->lang);
$xml->writeElement('basepath', $project->basepath);
if (isset($project->logo) and !empty($project->logo)) {
    $xml->writeElement('logo', bin2hex($project->logo));
    $xml->writeElement('logo_mimetype', $project->logo_mimetype);
}
if (isset($project->keywords) and !empty($project->keywords)) {
    $keywords = explode(',', $project->keywords);
    foreach ($keywords as $keyword) {
        if (!empty(trim($keyword))) $xml->writeElement('keyword', trim($keyword));
    }
}

$vocabularies = get_vocabularies_of_project($project->id);
foreach ($vocabularies as $vocabulary) {
    $xml->startElement('vocabulary');
    $xml->writeAttribute('id', $vocabulary->id);
    $xml->writeAttribute('name', $vocabulary->shortname);
    $xml->writeElement('longname', $vocabulary->longname);
    if (isset($vocabulary->description) and !empty($vocabulary->description)) {
        $xml->writeElement('description', $vocabulary->description);
    }
    if (isset($vocabulary->uri) and !empty($vocabulary->uri)) {
        $xml->writeElement('uri', $vocabulary->uri);
    }

    //
    // get all selections (flat lists)
    //
    $selections = get_selections_of_vocabulary($vocabulary->id);
    foreach ($selections as $selection) {
        $xml->startElement('selection');
        $xml->writeAttribute('id', $selection->id);
        $xml->writeAttribute('name', $selection->name);
        if (isset($selection->label) and is_array($selection->label)) {
            foreach ($selection->label as $label) {
                $xml->startElement('label');
                $xml->writeAttribute('lang', $label->shortname);
                $xml->text($label->label);
                $xml->endElement(); // label
            }
        }
        if (isset($selection->description) and is_array($selection->description)) {
            foreach ($selection->description as $description) {
                $xml->startElement('description');
                $xml->writeAttribute('lang', $description->shortname);
                $xml->text($description->description);
                $xml->endElement(); // description
            }
        }
        $xml->startElement('nodes');
        $selection_nodes = get_selection_by_id($selection->id);
        foreach ($selection_nodes as $selection_node) {

            $xml->startElement('node');
            $xml->writeAttribute('id', $selection_node->id);
            $xml->writeAttribute('name', $selection_node->name);
            if (isset($selection_node->label) and is_object($selection_node->label)) {
                foreach ((array) $selection_node->label as $lang => $value) {
                    $xml->startElement('label');
                    $xml->writeAttribute('lang', $lang);
                    $xml->text($value);
                    $xml->endElement(); // label
                }
            }
            $xml->endElement(); // node
        }
        $xml->endElement(); // nodes
        $xml->endElement(); // selection
    }

    //
    // get all hierarchies
    //
    $hlists = get_hlists_of_vocabulary($vocabulary->id);
    foreach ($hlists as $hlist) {
        $xml->startElement('hlist');
        $xml->writeAttribute('id', $hlist->id);
        $xml->writeAttribute('name', $hlist->name);
        if (isset($hlist->label) and is_array($hlist->label)) {
            foreach ($hlist->label as $label) {
                $xml->startElement('label');
                $xml->writeAttribute('lang', $label->shortname);
                $xml->text($label->label);
                $xml->endElement(); // label
            }
        }
        if (isset($hlist->description) and is_array($hlist->description)) {
            foreach ($hlist->description as $description) {
                $xml->startElement('description');
                $xml->writeAttribute('lang', $description->shortname);
                $xml->text($description->description);
                $xml->endElement(); // description
            }
        }
        $hlist_nodes = get_hlist_by_id($hlist->id);
        write_hlist_level($xml, $hlist_nodes);
        print_r($hlist_nodes);
        $xml->endElement(); // hlist
    }


    //
    // get all restypes
    //
    $restypes = get_resourcetypes_of_vocabulary($vocabulary->id);

    //
    // first we check if there are properties used by more than one resource
    //
    $prop_in_res = array();
    foreach ($restypes as $restype) {
        $restype_info = get_resourcetype($restype->id);
        foreach ($restype_info->properties as $property) {
            if (array_key_exists($property->vocabulary . ':' . $property->name, $prop_in_res)) {
                array_push($prop_in_res[$property->vocabulary . ':' . $property->name], $restype_info->name);
            }
            else {
                $prop_in_res[$property->vocabulary . ':' . $property->name] = array($restype_info->name);
            }
        }
    }


    foreach ($restypes as $restype) {
        $restype_info = get_resourcetype($restype->id);
        $xml->startElement('restype');

        list($salsah_voc, $restype_name) = explode(':', $restype_info->name);
        $xml->writeAttribute('name', $restype_name);
        $xml->writeAttribute('type', $restype_info->class);
        $xml->writeAttribute('id', $restype->id);
        if (isset($restype_info->label) and is_array($restype_info->label)) {
            foreach ($restype_info->label as $label) {
                $xml->startElement('label');
                $xml->writeAttribute('lang', $label->shortname);
                $xml->text($label->label);
                $xml->endElement(); // label
            }
        }
        if (isset($restype_info->description) and is_array($restype_info->description)) {
            foreach ($restype_info->description as $description) {
                $xml->startElement('description');
                $xml->writeAttribute('lang', $description->shortname);
                $xml->text($description->description);
                $xml->endElement(); // label
            }
        }
        $xml->writeElement('iconsrc', $restype_info->iconsrc);

        foreach ($restype_info->properties as $property) {
            $xml->startElement('property');
            $xml->writeAttribute('id', $property->id);
            $xml->writeAttribute('vocabulary', $property->vocabulary);
            $xml->writeAttribute('name', $property->name);
            if (isset($property->label) and is_array($property->label)) {
                foreach ($property->label as $label) {
                    $xml->startElement('label');
                    $xml->writeAttribute('lang', $label->shortname);
                    $xml->text($label->label);
                    $xml->endElement(); // label
                }
            }
            if (isset($property->description) and is_array($property->description)) {
                foreach ($property->description as $description) {
                    $xml->startElement('description');
                    $xml->writeAttribute('lang', $description->shortname);
                    $xml->text($description->description);
                    $xml->endElement(); // label
                }
            }

            foreach ($prop_in_res[$property->vocabulary . ':' . $property->name] as $p => $r) {
                $xml->writeElement('used_by_res', $r);
            }
            $xml->writeElement('valtype', $property->vt_php_constant);
            $xml->writeElement('occurrence', $property->occurrence);
            if ($property->vt_php_constant == 'VALTYPE_RESPTR') {
                $attrs = explode(';', $property->attributes);
                foreach ($attrs as $attr) {
                    $tmp = explode('=', $attr);
                    if ($tmp[0] == 'restypeid') {
                        $tmp_restype = get_resourcetype($tmp[1]);
                        $xml->writeElement('resptr', $tmp_restype->name);
                    }
                }

            }
            $xml->writeElement('attributes', $property->attributes);
            $xml->writeElement('gui_element', $property->gui_name);
            $xml->writeElement('gui_attributes', $property->gui_attributes);
            $xml->endElement(); // property
        }

        $xml->endElement(); // restype

    }

    $xml->endElement(); // vocabulary
}

$xml->endElement(); // project

$xml->endElement(); // salsah
$xml->endDocument();

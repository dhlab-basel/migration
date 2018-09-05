<?php
/**
 * Created by PhpStorm.
 * User: rosenth
 * Date: 05.09.18
 * Time: 16:57
 */

$GLOBALS['username'] = 'root';
$GLOBALS['password'] = 'SieuPfa15';



function get_all_res_ids($project, $show_nrows = -1, $start_at = 0) {

    $query = array(
        'searchtype' => 'extended',
        'project' => $project
    );

    if ($show_nrows > 0) {
        $query['show_nrows'] = $show_nrows;
    }
    if ($start_at > 0) {
        $query['start_at'] = $start_at;
    }
    $cid = curl_init('http://data.dasch.swiss/api/search' . http_build_query($query));
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
        die('curl_exec failed for project  "'. $project .'"!'.PHP_EOL);
    }

    $res_ids = array_map(function($ele) {
        return $ele['obj_id'];
    }, $retdata['subjects']);

}
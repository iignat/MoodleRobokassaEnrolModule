<?php
define('NO_DEBUG_DISPLAY', true);

require("../../config.php");
require_once("lib.php");
require_once("config.php");

$rec=$DB->get_record('config_plugins',array('plugin'=>'enrol_robokassa','name'=>'mrh_pass1'));
$mrh_pass1=$rec->value;


$rec=$DB->get_record('config_plugins',array('plugin'=>'enrol_robokassa','name'=>'mrh_pass2'));
$mrh_pass2=$rec->value;


$outsum=required_param('OutSum',PARAM_TEXT);
$invid=required_param('InvId',PARAM_TEXT);
$crc=required_param('SignatureValue',PARAM_TEXT);
$invdesc=optional_param('InvDesc','',PARAM_TEXT);

$userid=required_param('shp_userid',PARAM_TEXT);
$mode=optional_param('shp_mode','none',PARAM_TEXT);

if($mode<>'rec'){
    $courseid=required_param('shp_courseid',PARAM_TEXT);
    $instanceid=required_param('shp_instanceid',PARAM_TEXT);


    if (! $plugin_instance = $DB->get_record("enrol", array("id"=>$instanceid, "status"=>0))) {
	notice("Not a valid instance id", $data);
	die;
    } 


    $crc=strtoupper($crc);

    $mycrc=strtoupper(md5("$outsum:$invid:$mrh_pass1:shp_courseid=$courseid:shp_instanceid=$instanceid:shp_userid=$userid"));

    if ($mycrc!=$crc) {
	notice('bad sign\n courseid='.$courseid.'  userid='.$userid);
    
	exit();
    }

    if (! $plugin_instance = $DB->get_record("enrol", array("id"=>$instanceid, "status"=>0))) {
	notice("Not a valid instance id", $data);
	die;
    } 


    $courseid=(int)$courseid;
    $userid=(int)$userid;

    redirect($CFG->wwwroot.'/course/view.php?id='.$courseid,'Оплата доступа к курсу выполнена',5);
} else {

    $currency=required_param('shp_curr',PARAM_TEXT);
    $planid=required_param('shp_planid',PARAM_TEXT);
    $desc=required_param('shp_plandesc',PARAM_TEXT);

    $crc=strtoupper($crc);
    $hash_str = "$outsum:$invid:$mrh_pass1:shp_curr=$currency:shp_mode=rec:shp_plandesc=$desc:shp_planid=$planid:shp_userid=$USER->id";
    $my_crc=strtoupper(md5($hash_str));

    if ($my_crc != $crc) {
	
	notice(" Что-то пошло не так! Мы уже работаем над тем, что бы все исправить!<br>$crc<br>$my_crc<br>$hash_str");

	die();
    }

    redirect($CFG->wwwroot,'Оплата подписки выполнена',5);

}
<?php
define('NO_DEBUG_DISPLAY', true);

require("../../config.php");
require_once("lib.php");
require_once("config.php");

openlog("MoodleResult", LOG_PID, LOG_USER);

$rec=$DB->get_record('config_plugins',array('plugin'=>'enrol_robokassa','name'=>'mrh_pass2'));
$mrh_pass2 = $rec->value;   // merchant pass2 here



// HTTP parameters:

$outsum=required_param('OutSum',PARAM_TEXT);
$invid=required_param('InvId',PARAM_TEXT);
$crc=strtoupper(required_param('SignatureValue',PARAM_TEXT));
$invdesc=optional_param('InvDesc','',PARAM_TEXT);

$mode=optional_param('shp_mode','enrol',PARAM_TEXT);
$userid=required_param('shp_userid',PARAM_TEXT);

if($mode<>'rec' && $mode<>'rec1'){
    $courseid=required_param('shp_courseid',PARAM_TEXT);
    $instanceid=required_param('shp_instanceid',PARAM_TEXT);

    $str1="$outsum:$invid:$mrh_pass2:shp_courseid=$courseid:shp_instanceid=$instanceid:shp_userid=$userid";
    $my_crc = strtoupper(md5("$outsum:$invid:$mrh_pass2:shp_courseid=$courseid:shp_instanceid=$instanceid:shp_userid=$userid"));

    if ($my_crc != $crc)
    {
	notice(" bad signature\n");   
    }

    echo "OK$invid\n";

    debugging("OK$invid\n");

    if (! $plugin_instance = $DB->get_record("enrol", array("id"=>$instanceid, "status"=>0))) {
	notice("Not a valid instance id", $data);
	die;
    } 


    $courseid=(int)$courseid;
    $userid=(int)$userid;

    $course=$DB->get_record('course',array('id'=>$courseid));

    $plugin = enrol_get_plugin('robokassa');

    $data=new \stdClass();

    $data->item_name=$course->shortname;
    $data->courseid=$courseid;
    $data->userid=$userid;
    $data->instanceid=$instanceid;
    $data->outsum=(float)$outsum;
    $data->invid=(int)$invid;
    $data->invdesc=$invdesc;
    $data->payment_status='completed';
    $data->timeupdated=time();


    $data->id=$DB->insert_record('enrol_robokassa', $data);


    $event=\enrol_robokassa\event\payment_success::create_from_paymentrecord($data);
    $event->trigger();


    if ($plugin_instance->enrolperiod) {
	$timestart = time();
	$timeend   = $timestart + $plugin_instance->enrolperiod;
    } else {
	$timestart = 0;
	$timeend   = 0;
    }


    // Enrol user
    $plugin->enrol_user($plugin_instance, $userid, $plugin_instance->roleid, $timestart, $timeend);

} elseif($mode=='rec') {

    $currency=required_param('shp_curr',PARAM_TEXT);
    $planid=required_param('shp_planid',PARAM_TEXT);
    $plandesc=required_param('shp_plandesc',PARAM_TEXT);

    $hash_str = "$outsum:$invid:$mrh_pass2:shp_curr=$currency:shp_mode=rec:shp_plandesc=$plandesc:shp_planid=$planid:shp_userid=$userid";


    $my_crc = strtoupper(md5($hash_str));


    if ($my_crc != $crc)
    {
	notice(" bad signature\n");
    }

    echo "OK$invid\n";


    $data=new \stdClass();

    $data->userid=(int)$userid;
    $data->invid=(int)$invid;
    $data->planid=(int)$planid;
    $data->outsum=(float)$outsum;
    $data->outsumcurr=$currency;
    $data->plandesc=$plandesc;
    $data->status="active";


    $data->id=$DB->insert_record('subscription_robokassa', $data);

    $context = context_system::instance();
    $role = $DB->get_record('role', array('shortname' => 'sozdaniekursov'));
    role_assign($role->id, $userid, $context->id);

}else{

    $userid=required_param('shp_userid',PARAM_TEXT);

    $hash_str = "$outsum:$invid:$mrh_pass2:shp_mode=rec1:shp_userid=$userid";

    syslog(LOG_INFO,$hash_str);

    $my_crc = strtoupper(md5($hash_str));

    syslog(LOG_INFO,$my_crc);
    syslog(LOG_INFO,$crc);

    if ($my_crc != $crc)
    {
	notice(" bad signature\n");
    }else{
    }

    echo "OK$invid\n";

    $rec=$DB->get_record('subscription_robokassa',array('userid'=>$userid,'lastpayinvid'=>$invid));
    
    $rec->lastpaydate=date("Y-m-d");
    $rec->status = "active";

    $DB->update_record('subscription_robokassa',$rec);

}

closelog();

?>
<?php
require("../../config.php");

require_once("lib.php");
require_once("config.php");

$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_url('/enrol/robokassa/subscription.php');


require_login();

$out_summ = required_param('out_summ',-1,PARAM_TEXT);
$currency = required_param('currency',-1,PARAM_TEXT);
$desc =	 optional_param('desc',-1,PARAM_TEXT);

$rec=$DB->get_record('config_plugins',array('name'=>'mrh_login','plugin'=>'enrol_robokassa'));
$mrh_login=$rec->value;

$rec=$DB->get_record('config_plugins',array('name'=>'mrh_pass1','plugin'=>'enrol_robokassa'));
$mrh_pass1=$rec->value;

$rec=$DB->get_record('config_plugins',array('plugin'=>'enrol_robokassa','name'=>'is_test'));
$is_test=$rec->value;

$data = new \stdClass();
$data->time = (int)time();
$data->userid = (int)$USER->id;

$inv_id = $DB->insert_record('invid_robokassa', $data);

$crc = md5("$mrh_login:$out_summ:$inv_id:$currency:$mrh_pass1:shp_curr=$currency:shp_mode=rec:shp_plandesc=$desc:shp_planid=$planid:shp_userid=$USER->id");

$encoding = "utf-8";

//$redirect_url = "https://auth.robokassa.kz/Merchant/Index.aspx?".
  $redirect_url = $endpoint_url.
		"MerchantLogin=$mrh_login".
		"&OutSum=$out_summ".
		"&InvId=$inv_id".
		"&OutSumCurrency=$currency".
		"&Description=$desc".
		"&SignatureValue=$crc".
		"&IsTest=$is_test".
		"&shp_userid=$USER->id".
		"&shp_mode=rec".
		"&shp_planid=$planid".
		"&shp_plandesc=$desc".
		"&shp_curr=$currency".
		"&Recurring=true".
		"&Encoding=$encoding";

redirect($redirect_url);

?>
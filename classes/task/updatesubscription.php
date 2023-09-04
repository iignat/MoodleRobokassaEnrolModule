<?php

namespace enrol_robokassa\task;

defined('MOODLE_INTERNAL') || die();



class updatesubscription extends \core\task\scheduled_task {

    public function get_name() {
	return get_string('updatesubscription','enrol_robokassa');
    } 

    
    public function execute() {
	global $USER;
	global $DB;
	openlog("MoodleUpdSubscr_cron", LOG_PID, LOG_USER);

	$rec=$DB->get_record('config_plugins',array('name'=>'mrh_login','plugin'=>'enrol_robokassa'));
	$mrh_login=$rec->value;


	$rec=$DB->get_record('config_plugins',array('name'=>'mrh_pass1','plugin'=>'enrol_robokassa'));
	$mrh_pass1=$rec->value;

	$rec=$DB->get_record('config_plugins',array('plugin'=>'enrol_robokassa','name'=>'is_test'));
	$is_test=$rec->value;


	//$recs=$DB->get_records_sql('SELECT * FROM `cocoon_subscription_robokassa` WHERE day(initpaydate)=day(now()) OR  DATEDIFF(now(),lastpaydate)>= 30;');
	$recs=$DB->get_records_sql("SELECT * FROM cocoon_subscription_robokassa WHERE (status LIKE 'repeat' OR status LIKE 'active') AND DATEDIFF(now(),lastpaydate)>=31;");

	$encoding = "utf-8";

	$data = new \stdClass();

	$count = 0;

	foreach($recs as $rec){
    
	    $count++;

	    $out_summ = $rec->outsum;

	    $data->time  =(int)time(); 
	    $data->userid = (int)($USER->id);
	    $inv_id = $DB->insert_record('invid_robokassa', $data);

	    $currency = $rec->outsumcurr;
	    $desc = $rec->plandesc;
	    $previnv_id = (int)$rec->invid;

	    $hash_str = "$mrh_login:$out_summ:$inv_id:$currency:$mrh_pass1:shp_mode=rec1:shp_userid=$rec->userid";    
	    $crc = md5($hash_str);    
	    $request_url = "https://auth.robokassa.kz/Merchant/Recurring?".
		"MerchantLogin=$mrh_login".
		"&OutSum=$out_summ".
		"&InvId=$inv_id".
		"&OutSumCurrency=$currency".
		"&Description=$desc".
		"&SignatureValue=$crc".
		"&PreviousInvoiceID=$previnv_id".
		"&shp_mode=rec1".
		"&shp_userid=$rec->userid".
		"&Encoding=$encoding";

	    syslog(LOG_INFO,$hash_str);
	    syslog(LOG_INFO,$request_url);

	    $rec->lastpayinvid = (int)$inv_id;
	    $ans = "OK. This is the fake ansewer!";

	    $ans = file_get_contents($request_url);
    
	    syslog(LOG_INFO,"userid = $rec->userid, invid = $previnv_id, result = $ans");

	    $pos = strpos(strtoupper($ans), "ERROR");

	    if($pos===false){
		$rec->status = "pending";
	    }else {
		$rec->status="error";
	    }

	    $rec->comment = $ans;
	    $DB->update_record('subscription_robokassa',$rec);    
	    sleep(1);
	}

	syslog(LOG_INFO, "Processed $count record(s).");
	closelog();
    }
}


?>
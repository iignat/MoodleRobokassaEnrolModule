<?php
    require("../../config.php");

    require_once("lib.php");
    require_once("config.php");

    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
    $PAGE->set_url('/enrol/robokassa/enrol.php');

    
    $course_id = required_param('course_id',PARAM_INT);
    $user_id = required_param('user_id',PARAM_INT);
    
    $instance = $DB->get_record('enrol',array('courseid'=>$course_id,'enrol'=>'robokassa'));
    if(empty($instance))exit();
    
    $out_summ = $instance->cost;
    $currency = $instance->currency;


	if ($instcfg = $DB->get_record('enrol_robokassa_instcfg', ['instanceid' => $instance->id])) {
		if (has_capability('enrol/robokassa:payparamcfg', $context, $instcfg->userid)) { 
			$mrh_login=$instcfg->mrh_login;
			$mrh_pass1=$instcfg->mrh_pass1;
			$is_test=$instcfg->is_test;
		} else {
			$rec=$DB->get_record('config_plugins',array('name'=>'mrh_login','plugin'=>'enrol_robokassa'));
			$mrh_login=$rec->value;	
	
			$rec=$DB->get_record('config_plugins',array('name'=>'mrh_pass1','plugin'=>'enrol_robokassa'));
			$mrh_pass1=$rec->value;

			$rec=$DB->get_record('config_plugins',array('plugin'=>'enrol_robokassa','name'=>'is_test'));
			$is_test=$rec->value;
		}
	} else {
	
		$rec=$DB->get_record('config_plugins',array('name'=>'mrh_login','plugin'=>'enrol_robokassa'));
		$mrh_login=$rec->value;	
	
		$rec=$DB->get_record('config_plugins',array('name'=>'mrh_pass1','plugin'=>'enrol_robokassa'));
		$mrh_pass1=$rec->value;

		$rec=$DB->get_record('config_plugins',array('plugin'=>'enrol_robokassa','name'=>'is_test'));
		$is_test=$rec->value;

	}
    
    
	$data = new \stdClass();
	$data->time = (int)time();
	$data->userid = (int)$USER->id;
	$inv_id = $DB->insert_record('invid_robokassa', $data);

	$inv_desc = $course->fullname;
	$strforhash = "$mrh_login:$out_summ:$inv_id:$currency:$mrh_pass1:shp_courseid=$courseid:shp_instanceid=$instanceid:shp_userid=$USER->id";
	
	$crc = md5($strforhash);
	
	//$Location ="https://auth.robokassa.kz/Merchant/Index.aspx?".
	  $Location =$endpoint_url.
    		    "MerchantLogin=$mrh_login".
		    "&OutSum=$out_summ&InvoiceID=$inv_id".
		    "&OutSumCurrency=$currency".
		    "&SignatureValue=$crc".
		    "&IsTest=$is_test".
		    "&shp_courseid=$courseid".
		    "&shp_instanceid=$instanceid".
		    "&shp_userid=$USER->id";
?>
<html>
<head>
<meta http-equiv="refresh" content="0;URL=<?php echo $Location; ?>" />
</head>
<body>
<p>Переход в банк на страницу оплаты. <a href="<?php echo $Location; ?>">Кликните здесь что бы продолжить.</a></p>
</body>
</html>

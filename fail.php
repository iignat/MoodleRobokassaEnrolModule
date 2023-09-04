<?php

require("../../config.php");

$inv_id=optional_param('InvId','',PARAM_TEXT);

notice("Вы отказались от оплаты. Заказ# $inv_id\n You have refused payment. Order# $inv_id\n",new moodle_url('/'));


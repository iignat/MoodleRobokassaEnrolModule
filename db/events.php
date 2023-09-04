<?php
$observers=array(
    
    array(
        'eventname'   => 'enrol_robokassa\event\payment_success',
        'callback'    => 'enrol_robokassa_observer::payment_success',
        'includefile'=>'/enrol/robokassa/classes/observer.php'
    )
);
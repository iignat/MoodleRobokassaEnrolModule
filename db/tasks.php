<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'enrol_robokassa\task\updatesubscription',
        'blocking' => 0,
        'minute' => '10',
        'hour' => '22',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
];

?>
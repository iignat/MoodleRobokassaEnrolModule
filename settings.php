<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- settings ------------------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_robokassa_settings', '', get_string('pluginname_desc', 'enrol_robokassa')));

    $settings->add(new admin_setting_configtext('enrol_robokassa/mrh_login', 'Идентификатор магазина', '', ''));
    $settings->add(new admin_setting_configtext('enrol_robokassa/mrh_pass1', 'Пароль 1', '', ''));
    $settings->add(new admin_setting_configtext('enrol_robokassa/mrh_pass2', 'Пароль 2', '', ''));
    $settings->add(new admin_setting_configcheckbox_with_advanced('enrol_robokassa/is_test', 'Включить тестовый режим', 'Для отладки платежей', 1));
    
    $settings->add(new admin_setting_configtext('enrol_robokassa/robokassabusiness', get_string('businessemail', 'enrol_robokassa'), get_string('businessemail_desc', 'enrol_robokassa'), '', PARAM_EMAIL));

    $settings->add(new admin_setting_configcheckbox('enrol_robokassa/mailstudents', get_string('mailstudents', 'enrol_robokassa'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_robokassa/mailteachers', get_string('mailteachers', 'enrol_robokassa'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_robokassa/mailadmins', get_string('mailadmins', 'enrol_robokassa'), '', 0));

    // Note: let's reuse the ext sync constants and strings here, internally it is very similar,
    //       it describes what should happen when users are not supposed to be enrolled any more.
    $options = array(
        ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
        ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
    );
    $settings->add(new admin_setting_configselect('enrol_robokassa/expiredaction', get_string('expiredaction', 'enrol_robokassa'), get_string('expiredaction_help', 'enrol_robokassa'), ENROL_EXT_REMOVED_SUSPENDNOROLES, $options));

    //--- enrol instance defaults ----------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_robokassa_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                     ENROL_INSTANCE_DISABLED => get_string('no'));
    $settings->add(new admin_setting_configselect('enrol_robokassa/status',
        get_string('status', 'enrol_robokassa'), get_string('status_desc', 'enrol_robokassa'), ENROL_INSTANCE_DISABLED, $options));

    $settings->add(new admin_setting_configtext('enrol_robokassa/cost', get_string('cost', 'enrol_robokassa'), '', 0, PARAM_FLOAT, 4));
	
	/*
    $robokassacurrencies = enrol_get_plugin('robokassa')->get_currencies();
    $settings->add(new admin_setting_configselect('enrol_robokassa/currency', get_string('currency', 'enrol_robokassa'), '', 'USD', $robokassacurrencies));
	*/
	
    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_robokassa/roleid',
            get_string('defaultrole', 'enrol_robokassa'), get_string('defaultrole_desc', 'enrol_robokassa'), $student->id, $options));
    }

    $settings->add(new admin_setting_configduration('enrol_robokassa/enrolperiod',
        get_string('enrolperiod', 'enrol_robokassa'), get_string('enrolperiod_desc', 'enrol_robokassa'), 0));
}

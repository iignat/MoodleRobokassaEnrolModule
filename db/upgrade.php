<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_robokassa_upgrade($oldversion) {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2018091403) {
        
        // Define table enrol_robokassa_instcfg to be created.
        $table = new xmldb_table('enrol_robokassa_instcfg');
        
        // Adding fields to table enrol_robokassa_instcfg.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('mrh_login', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('mrh_pass1', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('mrh_pass2', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('is_test', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        
        // Adding keys to table enrol_robokassa_instcfg.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        
        // Conditionally launch create table for enrol_robokassa_instcfg.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        // Robokassa savepoint reached.
        upgrade_plugin_savepoint(true, 2018091403, 'enrol', 'robokassa');
    }
    
    
    
    return true;
}

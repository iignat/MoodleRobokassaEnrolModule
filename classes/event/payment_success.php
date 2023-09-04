<?php
namespace enrol_robokassa\event;

defined('MOODLE_INTERNAL') || die();

class payment_success extends \core\event\base
{
    public static function create_from_paymentrecord(\stdClass $paymentrecord)
    {
        $event=self::create(
            array(
                'objectid'=>$paymentrecord->id,
                'relateduserid'=>$paymentrecord->userid,
                'context'=>\context_system::instance(),
                'courseid' => $paymentrecord->courseid
            )
            );
        $event->add_record_snapshot('enrol_robokassa', $paymentrecord);
        return $event;
        
    }
    
    protected function init()
    {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'enrol_robokassa';
        
    }
    
    public function get_description()
    {
        return "Payment via Robokassa to enrol to course id ".$this->objectid;
    }
    
    public static function get_name()
    {
        return 'Robokassa payment succeeded';
    }
    
    public function get_url()
    {
        return new \moodle_url("/user/index.php?id=".$this->courseid);
    }
    
}
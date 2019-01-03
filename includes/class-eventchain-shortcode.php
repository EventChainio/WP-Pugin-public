<?php

require_once EVENTCHAIN_BASE_DIR . 'includes/class-eventchain-model.php';

class Eventchain_Shortcode extends Eventchain_Template
{
    const SHORTCODE_NAME = 'eventchain';

    /**
     * Eventchain_Shortcode constructor.
     */
    public function __construct()
    {
        add_shortcode( self::SHORTCODE_NAME, array($this, 'create_eventchain_shortcode') );
    }

    /**
     * Create Shortcode eventchain
     * Use the shortcode: [eventchain id=""]
     */
    public function create_eventchain_shortcode($atts) {
        // Attributes
        $atts = shortcode_atts(
            array(
                'id' => '',
            ),
            $atts,
            self::SHORTCODE_NAME
        );
        // Attributes in var
        $id = (int)$atts['id'];
        $details = Eventchain_Model::event_by_id($id);

        $vars = compact('id', 'details');

        return $this->get_template('eventchain_event_details.php', $vars);

    }

}
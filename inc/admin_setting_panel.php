<?php
/**
 * 2AM Awesome loginbar Settings Controls
 *
 * @version 1.0
 *
 */
if ( !class_exists('MAGE_Events_Setting_Controls' ) ):
class MAGE_Events_Setting_Controls {

    private $settings_api;

    function __construct() {
        $this->settings_api = new MAGE_Setting_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        //add_options_page( 'Event Settings', 'Event Settings', 'delete_posts', 'mep_event_settings_page', array($this, 'plugin_page') );

         add_submenu_page('edit.php?post_type=wbtm_bus', __('Buffer Time Settings','wbtm_bus'), __('Buffer Time Settings','wbtm_bus'), 'manage_options', 'wbtm_bus_settings_page', array($this, 'plugin_page'));
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'general_setting_sec',
                'title' => __( 'Time Buffer Settings', 'bus-ticket-booking-with-seat-reservation' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'general_setting_sec' => array(

                array(
                    'name' => 'bus_buffer_time',
                    'label' => __( 'Buffer Time', 'bus-ticket-booking-with-seat-reservation' ),
                    'desc' => __( 'Please enter here car buffer time in Hour. By default is 0', 'bus-ticket-booking-with-seat-reservation' ),
                    'type' => 'text',
                    'default' => ''
                )
            )

        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;

$settings = new MAGE_Events_Setting_Controls();


function bus_get_option( $option, $section, $default = '' ) {
    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }
    
    return $default;
}
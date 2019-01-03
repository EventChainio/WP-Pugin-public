<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://eventchain.io
 * @since      1.0.0
 *
 * @package    Eventchain
 * @subpackage Eventchain/admin
 */
require_once plugin_dir_path(__FILE__) . 'class-eventchain-admin-settings.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Eventchain
 * @subpackage Eventchain/admin
 * @author     EventChain SmartTicket Services Ltd. <info@eventchain.io>
 */
class Eventchain_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private $mSettingsPage;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/eventchain-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/eventchain-admin.js', array('jquery'), $this->version, false);

    }

    public function plugin_action_links(array $links, $file)
    {
        if ($file === EVENTCHAIN_BASE_NAME)
        {
            $settings_link = '<a href="' . admin_url('admin.php?page=eventchainevents') . '">Settings</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    public function admin_menu()
    {
        $this->mSettingsPage = new Eventchain_Settings_Page();
        $this->mSettingsPage->wph_create_settings();
    }

}

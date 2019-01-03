<?php

/* EventChain Events Settings Page */
require_once EVENTCHAIN_BASE_DIR . 'includes/class-eventchain-model.php';

if (false === class_exists( 'WP_List_Table'))
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class Eventchain_Settings_Page
 */
class Eventchain_Settings_Page extends WP_List_Table
{
    /** @var string */
    const SLUG = 'eventchainevents';

    /** @var string */
    const TEXTFIELD_ID = 'eventchain_email';

    /** @var int */
    const EVENTS_PER_PAGE = 20;


    /** @var int */
    private static $sRecordCount = 0;

    /** @var array */
    private static $sResults = [];

    /** @var string */
    private $mUserEmail = '';


    /**
     * Eventchain_Settings_Page constructor.
     */
    public function __construct()
    {
        parent::__construct(array(
            'singular' => __( 'Event', self::SLUG ),
            'plural'   => __( 'Events', self::SLUG ),
            'ajax'     => false
        ));

        add_action('admin_init', array($this, 'wph_setup_sections'));
        add_action('admin_init', array($this, 'wph_setup_fields'));
    }

    /**
     * Displays the admin menu
     */
    public function wph_create_settings()
    {
        $page_title = 'EventChain - Events';
        $menu_title = 'EventChain';
        $capability = 'manage_options';
        $slug = self::SLUG;
        $callback = array($this, 'wph_settings_content');
        $icon = 'dashicons-admin-appearance';
        $position = 30;
        add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
    }

    /**
     * Displays the body of the Settings page
     */
    public function wph_settings_content()
    {

        $optionValue = get_option(self::TEXTFIELD_ID);
        $eventsList = '';
        if (true === empty($optionValue) || false === filter_var($optionValue, FILTER_VALIDATE_EMAIL))
        {
            add_settings_error(self::SLUG, self::SLUG, 'You need to save your Email Address in order to see your EventChain events.', 'error');
        } else {
            $this->mUserEmail = $optionValue;
            $eventsList = '<div class="wrap">
                <h2>Events</h2>
                '.$this->renderListTable().'
            </div>';
        }
        echo '
        <div class="wrap">
            <h1>EventChain - Events</h1>
            '. settings_errors() . '
            <form method="POST" action="options.php">
                ';
                settings_fields(self::SLUG);
                do_settings_sections(self::SLUG);
                submit_button();
                echo '
            </form>
        </div>'
        . $eventsList;
    }

    /**
     * Initializes the visible sections
     */
    public function wph_setup_sections()
    {
        add_settings_section('eventchainevents_section', 'Embed your EventChain events right on your Wordpress pages', array(), self::SLUG);
    }

    /**
     * Initializes user-supplied fields
     */
    public function wph_setup_fields()
    {
        $fields = array(
            array(
                'label'       => 'EventChain Account',
                'id'          => self::TEXTFIELD_ID,
                'type'        => 'email',
                'section'     => 'eventchainevents_section',
                'placeholder' => 'Email address'
            ),
        );
        foreach ($fields as $field)
        {
            add_settings_field($field['id'], $field['label'], array($this, 'wph_field_callback'), self::SLUG, $field['section'], $field);
            register_setting(self::SLUG, $field['id']);
        }
    }

    /**
     * Callback to execute for each rendered field
     * @param array $field
     */
    public function wph_field_callback(array $field)
    {
        $value = get_option($field['id']);
        switch ($field['type'])
        {
            default:
                printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
                       $field['id'],
                       $field['type'],
                       $field['placeholder'],
                       $value
                );
        }
        if (isset($field['desc']) && $desc = $field['desc'])
        {
            printf('<p class="description">%s </p>', $desc);
        }
    }

    /**
     * Returns the events database results table HTML
     * @return string
     */
    private function renderListTable()
    {
        $this->prepare_items();
        return $this->display();
    }



    /**
     * Renders the string displayed when there are no results
     */
    public function no_items()
    {
        _e('No Events found', self::SLUG);
    }

    /**
     * Returns the data for the principle column
     * @param $item
     * @return mixed
     */
    public function column_name($item)
    {
        return $item['title'];
    }

    /**
     * Returns the data for the other columns in the results
     * @param object $item
     * @param string $columnName
     * @return mixed
     */
    public function column_default($item, $columnName)
    {
        switch($columnName)
        {
            case 'description' :
                return substr(strip_tags($item[$columnName]),0, 150);

            case 'id' :
                $value = htmlspecialchars("[eventchain id=\"{$item[$columnName]}\"]");
                return sprintf('<input type="text" class="eventchain_shortcode" value="%s">', $value);

            default :
                return $item[$columnName];
        }
    }

    /**
     * Returns the HTML to display for the bulk actions checkbox
     * @param object $item
     * @return string
     */
    public function column_cb($item)
    {
        return '';
    }

    /**
     * Returns the set of columns to show in the results table
     * @return array
     */
    public function get_columns()
    {
        return array(
            'cb' => '',
            'title' => __( 'Title', self::SLUG ),
            'startdate' => __( 'Start Date', self::SLUG ),
            'enddate' => __( 'End Date', self::SLUG ),
            'id' => __( 'Shortcode', self::SLUG )
        );
    }

    /**
     * Returns the set of available bulk actions
     * @return array
     */
    public function get_bulk_actions()
    {
        return [];
    }

    /**
     * Returns the set of columns that can be sorted by the user
     * @return array
     */
    public function get_sortable_columns()
    {
        return array(
            'title' => array( 'title', true ),
            'startdate' => array( 'startdate', true ),
            'enddate' => array( 'enddate', true )
        );
    }

    /**
     * Populates the results items from the database query and sets up the properties of the results table
     */
    public function prepare_items()
    {
        $response = Eventchain_Model::events_by_email($this->mUserEmail);
        $this->items = self::$sResults = $response;
        $totalItems = self::$sRecordCount = count($response);
        $this->_column_headers = $this->get_column_info();
        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => self::EVENTS_PER_PAGE
        ));
    }

    /**
     * Returns the results table HTML
     * @return string
     */
    public function display()
    {
        ob_start();
        parent::display();
        return ob_get_clean();
    }



    public static function get_records($per_page = self::EVENTS_PER_PAGE, $page_number = 1)
    {
        return self::$sResults;
    }

    public static function record_count()
    {
        return self::$sRecordCount;
    }

    public static function delete_event()
    {
        return null;
    }
}
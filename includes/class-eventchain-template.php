<?php

class Eventchain_Template
{
    /**
     * * Locate template.
     *
     * Locate the called template.
     * Search Order:
     * 1. /themes/theme/templates/$template_name
     * 2. /themes/theme/$template_name
     * 3. /plugins/eventchain/templates/$template_name.
     *
     * @param $template_name
     * @param string $template_path
     * @param string $default_path
     * @return mixed
     */
    protected function locate_template( $template_name, $template_path = '', $default_path = '' ) {
        // Set variable to search in main folder of theme.
        if ( true === empty($template_path) )
        {
            $template_path = 'templates/';
        }

        // Set default plugin templates path.
        if ( true === empty($default_path) )
        {
            $default_path = EVENTCHAIN_BASE_DIR . 'templates/'; // Path to the template folder
        }

        // Search template file in theme folder.
        $template = locate_template( array(
                                         $template_path . $template_name,
                                         $template_name
                                     ) );

        // Get plugins template file.
        if ( true === empty($template) )
        {
            $template = $default_path . $template_name;
        }

        return apply_filters( 'eventchain_locate_template', $template, $template_name, $template_path, $default_path );
    }

    /**
     * Get template.
     *
     * Search for the template and include the file.
     *
     * @param $template_name
     * @param array $args
     * @param string $tempate_path
     * @param string $default_path
     */
    protected function get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
        if ( is_array( $args ) && false === empty( $args ) )
        {
            extract($args);
        }

        $template_file = $this->locate_template( $template_name, $tempate_path, $default_path );
        if ( false === file_exists( $template_file ) )
        {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
            return;
        }

        include $template_file;
    }

}
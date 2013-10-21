<?php
/**
 * Settings Class 
 * 
 * @plugin My Own Plugins Tab
 */

# Busted!
!defined( 'ABSPATH' ) AND exit(
        "<pre>Hi there! I'm just part of a plugin, 
            <h1>&iquest;what exactly are you looking for?" );

class B5F_MOPT_Settings
{
    /**
     * Our multisite condition.
     * @type boolean
     */
    public $is_multisite;
    
    
    /**
     * Plugin settings name
     * @var string
     */
    public $option_name = 'my_plugins_tab_settings';
    
    
    /**
     * Plugin settings value
     * @var array
     */
    private $option_value;
    
    
    /**
     *
     * @see plugin_setup()
     * @wp-hook plugins_loaded
     * @return  void
     */
    public function __construct()
    {
        # Available for network is not available, force load it
        if ( ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        
        
        # Internal check
        $this->is_multisite = is_multisite() 
           && is_plugin_active_for_network( plugin_basename( B5F_MOPT_FILE ) );
        
        
        # Active in some site, but not Network Active
        if( 
            is_network_admin()
            && !is_plugin_active_for_network( plugin_basename( B5F_MOPT_FILE ) ) 
            && is_plugin_active( plugin_basename( B5F_MOPT_FILE ) ) 
            )
            return;
        
        
        # Check and set data
        $this->check_posted_data();
        $this->option_value = $this->get_options();
        
        
        # Add icon to plugin
        add_action(
            'after_plugin_row_' . B5F_MOPT_FILE, 
            array( $this, 'add_config_form' ), 
            10, 3
        );
        add_action( 'admin_print_scripts-plugins.php', array( $this, 'enqueue' ) );
    }


    /**
     * Check for $_POSTed data and update settings
     * 
     * @return void
     */
    public function check_posted_data()
    {
        if( !isset( $_POST['noncename_mopt'] ) )
            return;
        
        if( wp_verify_nonce( $_POST['noncename_mopt'], plugin_basename( B5F_MOPT_FILE ) ) )
        {
            if ( isset($_POST['mopt_config-authors']) )
                $this->option_value['authors'] = stripslashes_deep( $_POST['mopt_config-authors'] );

            if ( isset($_POST['mopt_config-icon']) )
                $this->option_value['icon'] = esc_html( $_POST['mopt_config-icon'] );

            if ( isset($_POST['mopt_config-others']) )
                $this->option_value['others'] = (int) $_POST['mopt_config-others'];
            
            if ( isset($_POST['mopt_config-subsites']) )
                $this->option_value['subsites'] = (int) $_POST['mopt_config-subsites'];
            
            $this->set_options();
        }
    }
    
    
    /**
     * Style and Scripts
     */
    public function enqueue()
    {
        wp_enqueue_style(
            'mopt-style', 
            plugin_dir_url( B5F_MOPT_FILE ) . 'css/my-plugins-style.css'
        );
        
        # FONT AWESOME
        $http = is_ssl() ? 'https:' : 'http:';
        $url = "$http//netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.min.csss";
        if( $this->get_http_response_code( $url ) )
            wp_enqueue_style( 
                'font-awesome', 
                $url
            );
        else
        {
            wp_enqueue_style(
                'font-awesome',
                plugin_dir_url( B5F_MOPT_FILE ) . 'css/font-awesome.min.css'
            );
            
        }
                
        wp_register_script(
            'mopt-js',
            plugin_dir_url( B5F_MOPT_FILE ) . 'js/my-plugins-script.js',
            array(),
            '',
            TRUE
        );
        wp_enqueue_script( 'mopt-js' );
        wp_localize_script(
            'mopt-js',
            'mopt_ajax_vars', 
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                '_nonce'    => wp_create_nonce( 'mopt-nonce' ),
                'open_btn'  => __( 'Open settings' ),
                'close_btn' => __( 'Close settings' ),
            )
        );
    }

    
    /**
     * Check if a file is online
     * 
     * brute force with suppress errors
     * 
     * @param string $url
     * @return boolean
     */
    private function get_http_response_code( $url ) 
    {
        $headers = @get_headers( $url );
        if( !$headers )
            return false;
        
        return substr($headers[0], 9, 3) === '200';
    }
    
        
    /**
     * Prints the settings form
     * 
     * @param   $wm_pluginfile Object
     * @param   $wm_plugindata Object (array)
     * @param   $wm_context    Object (all, active, inactive)
     * @return  void
     * @wp-hook after_plugin_row
     */
    public function add_config_form( $wm_pluginfile, $wm_plugindata, $wm_context )
    {
        $value = $this->option_value;   
        # Prevent wrong background if these conditions are met
        $class_active = ( is_network_admin() && is_plugin_active( B5F_MOPT_FILE ) && !is_plugin_active_for_network( B5F_MOPT_FILE ) ) ? 'inactive' : 'active';
        $config_row_class = 'config_hidden';      
        require_once 'settings-html.php';
    }

    
    /**
     * Return the options, check for install and active on WP multisite
     * 
     * @todo MULTISITE????
     * 
     * @return  array $values
     */
    public function get_options() 
    {
        if ( $this->is_multisite )
            $values = get_site_option( $this->option_name );
        else
            $values = get_option( $this->option_name );
            
        // check for non defaults
        if( !isset( $values['others'] ) )
            $values['others'] = 0;
        if( !isset( $values['subsites'] ) )
            $values['subsites'] = 0;
        
        return $values;
    }

    
    /**
     * Return the options, check for install and active on WP multisite
     * 
     * @todo MULTISITE????
     * 
     * @return  array $values
     */
    private function set_options() 
    {
        if ( $this->is_multisite )
           update_site_option( $this->option_name, $this->option_value );
        else
           update_option( $this->option_name, $this->option_value );
    }

    
    /**
     * Function to escape strings
     * Use WP default, if exists
     * - I don't know why it doesn't exist, 
     * - - but may be related to that require /wp-includes above
     * 
     * @param  String
     * @return String
     */
    private function esc_attr( $text ) {

        if ( function_exists('esc_attr') )
            $text = esc_attr($text);
        else
            $text = attribute_escape($text);

        return $text;
    }


    /**
     * Prints <select><option> 
     * 
     * @param array $data
     */
    private function print_dropdown( $data )
    {
        $return = "<th scope='row'>
                    <label for='{$data['id']}'>{$data['label']}</label></th><td>";
        $return .= "<select name='{$data['id']}' id='{$data['id']}'>";
        foreach( $data['values'] as $k => $v )
        {
            $return .= sprintf(
                '<option value="%s" %s>%s</option>',
                $k,
                selected( $data['option'], $k, false ),
                $v
            );
        }
        $return .= '</select></td>';
        echo $return;
    }
    
}
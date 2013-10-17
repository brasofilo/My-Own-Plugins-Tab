<?php

# Busted!
!defined( 'ABSPATH' ) AND exit(
        "<pre>Hi there! I'm just part of a plugin, 
            <h1>&iquest;what exactly are you looking for?" );

/*
 * Settings Class 
 * 
 * @plugin My Own Plugins Tab
 */
class B5F_MOPT_Settings
{
    /**
     * Our multisite condition.
     * @type boolean
     */
    private $is_multisite;
    
    public $option_name = 'my_plugins_tab_settings';
    
    private $option_value;

    
    /**
     *
     * @see plugin_setup()
     * @wp-hook plugins_loaded
     * @return  void
     */
    public function __construct()
    {
        # AJAX ACTION, leave outside $pagenow check
        add_action( 'wp_ajax_mopt_save_config', array( $this, 'save_config' ) );

        global $pagenow;
        if( 'plugins.php' != $pagenow )
            return;
        
        if ( ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        
         $this->is_multisite = is_multisite() 
            && is_plugin_active_for_network( plugin_basename( B5F_MOPT_FILE ) );
 
        $this->get_options();
        
        add_action(
            'after_plugin_row_' . B5F_MOPT_FILE, 
            array( $this, 'add_config_form' ), 
            10, 3
        );

        add_action( 'admin_print_scripts-plugins.php', array( $this, 'enqueue' ) );
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
        wp_enqueue_style( 
            'mopt-font-awesome', 
            '//netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.min.css'
        );
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
        $value = $this->get_options();   
        # Prevent wrong background if these conditions are met
        $class_active = ( is_network_admin() && is_plugin_active( B5F_MOPT_FILE ) && !is_plugin_active_for_network( B5F_MOPT_FILE ) ) ? 'inactive' : 'active';
        $config_row_class = 'config_hidden';      
        require_once 'settings-html.php';
    }

    
    
    
    
    /**
     * Ajax save options
     */
    public function save_config() 
    {
        $nonce = $_POST['nonce'];
        if ( ! wp_verify_nonce( $nonce, 'mopt-nonce' ) )
            wp_send_json_error( array( 
                'error' => __( 'Ajax error.' ) 
            ));

        # DONNOW WHY, but this ain't working
       /* if ( ! current_user_can( 'manage_options' ) )
            wp_send_json_error( array( 
                'error' => __( 'You are not authorised to perform this operation.' ) 
            ));*/

        $this->option_value = $this->get_options();

        if ( isset($_POST['mopt_config-authors']) )
            $this->option_value['authors'] = stripslashes_deep( $_POST['mopt_config-authors'] );

        if ( isset($_POST['mopt_config-icon']) )
            $this->option_value['icon'] = esc_html( $_POST['mopt_config-icon'] );

        if ( isset($_POST['mopt_config-others']) )
            $this->option_value['others'] = (int) $_POST['mopt_config-others'];

        $this->set_options();
        wp_send_json_success( __( 'Updated' ) );
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
    
    
    
    
    
    /**
     * ALL MESSED UP, CHECK ORIGINAL
     */
    private function ROLE_GRAB_UNUSED()
    {
       global $wp_roles;

        if( !isset( $value['role'][0] ) )
            $value['role'][0] = NULL;

        foreach( $wp_roles->roles as $role => $name )
        {
            if( function_exists( 'translate_user_role' ) )
                $role_name = translate_user_role( $name['name'] );
            elseif( function_exists( 'before_last_bar' ) )
                $role_name = before_last_bar( $name['name'], 'User role' );
            else
                $role_name = strrpos( $name['name'], '|' );

            if( $value['role'][0] !== $role )
                $selected = '';
            else
                $selected = ' selected="selected"';
            echo '<option value="' . $role . '"' . $selected . '>' . $role_name . ' (' . $role . ')' . ' </option>';
        }
    }
}
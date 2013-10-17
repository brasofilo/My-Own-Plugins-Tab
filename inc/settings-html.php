<?php

# Busted!
!defined( 'ABSPATH' ) AND exit(
        "<pre>Hi there! I'm just part of a plugin, 
            <h1>&iquest;what exactly are you looking for?" );

/*
 * Settings for after_plugin_row 
 * 
 * @plugin My Own Plugins Tab
 */
?>

<tr id="mopt-tr-settings" class="<?php echo $class_active; ?>">
    <th scope="row" class="check-column">&nbsp;</th>
    <td colspan="2">
        <a class="button-secondary" href="#" id="mopt-pluginconflink" title="<?php _e( 'Settings' ); ?>"><?php _e( 'Open settings' ); ?></a> 
    </td>
</tr>

<tr id="mopt_config_tr" class="<?php echo $class_active; ?>">
    <td colspan="3">
    <div id="mopt_config_row" class="<?php echo $config_row_class; ?>">
        <table class="form-table mopt-table">

            <!-- AUTHORS TEXT FIELD -->
            <tr valign="top">
                <th scope="row">
                    <label for="mopt_config-authors"><?php _e( 'User name/surmane:'); ?></label>
                </th>
                <td>
                    <input class="large-text wide-fat" type="text" id="mopt_config-authors" name="mopt_config-authors" value="<?php if( isset( $value['authors'] ) ) echo esc_attr( $value['authors'] ); ?>" />
                    <br />
                    <small><?php _e( '(comma separated list)'); ?></small>
                </td>
            </tr>

      
            <!-- ICON TEXT FIELD -->
            <tr valign="top">
                <th scope="row">
                    <label for="mopt_config-icon"><?php _e( 'Icon'); ?></label>
                </th>
                <td>
                    <input class="large-text wide-fat mopt-icon" type="text" id="mopt_config-icon" name="mopt_config-icon" value="<?php if( isset( $value['icon'] ) ) echo stripslashes(  $value['icon'] ); ?>" />
                    <br />
                    <small><?php _e( '(This plugin uses <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/">Font Awesome</a>), simply copy the icon or its code. This field accepts HTML too.'); ?></small>
                </td>
            </tr>

      
            <!-- BOOLEAN DROPDOWN -->
            <tr valign="top"><?php 
                $config_others = array(
                    '0' => __( 'False' ),
                    '1' => __( 'True' ),
                );
                $this->print_dropdown( array(
                    'label'     => __( 'Show not mine:' ),
                    'id'        => 'mopt_config-others', 
                    'option'    => $value['others'], 
                    'values'    => $config_others 
                    )); ?>
            </tr>

        </table>
    <br />
    
    <div class="plugin-update-tr">
        <p id="mopt-message"></p>
    </div>
    
    <p id="submitbutton">
        <input id="mopt_config_submit" type="button" value="<?php _e( 'Save'); ?>" class="button-primary" />
    </p>
</div>

    </td>
</tr>
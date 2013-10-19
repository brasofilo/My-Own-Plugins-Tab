<?php
/*
 * Settings for after_plugin_row 
 * 
 * @plugin My Own Plugins Tab
 */

# Busted!
!defined( 'ABSPATH' ) AND exit(
        "<pre>Hi there! I'm just part of a plugin, 
            <h1>&iquest;what exactly are you looking for?" );

$authors_field = isset( $value['authors'] ) ? esc_attr( $value['authors'] ) : '';
$icon_field = isset( $value['icon'] ) ? esc_attr( $value['icon'] ) : '';
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

        <form method="post" name="post-mopt-form" action="">
 
            <table class="form-table mopt-table">

                <!-- AUTHORS TEXT FIELD -->
                <tr valign="top">
                    <th scope="row">
                        <label for="mopt_config-authors"><?php _e( 'User name/surmane:'); ?></label>
                    </th>
                    <td>
                        <input class="large-text wide-fat" type="text" id="mopt_config-authors" name="mopt_config-authors" value="<?php echo $authors_field; ?>" />
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
                        <input class="large-text wide-fat mopt-icon" type="text" id="mopt_config-icon" name="mopt_config-icon" value="<?php echo $icon_field; ?>" />
                        <br />
                        <small><?php _e( '(This plugin uses <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_blank">Font Awesome</a>), simply copy the icon or its code. This field accepts HTML too.'); ?></small>
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
                
                <?php if( $this->is_multisite): ?>
                <!-- BOOLEAN DROPDOWN -->
                <tr valign="top"><?php 
                    $config_subsites = array(
                        '0' => __( 'False' ),
                        '1' => __( 'True' ),
                    );
                    $this->print_dropdown( array(
                        'label'     => __( 'Show in subsites:' ),
                        'id'        => 'mopt_config-subsites', 
                        'option'    => $value['subsites'], 
                        'values'    => $config_subsites 
                        )); ?>
                </tr>
                <?php endif; ?>

            </table>
        </form>
        <br />
        <div class="plugin-update-tr">
            <p id="mopt-message"></p>
        </div>
        <p id="submitbutton">
        <?php
          wp_nonce_field( plugin_basename( B5F_MOPT_FILE ), 'noncename_mopt' );
          submit_button( 'Save settings', 'primary', 'mopt_config_submit' );  ?>
        </p>
    </div>

    </td>
    
</tr>
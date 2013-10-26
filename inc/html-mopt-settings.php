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

$authors_field = isset( $value['authors'] ) 
    ? esc_attr( $value['authors'] ) : '';

$icon_field = isset( $value['icon'] ) 
    ? esc_attr( stripslashes( $value['icon'] ) ): '';

$name_mine_field = isset( $value['mine'] ) 
    ? esc_attr( stripslashes( $value['mine'] ) ) : '';

$name_not_mine_field = isset( $value['not-mine'] ) 
    ? esc_attr( stripslashes( $value['not-mine'] ) ) : '';

if( $this->posted_data ):
?>
<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>MOPT: Settings saved!!.</strong></p></div>
<?php
endif;
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
            <tbody>
                <!-- AUTHORS TEXT FIELD -->
                <tr valign="top">
                    <?php $this->print_text_field( array(
                        'field' => 'mopt_config-authors',
                        'text' => __( 'User name/surmane:'),
                        'value' => $authors_field,
                        'desc' => __( '(comma separated list)'),
                        'class' => ''
                    )); ?>
                </tr>
                    
                <!-- ICON TEXT FIELD -->
                <tr valign="top">
                    <?php $this->print_text_field( array(
                        'field' => 'mopt_config-icon',
                        'text' => __( 'Icon'),
                        'value' => $icon_field,
                        'desc' => __( "This plugin uses <a href='http://fortawesome.github.io/Font-Awesome/cheatsheet/' target='_blank'>Font Awesome</a>, simply copy the icon or its code. This field accepts HTML too." ),
                        'class' => 'mopt-icon'
                    )); ?>
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

                    
                <!-- MINE-STR TEXT FIELD -->
                <tr valign="top">
                    <?php $this->print_text_field( array(
                        'field' => 'mopt_config-mine',
                        'text' => __( "Name for 'mine'"),
                        'value' => $name_mine_field,
                        'desc' => __( '' ),
                        'class' => ''
                    )); ?>
                </tr>
                    
                <!-- NOT-MINE-STR TEXT FIELD -->
                <tr valign="top">
                    <?php $this->print_text_field( array(
                        'field' => 'mopt_config-not-mine',
                        'text' => __( "Name for 'not mine'"),
                        'value' => $name_not_mine_field,
                        'desc' => __( '' ),
                        'class' => ''
                    )); ?>
                </tr>
            </tbody>
            
            </table>
                       <p id="mopt-submitbutton">
                       <?php
                         wp_nonce_field( plugin_basename( B5F_MOPT_FILE ), 'noncename_mopt' );
                         submit_button( 'Save settings', 'primary', 'mopt_config_submit' );  ?>
                       </p>
       </form>
    </div>
    </td>
</tr>
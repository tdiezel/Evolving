<?php
/*
Plugin name: Smart Footnote
Plugin URI:
Description: A plugin to insert and edit footnotes through tinyMCE.
Author: Thiago Diezel - Havas
Author URI: https://mtl.havas.com
Text Domain: smart-footnote
Domain Path: /languages
Version: 1.0
*/

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

define( 'SMFN_VERSION',         '1.0' );
define( 'SMFN_FILE',            __FILE__ );
define( 'SMFN_PLUGIN_BASENAME', plugin_basename( SMFN_FILE ) );
define( 'SMFN_PLUGIN_DIR',      plugin_dir_path( SMFN_FILE ) );


add_action( 'plugins_loaded', 'smfn_init', 20 );

function smfn_init() {

    include( SMFN_PLUGIN_DIR . 'inc/shortcodes.php' );
    include( SMFN_PLUGIN_DIR . 'inc/tinymce.php' );

    //Load textdomain
    function smart_footnote_load_plugin_textdomain() {
        load_plugin_textdomain( 'smart-footnote', FALSE, SMFN_PLUGIN_DIR . '/languages' );
    }
    add_action( 'plugins_loaded', 'smart_footnote_load_plugin_textdomain' );

    //Add editor Styles
    add_filter( 'mce_css', 'smfn_editor_style' );
    function smfn_editor_style( $mce_css ){
        $mce_css .= ', ' . plugins_url( 'assets/css/smfn-admin.css', __FILE__ );
        return $mce_css;
    }
    //Add styles to the page
    add_action( 'wp_enqueue_scripts', 'add_scripts_to_front' );
    function add_scripts_to_front() {
        //wp_enqueue_style( 'smfn-front-style', plugin_dir_url(SMFN_FILE) . 'assets/css/front.css');
        $media = get_option('smfn_option_3') ? get_option('smfn_option_3') : "1023";
        wp_register_style('smfn-dynamic-css', plugin_dir_url(SMFN_FILE).'assets/css/dynamic.css.php?media=' . $media);
        wp_enqueue_style( 'smfn-dynamic-css');
        //wp_enqueue_script('smfn-actions', plugin_dir_url(SMFN_FILE) . 'assets/js/smfnfunctions.js', array(), '1.0.0', true);
    }

    // Add global variable for plugin settings
    add_action ( 'wp_head', 'smfn_settings_extra_vars' );

    if ( !function_exists( 'smfn_settings_extra_vars' ) ) {
        function smfn_settings_extra_vars() { ?>
            <script type="text/javascript">
                var SMFN_object = <?php echo json_encode(
                        array(
                            'show_more' => get_option('smfn_option_1') ? __(get_option('smfn_option_1'), "smart-footnote") : "Show more",
                            'show_less' => get_option('smfn_option_2') ? __(get_option('smfn_option_2'), "smart-footnote") : "Show less",
                            'media_query' => get_option('smfn_option_3') ? get_option('smfn_option_3') : "1023",
                        )
                    );
                    ?>;
            </script><?php
        }
    }

    // Creating settings page
    add_action('admin_menu', function() {
        add_options_page( 'Smart footnote settings', 'Smart Footnote', 'manage_options', 'smart-footnote', 'smart_footnote_plugin_page' );
    });

    add_action( 'admin_init', function() {
        register_setting( 'smart-footnote-settings', 'smfn_option_1' );
        register_setting( 'smart-footnote-settings', 'smfn_option_2' );
        register_setting( 'smart-footnote-settings', 'smfn_option_3' );
    });

    function smart_footnote_plugin_page() {
        ?>
          <div class="wrap">
            <form action="options.php" method="post">
                <?php
                settings_fields( 'smart-footnote-settings' );
                do_settings_sections( 'smart-footnote-settings' );
                ?>
                <table>
                    <tr><td colspan="2"><h1>Smart footnote settings</h1><p>&nbsp;</p>
                    </td></tr>
                    <tr>
                        <td align="left">Show more trigger</td>
                        <td><input type="text" placeholder="Show more" name="smfn_option_1" value="<?php echo esc_attr( get_option('smfn_option_1') ); ?>" size="20" /></td>
                    </tr>
                    <tr>
                        <td align="left">Show less trigger</td>
                        <td><input type="text" placeholder="Show less" name="smfn_option_2" value="<?php echo esc_attr( get_option('smfn_option_2') ); ?>" size="20" /></td>
                    </tr>
                    <tr>
                        <td align="left">Media query max-width (px)</td>
                        <td><input type="text" placeholder="1023" name="smfn_option_3" value="<?php echo esc_attr( get_option('smfn_option_3') ); ?>" size="6" /></td>
                    </tr>
                    <tr>
                        <td><?php submit_button(); ?></td>
                    </tr>

                </table>
            </form>
          </div>
        <?php
       }
}

?>
<?php

add_action( 'after_setup_theme', 'mytheme_theme_setup' );

if ( ! function_exists( 'mytheme_theme_setup' ) ) {
    function mytheme_theme_setup() {
        add_action( 'init', 'mytheme_buttons' );
    }
}

//Add button 'Add footnote' to tinymce

if ( ! function_exists( 'mytheme_buttons' ) ) {
    function mytheme_buttons() {
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }
        if ( get_user_option( 'rich_editing' ) !== 'true' ) {
            return;
        }
        add_filter( 'mce_external_plugins', 'mytheme_add_buttons' );
        add_filter( 'mce_buttons', 'mytheme_register_buttons' );
    }
}

if ( ! function_exists( 'mytheme_add_buttons' ) ) {
    function mytheme_add_buttons( $plugin_array ) {
        //Include Javascript
        $plugin_array['btn_footnote'] = plugin_dir_url(SMFN_FILE) . 'assets/js/tinymce_buttons.js';
        return $plugin_array;
    }
}

if ( ! function_exists( 'mytheme_register_buttons' ) ) {
    function mytheme_register_buttons( $buttons ) {
        array_push( $buttons, 'btn_footnote' );
        return $buttons;
    }
}

// Add global variable for admin
add_action ( 'after_wp_tiny_mce', 'smfn_tinymce_extra_vars' );

if ( !function_exists( 'smfn_tinymce_extra_vars' ) ) {
    function smfn_tinymce_extra_vars() { ?>
        <script type="text/javascript">
            var tinyMCE_object = <?php echo json_encode(
                    array('plugin_dir' => plugin_dir_url(SMFN_FILE))
                );
                ?>;
        </script><?php
    }
}

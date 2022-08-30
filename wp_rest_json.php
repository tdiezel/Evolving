<?php

//Add the featured image to the Rest api response
add_action('rest_api_init', 'register_rest_images');
function register_rest_images()
{
    register_rest_field(
        array('products'),
        'fimg_url',
        array(
            'get_callback'    => 'get_rest_featured_image',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}
function get_rest_featured_image($object, $field_name, $request)
{
    if ($object['featured_media']) {
        $img = wp_get_attachment_image_src($object['featured_media'], 'st-thumb');
        return $img[0];
    }
    return false;
}

if (get_Field('json_flag', 'option')) {
    add_action('acf/save_post', 'acf_options_save_post');
}
if (get_Field('json_flag_all', 'option')) {
    add_action('save_post', 'stelpro_createJsonCatProds');
    add_action('edited_terms', 'stelpro_createJsonCatProds');
}

function acf_options_save_post($post_id)
{
    $exp = explode('_', $post_id);
    $lang = isset($exp[1]) ? $exp[1] : '';

    if ($exp[0] == 'options') :

        $request = new WP_REST_Request('GET', '/acf/v3/options/options');
        $rps = rest_do_request($request);
        $server = rest_get_server();
        $data = $server->response_to_data($rps, false);
        $response = wp_json_encode($data);

        //$response = file_get_contents_curl(get_site_url() . '/' . $lang . '/wp-json/acf/v3/options/options');

        //_debugr($response);
        //_debug('SAVING JSON - ALL OPTIONS');
        //_debug('$lang: ' . $lang);
        //_debug('file_get_contents: ' . get_site_url() . '/' . $lang . '/wp-json/acf/v3/options/options');


        //Open json file. This file needs to have permission 707
        $fp = fopen(ABSPATH . 'wp-content/themes/stelpro/json/acf_options_' . $lang . '.json', 'w+') or die("File cannot be opened.");
        fwrite($fp, $response);
        fclose($fp);
    endif;
}

function stelpro_createJsonCatProds($post_id)
{

    $request = new WP_REST_Request('GET', '/wp/v2/category');
    $request->set_query_params(['per_page' => 99, 'parent' => 0, 'exclude' => 1, 'lang' => ICL_LANGUAGE_CODE]);
    $rps = rest_do_request($request);
    $server = rest_get_server();
    $data = $server->response_to_data($rps, false);
    $response = wp_json_encode($data);

    //$response = file_get_contents_curl(get_site_url() . '/wp-json/wp/v2/category?per_page=99&parent=0&exclude=1&lang=' . ICL_LANGUAGE_CODE);

    //_debugr($response);
    //_debug('WRITING JSON ALL PARENT CATEGORIES');

    //Open json file. This file needs to have permission 707
    $fp = fopen(ABSPATH . 'wp-content/themes/stelpro/json/all_categories_' . ICL_LANGUAGE_CODE . '.json', 'w+') or die("File cannot be opened.");
    fwrite($fp, $response);
    fclose($fp);

    //   ____   _       _   _       _      ____           _                                   _              
    //   / ___| | |__   (_) | |   __| |    / ___|   __ _  | |_    ___    __ _    ___    _ __  (_)   ___   ___ 
    //  | |     | '_ \  | | | |  / _` |   | |      / _` | | __|  / _ \  / _` |  / _ \  | '__| | |  / _ \ / __|
    //  | |___  | | | | | | | | | (_| |   | |___  | (_| | | |_  |  __/ | (_| | | (_) | | |    | | |  __/ \__ \
    //   \____| |_| |_| |_| |_|  \__,_|    \____|  \__,_|  \__|  \___|  \__, |  \___/  |_|    |_|  \___| |___/
    //                                                                  |___/                                 


    //Get all parent categories
    $terms = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => true,
        'parent' => 0
    ));
    //Get all child categories and save under theme/stelpro/json/cat_child_xxx.json  (xxx = child cat ID)
    foreach ($terms as $parent_cat) {
        $request_child = new WP_REST_Request('GET', '/wp/v2/category');
        $request_child->set_query_params(['per_page' => 99, 'parent' => $parent_cat->term_id]);
        $rps_child = rest_do_request($request_child);
        $server_child = rest_get_server();
        $data_child = $server_child->response_to_data($rps_child, false);
        $response_child = wp_json_encode($data_child);

        //_debug('-- WRITING JSON CHILDREN OF CATEGORY: ' . $parent_cat->term_id);
        //_debugr($response_child);

        //Open json file. This file needs to have permission 707
        $fp_child = fopen(ABSPATH . 'wp-content/themes/stelpro/json/cat_child_' . $parent_cat->term_id . '.json', 'w+') or die("File cannot be opened.");
        fwrite($fp_child, $response_child);
        fclose($fp_child);

        //  ######                                                         
        //  #     #  #####    ####   #####   #    #   ####   #####   ####  
        //  #     #  #    #  #    #  #    #  #    #  #    #    #    #      
        //  ######   #    #  #    #  #    #  #    #  #         #     ####  
        //  #        #####   #    #  #    #  #    #  #         #         # 
        //  #        #   #   #    #  #    #  #    #  #    #    #    #    # 
        //  #        #    #   ####   #####    ####    ####     #     ####  

        $all_child = json_decode($response_child);

        foreach ($all_child as $child) {
            //_debug('---- WRITING JSON PRODUCTS FOR CHILD CATEGORY: ' . $child->id);
            $request_prod = new WP_REST_Request('GET', '/wp/v2/products');
            $request_prod->set_query_params(['per_page' => 99, 'category' => $child->id]);
            $rps_prod = rest_do_request($request_prod);
            $server_prod = rest_get_server();
            $data_prod = $server_prod->response_to_data($rps_prod, false);
            $response_prod = wp_json_encode($data_prod);
            // _debug('***********************************************************');
            // _debug('******************* ' . $child->id . '*********************');
            // _debugr($response_prod);

            //Open json file. This file needs to have permission 707
            $fp_prod = fopen(ABSPATH . 'wp-content/themes/stelpro/json/prod_child_' . $child->id . '.json', 'w+') or die("File cannot be opened.");
            fwrite($fp_prod, $response_prod);
            fclose($fp_prod);
        }
    };
}

<?php
/*
Plugin Name: Post To QR Code
Plugin URI: https://redoyit.com/
Description: Used by millions, WordCount is quite possibly the best way in the world to <strong>protect your blog from spam</strong>. WordCount Anti-spam keeps your site protected even while you sleep. To get started: activate the WordCount plugin and then go to your WordCount Settings page to set up your API key.
Version: 5.3
Requires at least: 5.8
Requires PHP: 5.6.20
Author: Md. Redoy Islam
Author URI: https://redoyit.com/wordpress-plugins/
License: GPLv2 or later
Text Domain: poasttoqrcode
Domain Path: /languages
*/
/*
function wordcount_activation_hook(){}
register_activation_hook(__FILE__, "wordcount_activation_hook");

function wordcount_deactivation_hook(){}
register_deactivation_hook(__FILE__, "wordcount_deactivation_hook");
*/

function poasttoqrcode_load_textdomain()
{
    load_plugin_textdomain('poasttoqrcode', false, dirname(__FILE__) . '/languages');
}

add_action("plugins_loaded", "poasttoqrcode_load_textdomain");

$pqrc_countries = array(
    __('None', 'poasttoqrcode'),
    __('Afganistan','poasttoqrcode'),
    __('Bangladesh','poasttoqrcode'),
    __('India','poasttoqrcode'),
    __('Maldives','poasttoqrcode'),
    __('Nepal','poasttoqrcode'),
    __('Pakistan','poasttoqrcode'),
    __('Sri Lanka','poasttoqrcode'),
); 
function  pqrc_display_qr_code($content){
    $curent_post_id = get_the_ID();
    $current_post_title = get_the_title($curent_post_id);
    $current_post_url = urlencode(get_the_permalink($curent_post_id));
    $current_post_type = get_post_type($curent_post_id);
    /*
     Post Type Check
     */
    $excluted_post_types = apply_filters('pqrc_excluded_post_type', array());
    if(in_array($current_post_type, $excluted_post_types)){
        return $content;
    }
    $height = get_option('pqrc_height');
    $width = get_option('pqrc_width');
    $height = $height ? $height : 180;
    $width = $width ? $width : 180;
    $dimension = apply_filters('pqrc_qrcode_dimension', "{$width}x{$height}");
    $image_attributs = apply_filters('pqrc_qrcode_image_attributs','');
    $image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s',$dimension, $current_post_url);
    $content .= sprintf("<div class='qrcode'><img %s src='%s' alt='%s'></div>",$image_attributs, $image_src, $current_post_title);
    return $content;
}
add_filter('the_content', 'pqrc_display_qr_code');

function pqrc_settings_init_func(){
    add_settings_section('pqrc_section', __('Post To QR Code', 'poasttoqrcode'), 'pqrc_section_callback','general');
    add_settings_field('pqrc_height', __('QR Code Height', 'poasttoqrcode'), 'pqrc_display_field', 'general', 'pqrc_section', array('pqrc_height'));
    add_settings_field('pqrc_width', __('QR Code Width', 'poasttoqrcode'), 'pqrc_display_field', 'general', 'pqrc_section',array('pqrc_width'));
    //add_settings_field('pqrc_extra', __('Extra Field', 'poasttoqrcode'), 'pqrc_display_field', 'general', 'pqrc_section',array('pqrc_extra'));
    add_settings_field('pqrc_select', __('Dropdown', 'poasttoqrcode'), 'pqrc_display_select_field', 'general', 'pqrc_section');
    add_settings_field('pqrc_checkbox', __('Select Countries', 'poasttoqrcode'), 'pqrc_display_checkboxgroup_field', 'general', 'pqrc_section');
    
    register_setting('general', 'pqrc_height', array('sanitize_callback'=>'esc_attr'));
    register_setting('general', 'pqrc_width', array('sanitize_callback'=>'esc_attr'));
    //register_setting('general', 'pqrc_extra', array('sanitize_callback'=>'esc_attr'));
    register_setting('general', 'pqrc_select', array('sanitize_callback'=>'esc_attr'));
    register_setting('general', 'pqrc_checkbox');
}

function pqrc_display_checkboxgroup_field(){
    global $pqrc_countries;
    $option = get_option('pqrc_checkbox');
    $pqrc_countries = apply_filters('pqrc_countries', $pqrc_countries);
    foreach($pqrc_countries as $country){
        $selected = '';
        if(is_array($option) && is_array($country, $option)){
            $selected = 'checked';
        }
        printf('<input type="checkbox" name="pqrc_checkbox[]" value="%s" %s />%s <br />', $country, $selected, $country);
    }
}

function pqrc_display_select_field(){
    global $pqrc_countries;
    $option = get_option('pqrc_select');
    $pqrc_countries = apply_filters('pqrc_countries', $pqrc_countries);
    printf('<select id="%s" name="%s">', 'pqrc_select', 'pqrc_select');
    foreach($pqrc_countries as $country){
        $selected = '';
        if($option == $country) $selected='selected';
        printf('<option value="%s" %s>%s</option>', $country, $selected, $country);
    }
    echo "</select>";
}

function pqrc_section_callback(){
    echo "<p>". __('Settings for Posts To QR Code Plugin', 'poasttoqrcode') ."</p>";
}

function pqrc_display_field($args){
    $option = get_option($args[0]);
    printf("<input type='text' id='%s' name='%s' value='%s' />", $args[0], $args[0], $option);
}

function pqrc_display_height(){
    $height = get_option('pqrc_height');
    printf("<input type='text' id='%s' name='%s' value='%s' />", "pqrc_height", "pqrc_height", $height);
}
function pqrc_display_width(){
    $width = get_option('pqrc_width');
    printf("<input type='text' id='%s' name='%s' value='%s' />", "pqrc_width", "pqrc_width", $width);
}

add_action('admin_init', 'pqrc_settings_init_func');



function philosophy_button($attributes){
    $default = array(
        'type' => 'primary',
        'title' =>__("Buttoon Primary", "poasttoqrcode"),
        'url' => '#',
    );

    $button_attributes = shortcode_atts($default, $attributes);

    return sprintf('<a class="btn btn--%s full-width" href="%s">%s</a>', 
        $button_attributes['type'], 
        $button_attributes['url'],
        $button_attributes['title']
    );
}
add_shortcode('button', 'philosophy_button');

function philosophy_button2($attributes, $content=''){
    $default = array(
        'type' => 'primary',
        'title' =>__("Buttoon Primary", "poasttoqrcode"),
        'url' => '#',
    );

    $button_attributes = shortcode_atts($default, $attributes);

    return sprintf('<a class="btn btn--%s full-width" href="%s">%s</a>', 
        $button_attributes['type'], 
        $button_attributes['url'],
        do_shortcode($content),
    );
}
add_shortcode('button2', 'philosophy_button2');

function philosopy_uc($attributes, $content=''){
    return strtoupper(do_shortcode($content));
}
add_shortcode('uc', 'philosopy_uc');

function philosoophy_gooogle_map($attributes){
    $default = array(
        'place' => __("Dhaka", "poasttoqrcode"),
    );
    $params = shortcode_atts($default, $attributes);
    $map = <<<EOD
        <div>
            <div>
                <iframe src="https://maps.google.com/maps?q={$params['place']}............">
            </div>
        </div>
    EOD;
    return $map;
}
add_shortcode('gmap', 'philosoophy_gooogle_map');
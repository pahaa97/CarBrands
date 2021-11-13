<?php

/*
Plugin Name: CarBrands
Plugin URI: https://github.com/pahaa97/CarBrands
Description: Создание типа записей "Машины" и вывод фильтрации машин по брендам Shortcode [car-brands]
Version: 1.0
Author: pavelfedotov
*/

require_once('settings/settings.php');
require_once('settings/shortcodes.php');
require_once('models/Post.php');
// require_once('settings/shortcodes.php');


register_activation_hook( __FILE__, 'car_brands_install' );
register_deactivation_hook( __FILE__, 'car_brands_deactivation');

add_action( 'init' , 'car_brands_setup_post_taxonomy' );
add_action( 'init' , 'car_brands_setup_post_types' );
add_filter( 'post_type_link', 'brand_permalink', 1, 2 );


add_action( 'wp_enqueue_scripts', 'car_jquery' );
add_action( 'wp_footer', 'my_wp_head_js' );
add_action( 'wp_ajax_car', 'car_ajax' ); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action( 'wp_ajax_nopriv_car', 'car_ajax' );  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей


add_shortcode( 'car-brands' , 'car_brands_func' );

//error_log( print_r( $count_posts, 1) );




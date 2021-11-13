<?php

function car_ajax(){

	if (isset($_POST['post_brand']))
	{
		$post_brand = $_POST["post_brand"];
//		$my_posts = new WP_Query;
//		$myposts = $my_posts->query( array(
//			'post_type' => 'car',
//			'brand' => $post_brand
//		) );
		//echo json_encode($myposts);

		//echo $post_brand;
		$params = array(
			'post_type'   => 'car',
			'posts_per_page' => 10,
			'brand' => $post_brand
		);
		$recent_posts_array = get_posts( $params );
		//echo json_encode($recent_posts_array);
		$result[] = "";
		foreach( $recent_posts_array as $recent_post_single )
		{
			//error_log( print_r( $recent_posts_array, 1) );
			$result[] = '<div>';
			$result[] = '<a href="' . get_permalink( $recent_post_single ) .'">' .get_the_post_thumbnail( $recent_post_single ) .'</a>';
			$result[] = '<a href="' . get_permalink( $recent_post_single ) .'">'. $recent_post_single->post_title .'</a>';
			$result[] = '</div>';
		}
		echo json_encode($result);
	}

	die; // даём понять, что обработчик закончил выполнение
}

function car_jquery() {
	wp_enqueue_script( 'jquery' );
}

function my_wp_head_js() {
	echo '<script>


	jQuery( function( $ ){
	$(".load").click( function(){
	$(".load").siblings().removeClass("active");
    
	$(this).addClass("active"); 
    
    let post_brand = $(this).val();
    let data = {
                action: "car",
                post_brand: post_brand
		      };
		$.ajax({
	        type: "POST",
		      url: "'.admin_url( "admin-ajax.php" ).'",
		      dataType: "html",
		      data: data,
		      success: function (data) {
				$(".car-gallery").empty().append(JSON.parse(data));
	          },
	          error: function (XMLHttpRequest, textStatus) {
	          	console.log(XMLHttpRequest, textStatus);
            }
	    });
	});
});
	</script>';
}

function car_brands_install() {
	car_brands_setup_post_taxonomy();
	car_brands_setup_post_types();
	flush_rewrite_rules();

    $post = new Post();
    $post->start();
}

function car_brands_deactivation() {
    delete_option( 'true_plugin_settings' );
    flush_rewrite_rules();
}

function car_brands_setup_post_taxonomy() {
    register_taxonomy( 'brand', 'car', [
        'label'                 => 'Бренд', // определяется параметром $labels->name
        'labels'                => [
            'name'              => 'Бренд',
            'singular_name'     => 'Бренд',
            'search_items'      => 'Поиск бренда',
            'all_items'         => 'Все бренды',
            'view_item '        => 'Посмотреть бренд',
            'parent_item'       => 'Родительский бренд',
            'parent_item_colon' => 'Родительский бренд:',
            'edit_item'         => 'Изменить бренд',
            'update_item'       => 'Обновить бренд',
            'add_new_item'      => 'Добавить новый бренд',
            'new_item_name'     => 'Имя нового бренда',
            'menu_name'         => 'Бренды',
        ],
        'description'           => 'Бренды машин', // описание таксономии
        'public'                => true,
        'show_in_nav_menus'     => false, // равен аргументу public
        'show_ui'               => true, // равен аргументу public
        'show_tagcloud'         => false, // равен аргументу show_ui
        'hierarchical'          => true,
        'rewrite'               => array('slug'=>'car', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
        'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)

    ] );
}

function car_brands_setup_post_types() {
    register_post_type( 'car', array(
            'labels'             => array(
                'name'               => 'Машины', // Основное название типа записи
                'singular_name'      => 'Машина', // отдельное название записи типа Book
                'add_new'            => 'Добавить новую',
                'add_new_item'       => 'Добавить новую машину',
                'edit_item'          => 'Редактировать машину',
                'new_item'           => 'Новый пост',
                'view_item'          => 'Посмотреть пост',
                'search_items'       => 'Найти пост',
                'not_found'          => 'Постов не найдено',
                'parent_item_colon'  => '',
                'menu_name'          => 'Машины'
            ),
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_rest'        => false,
            'rest_base'           => '',
            'show_in_menu'        => true,
            'exclude_from_search' => false,
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'hierarchical'        => false,
            'rewrite'             => array( 'slug'=>'car/%brand%', 'with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ),
            'has_archive'         => 'car',
            'query_var'           => true,
            'supports'            => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies'          => array( 'brand' ),
	    )
    );
}

function brand_permalink( $permalink, $post ){
    // выходим если это не наш тип записи: без холдера %brand%
    if( strpos($permalink, '%brand%') === FALSE )
        return $permalink;

    // Получаем элементы таксы
    $terms = get_the_terms($post, 'brand');
    // если есть элемент заменим холдер
    if( ! is_wp_error($terms) && !empty($terms) && is_object($terms[0]) )
        $taxonomy_slug = $terms[0]->slug;
    else
        $taxonomy_slug = 'no-brand';

    return str_replace('%brand%', $taxonomy_slug, $permalink );
}


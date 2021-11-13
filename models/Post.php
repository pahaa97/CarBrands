<?php


class Post {

	public function start()
	{
		$issetPost = get_posts( array(
				'numberposts' => 1,
				'category'    => 0,
				'include'     => array(),
				'exclude'     => array(),
				'meta_key'    => '',
				'meta_value'  =>'',
				'post_type'   => 'car',
				'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
		));
		//error_log( print_r( $countPost, 1) );
		if (!isset($issetPost[0])) {
			$testdata = $this->testData();
			foreach ($testdata as $value)
			{
//            error_log( print_r( $value['car'], 1) );
//            error_log( print_r( $value['image'], 1) );
				$this->add_post($value['car'],$value['image'],$value['brand']);
			}
		}
	}

	public function add_post($title, $image, $brand) {

		$insert_res = wp_insert_term(
			$brand,  // новый термин
			'brand' // таксономия
		);

		$post_data = array(
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'car',
		);



		// Вставляем запись в базу данных
		$post_id  = wp_insert_post( $post_data );
		$media_id = media_sideload_image( $image, $post_id, null, 'id' );
		$res = set_post_thumbnail( $post_id, $media_id );
		wp_set_object_terms( $post_id, $brand, 'brand' );

		return $post_id;
	}

    public function testData() {
        return (object) array([
                'car'       => 'M440i xDrive Convertible 2021 года',
                'image'     => 'https://auto.vercity.ru/gallery/img/automobiles/BMW/m440i/900x/1615370209.jpg',
	            'brand'     => 'BMW'
            ],
	        [
                'car'       => 'Audi e-tron GT quattro 2021 года',
                'image'     => 'https://auto.vercity.ru/gallery/img/automobiles/Audi/e-tron%20GT%20quattro/900x/1634556390.jpg',
	            'brand'     => 'Audi'
            ],
	        [
                'car'       => 'Chevrolet Camaro SS Convertible 2020 года',
                'image'     => 'https://auto.vercity.ru/gallery/img/automobiles/Chevrolet/Camaro%20SS%20Convertible/900x/1607066206.jpg',
                'brand'     => 'Chevrolet'
            ]);
    }


	public function get_posts_car()
	{
		$categories = get_terms('brand');
		if($categories){
			echo '<div id="menu-switch-car">';
			echo '<button class="active load" id="load-post" value="">All</button>';

			foreach ($categories as $cat){
				// выводим элемент списка, где атрибут value равен ID рубрики, а $cat->name - название рубрики
				echo "<button class='load' id='load-post' value='{$cat->name}'>{$cat->name}</button>";
			}
			echo '</div>';
		}


		$params = array(
			'post_type'   => 'car',
			'posts_per_page' => 10
		);
		$recent_posts_array = get_posts( $params );
		echo '<div class="car-gallery">';

		foreach( $recent_posts_array as $recent_post_single )
		{
			echo '<div>';
			echo '<a href="' . get_permalink( $recent_post_single ) .'">' .get_the_post_thumbnail( $recent_post_single ) .'</a>';
			echo '<a href="' . get_permalink( $recent_post_single ) .'">'. $recent_post_single->post_title .'</a>';
			echo '</div>';
		}
		echo '</div>';

		echo '
		<style>
            .car-gallery div img {
                max-height: 500px;
                max-width: 500px;
                object-fit: cover;
            }
            .active {
            	color: #29303d !important;
            	background-color: white !important; 
            	pointer-events: none;
            }
		</style>';


	}
}
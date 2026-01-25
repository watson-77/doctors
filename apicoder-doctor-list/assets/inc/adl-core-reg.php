<?php
/**
 * Registers a custom post type 'doctors'.
 */
function apicoder_register_doctors_post_type() : void {
	$labels = [
		'name' => _x( 'Список Докторов', 'Post Type General Name', 'apicoder' ),
		'singular_name' => _x( 'Список Докторов', 'Post Type Singular Name', 'apicoder' ),
		'menu_name' => __( 'Список Докторов', 'apicoder' ),
		'name_admin_bar' => __( 'Список Докторов', 'apicoder' ),
		'archives' => __( 'Архив Докторов', 'apicoder' ),
		'attributes' => __( 'Аттрибуты докторов', 'apicoder' ),
		'parent_item_colon' => __( 'Основной список докторов:', 'apicoder' ),
		'all_items' => __( 'Все списки докторов', 'apicoder' ),
		'add_new_item' => __( 'Добавить нового доктора', 'apicoder' ),
		'add_new' => __( 'Добавить новый', 'apicoder' ),
		'new_item' => __( 'Новый доктор', 'apicoder' ),
		'edit_item' => __( 'Редактировать БИО доктора', 'apicoder' ),
		'update_item' => __( 'Обновить БИО доктора', 'apicoder' ),
		'view_item' => __( 'Просмотр БИО доктора', 'apicoder' ),
		'view_items' => __( 'Просмотр БИО докторов', 'apicoder' ),
		'search_items' => __( 'Поиск доктора', 'apicoder' ),
		'not_found' => __( 'Доктор не найден', 'apicoder' ),
		'not_found_in_trash' => __( 'БИО Доктора не найден в корзине', 'apicoder' ),
		'featured_image' => __( 'прикрепленное изображение', 'apicoder' ),
		'set_featured_image' => __( 'установить как прикрепленное изображение', 'apicoder' ),
		'remove_featured_image' => __( 'Удалить прикрепленное изображение', 'apicoder' ),
		'use_featured_image' => __( 'Использовать как прикрепленное изображение', 'apicoder' ),
		'insert_into_item' => __( 'Внести в список докторов', 'apicoder' ),
		'uploaded_to_this_item' => __( 'Обновить текущий список докторов', 'apicoder' ),
		'items_list' => __( 'Список докторов', 'apicoder' ),
		'items_list_navigation' => __( 'Навигация по списку докторов', 'apicoder' ),
		'filter_items_list' => __( 'Фильтр списка докторов', 'apicoder' ),
	];
	$labels = apply_filters( 'doctors-labels', $labels );

	$args = [
		'label' => __( 'Список докторов', 'apicoder' ),
		'description' => __( 'Список докторов', 'apicoder' ),
		'labels' => $labels,
		'supports' => [
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'comments',
			'trackbacks',
			'revisions',
			'custom-fields',
			'post-formats',
		],
		'taxonomies' => [
			'City',
			'Specialization',
		],
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-admin-post',
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'can_export' => false,
		'capability_type' => 'post',
		'show_in_rest' => true,
	];
	$args = apply_filters( 'doctors-args', $args );

	register_post_type( 'doctors', $args );
}
add_action( 'init', 'apicoder_register_doctors_post_type', 0 );

/**
 * Registers the 'City' taxonomy.
 * 
 * @return void
 */
function apicoder_register_City_taxonomy() : void {
	$labels = [
		'name' => _x( 'Города', 'Taxonomy Name', 'apicoder' ),
		'singular_name' => _x( 'Город', 'Taxonomy Singular Name', 'apicoder' ),
		'menu_name' => __( 'Города', 'apicoder' ),
		'all_items' => __( 'Все Города', 'apicoder' ),
		'parent_item' => __( 'Основной Город', 'apicoder' ),
		'parent_item_colon' => __( 'Основной Город: ', 'apicoder' ),
		'new_item_name' => __( 'Новый Город', 'apicoder' ),
		'add_new_item' => __( 'Добавить новый Город', 'apicoder' ),
		'edit_item' => __( 'Редактировать Город', 'apicoder' ),
		'update_item' => __( 'Обновить Город', 'apicoder' ),
		'view_item' => __( 'Просмотр Города', 'apicoder' ),
		'add_or_remove_items' => __( 'Добавить/удалить Город', 'apicoder' ),
		'choose_from_most_used' => __( 'Выберите из наиболее часто используемых городов.', 'apicoder' ),
		'popular_items' => __( 'Часто используемые Города', 'apicoder' ),
		'search_items' => __( 'Поиск Города', 'apicoder' ),
		'not_found' => __( 'Не найдено', 'apicoder' ),
		'no_terms' => __( 'Нету Города', 'apicoder' ),
		'items_list' => __( 'Список Городов', 'apicoder' ),
		'items_list_navigation' => __( 'Навигация по списку городов', 'apicoder' ),
	];

	$args = [
		'labels' => $labels,
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud' => true,
		'show_in_rest' => true,
		'rest_base' => 'city',
		'rest_controller_class' => 'WP_REST_city_Terms_Controller',
	];

	register_taxonomy( 'City', ['doctors'], $args );
}
add_action( 'init', 'apicoder_register_City_taxonomy' );

/**
 * Registers the 'Specialization' taxonomy.
 * 
 * @return void
 */
function apicoder_register_specialization_taxonomy() : void {
	$labels = [
		'name' => _x( 'Специализации', 'Taxonomy Name', 'apicoder' ),
		'singular_name' => _x( 'Специализация', 'Taxonomy Singular Name', 'apicoder' ),
		'menu_name' => __( 'Специализации', 'apicoder' ),
		'all_items' => __( 'Все Специализации', 'apicoder' ),
		'parent_item' => __( 'Основная Специализации', 'apicoder' ),
		'parent_item_colon' => __( 'Основная Специализация: ', 'apicoder' ),
		'new_item_name' => __( 'Новая Специализация', 'apicoder' ),
		'add_new_item' => __( 'Добавить новую Специализацию ', 'apicoder' ),
		'edit_item' => __( 'Редактировать Специализацию', 'apicoder' ),
		'update_item' => __( 'Обновить Специализацию', 'apicoder' ),
		'view_item' => __( 'Просмотреть Специализацию', 'apicoder' ),
		'add_or_remove_items' => __( 'Добавить/Удалить Специализации', 'apicoder' ),
		'choose_from_most_used' => __( 'Выберите из наиболее часто используемых Специализаций', 'apicoder' ),
		'popular_items' => __( 'Часто используемые Специализации', 'apicoder' ),
		'search_items' => __( 'Поиск Специализации', 'apicoder' ),
		'not_found' => __( 'Не найдено', 'apicoder' ),
		'no_terms' => __( 'Нет Специализации', 'apicoder' ),
		'items_list' => __( 'Список Специализации', 'apicoder' ),
		'items_list_navigation' => __( 'Навигация по списку специализаций', 'apicoder' ),
	];

	$args = [
		'labels' => $labels,
		'hierarchical' => true,
		'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud' => true,
		'show_in_rest' => true,
		'rest_base' => 'Specialization',
		'rest_controller_class' => 'WP_REST_specialization_Terms_Controller',
	];

	register_taxonomy( 'Specialization', ['doctors'], $args );
}
add_action( 'init', 'apicoder_register_specialization_taxonomy' );
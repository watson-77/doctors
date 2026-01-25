<?php 
/*
*
*	***** Apicoder Doctor list *****
*
*	Core Functions
*	
*/
// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if
/**
 * Register CMB2 metabox
 * 
 */
if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
    require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
    require_once dirname( __FILE__ ) . '/CMB2/init.php';
}
/*
*
* Custom Front End Ajax Scripts / Loads In WP Footer
*
*/
function adl_frontend_ajax_form_scripts(){
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    "use strict";
    // add basic front-end ajax page scripts here
    $('#adl_custom_plugin_form').submit(function(event){
        event.preventDefault();
        // Vars
        var myInputFieldValue = $('#myInputField').val();
        // Ajaxify the Form
        var data = {
            'action': 'adl_custom_plugin_frontend_ajax',
            'myInputFieldValue':   myInputFieldValue,
        };
        
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
        $.post(ajaxurl, data, function(response) {
                console.log(response);
                if(response.Status == true)
                {
                    console.log(response.message);
                    $('#adl_custom_plugin_form_wrap').html(response);

                }
                else
                {
                    console.log(response.message);
                    $('#adl_custom_plugin_form_wrap').html(response);
                }
        });
    });
}(jQuery));    
</script>
<?php }
add_action('wp_footer','adl_frontend_ajax_form_scripts');

// cmb2 metabox for cpt doctors
add_action( 'cmb2_init', 'cmb2_api_bio_metabox' );
function cmb2_api_bio_metabox() {

	$prefix = '_apicoder_';

	$cmb = new_cmb2_box( array(
		'id'           => $prefix . 'apicoder_bio',
		'title'        => __( 'Биография', 'apicoder' ),
		'object_types' => array( 'doctors' ),
		'context'      => 'advanced',
		'priority'     => 'high',
	) );

	$cmb->add_field( array(
		'name' => __( 'ФИО', 'apicoder' ),
		'id' => $prefix . 'fio',
		'type' => 'text',
		'default' => 'Фамилия Имя Отчество',
		'desc' => __( 'Фамилия Имя Отчество', 'apicoder' ),
	) );

    	$cmb->add_field( array(
		'name' => __( 'Стаж', 'apicoder' ),
		'id' => $prefix . 'stazh',
		'type' => 'text_small',
		'default' => '3',
		'desc' => __( 'Врачебный стаж', 'apicoder' ),
	) );

	$cmb->add_field( array(
		'name' => __( 'Цена от', 'apicoder' ),
		'id' => $prefix . 'cost_doc',
		'type' => 'text_small',
		'default' => '1000',
		'desc' => __( 'Цена за услугу', 'apicoder' ),
	) );

	$cmb->add_field( array(
		'name' => __( 'Рейтинг', 'apicoder' ),
		'id' => $prefix . 'reiting',
		'type' => 'text_small',
	) );

}

function my_excerpt_length($length ) {
    return 20;
}
add_filter( 'excerpt_length', 'my_excerpt_length' );

//Custom pagination
function apicoder_doctors_pagination() {
    global $wp_query;
    $big = 999999999;
    $current = max( 1, get_query_var( 'paged' ) );

    $paginate = paginate_links( array( 'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ), 'format' => '?paged=%#%', 'current' => $current, 'total' => $wp_query->max_num_pages, 'type' => 'array', 'prev_text' => __( '<span class="">&laquo;</span>' ), 'next_text' => __( '<span class="">&raquo;</span>' ) ) );

    if ( $paginate ) {
        echo '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        foreach ( $paginate as $page ) {
            if ( strpos( $page, 'current' ) ) {
                echo '<li class="page-item active">' . str_replace('page-numbers', 'page-link', $page) . '</li>';
            }
            else {
                echo '<li class="page-item">' . str_replace('page-numbers', 'page-link', $page) . '</li>';
            }
        }
        echo '</ul></nav>';
    }
}


/*
 * Doctors Filter - PHP Functions (ИСПРАВЛЕННАЯ ВЕРСИЯ)
 * Добавьте этот код в functions.php вашей темы
 */

// 1. Регистрация и подключение скриптов
function doctors_filter_enqueue_scripts() {
    // Подключаем только на странице архива doctors
    if (is_post_type_archive('doctors')) {
        
        // jQuery уже включен в WordPress
        wp_enqueue_script('jquery');
        
        // Подключаем наш скрипт фильтрации
        wp_enqueue_script(
            'doctors-filter',
            get_stylesheet_directory_uri() . '/js/doctors-filter.js',
            array('jquery'),
            '1.0.1', // Изменили версию для сброса кэша
            true // ВАЖНО: true = загружать в footer
        );
        
        // Передаем данные в JavaScript - ОБЯЗАТЕЛЬНО после регистрации скрипта
        wp_localize_script('doctors-filter', 'doctorsFilterAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('doctors_filter_nonce')
        ));
        
        // Подключаем стили
        wp_enqueue_style(
            'doctors-filter-style',
            get_stylesheet_directory_uri() . '/css/doctors-filter.css',
            array(),
            '1.0.1'
        );
    }
}
add_action('wp_enqueue_scripts', 'doctors_filter_enqueue_scripts');

// 2. AJAX обработчик для фильтрации
function doctors_filter_ajax_handler() {
    // Проверка nonce для безопасности
    check_ajax_referer('doctors_filter_nonce', 'nonce');
    
    // Получаем параметры фильтрации
    $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $specialization = isset($_POST['specialization']) ? sanitize_text_field($_POST['specialization']) : '';
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 9;
    // Формируем аргументы запроса
    $args = array(
        'post_type' => 'doctors',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC'
    );
    
    // Добавляем tax_query если выбраны фильтры
    $tax_query = array('relation' => 'AND');
    
    if (!empty($city) && $city !== 'all') {
        $tax_query[] = array(
            'taxonomy' => 'City',
            'field' => 'slug',
            'terms' => $city
        );
    }
    
    if (!empty($specialization) && $specialization !== 'all') {
        $tax_query[] = array(
            'taxonomy' => 'Specialization',
            'field' => 'slug',
            'terms' => $specialization
        );
    }
    
    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }
    
    // Выполняем запрос
    $query = new WP_Query($args);
    
    // Формируем HTML ответ
    ob_start();
    if ($query->have_posts()) { while ($query->have_posts()) { $query->the_post();
            // Получаем таксономии для текущего поста
            $doctor_cities = get_the_terms(get_the_ID(), 'city');
            $doctor_specializations = get_the_terms(get_the_ID(), 'specialization');
            ?>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3">
			<div class="card border border-warning">
				<div class="card-body">
					<div class="card-title">
					<h3 class="h5"><?php the_title(); ?></h3>
				</div>
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="img">
              <?php 
							$fio = get_post_meta( get_the_ID(), "_apicoder_fio", true );
							if (!empty( get_the_post_thumbnail())){
							$img_attr = array(	'class' => "img-thumbnail",	'alt' => trim( $fio), );
							the_post_thumbnail( 'medium', $img_attr );}
							else {
								echo '<img src="'.PLUGIN_URL.'/apicoder-doctor-list/assets/img/placeholder.jpg" class="img-thumbnail" alt="Фото доктора">';
							}
							?>
              </div>
							<div class="col-xs-12">Стаж: <?php echo get_post_meta( get_the_ID(), "_apicoder_stazh", true ); ?> лет</div>
							<div class="col-xs-12">Цена от <?php echo get_post_meta( get_the_ID(), "_apicoder_cost_doc", true ); ?> р</div>
							<div class="col-xs-12">Рейтинг <?php echo get_post_meta( get_the_ID(), "_apicoder_reiting", true ); ?></div>
						</div>
						<div class="col-xs-12 col-sm-8">
							<p>ФИО: <span class="h5"><span><?php echo $fio; ?></span></p>
              <?php
              $spec_terms = wp_get_object_terms( $post->ID, 'Specialization' );
                        if ( !empty( $spec_terms ) ) {
                            if ( !is_wp_error( $spec_terms ) ) {
								echo '<p> Специализация: ';
								$slastKey = array_key_last($spec_terms);
								$smax_key = count($spec_terms);
                                foreach ( $spec_terms as $skey=>$spec ) {
									if ($skey < ($smax_key - 1)) { $sznak = ', ';}
									if ($skey == ($smax_key - 1)) { $sznak = '.';}
                                    echo '<a class="text-decoration-none" href="' . get_term_link( $spec ) . '"><span class="text-muted">' . __( '' . $spec->name . '', 'apicoder' ) .$sznak. '</span></a>';
									//echo '<span>' .  $spec->name . $sznak.'</span>';
                                }
								echo '</p>';
                            }
                        }
               ?>
							<p><?php the_excerpt(20); ?></p>
						</div>
					</div>
				</div>
				<div class="card-footer text-right"><a href="<?php the_permalink(); ?>" class="btn btn-warning">Узнать подробнее</a></div>
			</div>
		</div>
            <?php
        }
    } else {
        echo '<div class="no-doctors-found">';
        echo '<p>По выбранным фильтрам врачи не найдены.</p>';
        echo '</div>';
    }
    wp_reset_postdata();
    $output = ob_get_clean();
    // Отправляем ответ
    wp_send_json_success(array(
        'html' => $output,
        'count' => $query->found_posts
    ));
}
// Регистрируем AJAX обработчики для авторизованных и неавторизованных пользователей
add_action('wp_ajax_doctors_filter', 'doctors_filter_ajax_handler');
add_action('wp_ajax_nopriv_doctors_filter', 'doctors_filter_ajax_handler');
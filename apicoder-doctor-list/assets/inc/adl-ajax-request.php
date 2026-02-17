<?php
/*
*
*	***** Apicoder Doctor list *****
*
*	Ajax Request
*	
*/
// If this file is called directly, abort. //
if (! defined('WPINC')) {
    die;
} // end if

function localize_doctors_filter_script()
{
    wp_localize_script('adl-core', 'doctorsFilterAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('doctors_filter')
    ));
}
add_action('wp_enqueue_scripts', 'localize_doctors_filter_script');

// Также добавляем nonce в глобальные переменные для использования в шаблонах
function add_global_nonce()
{
    echo '<script type="text/javascript">
        var doctorsFilterAjax = doctorsFilterAjax || {};
        doctorsFilterAjax.nonce = "' . wp_create_nonce('doctors_filter') . '";
    </script>';
}
add_action('wp_head', 'add_global_nonce');
/*
Ajax Filter Request
*/
add_action('wp_ajax_doctors_filter', 'handle_doctors_filter');
add_action('wp_ajax_nopriv_doctors_filter', 'handle_doctors_filter');

function handle_doctors_filter()
{
    // Проверка nonce
    if (!wp_verify_nonce($_POST['nonce'], 'doctors_filter')) {
        wp_send_json_error('Неверный nonce');
    }

    $city = sanitize_text_field($_POST['city']);
    $specialization = sanitize_text_field($_POST['specialization']);

    $args = array(
        'post_type' => 'doctors',
        'posts_per_page' => 9,
        'tax_query' => array()
    );

    // Фильтр по городу
    if ($city && $city !== 'all') {
        $args['tax_query'][] = array(
            'taxonomy' => 'City',
            'field' => 'slug',
            'terms' => $city
        );
    }

    // Фильтр по специализации
    if ($specialization && $specialization !== 'all') {
        $args['tax_query'][] = array(
            'taxonomy' => 'Specialization',
            'field' => 'slug',
            'terms' => $specialization
        );
    }

    // Если есть несколько условий, объединяем через AND
    if (count($args['tax_query']) > 1) {
        $args['tax_query']['relation'] = 'AND';
    }

    // Если нет условий, сбрасываем tax_query
    if (empty($args['tax_query'])) {
        unset($args['tax_query']);
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            // Включаем шаблон карточки врача
            include(ADL_CORE_TEMPL . 'doctor-card.php');
        endwhile;
    else :
        echo '<p>Врачи не найдены.</p>';
    endif;

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(array(
        'html' => $html,
        'count' => $query->found_posts
    ));
}

<?php
// фильтр передает переменную $template - путь до файла шаблона. Изменяя этот путь мы изменяем файл шаблона.
add_filter( 'template_include', 'doctors_template' );
// функция фильтрации
function doctors_template( $template ) {
    #
    if ( get_post_type() == 'doctors' ) {
        if ( is_tax( 'City', 'Specialization' ) ) {
            $template = ADL_CORE_TEMPL . '/taxonomy-doctors.php';
            return $template;
        }
        if ( is_archive( 'doctors' ) ) {
            $template = ADL_CORE_TEMPL . '/archive-doctors.php';
            return $template;
        }
        if ( is_single() ) {
            $template = ADL_CORE_TEMPL . '/single-doctors.php';
            return $template;
        }
    }


    global $wp_query;
    $post_type = get_query_var( 'post_type' );
    if ( $wp_query->is_search && $post_type == 'doctors' ) {
        $template = ADL_CORE_TEMPL . '/archive-doctors.php';  //  redirect to archive-search.php
        return $template;
    }
    return $template;
}
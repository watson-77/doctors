<?php
/*
Template Name: Taxonomy City
*/
get_header();

$current_term = get_queried_object(); // текущий город
// Получаем типы записей, привязанные к этой таксономии
$post_types = get_taxonomy($current_term->taxonomy)->object_type;
$cpt_slug = $post_types[0]; // Берем первый
$link_a = get_post_type_archive_link($cpt_slug);
// Получаем все специализации, которые есть в этом городе (для фильтра)
$all_specializations = [];
$city_terms = get_terms([
    'taxonomy' => 'City',
    'fields'   => 'ids',
    'slug'     => $current_term->slug,
]);
if (!empty($city_terms)) {
    $args = [
        'post_type'      => 'doctors',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'City',
                'field'    => 'term_id',
                'terms'    => $city_terms,
            ]
        ]
    ];
    $city_posts = get_posts($args);
    if (!empty($city_posts)) {
        $spec_terms = wp_get_object_terms($city_posts, 'Specialization',  array('orderby' => 'name', 'order'   => 'ASC', 'fields'  => 'all',));
        if (!empty($spec_terms) && !is_wp_error($spec_terms)) {
            $all_specializations = $spec_terms; // массив slug => name
        }
    }
}
?>

<div class="container mt-3">
    <div class="row align-items-center">
        <div class="col">
            <h1><?php echo esc_html($current_term->name); ?></h1>
            <div class="text-right float-start"><?php echo '<a href="' . esc_url($link_a) . '" class="btn btn-warning">К общему списку</a>'; ?></div>
            <div class="clearfix"></div>
        </div>
        <!-- Фильтр по специализации -->
        <?php if (!empty($all_specializations)) : ?>
            <div class="col-auto">
                <select id="specialization-filter" class="form-control form-select">
                    <option value="all">Все специализации</option>
                    <?php foreach ($all_specializations as $id) : ?>
                        <option value="<?php echo esc_attr($id->slug); ?>"><?php echo esc_html($id->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <!-- Скрытый селект для города, чтобы работал doctors-filter.js -->
        <div class="col-auto" style="display:none;">
            <select id="city-filter" class="form-control form-select">
                <option value="<?php echo esc_attr($current_term->slug); ?>" selected><?php echo esc_html($current_term->name); ?></option>
            </select>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <!-- Индикатор загрузки -->
        <div id="loading-indicator" class="loading-indicator" style="display: none;">
            <div class="spinner"></div>
            <span>Загрузка...</span>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<div class="container mt-3">
    <div id="doctors-container" class="row">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3 doctor-item">
                    <?php include(ADL_CORE_TEMPL . 'doctor-card.php'); ?>
                </div>
            <?php endwhile;
        else : ?>
            <div class="col-12">
                <p>Врачей не найдено.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <?php apicoder_doctors_pagination(); ?>
</div>
<div class="p-5"></div>

<?php get_footer(); ?>
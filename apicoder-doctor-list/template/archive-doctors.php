<?php get_header(); ?>
<div class="container mt-3">
    <div class="row">
        <h1><?php post_type_archive_title(); ?></h1>
        <div class="col-xs-12 col-md-12 text-right">
            <!-- Форма фильтрации -->
            <!-- Фильтры -->
            <div class="doctors-filters">
                <div class="btn-group filters-row">
                    <!-- Фильтр по городу -->
                    <div class="btn filter-group">
                        <select id="city-filter" class="form-control filter-select">
                            <option value="all">Все города</option>
                            <?php
                            $cities = get_terms(array(
                                'taxonomy' => 'City',
                                'hide_empty' => true,
                                'orderby' => 'name',
                                'order' => 'ASC'
                            ));

                            if (!empty($cities) && !is_wp_error($cities)) {
                                foreach ($cities as $city) {
                                    echo '<option value="' . esc_attr($city->slug) . '">';
                                    echo esc_html($city->name) . ' (' . $city->count . ')';
                                    echo '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Фильтр по специализации -->
                    <div class="btn filter-group">
                        <select id="specialization-filter" class="form-control filter-select">
                            <option value="all">Все специализации</option>
                            <?php
                            $specializations = get_terms(array(
                                'taxonomy' => 'Specialization',
                                'hide_empty' => true,
                                'orderby' => 'name',
                                'order' => 'ASC'
                            ));

                            if (!empty($specializations) && !is_wp_error($specializations)) {
                                foreach ($specializations as $spec) {
                                    echo '<option value="' . esc_attr($spec->slug) . '">';
                                    echo esc_html($spec->name) . ' (' . $spec->count . ')';
                                    echo '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Кнопка сброса -->
                    <div class="btn filter-group">
                        <button id="reset-filters" class="btn btn-warning reset-button">Сбросить фильтры</button>
                    </div>
                </div>
            </div>
            <!-- Индикатор загрузки -->
            <div id="loading-indicator" class="loading-indicator" style="display: none;">
                <div class="spinner"></div>
                <span>Загрузка...</span>
            </div>
        </div>
    </div>
</div>

<div class="container mt-3">
    <div id="doctors-container" class="row">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3">
                    <?php include(ADL_CORE_TEMPL . 'doctor-card.php'); ?>
                </div>

            <?php endwhile; ?>

        <?php else : ?>
            <h2><?php _e('Not found', 'apicoder'); ?></h2>
        <?php endif; ?>
    </div>
</div>
<div class="container">
    <?php apicoder_doctors_pagination(); ?>
</div>
<div class="p-5"> </div>
<?php get_footer(); ?>
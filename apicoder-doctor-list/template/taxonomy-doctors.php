<?php get_header();?>
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
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3">
			<div id="post-<?php the_ID(); ?>" class="card border border-warning doctor-item">
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
							
							<p><?php the_excerpt(); ?></p>
						</div>
					</div>
				</div>
				<div class="card-footer text-right"><a href="<?php the_permalink(); ?>" class="btn btn-warning">Узнать подробнее</a></div>
			</div>
		</div>
		
    <?php endwhile; ?>

<?php else : ?>
                <h2><?php _e( 'Not found', 'apicoder' ); ?></h2>
<?php endif; ?>
  </div>
</div>
<div class="container">
<?php apicoder_doctors_pagination(); ?>		
</div>
<div class="p-5"> </div>
<div class="container-fluid bg-dark fixed-bottom">
	<div class="row">
		<footer class="d-flex w-100 justify-content-center p-3">
			<div class="text-white">&copy; All right reserved <a href="https://apicoder.ru" class="text-decoration-none">Apicoder</a> 2026</div>
		</footer>
	</div>
</div>
<?php get_footer(); ?>
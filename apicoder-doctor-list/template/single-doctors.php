<?php get_header();?>
<div class="container mt-3">
	<div class="row">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<div id="post-<?php the_ID(); ?>" class="card">
				<div class="card-body">
					<div class="card-title">
					<?php  // echo '<h3>'. the_title(). '</h3>';?>
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

							<div class="col-xs-12">стаж <?php echo get_post_meta( get_the_ID(), "_apicoder_stazh", true ); ?> лет</div>
							<div class="col-xs-12">Цена от <?php echo get_post_meta( get_the_ID(), "_apicoder_cost_doc", true ); ?> р</div>
							<div class="col-xs-12">рейтинг <?php echo get_post_meta( get_the_ID(), "_apicoder_reiting", true ); ?></div>

						</div>
						<div class="col-xs-12 col-sm-8">
							<p>ФИО: <span class="h5">
								<?php echo get_post_meta( get_the_ID(), "_apicoder_fio", true ); ?>
						</span></p>
							<p><?php the_content();?></p>
						</div>
						<div class="clearfix"></div>
						<div class="w-100 text-right px-4">
<?php 
$city_terms = wp_get_object_terms( $post->ID, 'City' );

                        if ( !empty( $city_terms ) ) {
                            if ( !is_wp_error( $city_terms ) ) {
								echo '<p class="text-right">';
								
								$lastKey = array_key_last($city_terms);
								$max_key = count($city_terms);

                                foreach ( $city_terms as $key=>$city ) {
									if ($key < ($max_key - 1)) { $znak = ', ';}
									if ($key == ($max_key - 1)) { $znak = '.';}
                                    echo '<a class="text-decoration-none" href="' . get_term_link( $city ) . '"><span class=" text-muted">' . __( '' . $city->name . '', 'apicoder' ) .$znak. '</span></a>';
									/* echo '<span>' .  $city->name . $znak.'</span>';*/
                                }
								echo '</p>';
                            }
                        }
						

						$spec_terms = wp_get_object_terms( $post->ID, 'Specialization' );
                        if ( !empty( $spec_terms ) ) {
                            if ( !is_wp_error( $spec_terms ) ) {
								echo '<p class="text-right"> Специализация: ';
								
								$slastKey = array_key_last($spec_terms);
								$smax_key = count($spec_terms);

                                foreach ( $spec_terms as $skey=>$spec ) {
									if ($skey < ($smax_key - 1)) { $sznak = ', ';}
									if ($skey == ($smax_key - 1)) { $sznak = '.';}
                                    echo '<a class="text-decoration-none" href="' . get_term_link( $spec ) . '"><span class="text-muted">' . __( '' . $spec->name . '', 'apicoder' ) .$sznak. '</span></a>';
									/* echo '<span>' .  $spec->name . $sznak.'</span>'; */
                                }
								echo '</p>';
                            }
                        }
?>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
                    <?php edit_post_link( '<span class="btn btn-md btn-primary">'.__('Редактировать', 'apicoder').'</span>', '<div class="card-footer text-right my-1">', '</div>' ); ?>
				<div class="clearfix"></div>				
			</div>
				
<?php endwhile; ?>

<?php else : ?>
                <h2><?php _e( 'Не найдено', 'apicoder' ); ?></h2>
<?php endif; ?>

</div>
</div>
<div class="clearfix"></div>
<div class="p-5"> </div>
<div class="clearfix"></div>
<div class="container-fluid bg-dark fixed-bottom">
	<div class="row">
		<footer class="d-flex w-100 justify-content-center p-3">
			<div class="text-white">&copy; All right reserved <a href="https://apicoder.ru" class="text-decoration-none">Apicoder</a> 2026</div>
		</footer>
	</div>
</div>
<div class="clearfix"></div>
<?php get_footer(); ?>
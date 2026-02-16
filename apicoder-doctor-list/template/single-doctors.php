<?php get_header(); ?>
<div class="container mt-3">
	<div class="row">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<?php $doctor_id = get_the_ID(); ?>
				<div id="post-<?php echo $doctor_id; ?>" class="card border border-warning">
					<div class="card-body">
						<div class="card-title">
							<?php  // echo '<h3>'. the_title(). '</h3>';
							?>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-4">
								<div class="img">
									<?php
									$fio = get_post_meta($doctor_id, "_apicoder_fio", true);
									if (!empty(get_the_post_thumbnail())) {
										$img_attr = array('class' => "img-thumbnail",	'alt' => trim($fio),);
										the_post_thumbnail('medium', $img_attr);
									} else {
										echo '<img src="' . PLUGIN_URL . '/apicoder-doctor-list/assets/img/placeholder.jpg" class="img-thumbnail" alt="' . trim($fio) . '">';
									}
									?>
								</div>

								<div class="col-xs-12">стаж <?php echo get_post_meta($doctor_id, "_apicoder_stazh", true); ?> лет</div>
								<div class="col-xs-12">Цена от <?php echo get_post_meta($doctor_id, "_apicoder_cost_doc", true); ?> р</div>
								<div class="col-xs-12">рейтинг <?php echo get_post_meta($doctor_id, "_apicoder_reiting", true); ?></div>

							</div>
							<div class="col-xs-12 col-sm-8">
								<p>ФИО: <span class="h5">
										<?php echo get_post_meta($doctor_id, "_apicoder_fio", true); ?>
									</span></p>
								<p><?php the_content(); ?></p>
							</div>
							<div class="clearfix"></div>
							<div class="w-100 text-right px-4">
								<?php apicoder_doctors_specialization($doctor_id); ?>
								<?php apicoder_doctor_city($doctor_id); ?>

							</div>
							<div class="clearfix"></div>
						</div>
					</div>
					<?php edit_post_link('<span class="btn btn-md btn-primary">' . __('Редактировать', 'apicoder') . '</span>', '<div class="card-footer text-right my-1">', '</div>'); ?>
					<div class="clearfix"></div>
				</div>
<div class="clearfix mb-5"> </div>

<div class="col-12"><div class="card border border-info"><?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?></div></div>
			<?php endwhile; ?>

		<?php else : ?>
			<h2><?php _e('Не найдено', 'apicoder'); ?></h2>
		<?php endif; ?>

	</div>
</div>
<div class="clearfix"></div>
<div class="p-5"> </div>
<div class="clearfix"></div>
<?php get_footer(); ?>
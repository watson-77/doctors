<?php $doctor_id = get_the_ID(); ?>
<div id="post-<?php echo $doctor_id; ?>" class="card border border-warning doctor-item">
    <div class="card-body">
        <div class="card-title">
            <h3 class="h5"><?php the_title(); ?></h3>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="img">
                    <?php
                    $fio = get_post_meta($doctor_id, "_apicoder_fio", true);
                    if (!empty(get_the_post_thumbnail())) {

                        $img_attr = array('class' => "img-thumbnail",    'alt' => trim($fio),);
                        the_post_thumbnail('medium', $img_attr);
                    } else {
                        echo '<img src="' . PLUGIN_URL . '/apicoder-doctor-list/assets/img/placeholder.jpg" class="img-thumbnail" alt="Фото доктора">';
                    }
                    ?>
                </div>

                <div class="col-xs-12">Стаж: <?php echo get_post_meta($doctor_id, "_apicoder_stazh", true); ?> лет</div>
                <div class="col-xs-12">Цена от <?php echo get_post_meta($doctor_id, "_apicoder_cost_doc", true); ?> р</div>
                <div class="col-xs-12">Рейтинг <?php echo get_post_meta($doctor_id, "_apicoder_reiting", true); ?></div>

            </div>
            <div class="col-xs-12 col-sm-8">
                <p>ФИО: <span class="h5"><span><?php echo $fio; ?></span></p>
                <?php apicoder_doctors_specialization($doctor_id); ?>
                <p><?php apicoder_excerpt(); ?></p>
                <p class="col-xs-12 text-right"><?php apicoder_doctor_city($doctor_id); ?></p>
            </div>
        </div>
    </div>
    <div class="card-footer text-right"><a href="<?php the_permalink(); ?>" class="btn btn-warning">Узнать подробнее</a></div>
</div>
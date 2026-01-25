<?php
/**
 * 
 * @param array $actions Массив действий плагина
 * @param string $plugin_file Путь к основному файлу плагина
 * @param array $plugin_data Данные плагина
 * @param string $context Контекст (active, inactive и т.д.)
 * @return array Модифицированный массив действий
 */
function apicoder_add_install_demo_link($actions, $plugin_file, $plugin_data, $context) {
    // Указываем путь к основному файлу плагина
    $main_plugin_file = PLUGIN_DIR.'apicoder-doctor-list.php';
    
    // Проверяем, что это наш плагин
    if ($plugin_file === $main_plugin_file) {
        // Создаем nonce для безопасности
        $nonce = wp_create_nonce('apicoder_install_demo');
        
        // URL для установки демо-данных
        $demo_url = admin_url('admin.php?page=apicoder-doctors-demo&action=install_demo&_wpnonce=' . $nonce);
        
        // URL для удаления демо-данных
        $remove_demo_url = admin_url('admin.php?page=apicoder-doctors-demo&action=remove_demo&_wpnonce=' . $nonce);
        
        // Проверяем, установлены ли уже демо-данные
        $demo_installed = get_option('apicoder_demo_data_installed', false);
        
        if (!$demo_installed) {
            // Добавляем ссылку "Установить демо"
            $demo_link = '<a href="' . esc_url($demo_url) . '" class="install-demo-link" style="color: #46b450; font-weight: 500;">' . 
                         '<span class="dashicons dashicons-database-add" style="vertical-align: text-bottom; margin-right: 3px;"></span>' .
                         __('Установить демо (30 записей)', 'apicoder') . '</a>';
        } else {
            // Добавляем ссылку "Удалить демо"
            $demo_link = '<a href="' . esc_url($remove_demo_url) . '" class="remove-demo-link" style="color: #dc3232; font-weight: 500;">' .
                         '<span class="dashicons dashicons-database-remove" style="vertical-align: text-bottom; margin-right: 3px;"></span>' .
                         __('Удалить демо', 'apicoder') . '</a>';
        }
        
        // Добавляем ссылку в начало массива действий
        $actions = array_merge(['demo' => $demo_link], $actions);
    }
    
    return $actions;
}
add_filter('plugin_action_links', 'apicoder_add_install_demo_link', 10, 4);

/**
 * Добавляет страницу для установки демо-данных
 */
function apicoder_add_demo_page() {
    add_submenu_page(
        null, // Не показывать в меню
        __('Установка демо-данных', 'apicoder'),
        __('Установка демо-данных', 'apicoder'),
        'manage_options',
        'apicoder-doctors-demo',
        'apicoder_handle_demo_page'
    );
}
add_action('admin_menu', 'apicoder_add_demo_page');

/**
 * Обрабатывает страницу установки демо-данных
 */
function apicoder_handle_demo_page() {
    // Проверяем права пользователя
    if (!current_user_can('manage_options')) {
        wp_die(__('У вас недостаточно прав для выполнения этого действия.', 'apicoder'));
    }
    
    // Проверяем nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'apicoder_install_demo')) {
        wp_die(
            '<h1>' . __('Ошибка безопасности', 'apicoder') . '</h1>' .
            '<p>' . __('Неверный или просроченный токен безопасности. Пожалуйста, попробуйте еще раз.', 'apicoder') . '</p>' .
            '<p><a href="' . admin_url('plugins.php') . '">' . __('Вернуться к списку плагинов', 'apicoder') . '</a></p>'
        );
    }
    
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    
    if ($action === 'install_demo') {
        apicoder_install_demo_data();
    } elseif ($action === 'remove_demo') {
        // Проверяем подтверждение удаления
        if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
            // Показываем страницу подтверждения
            ?>
            <div class="wrap">
                <h1><?php _e('Удаление демо-данных', 'apicoder'); ?></h1>
                <div class="card" style="max-width: 600px; margin-top: 20px;">
                    <h2 class="title"><?php _e('Подтверждение удаления', 'apicoder'); ?></h2>
                    <p><?php _e('Вы уверены, что хотите удалить все демо-данные? Это действие нельзя отменить.', 'apicoder'); ?></p>
                    <p><strong><?php _e('Будут удалены:', 'apicoder'); ?></strong></p>
                    <ul>
                        <li><?php _e('30 демо-записей врачей', 'apicoder'); ?></li>
                        <li><?php _e('Информация о демо-данных в базе', 'apicoder'); ?></li>
                    </ul>
                    
                    <p style="margin-top: 20px;">
                        <a href="<?php echo admin_url('admin.php?page=apicoder-doctors-demo&action=remove_demo&_wpnonce=' . $_GET['_wpnonce'] . '&confirm=yes'); ?>" 
                           class="button button-primary button-delete" style="background: #dc3232; border-color: #dc3232; color: #fff;">
                            <?php _e('Да, удалить демо-данные', 'apicoder'); ?>
                        </a>
                        <a href="<?php echo admin_url('plugins.php'); ?>" class="button">
                            <?php _e('Отмена', 'apicoder'); ?>
                        </a>
                    </p>
                </div>
            </div>
            <?php
            return;
        } else {
            apicoder_remove_demo_data();
        }
    } else {
        wp_die(__('Неверное действие.', 'apicoder'));
    }
}

/**
 * Устанавливает демо-данные (30 записей) - простая версия без AJAX
 */
function apicoder_install_demo_data() {
    // Включаем необходимые файлы WordPress для работы с постами
    require_once(ABSPATH . 'wp-admin/includes/post.php');
    require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
    
    // Массив мужских имен и фамилий
    $male_names = [
        'Иван', 'Александр', 'Сергей', 'Дмитрий', 'Андрей', 'Михаил', 'Алексей', 
        'Владимир', 'Евгений', 'Павел', 'Николай', 'Константин', 'Артем', 'Максим',
        'Олег', 'Григорий', 'Виктор', 'Юрий', 'Борис', 'Валерий'
    ];
    
    $male_surnames = [
        'Иванов', 'Петров', 'Сидоров', 'Смирнов', 'Кузнецов', 'Попов', 'Васильев',
        'Федоров', 'Михайлов', 'Новиков', 'Морозов', 'Волков', 'Соловьев', 'Лебедев',
        'Козлов', 'Зайцев', 'Ершов', 'Титов', 'Алексеев', 'Григорьев'
    ];
    
    $male_patronymics = [
        'Иванович', 'Александрович', 'Сергеевич', 'Дмитриевич', 'Андреевич',
        'Михайлович', 'Алексеевич', 'Владимирович', 'Евгеньевич', 'Павлович',
        'Николаевич', 'Константинович', 'Артемович', 'Максимович', 'Олегович'
    ];
    
    // Массив женских имен и фамилий
    $female_names = [
        'Елена', 'Ольга', 'Татьяна', 'Наталья', 'Анна', 'Мария', 'Светлана',
        'Ирина', 'Екатерина', 'Юлия', 'Анастасия', 'Виктория', 'Людмила', 'Галина',
        'Валентина', 'Любовь', 'Надежда', 'Вера', 'Маргарита', 'София'
    ];
    
    $female_surnames = [
        'Иванова', 'Петрова', 'Сидорова', 'Смирнова', 'Кузнецова', 'Попова',
        'Васильева', 'Федорова', 'Михайлова', 'Новикова', 'Морозова', 'Волкова',
        'Соловьева', 'Лебедева', 'Козлова', 'Зайцева', 'Ершова', 'Титова'
    ];
    
    $female_patronymics = [
        'Ивановна', 'Александровна', 'Сергеевна', 'Дмитриевна', 'Андреевна',
        'Михайловна', 'Алексеевна', 'Владимировна', 'Евгеньевна', 'Павловна',
        'Николаевна', 'Константиновна', 'Артемовна', 'Максимовна'
    ];
    
    // Города
    $cities = [
        'Москва', 'Санкт-Петербург', 'Новосибирск', 'Екатеринбург', 'Казань',
        'Нижний Новгород', 'Челябинск', 'Самара', 'Омск', 'Ростов-на-Дону',
        'Уфа', 'Красноярск', 'Воронеж', 'Пермь', 'Волгоград'
    ];
    
    // Специализации
    $specializations = [
        'Терапевт', 'Хирург', 'Кардиолог', 'Невролог', 'Офтальмолог',
        'Отоларинголог', 'Гинеколог', 'Уролог', 'Эндокринолог', 'Дерматолог',
        'Стоматолог', 'Педиатр', 'Онколог', 'Психиатр', 'Травматолог',
        'Гастроэнтеролог', 'Нефролог', 'Пульмонолог', 'Аллерголог', 'Ревматолог'
    ];
    
    // Описания врачей
    $descriptions = [
        'Опытный специалист с многолетней практикой. Проходил стажировку в ведущих клиниках Европы. Регулярно участвует в медицинских конференциях и симпозиумах.',
        'Врач высшей категории. Автор более 20 научных публикаций. Специализируется на сложных случаях и разработке индивидуальных программ лечения.',
        'Молодой перспективный специалист, применяющий в работе современные методики диагностики и лечения. Особое внимание уделяет профилактике заболеваний.',
        'Врач с богатым клиническим опытом. Занимается научной деятельностью, является членом нескольких профессиональных ассоциаций.',
        'Специалист, известный своим внимательным отношением к пациентам. В работе использует как классические, так и инновационные методы лечения.',
        'Врач, прошедший обучение за рубежом. Владеет передовыми технологиями диагностики и лечения. Индивидуальный подход к каждому пациенту.',
        'Специалист с уникальным опытом работы в экстренной медицине. Быстрая и точная диагностика, эффективное лечение.',
        'Врач, сочетающий в работе традиционные и современные подходы. Постоянно повышает квалификацию, следит за новинками медицинской науки.'
    ];
    
    $created_count = 0;
    
    // Начинаем создание записей
    ?>
    <div class="wrap">
        <h1><?php _e('Установка демо-данных', 'apicoder'); ?></h1>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2 class="title"><?php _e('Процесс создания 30 записей врачей', 'apicoder'); ?></h2>
            
            <div id="progress-container">
                <div id="progress-bar" style="height: 20px; background: #f1f1f1; border-radius: 3px; overflow: hidden; margin-bottom: 10px;">
                    <div id="progress" style="height: 100%; width: 0%; background: #46b450; transition: width 0.3s;"></div>
                </div>
                <div id="progress-messages" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                    <!-- Сообщения о прогрессе будут здесь -->
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            padding: 25px;
        }
        .card h2.title {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .progress-message {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .progress-message:last-child {
            border-bottom: none;
        }
        .progress-message.success {
            color: #46b450;
        }
        .progress-message.error {
            color: #dc3232;
        }
    </style>
    
    <?php
    // Создаем 30 записей
    for ($i = 1; $i <= 30; $i++) {
        // Обновляем прогресс-бар
        $progress = ($i / 30) * 100;
        echo '<script>document.getElementById("progress").style.width = "' . $progress . '%";</script>';
        echo str_pad('', 4096); // Для flush буфера
        ob_flush();
        flush();
        
        // Определяем пол
        $is_male = (rand(0, 100) > 40);
        
        if ($is_male) {
            $name = $male_names[array_rand($male_names)];
            $surname = $male_surnames[array_rand($male_surnames)];
            $patronymic = $male_patronymics[array_rand($male_patronymics)];
            $gender_text = 'мужской';
        } else {
            $name = $female_names[array_rand($female_names)];
            $surname = $female_surnames[array_rand($female_surnames)];
            $patronymic = $female_patronymics[array_rand($female_patronymics)];
            $gender_text = 'женский';
        }
        
        $full_name = "$surname $name $patronymic";
        $experience = rand(5, 30);
        $price = rand(1000, 3000);
        $rating = rand(40, 100) / 10;
        
        // Выбираем случайные города
        $post_cities = [];
        $num_cities = rand(1, min(3, count($cities)));
        $city_keys = array_rand($cities, $num_cities);
        
        if (is_array($city_keys)) {
            foreach ($city_keys as $key) {
                $post_cities[] = $cities[$key];
            }
        } else {
            $post_cities[] = $cities[$city_keys];
        }
        
        // Выбираем случайные специализации
        $post_specializations = [];
        $num_specs = rand(1, min(3, count($specializations)));
        $spec_keys = array_rand($specializations, $num_specs);
        
        if (is_array($spec_keys)) {
            foreach ($spec_keys as $key) {
                $post_specializations[] = $specializations[$key];
            }
        } else {
            $post_specializations[] = $specializations[$spec_keys];
        }
        
        // Описание врача
        $description = $descriptions[array_rand($descriptions)];
        $content = "<h3>О враче</h3>";
        $content .= "<p>Доктор $full_name — $description</p>";
        $content .= "<p><strong>Пол:</strong> $gender_text</p>";
        $content .= "<p><strong>Врачебный стаж:</strong> $experience лет</p>";
        $content .= "<p><strong>Средняя стоимость приема:</strong> от $price рублей</p>";
        $content .= "<p><strong>Рейтинг среди пациентов:</strong> $rating/10</p>";
        
        $excerpt = "Врач $full_name. Стаж: $experience лет. Специализации: " . implode(', ', $post_specializations);
        
        // Создаем пост
        $post_data = [
            'post_title'    => "$surname $name $patronymic",
            'post_content'  => $content,
            'post_excerpt'  => wp_strip_all_tags($excerpt),
            'post_status'   => 'publish',
            'post_type'     => 'doctors',
            'post_author'   => get_current_user_id(),
            'comment_status' => 'closed'
        ];
        
        $post_id = wp_insert_post($post_data, true);
        
        if ($post_id && !is_wp_error($post_id)) {
            // Добавляем произвольные поля
            update_post_meta($post_id, '_apicoder_fio', sanitize_text_field($full_name));
            update_post_meta($post_id, '_apicoder_stazh', intval($experience));
            update_post_meta($post_id, '_apicoder_cost_doc', intval($price));
            update_post_meta($post_id, '_apicoder_reiting', floatval($rating));
            
            // Добавляем города
            if (!empty($post_cities)) {
                $city_terms = [];
                foreach ($post_cities as $city_name) {
                    $city_name = sanitize_text_field($city_name);
                    $city_slug = sanitize_title($city_name);
                    
                    $term = term_exists($city_name, 'City');
                    if (!$term) {
                        $term = wp_insert_term($city_name, 'City', ['slug' => $city_slug]);
                    }
                    
                    if (!is_wp_error($term) && isset($term['term_id'])) {
                        $city_terms[] = (int)$term['term_id'];
                    }
                }
                
                if (!empty($city_terms)) {
                    wp_set_object_terms($post_id, $city_terms, 'City');
                }
            }
            
            // Добавляем специализации
            if (!empty($post_specializations)) {
                $spec_terms = [];
                foreach ($post_specializations as $spec_name) {
                    $spec_name = sanitize_text_field($spec_name);
                    $spec_slug = sanitize_title($spec_name);
                    
                    $term = term_exists($spec_name, 'Specialization');
                    if (!$term) {
                        $term = wp_insert_term($spec_name, 'Specialization', ['slug' => $spec_slug]);
                    }
                    
                    if (!is_wp_error($term) && isset($term['term_id'])) {
                        $spec_terms[] = (int)$term['term_id'];
                    }
                }
                
                if (!empty($spec_terms)) {
                    wp_set_object_terms($post_id, $spec_terms, 'Specialization');
                }
            }
            
            // Помечаем запись как демо-данные
            update_post_meta($post_id, '_demo_data', 'yes');
            
            $created_count++;
            
            // Выводим сообщение о прогрессе
            echo '<script>
                var messages = document.getElementById("progress-messages");
                messages.innerHTML += "<div class=\"progress-message success\">✓ Создан врач: ' . esc_js($full_name) . '</div>";
                messages.scrollTop = messages.scrollHeight;
            </script>';
            ob_flush();
            flush();
            
            // Небольшая пауза для стабильности
            if ($i % 5 === 0) {
                usleep(100000); // 0.1 секунды
            }
        } else {
            echo '<script>
                var messages = document.getElementById("progress-messages");
                messages.innerHTML += "<div class=\"progress-message error\">✗ Ошибка создания записи ' . $i . '</div>";
                messages.scrollTop = messages.scrollHeight;
            </script>';
            ob_flush();
            flush();
        }
    }
    
    // Сохраняем информацию об установке
    update_option('apicoder_demo_data_installed', true);
    update_option('apicoder_demo_data_count', $created_count);
    update_option('apicoder_demo_data_installed_date', current_time('mysql'));
    
    // Показываем финальное сообщение
    ?>
    <script>
        setTimeout(function() {
            var messages = document.getElementById("progress-messages");
            messages.innerHTML += "<div class=\"progress-message success\" style=\"font-weight: bold; background: #f0f9f0; padding: 10px; margin: 10px 0;\">✅ Установка завершена! Создано <?php echo $created_count; ?> записей из 30.</div>";
            messages.scrollTop = messages.scrollHeight;
            
            // Добавляем кнопки для навигации
            var buttons = '<div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">';
            buttons += '<a href="<?php echo admin_url("edit.php?post_type=doctors"); ?>" class="button button-primary">Просмотреть всех врачей</a> ';
            buttons += '<a href="<?php echo get_post_type_archive_link("doctors"); ?>" class="button" target="_blank">Посмотреть на сайте</a> ';
            buttons += '<a href="<?php echo admin_url("plugins.php"); ?>" class="button">Вернуться к плагинам</a>';
            buttons += '</div>';
            
            messages.innerHTML += buttons;
            messages.scrollTop = messages.scrollHeight;
        }, 500);
    </script>
    
    <div style="margin-top: 20px;">
        <p><strong>Процесс установки завершен.</strong> Демо-данные успешно созданы и готовы к использованию.</p>
    </div>
    <?php
}

/**
 * Удаляет демо-данные
 */
function apicoder_remove_demo_data() {
    // Находим все демо-записи
    $demo_posts = get_posts([
        'post_type' => 'doctors',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'meta_query' => [[
            'key' => '_demo_data',
            'value' => 'yes',
            'compare' => '='
        ]]
    ]);
    
    $deleted_count = 0;
    
    // Показываем процесс удаления
    ?>
    <div class="wrap">
        <h1><?php _e('Удаление демо-данных', 'apicoder'); ?></h1>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2 class="title"><?php _e('Процесс удаления', 'apicoder'); ?></h2>
            
            <div id="progress-container">
                <div id="progress-bar" style="height: 20px; background: #f1f1f1; border-radius: 3px; overflow: hidden; margin-bottom: 10px;">
                    <div id="progress" style="height: 100%; width: 0%; background: #dc3232; transition: width 0.3s;"></div>
                </div>
                <div id="progress-messages" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                    <!-- Сообщения о прогрессе будут здесь -->
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            padding: 25px;
        }
        .card h2.title {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .progress-message {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .progress-message:last-child {
            border-bottom: none;
        }
        .progress-message.success {
            color: #46b450;
        }
        .progress-message.error {
            color: #dc3232;
        }
    </style>
    
    <?php
    ob_flush();
    flush();
    
    // Удаляем посты
    $total_posts = count($demo_posts);
    $current = 0;
    
    foreach ($demo_posts as $post) {
        $current++;
        $progress = ($current / $total_posts) * 100;
        
        echo '<script>document.getElementById("progress").style.width = "' . $progress . '%";</script>';
        echo str_pad('', 4096);
        ob_flush();
        flush();
        
        if (wp_delete_post($post->ID, true)) {
            $deleted_count++;
            echo '<script>
                var messages = document.getElementById("progress-messages");
                messages.innerHTML += "<div class=\"progress-message success\">✓ Удален врач: ' . esc_js(get_post_meta($post->ID, "_apicoder_fio", true)) . '</div>";
                messages.scrollTop = messages.scrollHeight;
            </script>';
        } else {
            echo '<script>
                var messages = document.getElementById("progress-messages");
                messages.innerHTML += "<div class=\"progress-message error\">✗ Ошибка удаления записи ' . $post->ID . '</div>";
                messages.scrollTop = messages.scrollHeight;
            </script>';
        }
        
        ob_flush();
        flush();
        
        // Небольшая пауза
        if ($current % 5 === 0) {
            usleep(100000);
        }
    }
    
    // Удаляем опции
    delete_option('apicoder_demo_data_installed');
    delete_option('apicoder_demo_data_count');
    delete_option('apicoder_demo_data_installed_date');
    
    // Показываем финальное сообщение
    ?>
    <script>
        setTimeout(function() {
            var messages = document.getElementById("progress-messages");
            messages.innerHTML += "<div class=\"progress-message success\" style=\"font-weight: bold; background: #f9f0f0; padding: 10px; margin: 10px 0;\">✅ Удаление завершено! Удалено <?php echo $deleted_count; ?> записей.</div>";
            messages.scrollTop = messages.scrollHeight;
            
            // Добавляем кнопки для навигации
            var buttons = '<div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">';
            buttons += '<a href="<?php echo admin_url("edit.php?post_type=doctors"); ?>" class="button">Просмотреть оставшиеся записи</a> ';
            buttons += '<a href="<?php echo admin_url("plugins.php"); ?>" class="button button-primary">Вернуться к плагинам</a>';
            buttons += '</div>';
            
            messages.innerHTML += buttons;
            messages.scrollTop = messages.scrollHeight;
        }, 500);
    </script>
    
    <div style="margin-top: 20px;">
        <p><strong>Процесс удаления завершен.</strong> Все демо-данные были удалены из базы данных.</p>
    </div>
    <?php
    exit;
}

/**
 * Добавляет страницу настроек плагина
 */
function apicoder_add_admin_menu() {
    add_menu_page(
        __('Доктора Apicoder', 'apicoder'),
        __('Доктора', 'apicoder'),
        'manage_options',
        'apicoder-doctors',
        'apicoder_admin_page',
        'dashicons-heart',
        30
    );
}
add_action('admin_menu', 'apicoder_add_admin_menu');

/**
 * Отображает страницу админки плагина
 */
function apicoder_admin_page() {
    $demo_installed = get_option('apicoder_demo_data_installed', false);
    $demo_count = get_option('apicoder_demo_data_count', 0);
    $demo_date = get_option('apicoder_demo_data_installed_date', '');
    ?>
    <div class="wrap">
        <h1><?php _e('Доктора Apicoder - Управление', 'apicoder'); ?></h1>
        
        <div class="card">
            <h2 class="title"><?php _e('Демо-данные', 'apicoder'); ?></h2>
            <p><?php _e('Вы можете установить демо-данные для тестирования функционала плагина.', 'apicoder'); ?></p>
            
            <?php if ($demo_installed): ?>
                <div class="notice notice-info inline">
                    <p>
                        <?php printf(
                            __('Демо-данные установлены %s.', 'apicoder'), 
                            date_i18n(get_option('date_format'), strtotime($demo_date))
                        ); ?><br>
                        <?php printf(__('Записей: %d из 30', 'apicoder'), $demo_count); ?>
                    </p>
                </div>
                <p>
                    <?php 
                    $remove_nonce = wp_create_nonce('apicoder_install_demo');
                    $remove_url = admin_url('admin.php?page=apicoder-doctors-demo&action=remove_demo&_wpnonce=' . $remove_nonce);
                    ?>
                    <a href="<?php echo admin_url('edit.php?post_type=doctors'); ?>" class="button button-primary">
                        <span class="dashicons dashicons-list-view"></span>
                        <?php _e('Просмотреть записи', 'apicoder'); ?>
                    </a>
                    <a href="<?php echo esc_url($remove_url); ?>" class="button button-secondary">
                        <span class="dashicons dashicons-trash"></span>
                        <?php _e('Удалить демо-данные', 'apicoder'); ?>
                    </a>
                </p>
            <?php else: ?>
                <p>
                    <?php 
                    $install_nonce = wp_create_nonce('apicoder_install_demo');
                    $install_url = admin_url('admin.php?page=apicoder-doctors-demo&action=install_demo&_wpnonce=' . $install_nonce);
                    ?>
                    <a href="<?php echo esc_url($install_url); ?>" class="button button-primary">
                        <span class="dashicons dashicons-database-add"></span>
                        <?php _e('Установить демо-данные (30 записей)', 'apicoder'); ?>
                    </a>
                </p>
                <p class="description">
                    <?php _e('Будут созданы 30 демо-записей врачей с:', 'apicoder'); ?><br>
                    <span class="dashicons dashicons-yes"></span> <?php _e('Уникальными именами и фамилиями', 'apicoder'); ?><br>
                    <span class="dashicons dashicons-yes"></span> <?php _e('Разными специализациями и городами', 'apicoder'); ?><br>
                    <span class="dashicons dashicons-yes"></span> <?php _e('Реалистичными данными (стаж, цена, рейтинг)', 'apicoder'); ?><br>
                    <span class="dashicons dashicons-yes"></span> <?php _e('Подготовленными для работы с the_post_thumbnail()', 'apicoder'); ?>
                </p>
                
                <div class="notice notice-info inline" style="margin-top: 15px;">
                    <p>
                        <strong><?php _e('Важно:', 'apicoder'); ?></strong> 
                        <?php _e('Записи создаются без изображений. Вы можете добавить изображения вручную через медиабиблиотеку WordPress.', 'apicoder'); ?>
                        <?php _e('После добавления изображений они будут отображаться стандартной функцией.', 'apicoder'); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2 class="title"><?php _e('Статистика', 'apicoder'); ?></h2>
            <ul>
                <li>
                    <strong><?php _e('Всего врачей:', 'apicoder'); ?></strong> 
                    <?php
                    $doctors_count = wp_count_posts('doctors');
                    echo esc_html($doctors_count->publish);
                    ?>
                </li>
                <li>
                    <strong><?php _e('Городов:', 'apicoder'); ?></strong> 
                    <?php
                    $cities = get_terms(['taxonomy' => 'City', 'hide_empty' => false]);
                    echo count($cities);
                    ?>
                </li>
                <li>
                    <strong><?php _e('Специализаций:', 'apicoder'); ?></strong> 
                    <?php
                    $specializations = get_terms(['taxonomy' => 'Specialization', 'hide_empty' => false]);
                    echo count($specializations);
                    ?>
                </li>
            </ul>
        </div>
        
        <div class="card">
            <h2 class="title"><?php _e('Быстрые ссылки', 'apicoder'); ?></h2>
            <p>
                <a href="<?php echo admin_url('post-new.php?post_type=doctors'); ?>" class="button">
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e('Добавить нового врача', 'apicoder'); ?>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=doctors'); ?>" class="button">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('Все врачи', 'apicoder'); ?>
                </a>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=City&post_type=doctors'); ?>" class="button">
                    <span class="dashicons dashicons-location"></span>
                    <?php _e('Города', 'apicoder'); ?>
                </a>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=Specialization&post_type=doctors'); ?>" class="button">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php _e('Специализации', 'apicoder'); ?>
                </a>
            </p>
        </div>
        
        <?php if (!$demo_installed): ?>
        <div class="card">
            <h2 class="title"><?php _e('Как добавить изображения', 'apicoder'); ?></h2>
            <p><?php _e('После установки демо-данных вы можете добавить изображения к врачам:', 'apicoder'); ?></p>
            <ol>
                <li><?php _e('Перейдите в раздел "Все врачи"', 'apicoder'); ?></li>
                <li><?php _e('Нажмите "Изменить" на нужном враче', 'apicoder'); ?></li>
                <li><?php _e('В правой колонке найдите блок "Изображение записи"', 'apicoder'); ?></li>
                <li><?php _e('Нажмите "Установить изображение записи"', 'apicoder'); ?></li>
                <li><?php _e('Выберите или загрузите изображение', 'apicoder'); ?></li>
                <li><?php _e('После сохранения изображение будет отображаться через the_post_thumbnail()', 'apicoder'); ?></li>
            </ol>
        </div>
        <?php endif; ?>
    </div>
    
    <style>
        .card {
            margin: 20px 0;
            padding: 20px;
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .card a .dashicons {
                vertical-align: middle;
        }
        .card h2.title {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .card ul, .card ol {
            margin-left: 20px;
        }
        .card ul li, .card ol li {
            margin-bottom: 10px;
        }
        .card .description {
            color: #666;
            margin-top: 10px;
            line-height: 1.8;
        }
        .card .description .dashicons-yes {
            color: #46b450;
            margin-right: 5px;
        }
        .card .notice.inline {
            margin: 0;
            padding: 10px 15px;
        }
    </style>
    <?php
}
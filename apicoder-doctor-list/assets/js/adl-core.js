/**
 * Doctors Filter - JavaScript/jQuery
 */

jQuery(document).ready(function ($) {

    // ПРОВЕРКА: убедимся что doctorsFilterAjax определен
    if (typeof doctorsFilterAjax === 'undefined') {
        console.error('ERROR: doctorsFilterAjax не определен. Проверьте functions.php');
        return;
    }

    console.log('Doctors Filter загружен успешно');
    console.log('AJAX URL:', doctorsFilterAjax.ajaxurl);

    // Кэшируем элементы
    var $cityFilter = $('#city-filter');
    var $specializationFilter = $('#specialization-filter');
    var $doctorsContainer = $('#doctors-container');
    var $loadingIndicator = $('#loading-indicator');

    // Проверяем наличие элементов
    if ($cityFilter.length === 0 || $specializationFilter.length === 0) {
        console.error('ERROR: Фильтры не найдены на странице');
        return;
    }

    /**
     * Функция фильтрации докторов
     */
    function filterDoctors() {
        console.log('Начинаем фильтрацию...');

        // Получаем выбранные значения
        var city = $cityFilter.val();
        var specialization = $specializationFilter.val();

        console.log('Город:', city, 'Специализация:', specialization);

        // Показываем индикатор загрузки
        $loadingIndicator.fadeIn(200);
        $doctorsContainer.css('opacity', '0.5');

        // Выполняем AJAX запрос
        $.ajax({
            url: doctorsFilterAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'doctors_filter',
                nonce: doctorsFilterAjax.nonce,
                city: city,
                specialization: specialization
            },
            beforeSend: function () {
                console.log('Отправка AJAX запроса...');
            },
            success: function (response) {
                console.log('Получен ответ:', response);

                if (response.success) {
                    // Обновляем контейнер с результатами
                    $doctorsContainer.html(response.data.html);

                    // Добавляем анимацию появления
                    $doctorsContainer.css('opacity', '0').animate({
                        opacity: 1
                    }, 400);

                    // Обновляем URL без перезагрузки страницы
                    updateURL(city, specialization);

                    console.log('Фильтрация выполнена успешно. Найдено:', response.data.count);

                } else {
                    console.error('Ошибка фильтрации:', response);
                    $doctorsContainer.html('<p class="error-message">Произошла ошибка при загрузке данных.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX ошибка:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                $doctorsContainer.html('<p class="error-message">Ошибка соединения с сервером. Проверьте консоль для деталей.</p>');
            },
            complete: function () {
                console.log('AJAX запрос завершен');
                // Скрываем индикатор загрузки
                $loadingIndicator.fadeOut(200);
                $doctorsContainer.css('opacity', '1');
            }
        });
    }

    /**
     * Обновление URL без перезагрузки страницы
     * Сохраняем в state только активные фильтры (не 'all')
     */
    function updateURL(city, specialization) {
        if (history.pushState) {
            var newUrl = window.location.pathname;
            var params = [];
            var state = {};

            if (city && city !== 'all') {
                params.push('city=' + encodeURIComponent(city));
                state.city = city;
            }

            if (specialization && specialization !== 'all') {
                params.push('specialization=' + encodeURIComponent(specialization));
                state.specialization = specialization;
            }

            if (params.length > 0) {
                newUrl += '?' + params.join('&');
            }

            window.history.pushState(state, '', newUrl);
        }
    }

    /**
     * Восстановление фильтров из URL при загрузке страницы
     */
    function restoreFiltersFromURL() {
        var urlParams = new URLSearchParams(window.location.search);
        var city = urlParams.get('city');
        var specialization = urlParams.get('specialization');

        if (city) {
            $cityFilter.val(city);
        }

        if (specialization) {
            $specializationFilter.val(specialization);
        }

        // Если есть параметры в URL, применяем фильтрацию
        if (city || specialization) {
            filterDoctors();
        }
    }

    /**
     * Обработка кнопки "назад" в браузере
     */
    window.addEventListener('popstate', function (event) {
        var state = event.state || {};
        // Устанавливаем значения из state, если они есть, иначе 'all'
        $cityFilter.val(state.city || 'all');
        $specializationFilter.val(state.specialization || 'all');
        filterDoctors();
    });

    /**
     * Обработчики событий для select'ов
     */
    $cityFilter.on('change', function () {
        console.log('Изменен фильтр города');
        filterDoctors();
    });

    $specializationFilter.on('change', function () {
        console.log('Изменен фильтр специализации');
        filterDoctors();
    });

    /**
     * Кнопка сброса фильтров (опционально)
     */
    $('#reset-filters').on('click', function (e) {
        e.preventDefault();
        console.log('Сброс фильтров');
        $cityFilter.val('all');
        $specializationFilter.val('all');
        filterDoctors();
    });

    // Восстанавливаем фильтры из URL при загрузке
    restoreFiltersFromURL();

});

/**
 * Простое выравнивание карточек докторов
 */
jQuery(document).ready(function ($) {

    // Основная функция выравнивания
    function equalizeDoctorCards() {
        // Отключаем на мобильных
        if ($(window).width() < 768) {
            $('.doctor-item .card-body').css({
                'height': 'auto',
                'min-height': ''
            });
            return;
        }

        var $cards = $('.doctor-item .card-body');
        if ($cards.length < 2) return;

        var maxHeight = 0;

        // Сбрасываем высоту для корректного расчета
        $cards.css('height', 'auto');

        // Ищем максимальную высоту
        $cards.each(function () {
            var cardHeight = $(this).outerHeight();
            if (cardHeight > maxHeight) {
                maxHeight = cardHeight;
            }
        });

        // Устанавливаем высоту всем карточкам
        $cards.css('height', maxHeight + 'px');
    }

    // Запуск при загрузке
    $(window).on('load', function () {
        setTimeout(equalizeDoctorCards, 300);
    });

    // Запуск при ресайзе окна
    var resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(equalizeDoctorCards, 250);
    });

    // Запуск при AJAX загрузке
    $(document).ajaxComplete(function () {
        setTimeout(equalizeDoctorCards, 400);
    });

    // Запуск при загрузке изображений
    $('.doctor-item img').on('load', function () {
        setTimeout(equalizeDoctorCards, 200);
    });

    // Запуск при изменении фильтров (если есть фильтрация)
    $(document).on('doctorsFiltered', function () {
        setTimeout(equalizeDoctorCards, 500);
    });

});
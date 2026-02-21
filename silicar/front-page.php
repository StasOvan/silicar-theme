<?php get_header(); ?>

<div class="container">
    
    <!-- === СЛАЙДЕР (произвольный тип записи "slider") === -->
    <section class="slider-section">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php
                $slides = new WP_Query( array(
                    'post_type'      => 'slider',
                    'posts_per_page' => -1,
                    'order'          => 'ASC',
                ) );
                if ( $slides->have_posts() ) :
                    while ( $slides->have_posts() ) : $slides->the_post();
                        // Показываем только слайды с миниатюрой
                        if ( has_post_thumbnail() ) :
                            $link = get_post_meta( get_the_ID(), '_slider_link', true );
                            $img  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                ?>
                        <div class="swiper-slide">
                            <?php if ( $link ) : ?><a href="<?= esc_url( $link ); ?>"><?php endif; ?>
                                <img src="<?= esc_url( $img ); ?>" alt="<?php the_title_attribute(); ?>">
                            <?php if ( $link ) : ?></a><?php endif; ?>
                        </div>
                <?php
                        endif;
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <!--div class="swiper-pagination"></div-->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </section>
    
    <!-- === ТЕКСТОВЫЙ БЛОК (из страницы "Главная") === -->
    <section class="text-section">
        <?php
        $front_page_id = get_option( 'page_on_front' ); // Получаем ID страницы, которая назначена главной в настройках WordPress
        if ( $front_page_id ) {
            $homepage = get_post( $front_page_id );
            echo apply_filters( 'the_content', $homepage->post_content );
        }
        ?>
    </section>    

    
    
    <!-- === КАТАЛОГ ТОВАРОВ (WooCommerce) === -->

<section class="products-section" id="products">
    <h2>Каталог товаров</h2>
    <div class="search-btn">Искать по авто</div>
    <div id="search-info">
        <?php
        // Показываем информацию о применённом фильтре и ссылку для сброса
        $brand_filter = isset($_GET['brand']) ? sanitize_text_field($_GET['brand']) : '';
        $model_filter = isset($_GET['model']) ? sanitize_text_field($_GET['model']) : '';
        if (!empty($brand_filter) || !empty($model_filter)) {
            $info_parts = array();
            if (!empty($brand_filter)) {
                $info_parts[] = $brand_filter;
            }
            if (!empty($model_filter)) {
                $info_parts[] = $model_filter;
            }
            $info = 'Показаны товары для "' . implode(' ', $info_parts) . '"';
            echo '<div class="filter-badge">' . $info . '<br> <a href="/?back=true"> x (сбросить)</a></div>';
        }
        ?>
    </div>
    
    <?php
    // Получаем параметры фильтрации из GET
    $brand = isset($_GET['brand']) ? sanitize_text_field($_GET['brand']) : '';
    $model = isset($_GET['model']) ? sanitize_text_field($_GET['model']) : '';

    $tax_query = array();

    if (!empty($brand)) { // Добавляем условие по марке (тег с префиксом brand-)
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => 'brand-' . $brand );
    }

    if (!empty($model)) { // Добавляем условие по модели (тег с префиксом model-)
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => 'model-' . $model );
    }

    // Если фильтров несколько, устанавливаем отношение AND
    if (count($tax_query) > 1) 
        $tax_query['relation'] = 'AND';

    // Базовые аргументы запроса
    $args = array( 'limit'  => 12, 'status' => 'publish' );

    // Добавляем tax_query только если есть фильтры
    if (!empty($tax_query)) 
        $args['tax_query'] = $tax_query;


    $products = wc_get_products( $args );
    
    if ( !empty($products) ) {
        echo '<div class="products-grid">';
        foreach ( $products as $product ) {
            $id      = $product->get_id();
            $name    = $product->get_name();
            $price   = $product->get_price_html();
            $link    = get_permalink( $id );
            $img     = get_the_post_thumbnail_url( $id, 'medium' ) ?: wc_placeholder_img_src();
            
            // Получаем метки товара (теги)
            $terms = get_the_terms( $id, 'product_tag' );
            $tags = '';
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $tag_names = array();
                foreach ( $terms as $term ) { // Убираем префиксы для отображения
                    $temp = esc_html( $term->name );
                    $temp = str_replace(['brand-', 'model-', 'year-'], '', $temp);
                    $tag_names[] = $temp;
                }
                $tags = '<div class="product-tags">' . implode( ' ', $tag_names ) . '</div>';
            }
            ?>
            <div class="product-card">
                <a href="<?= esc_url( $link ); ?>">
                    <img src="<?= esc_url( $img ); ?>" alt="<?= esc_attr( $name ); ?>">
                    <h3><?= esc_html( $name ); ?></h3>
                    <div class="price">
                        <?= $tags; ?>
                        <?= $price; ?>
                    </div>
                </a>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<p align="center"><b>Товаров по выбранным параметрам не найдено!</b></p>';
    }
?>

</section>


    <!-- === ФОРМА ОБРАТНОЙ СВЯЗИ === -->
    <section class="contact-form-section">
        <h2>Есть вопросы?</h2>
        <p>оставьте свои данные и мы с вами свяжемся</p>
        <br>

        <form action="<?= esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
            <?php wp_nonce_field( 'contact_form', 'contact_nonce' ); ?>
            <input type="hidden" name="action" value="submit_contact_form">
            
            <p>
                <label for="name">Имя:</label>
                <input type="text" name="name" id="name">
            </p>
            <p>
                <label for="phone">Телефон:</label>
                <input type="tel" name="phone" id="phone" required>
            </p>
            <p>
                <label for="email">Eмайл:</label>
                <input type="email" name="email" id="email" required>
            </p>
            <p>
                <label for="message">Сообщение:</label>
                <textarea name="message" id="message" rows="5" required></textarea>
            </p>
            <p style="text-align: right;">
                <button type="submit">Отправить</button>
            </p>
        </form>

        <?php if ( isset( $_GET['contact_sent'] ) && $_GET['contact_sent'] == 1 ) : ?>
            <div class="alert success">Спасибо! Ваше сообщение отправлено.</div>
        <?php endif; ?>

    </section>
    
</div> <!-- .container -->



<!-- Модальное окно Выбор автомобиля -->
<div id="searchAutoModal" class="search-auto-modal">
    <div class="search-auto-modal-content">
        <span class="search-auto-close">&times;</span>
        <h2>Подбор автомобиля</h2>
        <div class="search-auto-error"></div>
        
        <label for="auto-brand-input">Марка:</label>
        <input type="text" id="auto-brand-input" class="search-auto-input" placeholder="Начните вводить марку..." autocomplete="off">
        <div id="auto-brand-suggestions" class="search-auto-suggestions"></div>
        <input type="hidden" id="auto-brand-id" value="">

        <label for="auto-model-input">Модель:</label>
        <input type="text" id="auto-model-input" class="search-auto-input" placeholder="Сначала выберите марку" autocomplete="off" disabled>
        <div id="auto-model-suggestions" class="search-auto-suggestions"></div>
        <input type="hidden" id="auto-model-id" value="">

        <label for="auto-year">Год выпуска:</label>
        <select id="auto-year" disabled>
            <option value="">Сначала выберите модель</option>
        </select>

        <button class="search-auto-button" disabled>Найти</button>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Получаем параметры из URL
    const urlParams = new URLSearchParams(window.location.search);
    
    // Проверяем наличие любых параметров, кроме 'back'
    const hasParamsExceptBack = [...urlParams.keys()].some(key => key !== 'back');
    
    // Целевой элемент для прокрутки
    const productsBlock = document.getElementById('products');
    
    // Если элемента нет на странице — прекращаем выполнение
    if (!productsBlock) return;

    // 1. Если есть параметры (brand, model, year_auto и т.д.) — прокручиваем
    if (hasParamsExceptBack) {
        smoothScrollToProducts();
        return;
    }

    // 2. Если есть параметр 'back=true' — прокручиваем
    if (urlParams.has('back') && urlParams.get('back') === 'true') {
        smoothScrollToProducts();
        return;
    }

    // 3. Если параметров нет — ничего не делаем (условие выполнено автоматически)

    // Функция плавной прокрутки
    function smoothScrollToProducts() {
        // Небольшая задержка для гарантии полной загрузки всех элементов
        setTimeout(function() {
            productsBlock.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 100); // Задержка 100 мс
    }
});
</script>

<?php get_footer(); ?>
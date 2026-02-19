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
                            <?php if ( $link ) : ?><a href="<?php echo esc_url( $link ); ?>"><?php endif; ?>
                                <img src="<?php echo esc_url( $img ); ?>" alt="<?php the_title_attribute(); ?>">
                            <?php if ( $link ) : ?></a><?php endif; ?>
                        </div>
                <?php
                        endif;
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </section>
    
    <!-- === ТЕКСТОВЫЙ БЛОК (из страницы "Главная") === -->
    <section class="text-section">
        <?php
        $homepage = get_page_by_path( 'home' );
        if ( ! $homepage ) $homepage = get_page_by_title( 'Главная' );
        echo apply_filters( 'the_content', $homepage->post_content );
        ?>
    </section>
    
    <!-- === КАТАЛОГ ТОВАРОВ (WooCommerce) === -->
    <section class="products-section">
        <h2>Каталог товаров</h2>
        <?php
        if ( class_exists( 'WooCommerce' ) ) {
            $products = wc_get_products( array(
                'limit'  => 12,
                'status' => 'publish',
            ) );
            
            if ( ! empty( $products ) ) {
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
                        foreach ( $terms as $term ) 
                            $tag_names[] = '<span class="product-tag">' . esc_html( $term->name ) . '</span>';
                        $tags = '<div class="product-tags">' . implode( ' ', $tag_names ) . '</div>';
                    }
                    ?>
                    <div class="product-card">
                        <a href="<?php echo esc_url( $link ); ?>">
                            <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $name ); ?>">
                            <h3><?php echo esc_html( $name ); ?></h3>
                            <div class="price">
                                <?php echo $tags; ?>
                                <?php echo $price; ?>
                            </div>
                        </a>
                    </div>
                    <?php
                }
                echo '</div>';
            } else {
                echo '<p>Товаров пока нет.</p>';
            }
        } else {
            echo '<p>Установите и активируйте WooCommerce.</p>';
        }
        ?>
    </section>

    <!-- === ФОРМА ОБРАТНОЙ СВЯЗИ === -->
    <section class="contact-form-section">
        <h2>Есть вопросы?</h2>
        <p>оставьте свои данные и мы с вами свяжемся</p>
        <br>

        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
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

<?php get_footer(); ?>
<?php get_header(); ?>

<div class="container">
    <?php if ( have_posts() ) : ?>
        <div class="woocommerce-products-header">
            <?php do_action( 'woocommerce_before_main_content' ); ?>
            <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
        </div>
        <?php
        woocommerce_product_loop_start();
        while ( have_posts() ) : the_post();
            wc_get_template_part( 'content', 'product' );
        endwhile;
        woocommerce_product_loop_end();
        do_action( 'woocommerce_after_main_content' );
    else :
        do_action( 'woocommerce_no_products_found' );
    endif;
    ?>
</div>

<?php get_footer(); ?>
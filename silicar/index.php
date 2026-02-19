<?php get_header(); ?>

<div class="container">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            the_title( '<h2>', '</h2>' );
            the_content();
        endwhile;
    else :
        echo '<p>Записей нет.</p>';
    endif;
    ?>
</div>

<?php get_footer(); ?>
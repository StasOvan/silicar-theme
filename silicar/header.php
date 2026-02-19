<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Подключение Font Awesome 6 (бесплатная версия) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Sofia+Sans+Extra+Condensed:ital,wght@0,1..1000;1,1..1000&display=swap" rel="stylesheet">

    <?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>


<header class="site-header">
    <div class="container">
        <div class="header-row">
            <div class="logo">
                <?php echo myshop_get_logo(); ?>
            </div>

            <div class="header-text">
                <h1>Силиконовые вставки для подстаканников&nbsp;и&nbsp;ниш</h1>
            </div>

            <div class="header-contacts">
            <!-- Соцсети с иконками Font Awesome -->
                <div class="socials">
                    <?php if ( myshop_get_telegram() ) : ?>
                        <a href="<?php echo esc_url( myshop_get_telegram() ); ?>" class="social-telegram" target="_blank">
                            <i class="fab fa-telegram-plane"></i> 
                        </a>
                    <?php endif; ?>
                    <?php if ( myshop_get_whatsapp() ) : ?>
                        <a href="<?php echo esc_url( myshop_get_whatsapp() ); ?>" class="social-whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i> 
                        </a>
                    <?php endif; ?>

                    <a class="social-max" href="https://max.ru/u/f9LHodD0cOJgSc7fVshVE57fciQJj9aYC3byXvZktOelEwz8Wd8NEIYu2lw" target="_blank">
                        <svg fill="#ffffff" viewBox="0 0 45 45" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.47 41.88c-4.11 0-6.02-.6-9.34-3-2.1 2.7-8.75 4.81-9.04 1.2 0-2.71-.6-5-1.28-7.5C1 29.5.08 26.07.08 21.1.08 9.23 9.82.3 21.36.3c11.55 0 20.6 9.37 20.6 20.91a20.6 20.6 0 0 1-20.49 20.67Zm.17-31.32c-5.62-.29-10 3.6-10.97 9.7-.8 5.05.62 11.2 1.83 11.52.58.14 2.04-1.04 2.95-1.95a10.4 10.4 0 0 0 5.08 1.81 10.7 10.7 0 0 0 11.19-9.97 10.7 10.7 0 0 0-10.08-11.1Z" </path>
                        </svg>
                    </a>
                </div>    
            <!-- Телефон -->
                <a href="tel:<?php echo esc_attr( myshop_get_phone() ); ?>" class="phone">
                    <?php echo esc_html( myshop_get_phone() ); ?>
                </a>
                
                
            </div>
        </div>
    </div>
</header>

<nav class="main-menu">
    <div class="container">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'menu_class'     => 'primary-menu',
            'container'      => false,
            'fallback_cb'    => false
        ) );
        ?>
    </div>
</nav>
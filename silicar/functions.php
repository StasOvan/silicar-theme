<?php
/**
 * Функции темы Silicar
 */

// Защита от прямого доступа
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Поддержка WooCommerce
add_action( 'after_setup_theme', 'myshop_setup' );
function myshop_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'post-thumbnails' );
}

// Дополнительная поддержка WooCommerce
add_action( 'after_setup_theme', 'myshop_woocommerce_support' );
function myshop_woocommerce_support() {
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 300,
        'single_image_width'    => 600,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 10,
            'default_columns' => 3,
            'min_columns'     => 1,
            'max_columns'     => 6,
        ),
    ) );
    
    // Включить галерею товаров (зум, слайдер, светбокс)
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}

// 2. Регистрация меню
register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'myshop' ),
    'footer'  => __( 'Footer Menu', 'myshop' ),
) );

// 3. Подключение стилей и скриптов
add_action( 'wp_enqueue_scripts', 'myshop_enqueue_assets' );
function myshop_enqueue_assets() {
    // Основные стили
    wp_enqueue_style( 'myshop-style', get_stylesheet_uri(), array(), '1.0' );
    wp_enqueue_style( 'myshop-woocommerce', get_template_directory_uri() . '/woocommerce.css', array(), '1.0' );

    // Swiper CSS JS CDN
    wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0' );
    wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0', true );

    // Стили и скрипт слайдера темы
    wp_enqueue_style( 'myshop-slider', get_template_directory_uri() . '/assets/css/slider.css', array(), '1.0' );
    wp_enqueue_script( 'myshop-slider', get_template_directory_uri() . '/assets/js/slider.js', array( 'swiper-js' ), '1.0', true );
    
}

// 4. Регистрация типа записи "Слайдер"
add_action( 'init', 'myshop_register_slider_post_type' );
function myshop_register_slider_post_type() {
    $labels = array(
        'name'               => __( 'Слайды', 'myshop' ),
        'singular_name'      => __( 'Слайд', 'myshop' ),
        'menu_name'          => __( 'Слайдер', 'myshop' ),
        'add_new'            => __( 'Добавить слайд', 'myshop' ),
    );
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-images-alt2',
        'supports'           => array( 'title', 'thumbnail', 'custom-fields' ),
        'show_in_rest'       => true,
    );
    register_post_type( 'slider', $args );
}

// 5. Добавление метабоксов для слайдера (ссылка и описание)
add_action( 'add_meta_boxes', 'myshop_slider_meta_boxes' );
function myshop_slider_meta_boxes() {
    add_meta_box(
        'slider_link',
        __( 'Ссылка слайда', 'myshop' ),
        'myshop_slider_link_callback',
        'slider',
        'normal',
        'default'
    );
}
function myshop_slider_link_callback( $post ) {
    wp_nonce_field( 'slider_link_nonce', 'slider_link_nonce' );
    $link = get_post_meta( $post->ID, '_slider_link', true );
    echo '<input type="url" name="slider_link" value="' . esc_attr( $link ) . '" style="width:100%;" />';
}
add_action( 'save_post', 'myshop_slider_save_meta' );
function myshop_slider_save_meta( $post_id ) {
    if ( ! isset( $_POST['slider_link_nonce'] ) || ! wp_verify_nonce( $_POST['slider_link_nonce'], 'slider_link_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( isset( $_POST['slider_link'] ) ) {
        update_post_meta( $post_id, '_slider_link', esc_url_raw( $_POST['slider_link'] ) );
    }
}

// 6. Настройки темы через Customizer
add_action( 'customize_register', 'myshop_customize_register' );
function myshop_customize_register( $wp_customize ) {
    // Секция настроек темы
    $wp_customize->add_section( 'myshop_settings', array(
        'title'    => __( 'Настройки магазина', 'myshop' ),
        'priority' => 30,
    ) );
    
    // Телефон
    $wp_customize->add_setting( 'shop_phone', array(
        'default'           => '+7 (999) 123-45-67',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'shop_phone', array(
        'label'    => __( 'Телефон', 'myshop' ),
        'section'  => 'myshop_settings',
        'type'     => 'text',
    ) );
    
    // Telegram
    $wp_customize->add_setting( 'shop_telegram', array(
        'default'           => 'https://t.me/username',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'shop_telegram', array(
        'label'    => __( 'Telegram ссылка', 'myshop' ),
        'section'  => 'myshop_settings',
        'type'     => 'url',
    ) );
    
    // WhatsApp
    $wp_customize->add_setting( 'shop_whatsapp', array(
        'default'           => 'https://wa.me/79991234567',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'shop_whatsapp', array(
        'label'    => __( 'WhatsApp ссылка', 'myshop' ),
        'section'  => 'myshop_settings',
        'type'     => 'url',
    ) );
    
    // Логотип (дубль, если не используется custom-logo)
    $wp_customize->add_setting( 'shop_logo', array(
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'shop_logo', array(
        'label'    => __( 'Логотип (если не выбран в настройках сайта)', 'myshop' ),
        'section'  => 'myshop_settings',
    ) ) );
    
    // Владелец (для копирайта)
    $wp_customize->add_setting( 'shop_owner', array(
        'default'           => 'ИП Иванов И.И.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'shop_owner', array(
        'label'    => __( 'Владелец', 'myshop' ),
        'section'  => 'myshop_settings',
        'type'     => 'text',
    ) );
    
    // ИНН
    $wp_customize->add_setting( 'shop_inn', array(
        'default'           => '1234567890',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'shop_inn', array(
        'label'    => __( 'ИНН', 'myshop' ),
        'section'  => 'myshop_settings',
        'type'     => 'text',
    ) );
    
    // Страница политики конфиденциальности
    $wp_customize->add_setting( 'privacy_policy_page', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'privacy_policy_page', array(
        'label'    => __( 'Страница политики конфиденциальности', 'myshop' ),
        'section'  => 'myshop_settings',
        'type'     => 'dropdown-pages',
    ) );
}

// 7. Обработка формы обратной связи
add_action( 'admin_post_nopriv_submit_contact_form', 'myshop_handle_contact_form' );
add_action( 'admin_post_submit_contact_form', 'myshop_handle_contact_form' );
function myshop_handle_contact_form() {
    if ( ! isset( $_POST['contact_nonce'] ) || ! wp_verify_nonce( $_POST['contact_nonce'], 'contact_form' ) ) {
        wp_die( 'Ошибка безопасности' );
    }

	$name  = sanitize_text_field( $_POST['name'] );
	$phone = sanitize_text_field( $_POST['phone'] );
	$email = sanitize_email( $_POST['email'] );
	$msg   = sanitize_textarea_field( $_POST['message'] );
	$body  = "	<b>Имя:</b> $name<br>
				<b>Телефон:</b> $phone<br>
				<b>Email:</b> $email<br>
				<b>Сообщение:</b><br>$msg";
	
    $to      = get_option( 'admin_email' );
    $subject = 'Сообщение с сайта от ' . $name;
    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    
    wp_mail( $to, $subject, $body, $headers );
    
    wp_redirect( add_query_arg( 'contact_sent', '1', wp_get_referer() ) );
    exit;
}

// 8. Вспомогательные функции для шаблона
function myshop_get_phone() {return get_theme_mod( 'shop_phone', '+7 (999) 123-45-67' );}
function myshop_get_telegram() {return get_theme_mod( 'shop_telegram', 'https://t.me/username' );}
function myshop_get_whatsapp() {return get_theme_mod( 'shop_whatsapp', 'https://wa.me/79991234567' );}
function myshop_get_owner() {return get_theme_mod( 'shop_owner', 'ИП Иванов И.И.' );}
function myshop_get_inn() {return get_theme_mod( 'shop_inn', '1234567890' );}
function myshop_get_privacy_page_url() {$page_id = get_theme_mod( 'privacy_policy_page', 0 ); return $page_id ? get_permalink( $page_id ) : '#';}
function myshop_get_logo() {
    if ( has_custom_logo() ) return get_custom_logo();
    // $logo_url = get_theme_mod( 'shop_logo', '' );
    // if ( $logo_url ) {
    //     return '<img src="' . esc_url( $logo_url ) . '" alt="' . get_bloginfo( 'name' ) . '" class="custom-logo">';
    // }
    // return '<h1 class="site-title">' . get_bloginfo( 'name' ) . '</h1>';
}





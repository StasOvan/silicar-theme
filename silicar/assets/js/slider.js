// assets/js/slider.js

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        new Swiper('.mySwiper', {
            // Основные параметры
            slidesPerView: 1,          // по умолчанию для мобильных
            centeredSlides: true,
            loop: true,
            initialSlide: 0,            // первый слайд становится центральным
            spaceBetween: 0,            // отступ между слайдами
            grabCursor: true,

            // Пагинация и навигация
            // pagination: {
            //     el: '.swiper-pagination',
            //     clickable: true
            // },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false
            },

            // Адаптивность: от 769px показываем 3 слайда
            breakpoints: {
                769: {
                    slidesPerView: 3
                }
            }
        });
    }
});
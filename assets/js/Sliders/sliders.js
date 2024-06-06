import Swiper from 'swiper/bundle';

const mainSlider = new Swiper('.intro-slider .swiper-container', {
    slidesPerView: 1,
    spaceBetween: 0,
    effect: 'fade',
    loop: true,
    // autoplay: {
    //     delay: 5000,
    //     disableOnInteraction: false,
    // },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
});

const productPhotosSlider = new Swiper('.reviews__photo-box', {
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        240: {
            slidesPerView: 2,
            spaceBetween: 47
        },
        465: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 4,
            spaceBetween: 40
        },
        992: {
            slidesPerView: 6,
            spaceBetween: 20
        },
        1200: {
            slidesPerView: 7
        },
    }
});

const productsSlider = new Swiper('.products-slider .swiper-container', {
    slidesPerView: 4,
    spaceBetween: 30,
    loop: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    // autoplay: {
    //     delay: 5000,
    //     disableOnInteraction: false,
    // },
    breakpoints: {
        320: {
            slidesPerView: 2,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 10,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 10,
        },
        1200: {
            slidesPerView: 4,
            // spaceBetween: 10,
        },
    }
});

const blogSlider = new Swiper('.blog-slider .swiper-container', {
    slidesPerView: 3,
    spaceBetween: 30,
    loop: true,
    // autoplay: {
    //     delay: 5000,
    //     disableOnInteraction: false,
    // },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        320: {
            slidesPerView: 2,
            spaceBetween: 10,
        },
        1200: {
            slidesPerView: 3,
            spaceBetween: 30,
        },
    }
});

const instructionSlider = new Swiper('#instruction-slider', {
    slidesPerView: 4,
    spaceBetween: 30,
    loop: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 30,
        },
        1200: {
            slidesPerView: 4,
        },
    }
});


const galleryThumbs = new Swiper('.product-slider__thumbs', {
    spaceBetween: 10,
    slidesPerView: 3,
    freeMode: true,
    watchSlidesVisibility: true,
    watchSlidesProgress: true,

});

const galleryTop = new Swiper('.product-slider__top', {
    thumbs: {
        swiper: galleryThumbs
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
});

const reviewsPageSlider = new Swiper('#reviews-latest', {
    slidesPerView: 2,
    spaceBetween: 30,
    navigation: {
        nextEl: '.reviews-slider-next',
        prevEl: '.reviews-slider-prev',
    },
    autoHeight: true,
    breakpoints: {
        240: {
            slidesPerView: 1,
            spaceBetween: 15,
        },
        769: {
            slidesPerView: 2,
            spaceBetween: 30,
        }
    }
});

const zoomBtns = document.querySelectorAll('.gallery-top__zoom-btn');

zoomBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        btn.mySwiper.zoom.in();
    });
});

// const catalogSlider = new Swiper('.cart__slider', {
//     slidesPerView: 6,
//     spaceBetween: 30,
//     navigation: {
//         nextEl: '.swiper-button-next',
//         prevEl: '.swiper-button-prev',
//     },
// });

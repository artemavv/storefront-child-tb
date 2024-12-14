<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

/*if(is_home() || is_front_page()){
?>

<link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script>
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
</script>

<!-- Review Gallery Section Started Here -->
        <?php 
        $args = array(
                'number' => 1000,
                'status' => 'approve',
                'post_status' => 'publish',
                'post_type' => 'product',
                'order' => 'DESC',
                'orderby' => 'comment_id'
            );

            $comments = $wpdb->get_results("SELECT * 
              FROM wp_comments 
              WHERE comment_type = 'review' AND comment_approved = 1");
              
            $comments = array_values(array_combine(
                array_column($comments, 'comment_content'),
                $comments
            ));

            $commentMeta = $wpdb->get_results("SELECT * 
              FROM wp_commentmeta 
              WHERE meta_key = 'reviews-images'");

            $args = array(
                'number' => 50,
                'status' => 'approve',
                'post_status' => 'publish',
                'post_type' => 'product',
                'order' => 'DESC',
                'orderby' => 'comment_id'
            );

            $commentsList = get_comments($args);

            $allReviewsImages = [];

            foreach ($comments as $comment) {
                $commentId = $comment->comment_ID;
                $commentReviewsImagesMeta = get_comment_meta($commentId, 'reviews-images', false);

                if (count($commentReviewsImagesMeta) > 0) {
                    foreach ($commentReviewsImagesMeta as $postId) {
                        $allReviewsImages[] = get_post_meta($postId[0], '_wp_attached_file');
                    }
                }
            }
        ?>
        <div class="reviews_container">
            <div class="swiper-button-prev">
                <svg class="icon">
                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#arrow-left"></use>
                </svg>
            </div>

            <div class="reviews__photo">
                <h3 class="reviews__photo-title">Buyer photos</h3>
                <div class="reviews__photo-box">

                    <div class="reviews__photo-inner swiper-wrapper">
                        <?php foreach ($allReviewsImages as $allReviewsImage) : ?>
                            <div class="reviews__photo-item swiper-slide">
                                <a href="/wp-content/uploads/<?php echo $allReviewsImage[0]; ?>" target="blank">
                                    <img class="alModalTrigger"
                                         src="/wp-content/uploads/<?php echo $allReviewsImage[0]; ?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>


            <div class="swiper-button-next">
                <svg class="icon">
                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#arrow-right"></use>
                </svg>
            </div>
        </div>
        <!-- Review Gallery Section Ended Here -->
<?php 
/*} */
?>

<script>
	jQuery( function( $ ) {
    // Цепляемся за событие adding_to_cart
    $('.menu-btn__icon').on('click', function () {
        $('.mobile-menu').toggleClass('active')
        $('.menu-icon').toggleClass('active')
        $('.menu-btn__text').toggleClass('active')
    
        if($('.menu-btn__text').hasClass('active')) {
            $('.menu-btn__text').text('Close')
        } else {
            $('.menu-btn__text').text('Menu')
        }
    })

        $('.product__select').on('change', function(){
            window.location = $(this).val();
        });
    
    $( document.body ).on( 'product-card__btn_add-cart', function( event, button ) {
        // Выцепляем инициатора события (ссылка/кнопка)
        var $btn = $( button[0] );
 
        // Пытаемся найти в вёрстке название товара
        var product_title = $btn.parents( 'li.product' ).find( '.woocommerce-loop-product__title' ).text();
 
        if ( product_title ) {
            // Формируем шаблон попапа
            var tpl = '';
            tpl += '<p>Товар "' + product_title + '" добавлен в корзину</p>';
            tpl += '<div>';
            tpl += '<a class="button" onclick="jQuery.unblockUI();">Продолжить</a>';
            tpl += '<a href="/shop/cart/" class="button alt">Оформить</a>';
            tpl += '</div>';
 
            // Выводим шаблон в модальное окно.
            // Используем blockUI из WooCommerce
            $.blockUI({
                message: tpl,
                timeout: 4000,
                css: {
                    width: '300px',
                    border: 0,
                    padding: 30
                }
            } );
        }
    } );
} );
</script>
<footer class="footer">
    <div class="footer__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <nav class="footer-menu">
                        <ul class="footer-menu__list">
						<?php if( have_rows('top-menu', 'option') ): ?>
                            <?php while( have_rows('top-menu', 'option') ): the_row(); 
                            $name = get_sub_field('link_name');
                            $link = get_sub_field('link_menu');
                        
                        ?>
                            <li>
                                <a href="<?php echo $link; ?>"><?php echo $name; ?></a>
                               
                            </li>
							 <?php endwhile; ?>  
               <?php endif; ?>
                          
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-2">
                    <a class="logo footer__logo" href="/">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#logo"></use>
                        </svg>
                    </a>
                </div>
                <div class="col-lg-5">
                    <div class="footer__contacts">
                        <div class="footer__inner">
                            <div class="footer__work">10:00-22:00</div>
                            <div class="footer__address"><?php the_field('adress', 'option'); ?></div>
                        </div>
                        <div class="footer__inner">
                            <a class="footer__link" href="mailto:<?php the_field('email', 'option'); ?>">
                                <svg class="icon">
                                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#mail"></use>
                                </svg>
                                <?php the_field('email', 'option'); ?>
                            </a>
                            <a class="footer__link" href="tel:<?php the_field('phone', 'option'); ?>">
                                <svg class="icon">
                                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#phone"></use>
                                </svg>
                               <?php the_field('phone', 'option'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--     <div class="footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer__info">
                        <span>ИП Васильев</span>
                        <span>ИНН 110209963700</span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer__social">
                        <h5 class="footer__social-title">Мы в социальных сетях</h5>
                        <div>
                            <a class="footer__social-link" href="<?php the_field('vk_link', 'option'); ?>">
                                <svg class="icon">
                                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#vk"></use>
                                </svg>
                            </a>
                            <a class="footer__social-link" href="<?php the_field('inst_link', 'option'); ?>">
                                <svg class="icon">
                                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#instagram"></use>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer__dev">
                        <a href="https://richbee.ru/">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets//svg/sprite/sprite.svg#richbee"></use>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</footer>


   


<?php wp_footer(); ?>
<script>
    jQuery(document).ready(function(){
        console.log("hello");
        jQuery(".woocommerce-billing-fields").find("h3").html("Shipping Details");
        
    });
</script>
<script>
    if(jQuery("label[for=payment_method_stripe_applepay]").length){

        jQuery("label[for=payment_method_stripe_applepay]").find("img").attr("src","<?php echo get_template_directory_uri(); ?>/assets/images/apple-pay.jpg");
    }
</script>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/Main/dropdown.js"></script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(88676318, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/88676318" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->



</body>

</html>

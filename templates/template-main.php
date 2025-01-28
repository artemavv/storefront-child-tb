<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Main Page
 *
 * @package storefront
 */

get_header(); ?>
<main id="content" role="main">

 <div class="intro">
        <div class="intro__inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="intro__content">
                            <h1 class="intro__title">
                               <?php the_field('main_header'); ?>
                            </h1>
                            <h2 class="intro__subtitle">
                                <?php the_field('header_sub_text'); ?>
                            </h2>
                            <a class="btn btn-outline-primary intro__btn"
                               href='<?php the_field('link'); ?>'>
                               <?php the_field('link_text'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="intro-slider">
                          <div class="intro-slider__overlay">
                              <div class="intro-slider__overlay-circle"></div>
                              <img class="intro-slider__overlay-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/intro-slider/decor.png" alt="">
                          </div>
                          
                          <?php if (get_field('main_product')) : ?>
                           
                          <a href="<?php echo get_permalink(get_field('main_product')); ?>">
                              <div class="intro-slider__item">
                                  <img class="intro-slider__item-img" src="https://tannybunny.com/wp-content/uploads/2024/04/Kitsune.webp<?php /* the_field('main_product_img'); */ ?>" alt="">
                                  <div class="intro-slider__item-content">
                                      <h3 class="intro-slider__item-title">
                                          <?php echo get_the_title( get_field('main_product') ); ?>
                                      </h3>
                                      <h4 class="intro-slider__item-subtitle">
                                          <?php $terms = get_the_terms(get_field('main_product'), 'product_cat');
                                          echo $terms[0]->name; ?>
                                      </h4>
                                      <div class="intro-slider__item-price">
                                          <?php
                                          // $price = number_format((float)$product->get_variation_price( 'min', true ), 2, '.', '');
                                          // echo $price.''.get_woocommerce_currency_symbol();  

                                            $price = get_post_meta( get_field('main_product'), '_price', true);
                                            $price_fm = do_shortcode('[woo_multi_currency_exchange price="' . $price . '" ]');

                                            echo $price_fm;
                                          ?>
                                      </div>
                                  </div>
                              </div>
                          </a>
                          
                          <?php endif; ?>
                        </div>
                    </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
	
	<div class="gallery">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <a href="<?php the_field('left_block_link'); ?>" class="gallery__item gallery__item_left">
                        <div class="gallery__item-img">
						<?php if( get_field('left_block_img') ): ?>
                            <img src="<?php the_field('left_block_img'); ?>" alt="">
							<?php endif; ?>
                        </div>
                        <div class="gallery__item-content">
                            <h3 class="gallery__item-title"><?php the_field('left_block_title'); ?></h3>
                            <h4 class="gallery__item-subtitle"><?php the_field('left_block_subtitle'); ?></h4>
                            <div class="gallery__item-more">
                                <?php the_field('left_block_link_text'); ?>
                                <svg class="icon">
                                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>		
                <?php 
$loop = new WP_Query( array( 
  'post_type' => 'product', 
  'posts_per_page' => 4,
  'orderby' => 'menu_order', 
  'order' => 'ASC',
  )); 
while ( $loop->have_posts() ): $loop->the_post(); ?>
                                     <div class="col-lg-3 mb-3 d-none d-lg-block">
                       				
<a href="<?php the_permalink(); ?>" class="gallery__card">
                            <div class="gallery__card-img">
                              <img src="<?php echo (get_field('product_cover') ? get_field('product_cover') : get_the_post_thumbnail_url()); ?>" "/>
                            </div>
                            <div class="gallery__innerDesc">
                              <h3 class="gallery__card-title"><?php 
                                $countSymbol = iconv_strlen(get_the_title());
                                
                                if ($countSymbol < 67) {
                                  echo get_the_title();
                                } else {
                                  echo mb_substr(get_the_title(),0,65, 'UTF-8') . '...'; 
                                }
                              ?></h3>
                              <h4 class="gallery__card-subtitle"><?php $terms = get_the_terms($product->get_id(), 'product_cat');
                                echo $terms[0]->name; ?></h4>
                              <div class="gallery__card-price">
                                <?php 
                                                    
                                    $price = round($product->get_variation_price('min', true));
                                    $wmc = WOOMULTI_CURRENCY_Data::get_ins();

                                    $currency = $wmc->get_current_currency();

                                    $selected_currencies = $wmc->get_list_currencies();

                                    if ( $currency && isset( $selected_currencies[ $currency ] ) && is_array( $selected_currencies[ $currency ] ) ) {
                                        $data   = $selected_currencies[ $currency ];
                                        $format = WOOMULTI_CURRENCY_Data::get_price_format( $data['pos'] );
                                        $args   = array(
                                            'currency'     => $currency,
                                            'price_format' => $format
                                        );
                                        if ( isset( $data['decimals'] ) ) {
                                            $args['decimals'] = absint( $data['decimals'] );
                                        }

                                        $price_fm = wc_price($price, $args);
                                    }
                                    else {
                                        $price_fm = wc_price($price);
                                    }
                                    
                                    //$price_fm =  do_shortcode('[woo_multi_currency_exchange price="' . $price . '" ]');
                                    echo $price_fm;
                                    ?>
                              </div>
                              <div class="gallery__card-more">Read more</div>
                            </div>
                        </a>

                    </div>
					<?php endwhile; ?>
				<?php wp_reset_query(); // Remember to reset
   ?>
                                   
                                   
                                   
                                <div class="col-lg-6 mb-3">
                    <a href="<?php the_field('right_block_link'); ?>" class="gallery__item gallery__item_right">
                        <div class="gallery__item-content">
                            <h3 class="gallery__item-title"><?php the_field('right_block_title'); ?></h3>
                            <h4 class="gallery__item-subtitle"><?php the_field('right_block_subtitle'); ?></h4>
                            <div class="gallery__item-more">
                                <svg class="icon">
                                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#arrow-left"></use>
                                </svg>
                                <?php the_field('right_block_link_text'); ?>
                            </div>
                        </div>
                        <div class="gallery__item-img">
						<?php if( get_field('right_block_img') ): ?>
                            <img src="<?php the_field('right_block_img'); ?>" alt="">
							<?php endif; ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>


</div>


<?php wp_reset_query(); // Remember to reset
   ?>


<!-- 

   <div class="products-slider">
        <div class="container">
            <h2 class="title">Popular ear cuffs</h2>
            <div class="products-slider__wrapper">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                      
                      <?php if ( have_rows( 'popular_cuffs' ) ) : ?>
                          <?php while ( have_rows( 'popular_cuffs' ) ) : the_row(); 
                              $name = get_the_title( get_sub_field( 'cuff' ) );
                          ?>
                              <div class="product-card">
                                  <div class="product-card__inner">
                                
                                      <a href="https://tannybunny.com/product/test-cuff/" class="product-card__img">
                                          <img width="1588" height="2382" src="https://tannybunny.com/wp-content/uploads/2022/03/il_1588xN.2469443402_jgr2.jpg" class="attachment-thumbnail-215x300 size-thumbnail-215x300 wp-post-image" alt="" srcset="https://tannybunny.com/wp-content/uploads/2022/03/il_1588xN.2469443402_jgr2.jpg 1588w, https://tannybunny.com/wp-content/uploads/2022/03/il_1588xN.2469443402_jgr2-600x900.jpg 600w, https://tannybunny.com/wp-content/uploads/2022/03/il_1588xN.2469443402_jgr2-200x300.jpg 200w, https://tannybunny.com/wp-content/uploads/2022/03/il_1588xN.2469443402_jgr2-683x1024.jpg 683w, https://tannybunny.com/wp-content/uploads/2022/03/il_1588xN.2469443402_jgr2-768x1152.jpg 768w, https://tannybunny.com/wp-content/uploads/2022/03/il_1588xN.2469443402_jgr2-1024x1536.jpg 1024w, https://tannybunny.com/wp-content/uploads/2022/03/il_1588xN.2469443402_jgr2-1365x2048.jpg 1365w" sizes="(max-width: 1588px) 100vw, 1588px">            <div class="product-card__done-img">
                                              <svg class="product-card__done-svg">
                                                  <use xlink:href="/svg/sprite/sprite.svg#done"></use>
                                              </svg>
                                          </div>
                                      </a>
                                      <div class="product-card__innerDesc">
                                          <h4 class="product-card__title"><?php echo $name; ?></h4>
                                          <p class="product-card__descr">Каффы ручной работы</p>
                                          <ul class="product-card__list">
                                              <li class="product-card__list-item">
                                                  Материал: <span>Silver</span>
                                              </li>
                                              <li class="product-card__list-item">
                                                  Покрытие: <span>Left ear, Pair, Right ear</span>
                                              </li>
                                          </ul>
                                          <div class="product-card__price">58$</div>
                                      </div>
                                             <button class="product-card__btn product-card__btn_add-favorite add-to-favorites" data-id="1518" data-action="add" data-page="catalog">
                                           <a class="tinvwl_add_to_wishlist_button tinvwl-icon-heart  tinvwl-position-after ftinvwl-animated" data-tinv-wl-list="[]" data-tinv-wl-product="{{ data.post_id }}" data-tinv-wl-producttype="{{ data.product_type }}" data-tinv-wl-action=""><svg class="icon">
                                    
                                              <use xlink:href="https://tannybunny.com/wp-content/themes/storefront-child/assets/svg/sprite/sprite.svg#heart"></use>
                                          </svg></a>
                                      </button>
                                               <div class="product-add__box-btn-wrap" id="product-add-to-cart__box-1518"><a href="https://tannybunny.com/product/test-cuff/">
                                                          <button class="product-card__btn product-card__btn_add-cart btn btn-outline-primary" data-id="1518" data-quantity="1" data-page="catalog" data-variation="" data-material="79">
                                              <svg class="icon">
                                                  <use xlink:href="https://tannybunny.com/wp-content/themes/storefront-child/assets/svg/sprite/sprite.svg#cart"></use>
                                              </svg>
                                          </button></a>
                                          </div>
                               
                                  </div>
                              </div>
                          <?php endwhile; ?>
                      <?php endif; ?>
   
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <a href="/shop" class="btn btn-outline-primary products-slider__btn">Show all</a>
        </div>
    </div>

 -->




<!--    <div class="idea">
        <div class="container">
            <h2 class="title">Idea for gifts</h2>
            <div class="row">
			<?php if( have_rows('gifts') ): ?>
			<?php while( have_rows('gifts') ): the_row(); 
			$image = get_sub_field('gift_img');
			$name = get_sub_field('gift_name');
			$link = get_sub_field('gift_link');

		?>
                <div class="col-lg-3 col-6 pr-1 pr-sm-3">
				<?php if( $link ): ?>
                    <a class="idea__item" href="<?php echo $link; ?>">
			<?php endif; ?>
						<img class="idea__item-img" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt'] ?>" />
                        <h4 class="idea__item-title"><?php echo $name; ?></h4>
                    <?php if( $link ): ?>
				</a>
			<?php endif; ?>
                </div>
               <?php endwhile; ?>
               
               <?php endif; ?>
			   
            </div>
        </div>
    </div> -->
    
    
 <div class="about">
        <div class="container">
            <h2 class="title">About us</h2>
            <div class="row">
                <div class="col-lg-5 order-lg-1 order-2">
                    <div class="about__item">
					
<?php if( get_field('author_img') ): ?>
                <img  src="<?php the_field('author_img'); ?>" >
				<?php endif; ?>
					</div>
                </div>
                <div class="col-lg-7 order-lg-2 order-1">
                 <?php the_field('author_text'); ?>
                </div>
            </div>
        </div>
    </div>
	
	
	
	 <div class="contacts">
        <div class="container">
            <h2 class="title">Contacts</h2>
            <div class="contacts__inner">
                <div class="contacts__info">
                    <div class="contacts__info-item">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#map"></use>
                        </svg>
                        <h4 class="contacts__info-title">Address</h4>
                        <div class="contacts__info-text"><?php the_field('adress', 'option'); ?></div>
                    </div>
                    <div class="contacts__info-item">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#phone"></use>
                        </svg>
                        <h4 class="contacts__info-title">Phone</h4>
                        <a class="contacts__info-link" href="tel:<?php the_field('phone', 'option'); ?>"><?php the_field('phone', 'option'); ?></a>
                    </div>
                    <div class="contacts__info-item">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#mail"></use>
                        </svg>
                        <h4 class="contacts__info-title">E-mail</h4>
                        <a class="contacts__info-link"
                           href="mailto:<?php the_field('adress', 'option'); ?>"><?php the_field('email', 'option'); ?></a>
                    </div>
                </div>

                <div class="contacts__map">
                    <?php the_field('map', 'option'); ?>
                </div>
            </div>
        </div>
    </div>
	

<?php
get_footer();


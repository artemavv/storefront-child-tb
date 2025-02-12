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
                          
													<?php $main_product_id = get_field('main_product'); ?>
														
                          <?php if ( $main_product_id ) : ?>
                          
														<?php $product_title = esc_attr(get_the_title( $main_product_id )); ?>
														<a href="<?php echo get_permalink($main_product_id); ?>">
																<div class="intro-slider__item">
																		<img class="intro-slider__item-img" alt="<?php echo $product_title; ?>" src="<?php echo the_field('main_product_img'); ?>" >
																		<div class="intro-slider__item-content">
																				<h3 class="intro-slider__item-title">
																						<?php echo $product_title; ?>
																				</h3>
																				<h4 class="intro-slider__item-subtitle">
																						<?php $terms = get_the_terms( $main_product_id, 'product_cat');
																						echo $terms[0]->name; ?>
																				</h4>
																				<div class="intro-slider__item-price">
																						<?php
																						// $price = number_format((float)$product->get_variation_price( 'min', true ), 2, '.', '');
																						// echo $price.''.get_woocommerce_currency_symbol();  

																							$price = get_post_meta( $main_product_id, '_price', true);
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
																	<img src="<?php the_field('left_block_img'); ?>" alt="<?php the_field('left_block_title'); ?>">
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
								
								
												<?php $product_title = get_the_title(); ?>
                         <div class="col-lg-3 mb-3 d-none d-lg-block">
                       				
														<a href="<?php the_permalink(); ?>" class="gallery__card">
                            <div class="gallery__card-img">
                              <img src="<?php echo (get_field('product_cover') ? get_field('product_cover') : get_the_post_thumbnail_url()); ?>" alt="<?php echo $product_title; ?>"/>
                            </div>
                            <div class="gallery__innerDesc">
                              <h3 class="gallery__card-title"><?php 
                                $countSymbol = iconv_strlen($product_title);
                                
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
                            <img src="<?php the_field('right_block_img'); ?>" alt="<?php the_field('right_block_title'); ?>">
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

    
 <div class="about">
        <div class="container">
            <h2 class="title">About us</h2>
            <div class="row">
                <div class="col-lg-5 order-lg-1 order-2">
                    <div class="about__item">
					
<?php if( get_field('author_img') ): ?>
                <img  src="<?php the_field('author_img'); ?>" alt="We are Tanya and Stephan, jewelry artists who devoted themselves to making ear cuffs." >
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
                           href="mailto:<?php the_field('email', 'option'); ?>"><?php the_field('email', 'option'); ?></a>
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


<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

// получаем продуктовые теги
$tags = get_terms( 'product_tag' );
?>


<main id="content" role="main">

   
    <div class="catalog">
            <div class="container">

                <div id="breadcrumbs" class="breadcrumbs">
                    <ul class="breadcrumbs__list">
                        <li class="breadcrumbs__list-item">
                            <a href="/">Main</a>
                        </li>
                        <li class="breadcrumbs__list-item">
                            <a href="/shop">Shop</a>
                        </li>
                    </ul>
                </div>
 

                <div class="tabs catalog-tabs">

										<?php echo do_shortcode('[tannybunny_warehouse_filter]'); ?> 
										
                    <div class="tabs__content catalog-tabs__content">
                        <div class="tabs__content-item catalog-tabs__content-item active">
                            <div class="row">
                              <div class="col-lg-3">  
                                  <div class="for_mobile">
                                    <div class="for_mobile1">
                                        <div class="desktop">
                                            <?php echo do_shortcode('[wpf-filters id=1]') ?> 
                                        </div>
                                        <div class="mobile">
                                            <div id="mySidenav" class="sidenav">
                                              <?php /*echo do_shortcode('[wpf-filters id=5]');*/ ?>
                                              <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times; Close</a>
                                              <?php echo do_shortcode('[wpf-filters id=4]') ?> 
                                            </div>

                                            <div id="main">

                                              <span style="font-size:16px;cursor:pointer" onclick="openNav()">&#9776; Filter</span>
                                            </div>
                                            <script>
                                            function openNav() {
                                              document.getElementById("mySidenav").style.width = "67%";
                                              document.getElementById("main").style.marginLeft = "100%";
                                            }

                                            function closeNav() {
                                              document.getElementById("mySidenav").style.width = "0";
                                              document.getElementById("main").style.marginLeft= "0";
                                            }
                                            </script>                                           
                                        </div>
                                        
                                    </div>
                                    <div class="for_mobile2">
                                        <?php echo do_shortcode('[wpf-filters id=5]') ?> 
                                     
                                    </div>                                    
                                  </div>
                                  
                              </div>
                                <div class="col-lg-9">
                                    
                                    <?php if (woocommerce_page_title(false) !== 'Shop') : ?>
                                    <div class="catalog-banner">
                                        <h1 class="catalog-banner__title"><?php woocommerce_page_title(); ?></h1>
                                        <div class="catalog-banner__text">
                                            <?php woocommerce_taxonomy_archive_description(); ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="products products-list">
                                       
                                       <div class="row">
                                <?php
                                if ( woocommerce_product_loop() ) {

                                    /**
                                     * Hook: woocommerce_before_shop_loop.
                                     *
                                     * @hooked woocommerce_output_all_notices - 10
                                     * @hooked woocommerce_result_count - 20
                                     * @hooked woocommerce_catalog_ordering - 30
                                     */


                                    woocommerce_product_loop_start();

                                    if ( wc_get_loop_prop( 'total' ) ) {
                                        while ( have_posts() ) {
                                            the_post();

                                            /**
                                             * Hook: woocommerce_shop_loop.
                                             */
                                            do_action( 'woocommerce_shop_loop' );

                                            wc_get_template_part( 'content', 'product' );
                                        }
                                    }

                                    woocommerce_product_loop_end();

                                    /**
                                     * Hook: woocommerce_after_shop_loop.
                                     *
                                     * @hooked woocommerce_pagination - 10
                                     */

                                } else {
                                    /**
                                     * Hook: woocommerce_no_products_found.
                                     *
                                     * @hooked wc_no_products_found - 10
                                     */

                                }

                                /**
                                 * Hook: woocommerce_after_main_content.
                                 *
                                 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
                                 */


                                /**
                                 * Hook: woocommerce_sidebar.
                                 *
                                 * @hooked woocommerce_get_sidebar - 10
                                 */
                                ?>
                                        </div>
                                        <nav>
        <ul class="pagination">
            
<!--                             <li class="page-item disabled pagination-mobile-hide" aria-disabled="true" aria-label="« Previous">
                    <span class="page-link" aria-hidden="true">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#arrow-left"></use>
                        </svg>
                    </span>
                </li> -->
                        
                                        
                           
                           
                           
                                                            
                    <?php the_posts_pagination(); ?>
            

            
<!--                             <li class="page-item pagination-mobile-hide">
                    <a class="page-link" href="https://tannybunny.dev2tech.ru/catalog?page=2" rel="next" aria-label="Next »">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#arrow-right"></use>
                        </svg>
                    </a>
                </li> -->
                    </ul>
    </nav>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  
</main>
<?php
get_footer();


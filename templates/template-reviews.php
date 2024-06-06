<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: reviews
 *
 * @package storefront
 */

get_header();

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
    <link
            rel="stylesheet"
            href="https://unpkg.com/swiper@8/swiper-bundle.min.css"
    />
    <main id="content" role="main" class="delivery-page">


        <div class="container">


            <h2 class="title" style="margin-top: 50px;">
                All reviews <span class="reviews-counter"><?php echo count($comments); ?></span>
            </h2>

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
                                    <!-- <a href="/wp-content/uploads/<?php echo $allReviewsImage[0]; ?>" target="blank"> -->
                                        <img class="myBtn alModalTrigger swiper-lazy-modal"
                                             src="/wp-content/uploads/<?php echo $allReviewsImage[0]; ?>">
                                   <!--  </a> -->
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

            <div class='reviews-container'>

                <?php foreach ($commentsList as $comment) :

                    $product_id = $comment->comment_post_ID;
                    $_product = wc_get_product($product_id);

                    $material = $_product->get_attribute('materials');

                    $name = get_the_title($product_id);
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'single-post-thumbnail');
                    ?>


                    <div class="reviews-page__item">
                        <div class="reviews-page__item-inner">
                            <div class="reviews-page__item-product">
                                <a href="<?php echo $_product->get_permalink(); ?>">
                                    <img class="lkreviews__product-img" src="<?php echo $image[0]; ?>"
                                         alt="<?php echo $name; ?>"
                                         title="<?php echo $name; ?>">
                                </a>
                                <div class="lkreviews__product-info">
                                    <div class="lkreviews__product-title"><a
                                                href="<?php echo $_product->get_permalink(); ?>"><?php echo $name; ?></a>
                                    </div>
                                    <ul class="lkreviews__product-list">
                                        <li class="lkreviews__product-listitem">
                                            Materail: <span>
                                        <?php echo($material ? $material : 'N/A'); ?>
                                    </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="lkreviews__message">
                                <div class="lkreviews__message-date"><?php echo $comment->comment_date_gmt; ?></div>
                                <p class="lkreviews__message-text"><?php
                                    echo stripslashes($comment->comment_content);
                                    ?></p>
                                <div class="reviews__item-footer">
                                    <?php
                                    $allReviewsImages = [];
                                    $commentReviewsImagesMeta = get_comment_meta($comment->comment_ID, 'reviews-images', false);

                                    if (count($commentReviewsImagesMeta) > 0) {
                                        foreach ($commentReviewsImagesMeta as $postId) {
                                            $allReviewsImages[] = get_post_meta($postId[0], '_wp_attached_file');
                                        }
                                    }

                                if (is_array($allReviewsImages)) :
                                    if (is_countable($allReviewsImages)) :
                                    if (count(array($allReviewsImages))) :
                                        foreach ($allReviewsImages as $allReviewsImage) :

                                            ?>
                                            <div class="reviews__item-photo">
                                                <a href="/wp-content/uploads/<?php echo $allReviewsImage[0]; ?>"
                                                   target="blank">
                                                    <img class="alModalTrigger"
                                                         src="/wp-content/uploads/<?php echo $allReviewsImage[0]; ?>">
                                                </a>
                                            </div>
                                        <?php endforeach; endif; endif; endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content review-image">
    <span class="close">&times;</span>
   <img class="review-img alModalTrigger swiper-lazy-modal"
             src="/wp-content/uploads/<?php echo $allReviewsImage[0]; ?>">
  </div>

</div>
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

        /* Modal script here */
        // Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementsByClassName("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
jQuery(".myBtn").on("click",function(){
  console.log("hi");
  var src = jQuery(this).attr('src');
  console.log(src);
  jQuery(".review-img").attr('src',src);
  jQuery("#myModal").show();
  jQuery("html").addClass("hideoverflow");
});

  

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
  document.getElementsByTagName("html").classList.remove("hideoverflow");
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
    document.getElementsByTagName("html").classList.remove("hideoverflow");
  }
}
    </script>


<?php
get_footer();


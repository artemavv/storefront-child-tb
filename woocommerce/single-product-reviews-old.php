<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.3.0
 */

defined('ABSPATH') || exit;

global $product;

if (!comments_open()) {
    return;
}

$args2 = array(
    'post_type' => 'product',
    'type' => 'review',
    'post_id' => $product->id,  // Product Id
    'status' => 'approve', // Status you can also use 'hold', 'spam', 'trash',
    'number' => 1000  // Number of comment you want to fetch I want latest approved post soi have use 1
);

$commentsProduct = get_comments($args2);

$allReviewsImages = [];

foreach ($commentsProduct as $commentProduct) {
    $commentId = $commentProduct->comment_ID;
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
<div id="reviews" class="woocommerce-Reviews">
    <div id="comments">
        <h2 class="reviews__title">Reviews 
        <?php 
            $count = $product->get_review_count();
            if($count>0){
            ?>
            <span><?php echo $count; ?></span>
            <?php
            }
        ?>
        </h2>

        <?php if (count($allReviewsImages) > 0) : ?>
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
        <?php endif; ?>

        <?php if (have_comments()) : ?>
            <ol class="commentlist">
                <?php wp_list_comments(apply_filters('woocommerce_product_review_list_args', array('callback' => 'woocommerce_comments'))); ?>
            </ol>

            <?php
            if (get_comment_pages_count() > 1 && get_option('page_comments')) :
                echo '<nav class="woocommerce-pagination">';
                paginate_comments_links(
                    apply_filters(
                        'woocommerce_comment_pagination_args',
                        array(
                            'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
                            'next_text' => is_rtl() ? '&larr;' : '&rarr;',
                            'type' => 'list',
                        )
                    )
                );
                echo '</nav>';
            endif;
            ?>
        <?php else : ?>
            <p class="woocommerce-noreviews"><?php esc_html_e('There are no reviews yet.', 'woocommerce'); ?></p>
        <?php endif; ?>
    </div>

    <?php if (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id())) : ?>
        <div id="review_form_wrapper">
            <div id="review_form">
                <?php
                $commenter = wp_get_current_commenter();
                $comment_form = array(
                    /* translators: %s is product title */
                    'title_reply' => have_comments() ? esc_html__('Add a review', 'woocommerce') : sprintf(esc_html__('Be the first to review &ldquo;%s&rdquo;', 'woocommerce'), get_the_title()),
                    /* translators: %s is product title */
                    'title_reply_to' => esc_html__('Leave a Reply to %s', 'woocommerce'),
                    'title_reply_before' => '<span id="reply-title" class="comment-reply-title">',
                    'title_reply_after' => '</span>',
                    'comment_notes_after' => '',
                    'label_submit' => esc_html__('Submit', 'woocommerce'),
                    'logged_in_as' => '',
                    'comment_field' => '',
                );

                $name_email_required = (bool)get_option('require_name_email', 1);
                $fields = array(
                    'author' => array(
                        'label' => __('Name', 'woocommerce'),
                        'type' => 'text',
                        'value' => $commenter['comment_author'],
                        'required' => $name_email_required,
                    ),
                    'email' => array(
                        'label' => __('Email', 'woocommerce'),
                        'type' => 'email',
                        'value' => $commenter['comment_author_email'],
                        'required' => $name_email_required,
                    ),
                );

                $comment_form['fields'] = array();

                foreach ($fields as $key => $field) {
                    $field_html = '<p class="comment-form-' . esc_attr($key) . '">';
                    $field_html .= '<label for="' . esc_attr($key) . '">' . esc_html($field['label']);

                    if ($field['required']) {
                        $field_html .= '&nbsp;<span class="required">*</span>';
                    }

                    $field_html .= '</label><input id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($field['value']) . '" size="30" ' . ($field['required'] ? 'required' : '') . ' /></p>';

                    $comment_form['fields'][$key] = $field_html;
                }

                $account_page_url = wc_get_page_permalink('myaccount');
                if ($account_page_url) {
                    /* translators: %s opening and closing link tags respectively */
                    $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(esc_html__('You must be %1$slogged in%2$s to post a review.', 'woocommerce'), '<a href="' . esc_url($account_page_url) . '">', '</a>') . '</p>';
                }

                if (wc_review_ratings_enabled()) {
                    $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__('Your rating', 'woocommerce') . (wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '') . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
						<option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
						<option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
						<option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
						<option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
						<option value="1">' . esc_html__('Very poor', 'woocommerce') . '</option>
					</select></div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Your review', 'woocommerce') . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                ?>
            </div>
        </div>
    <?php else : ?>
        <p class="woocommerce-verification-required"><?php esc_html_e('Only logged in customers who have purchased this product may leave a review.', 'woocommerce'); ?></p>
    <?php endif; ?>

    <div class="clear"></div>
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

</div>

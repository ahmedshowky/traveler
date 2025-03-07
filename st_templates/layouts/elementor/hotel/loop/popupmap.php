<?php
global $post;
$post_id = get_the_ID();
$post_translated = TravelHelper::post_translated($post_id);
$url = st_get_link_with_search(get_permalink($post_translated), array('start', 'end', 'date', 'adult_number', 'child_number'), $_GET);
$phone_number = get_post_meta($post_translated,'phone',true);
?>
<div class="col-lg-12 item-service" itemscope itemtype="https://schema.org/Hotel">
    <div class="item-service-inner st-border-radius">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-6">
                <div class="thumb">
                    <?php if (is_user_logged_in()) { ?>
                        <?php $data = STUser_f::get_icon_wishlist(); ?>
                        <div class="service-add-wishlist login <?php echo ($data['status']) ? 'added' : ''; ?>"
                             data-id="<?php echo esc_attr($post_translated); ?>"
                             data-type="<?php echo get_post_type($post_translated); ?>"
                             title="<?php echo ($data['status']) ? __('Remove from wishlist', 'traveler') : __('Add to wishlist', 'traveler'); ?>">
                            <i class="fa fa-heart"></i>
                            <div class="lds-dual-ring"></div>
                        </div>
                    <?php } else { ?>
                        <a href="#" class="login" data-bs-toggle="modal" data-bs-target="#st-login-form">
                            <div class="service-add-wishlist"
                                 title="<?php echo __('Add to wishlist', 'traveler'); ?>">
                                <i class="fa fa-heart"></i>
                                <div class="lds-dual-ring"></div>
                            </div>
                        </a>
                    <?php } ?>
                    <div class="service-tag bestseller">
                        <?php echo STFeatured::get_featured(); ?>
                    </div>
                    <a href="<?php echo esc_url($url); ?>" itemprop="photo">
                        <?php
                        if (has_post_thumbnail()) {
                            echo get_the_post_thumbnail($post_translated, array(450, 300), array('alt' => TravelHelper::get_alt_image(), 'class' => 'img-responsive'));
                        } else {
                            echo '<img src="' . get_template_directory_uri() . '/img/no-image.png' . '" alt="Default Thumbnail" class="img-responsive" />';
                        }
                        ?>
                    </a>
                    <?php
                        if(has_post_thumbnail()){
                            echo get_the_post_thumbnail($post_translated, array(450, 417), array('alt' => TravelHelper::get_alt_image(), 'class' => 'd-none', 'itemprop'=>"image"));
                        }
                    ?>
                    <?php 
                        if(!empty($phone_number)){?>
                            <span class="d-none" itemprop="telephone"><?php echo esc_html($phone_number);?></span>
                        <?php }
                    ?>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-6 item-content">
                <?php
                $view_star_review = st()->get_option('view_star_review', 'review');
                if ($view_star_review == 'review') :
                    ?>
                    <ul class="icon-group text-color booking-item-rating-stars">
                        <?php
                        $avg = STReview::get_avg_rate();
                        echo TravelHelper::rate_to_string($avg);
                        ?>
                    </ul>
                <?php elseif ($view_star_review == 'star'): ?>
                    <ul class="icon-list icon-group booking-item-rating-stars">
                        <span class="pull-left mr10"><?php echo __('Hotel star', 'traveler'); ?></span>
                        <?php
                        $star = STHotel::getStar();
                        echo TravelHelper::rate_to_string($star, $star);
                        ?>
                    </ul>
                <?php endif; ?>
                <h4 class="service-title"  itemprop="name"><a
                            href="<?php echo esc_url($url); ?>"><?php echo get_the_title($post_translated); ?></a></h4>
                <?php if ($address = get_post_meta($post_translated, 'address', TRUE)): ?>
                    <p class="service-location d-flex align-items-center" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress"><?php echo TravelHelper::getNewIcon('Ico_maps', '#666666', '15px', '15px', true); ?><?php echo esc_html($address); ?></p>
                <?php endif; ?>

                <div class="section-review">
                    <div class="service-review" itemprop="starRating" itemscope itemtype="https://schema.org/Rating">
                        <?php
                        $count_review = get_comment_count(get_the_ID())['approved'];
                        $avg = STReview::get_avg_rate();
                        ?>
                        <span class="rating"><?php echo esc_html($avg); ?>/5 <?php echo TravelHelper::get_rate_review_text($avg, $count_review); ?></span>
                        <span class="st-dot"></span>
                        <span class="review"><?php echo esc_html($count_review) . ' ' . _n(esc_html__('Review', 'traveler'), esc_html__('Reviews', 'traveler'), $count_review); ?></span>
                    </div>
                    <div class="service-price" itemprop="priceRange">
                        <span>
                            <?php echo TravelHelper::getNewIcon('thunder', '#ffab53', '10px', '16px'); ?>
                            <?php if (STHotel::is_show_min_price()): ?>
                                <?php _e("From", 'traveler') ?>
                            <?php else: ?>
                                <?php _e("Avg", 'traveler') ?>
                            <?php endif; ?>
                        </span>
                        <span class="price">
                            <?php
                            $price = STHotel::get_price();
                            echo TravelHelper::format_money($price);
                            ?>
                        </span>
                        <span><?php echo __('/night', 'traveler'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

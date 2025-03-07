<?php
get_header();
$current_user_upage = get_user_by('slug', get_query_var('author_name'));

$role = $current_user_upage->roles[0];
$user_meta = get_user_meta($current_user_upage->ID);
$user_meta = array_filter(array_map(function ($a) {
    return $a[0];
}, $user_meta));

$list_info = st()->get_option('display_list_partner_info', '');
if($list_info == 'all'){
    $list_info = array('all');
}
if(empty($list_info)){
    $list_info = array('all');
}

$arr_service = STUser_f::getListServicesAuthor($current_user_upage);
if (!empty($arr_service)) {
    $active_tab = STInput::get('service', $arr_service[0]);
}
$inner_style = '';
$header_bg  = st()->get_option('patner_page_header_bg', '');
if(!empty($header_bg)){
    $inner_style = Assets::build_css("background-image: url(". esc_url($header_bg) .") !important;");
    $img = $header_bg;
} else {
    $thumb_id = get_post_thumbnail_id(get_the_ID());
    $img = wp_get_attachment_image_url($thumb_id, 'full');
    $inner_style = Assets::build_css("background-image: url(" . esc_url($img) . ") !important;");
}
?>
    <div class="st-author-page">
        
        <?php
        $menu_transparent = st()->get_option('menu_transparent', '');
        if($menu_transparent === 'on'){
            
            if($img){
                echo '<div id="st-content-wrapper" class="st-style-elementor">';

                    echo stt_elementorv2()->loadView('components/banner', ['img_url' => $img, 'type_page' => 'author_page']);
                echo '</div>';
            }
            
        } else {?>
        <div class="banner <?php echo esc_attr($inner_style); ?>">
            <div class="container">
                <h1>
                    <?php echo __('Partner Page', 'traveler'); ?>
                </h1>
            </div>
        </div>
        <?php }?>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="author-header">
                        <div class="author-avatar">
                            <?php  echo st_get_profile_avatar( $current_user_upage->ID, 100 );  ?>
                        </div>
                        <h3 class="author-name">
                            <?php echo esc_html( $current_user_upage->display_name ) ?>
                        </h3>
                        <div class="author-review">
                            <?php
                            $review_data = STUser_f::getReviewsDataAuthor($arr_service, $current_user_upage);
                            if (!empty($review_data)) {
                                $avg_rating = STUser_f::getAVGRatingAuthor($review_data);
                                ?>
                                <div class="author-review-box">
                                    <div class="author-start-rating">
                                        <div class="stm-star-rating">
                                            <div class="inner">
                                                <div class="stm-star-rating-upper"
                                                     style="width:<?php echo (float)$avg_rating / 5 * 100; ?>%;"></div>
                                                <div class="stm-star-rating-lower"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="author-review-label">
                                        <?php printf(__('%d Reviews', 'traveler'), count($review_data)); ?>
                                    </p>
                                </div>
                            <?php }
                            ?>
                        </div>
                        <div class="author-membersince">
                            <small>
                                <?php echo __('Member since ', 'traveler') . mysql2date( ' M d, Y', $current_user_upage->data->user_registered ); ?>
                            </small>
                        </div>
                    </div>
                    <div class="author-body">
                        <ul class="author-list-info">
                            <?php if ( isset( $user_meta['st_is_check_show_info'] ) && $user_meta['st_is_check_show_info'] == 'on' ): ?>
                                <?php if ( (in_array('all', $list_info) || in_array('email', $list_info)) ) { ?>
                                    <li>
                                        <?php echo '<strong>' . __( 'Email: ', 'traveler' ) . '</strong>' . esc_html($current_user_upage->user_email); ?>
                                    </li>
                                <?php } ?>
                                <?php if ( isset( $user_meta['st_phone'] ) ) { ?>
                                    <?php if ( $user_meta['st_phone'] != '' && (in_array('all', $list_info) || in_array('phone', $list_info)) ) { ?>
                                        <li><?php echo '<strong>' . __( 'Phone: ', 'traveler' ) . '</strong>' . esc_html($user_meta['st_phone']); ?>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ( isset( $user_meta['st_paypal_email'] ) ) { ?>
                                    <?php if ( $user_meta['st_paypal_email'] != '' && (in_array('all', $list_info) || in_array('email_paypal', $list_info)) ) { ?>
                                        <li>
                                           <?php echo '<strong>' . __( 'Email Paypal: ', 'traveler' ) . '</strong>' . esc_html($user_meta['st_paypal_email']); ?>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            <?php endif; ?>
                            <?php if ( isset( $user_meta['st_airport'] ) ): ?>
                                <?php if ( $user_meta['st_airport'] != '' && (in_array('all', $list_info) || in_array('home_airport', $list_info)) ) { ?>
                                    <li>
                                        <?php echo '<strong>' . __( 'Home Airport: ', 'traveler' ) . '</strong>' . esc_html($user_meta['st_airport']); ?>
                                    </li>
                                <?php } ?>
                            <?php endif; ?>
                            <?php if ( isset( $user_meta['st_address'] ) || isset( $user_meta['st_city'] ) || isset( $user_meta['st_country'] ) ): ?>
                                <?php if ((in_array('all', $list_info) || in_array('address', $list_info))) { ?>
                                    <li>
                                        <?php
                                        $address = '';
                                        echo '<strong>' . __( 'Address: ', 'traveler' ) . '</strong>';
                                        if ( isset( $user_meta['st_address'] ) ) {
                                            $address .= $user_meta['st_address'];
                                        }
                                        if ( isset( $user_meta['st_city'] ) ) {
                                            $address .= ', ' . esc_html($user_meta['st_city']);
                                        }
                                        if ( isset( $user_meta['st_country'] ) ) {
                                            $address .= ', ' . esc_html($user_meta['st_country']);
                                        }
                                        echo esc_html($address);
                                        ?>
                                    </li>
                                <?php } ?>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="author-verify">
                        <h4 class="verify-title"><?php esc_html_e('Verifications','traveler') ?></h4>
                        <ul>
                            <li>
                                <span class="left-icon">
                                    <?php echo TravelHelper::getNewIcon('phone-retro-1', '#A0A9B2', '15px', '15px', false); ?>
                                </span>
                                <span><?php esc_html_e('Phone number','traveler') ?></span>
                                <span class="right-icon">
                                    <?php
                                    if(st_check_user_verify('phone', $current_user_upage->ID) || current_user_can('administrator')){
                                        echo TravelHelper::getNewIcon('check-1', '#2ECC71', '18px', '18px', false);
                                    }else{
                                        echo TravelHelper::getNewIcon('remove', '#FA5636', '22px', '22px', false);
                                    }
                                    ?>
                                </span>
                            <li>
                                <span class="left-icon">
                                    <?php echo TravelHelper::getNewIcon('single-man-profile-picture', '#A0A9B2', '15px', '15px', false); ?>
                                </span>
                                <span><?php esc_html_e('ID Card','traveler') ?></span>
                                <span class="right-icon">
                                    <?php
                                    if(st_check_user_verify('passport', $current_user_upage->ID) || current_user_can('administrator')){
                                        echo TravelHelper::getNewIcon('check-1', '#2ECC71', '18px', '18px', false);
                                    }else{
                                        echo TravelHelper::getNewIcon('remove', '#FA5636', '22px', '22px', false);
                                    }
                                    ?>
                                </span>
                            <li>
                                <span class="left-icon">
                                    <?php echo TravelHelper::getNewIcon('programming-language-code', '#A0A9B2', '15px', '15px', false); ?>
                                </span>
                                <span><?php esc_html_e('Travel Certificate','traveler') ?></span>
                                <span class="right-icon">
                                    <?php
                                    if(st_check_user_verify('travel_certificate', $current_user_upage->ID) || current_user_can('administrator')){
                                        echo TravelHelper::getNewIcon('check-1', '#2ECC71', '18px', '18px', false);
                                    }else{
                                        echo TravelHelper::getNewIcon('remove', '#FA5636', '22px', '22px', false);
                                    }
                                    ?>
                                </span>
                            <li>
                                <span class="left-icon">
                                    <?php echo TravelHelper::getNewIcon('email-action-unread', '#A0A9B2', '15px', '15px', false); ?>
                                </span>
                                <span><?php esc_html_e('Email','traveler') ?></span>
                                <span class="right-icon">
                                    <?php
                                    if(st_check_user_verify('email', $current_user_upage->ID) || current_user_can('administrator')){
                                        echo TravelHelper::getNewIcon('check-1', '#2ECC71', '18px', '18px', false);
                                    }else{
                                        echo TravelHelper::getNewIcon('remove', '#FA5636', '22px', '22px', false);
                                    }
                                    ?>
                                </span>
                            <li>
                                <span class="left-icon">
                                    <?php echo TravelHelper::getNewIcon('folder-media-1', '#A0A9B2', '15px', '15px', false); ?>
                                </span>
                                <span><?php esc_html_e('Social media','traveler') ?></span>
                                <span class="right-icon">
                                    <?php
                                    if(st_check_user_verify('social', $current_user_upage->ID) || current_user_can('administrator')){
                                        echo TravelHelper::getNewIcon('check-1', '#2ECC71', '18px', '18px', false);
                                    }else{
                                        echo TravelHelper::getNewIcon('remove', '#FA5636', '22px', '22px', false);
                                    }
                                    ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <?php if(isset($user_meta['st_bio'])){ ?>
                    <div class="author-about">
                        <h3 class="title"><?php echo __('About', 'traveler'); ?></h3>
                        <div class="about-content">
                            <div class="st-cut-text" data-count="45" data-text-more="<?php echo __('More', 'traveler') ?>" data-text-less="<?php echo __('Less', 'traveler') ?>">
                                <?php echo wp_kses_post(nl2br($user_meta['st_bio'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <?php
                        echo st()->load_template('layouts/elementor/page/elements/partner', 'service', array('arr_service' => $arr_service, 'current_user_upage' => $current_user_upage));
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
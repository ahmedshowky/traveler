<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 20-11-2018
     * Time: 9:18 AM
     * Since: 1.0.0
     * Updated: 1.0.0
     */
    if(check_using_elementor()){
        
        $style = get_post_meta( get_the_ID(), 'st_custom_layout_new', true );
        if($style == 2 || $style == 3){
            echo stt_elementorv2()->loadView('services/car/car_review_form');
        } else {
            echo st()->load_template('layouts/elementor/car/car_review_form');
        }
        return;
    }
    $name = $email = '';
    $userdata = get_userdata(get_current_user_id());
    if($userdata){
        $name = esc_html($userdata->first_name ).' '. esc_html($userdata->last_name);
        $email = $userdata->user_email;
    }
    ?>
<div class="form-wrapper">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control"
                       name="author" value="<?php echo esc_attr($name); ?>"
                       placeholder="<?php _e('Name *', 'traveler') ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <input type="email" class="form-control"
                       name="email" value="<?php echo esc_attr($email) ?>"
                       placeholder="<?php _e('Email *', 'traveler') ?>">
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <input type="text" class="form-control"
                       name="comment_title"
                       placeholder="<?php _e('Title', 'traveler') ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-4 col-md-push-8">
            <div class="form-group review-items has-matchHeight">
                <?php
                    $stats = STReview::get_review_stats( get_the_ID() );
                    if ( !empty( $stats ) ) {
                        foreach ( $stats as $stat ) {
                            ?>
                            <div class="item">
                                <label><?php echo esc_html($stat[ 'title' ]); ?></label>
                                <input class="st_review_stats" type="hidden"
                                       name="st_review_stats[<?php echo trim( $stat[ 'title' ] ); ?>]">
                                <div class="rates">
                                    <?php
                                        for ( $i = 1; $i <= 5; $i++ ) {
                                            echo '<i class="fa fa-smile-o grey"></i>';
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                ?>
            </div>
        </div>
        <div class="col-xs-12 col-md-8 col-md-pull-4">
            <div class="form-group">
                <textarea name="comment"
                          class="form-control has-matchHeight"
                          placeholder="<?php _e('Content', 'traveler') ?>"></textarea>
            </div>
        </div>
    </div>
</div>

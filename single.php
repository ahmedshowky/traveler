<?php
/**
 * @package    WordPress
 * @subpackage Traveler
 * @since      1.0
 *
 * Single blog
 *
 * Created by ShineTheme
 *
 */
$new_layout = st()->get_option('st_theme_style', 'modern');
$single_layout = st()->get_option('st_hotel_alone_single_blog');
if ($single_layout == 'on') {
    echo st()->load_template('layouts/modern/single_hotel/blog/single');
    return;
} else {
    if ($new_layout == 'modern') {
        get_header();
        $style = st()->get_option('blog_single_style_modern', 1);
        switch ($style) {
            case 1:
                if(check_using_elementor()){
                    echo apply_filters('st_single_blog_template',st()->load_template('layouts/elementor/page/single'));
                } else {
                    echo st()->load_template('layouts/modern/page/single');
                }
                
                break;
            case 2:
                echo st()->load_template('layouts/modern/blog/single/loop/loop' . $style);
                break;
            
            default :
                echo st()->load_template('layouts/modern/page/single');
                break;
        }
        get_footer();

        return;
    }
    get_header();
    ?>
    <div class="container">
        <h1 class="page-title"><?php the_title() ?></h1>
        <div class="row">
            <?php
            $sidebar_pos = apply_filters('st_blog_sidebar', 'right');
            if ($sidebar_pos == "left") {
                get_sidebar('blog');
            }
            ?>
            <div class="<?php echo apply_filters('st_blog_sidebar', 'right') == 'no' ? 'col-sm-12' : 'col-sm-9'; ?>">
                <?php
                while (have_posts()) {
                    the_post();
                    get_template_part('content-single');
                    if (comments_open() || '0' != get_comments_number()) :
                        comments_template();
                    endif;
                }
                ?>
            </div>
            <?php
            if ($sidebar_pos == "right") {
                get_sidebar('blog');
            }
            ?>
        </div>
    </div>
    <?php
    get_footer();
}
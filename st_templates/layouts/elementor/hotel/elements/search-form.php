<?php
$result_page = st()->get_option( 'hotel_search_result_page' );
$class = '';
$id = 'id="sticky-nav"';
if(isset($in_tab)) {
    $class = 'in_tab';
    $id = '';
}

?>
<div class="search-form <?php echo esc_attr($class); ?> st-border-radius">
    <form action="<?php echo esc_url( get_the_permalink( $result_page ) ); ?>" class="form" method="get">
        <div class="row">
            <div class="col-md-3 border-right">
                <?php echo st()->load_template( 'layouts/elementor/hotel/elements/search/location', '', [ 'has_icon' => true ] ) ?>
            </div>
            <div class="col-md-3">
                <?php echo st()->load_template( 'layouts/elementor/hotel/elements/search/date', '', [ 'has_icon' => true ] ) ?>
            </div>
            <div class="col-md-3">
                <?php echo st()->load_template( 'layouts/elementor/hotel/elements/search/guest', '', [ 'has_icon' => true ] ) ?>
            </div>
            <div class="col-md-3">
                <?php echo st()->load_template( 'layouts/elementor/hotel/elements/search/advanced', '' ) ?>
            </div>
        </div>
    </form>
</div>

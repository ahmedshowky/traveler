<div class="sidebar-item pag st-icheck">
    <div class="item-title">
        <label><?php echo esc_html($title); ?></label>
        <i class="fa fa-angle-up" aria-hidden="true"></i>
    </div>
    <div class="item-content">
        <?php New_Layout_Helper::listTaxTreeFilter($taxonomy, 0, -1, 'st_cars'); ?>
        <button class="btn btn-link btn-more-item"><?php echo __('More', 'traveler'); ?> <i class="fa fa-caret-down"></i></button>
    </div>
</div>
<?php
$data_min_max = TravelerObject::get_min_max_price('st_cars');

$max = ((float)$data_min_max['price_max'] > 0) ? (float)$data_min_max['price_max'] : 0;
$min = ((float)$data_min_max['price_min'] > 0) ? (float)$data_min_max['price_min'] : 0;
$rate_change = false;
if (TravelHelper::get_default_currency('rate') != 0 and TravelHelper::get_default_currency('rate')) {
    $rate_change = TravelHelper::get_current_currency('rate') / TravelHelper::get_default_currency('rate');
    $max = round($rate_change * $max);
    if ((float)$max < 0) $max = 0;

    $min = round($rate_change * $min);
    if ((float)$min < 0) $min = 0;
}

$value_show = $min . ";" . $max; // default if error

if ($rate_change) {
    if (STInput::request('price_range')) {
        $price_range = explode(';', STInput::request('price_range'));

        $value_show = $price_range[0] . ";" . $price_range[1];
    } else {

        $value_show = $min . ";" . $max;
    }
}
?>
<div class="sidebar-item range-slider st-border-radius">
    <div class="item-title d-flex justify-content-between align-items-center">
        <div><?php echo esc_attr($title); ?></div>
        <i class="fa fa-angle-up" aria-hidden="true"></i>
    </div>
    <div class="item-content range-slider">
        <input type="text" class="price_range" name="price_range" value="<?php echo esc_attr($value_show); ?>" data-symbol="<?php echo TravelHelper::get_current_currency('symbol'); ?>" data-min="<?php echo esc_attr($min); ?>" data-max="<?php echo esc_attr($max); ?>" data-step="<?php echo st()->get_option('search_price_range_step',0); ?>"/>
        <div class="min-max-value">
            <div class="item-value">
                <?php echo esc_html__('Min price', 'traveler'); ?>
                <span><?php echo esc_html(TravelHelper::format_money($min)); ?></span>
            </div>
            <div class="item-value">
                <?php echo esc_html__('Max price', 'traveler'); ?>
                <span><?php echo esc_html(TravelHelper::format_money($max)); ?></span>
            </div>
        </div>
        <div class="price-action">
            <a href="javascript:void(0);" class="clear-price"><?php echo esc_html__('Clear', 'traveler'); ?></a>
            <button class="btn btn-link btn-apply-price-range"><?php echo __('Apply', 'traveler'); ?></button>
        </div>
    </div>
</div>
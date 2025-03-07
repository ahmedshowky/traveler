<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 2/6/2017
 * Version: 1.0
 */
$default = [
	'title'       => '',
	'is_required' => '',
	'placeholder' => '',
];

if ( isset( $data ) ) {
	extract( wp_parse_args( $data, $default ) );
} else {
	extract( $default );
}
if ( ! isset( $field_size ) ) {
	$field_size = 'lg';
}

?>
<div class="form-group form-passengers-class form-group-<?php echo esc_attr( $field_size ) ?> ">
	<label><?php echo esc_html( $title ) ?></label>
	<div class="tp_group_display">
		<span class="display-passengers"><span class="quantity-passengers">1</span> <?php echo esc_html__( 'passenger(s)', 'traveler' ) ?></span>
		<span class="display-class" data-economy="<?php echo esc_html__( 'economy class', 'traveler' ); ?>" data-business="<?php echo esc_html__( 'business class', 'traveler' ); ?>"><?php echo esc_html__( 'economy class', 'traveler' ); ?></span>
		<span class="display-icon-dropdown"><i class="fa fa-chevron-down"></i></span>
	</div>
	<div class="tp-form-passengers-class none">
		<div class="twidget-passenger-form-wrapper">
			<ul class="twidget-age-group passengers-class">
				<li>
					<div class="twidget-cell twidget-age-name"><?php echo esc_html__( 'Adults', 'traveler' ); ?></div>
					<div class="twidget-cell twidget-age-select">
						<span class="twidget-num"><input type="number" min="1" max="9" name="adults" value="1"></span>
					</div>
				</li>
				<li>
					<div class="twidget-cell twidget-age-name"><?php echo wp_kses( __( 'Children to 12<br>years', 'traveler' ), [ 'br' => [] ] ) ?></div>
					<div class="twidget-cell twidget-age-select">
						<span class="twidget-num"><input type="number" min="0" max="8" name="children" value="0"></span>
					</div>
				</li>
				<li>
					<div class="twidget-cell twidget-age-name"><?php echo wp_kses( __( 'Infants to 2<br>years', 'traveler' ), [ 'br' => [] ] ) ?></div>
					<div class="twidget-cell twidget-age-select">
						<span class="twidget-num"><input type="number" min="0" max="8" name="infants" value="0"></span>
					</div>
				</li>
			</ul>
			<span class="notice none">
				<?php echo esc_html__( 'Maxium 9 passengers', 'traveler' ); ?>
			</span>
			<hr>
			<div class="tp-checkbox-class">
				<label><input class="i-check checkbox-class" type="checkbox" value="1" /> <?php echo esc_html__( 'Business class', 'traveler' ); ?></label>
				<input type="hidden" name="trip_class" value="0">
			</div>
		</div>
	</div>
</div>

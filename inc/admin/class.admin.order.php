<?php
    /**
     * @package    WordPress
     * @subpackage Traveler
     * @since      1.0
     *
     * Class STAdminOrder
     *
     * Created by ShineTheme
     *
     */
    if ( !class_exists( 'STAdminOrder' ) ) {

        class STAdminOrder extends STAdmin
        {
            function __construct()
            {
                parent::__construct();
                add_action( 'add_meta_boxes', [ $this, 'add_item_metabox' ] );


                add_action( 'admin_enqueue_scripts', [ $this, 'admin_queue_scripts' ] );

                add_action( 'wp_ajax_st_order_select', [ $this, 'st_order_select' ] );
                add_action( 'wp_ajax_save_order_item', [ $this, 'save_order_item' ] );

                add_action( 'wp_ajax_st_delete_order_item', [ $this, 'st_delete_order_item' ] );

                add_filter( 'woocommerce_attribute_label', [ $this, 'st_change_name_meta_order_item' ],11 );
                add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ $this, 'st_change_value_meta_order_item' ] ,10,2);
                add_filter( 'woocommerce_hidden_order_itemmeta', [ $this, 'custom_woocommerce_hidden_order_itemmeta' ] );
                add_filter( 'woocommerce_order_item_get_formatted_meta_data', [$this, 'filter_woocommerce_order_item_get_formatted_meta_data'] );

            }

            function custom_woocommerce_hidden_order_itemmeta($arr) {
                $arr = apply_filters('stt-hide-meta-order-admin',array(
                    '_st_location_id',
                    '_st_check_in_timestamp',
                    '_st_check_out_timestamp',
                    '_st_wc_order_id',

                ));
                return $arr;
            }

            function st_change_value_meta_order_item($formatted_meta, $item){
               
                foreach($formatted_meta as $key => $meta){
                    
                    if($meta->key =='_st_room_id'){
                        $formatted_meta[$key]->display_value= get_the_title(intval($meta->value));
                    }

                    if($meta->key =='_st_extra_price'){
                        $extras = $item->get_meta( '_st_extras', true );
                        $data_extra = [];
                        if ( isset( $extras[ 'value' ] ) && is_array( $extras[ 'value' ] ) && count( $extras[ 'value' ] ) ) {
                            foreach ( $extras[ 'value' ] as $name => $number ) {
                                if(!empty($extras[ 'value' ][ $name ])){
                                    $data_extra[ $name ] = array(
                                        'title'=>$extras[ 'title' ][ $name ],
                                        'price'=>$extras[ 'price' ][ $name ],
                                        'value'=>$extras[ 'value' ][ $name ],
                                    );
                                }
                            }
                        }


                        $html_extra_detail = '
                        - <b class="booking-cart-item-title">'.__("Detail","traveler").'</b>
                        <span class="booking-item-payment-price-amount">';
                            foreach($data_extra as $key_extra => $item):
                                
               
                                $number_item = $item['price'];
                                $title = $item['title'];
                                if($number_item <= 0) $number_item = 0;
                                ?><?php if($number_item){ 
                                $html_extra_detail .= '<span style="padding-left: 10px ">';
                                $html_extra_detail .= esc_attr($title).": ".esc_attr($item['value']).' x <b>'.wc_price($number_item) . '</b>';
                                    $html_extra_detail .= ' </span> <br />';
                            }
                            endforeach;
                        $html_extra_detail .= '</span>';
                        $formatted_meta[$key]->display_value= wc_price($meta->value). ' '.$html_extra_detail;
                    }
                    if($meta->key =='_st_adult_price' || $meta->key =='_st_child_price' || $meta->key =='_st_infant_price'
                    || $meta->key =='_st_ori_price' ||  $meta->key =='_st_total_price' ||  $meta->key =='_st_sale_price' 
                    ||  $meta->key =='_st_base_price'
                    ||  $meta->key =='_st_price_type'
                    ||  $meta->key =='_st_package_hotel_price'
                    ||  $meta->key =='_st_package_activity_price'
                    ||  $meta->key =='_st_package_car_price'
                    ){
                        $formatted_meta[$key]->display_value=  wc_price($meta->value);
                    }
                }
                return $formatted_meta;
            }

            function st_change_name_meta_order_item( $meta )
            {
                switch ( $meta ) {
                    case "_st_item_price":
                        $meta = __( "Item Price", 'traveler' );
                        break;
                    case "_st_ori_price":
                        $meta = __( "Ori Price", 'traveler' );
                        break;
                    case "_st_check_in":
                        $meta = __( "Check In", 'traveler' );
                        break;
                    case "_st_check_out":
                        $meta = __( "Check Out", 'traveler' );
                        break;
                    case "_st_room_num_search":
                        $meta = __( "Room Number", 'traveler' );
                        break;
                    case "_st_extra_type":
                        $meta = __( "Extra type", 'traveler' );
                        break;
                    case "_st_total_price_origin":
                        $meta = __( "Total price origin", 'traveler' );
                        break;
                    case "_st_title_cart":
                        $meta = __( "Name", 'traveler' );
                        break;
                    case "_st_room_id":
                        $meta = __( "Room name", 'traveler' );
                        break;
                    case "_st_adult_number":
                        $meta = __( "Adult Number", 'traveler' );
                        break;
                    case "_st_child_number":
                        $meta = __( "Child Number", 'traveler' );
                        break;
                    case "_st_extra_price":
                        $meta = __( "Extra Price", 'traveler' );
                        break;
                    case "_st_commission":
                        $meta = __( "Commission", 'traveler' );
                        break;
                    case "_st_discount_rate":
                        $meta = __( "Discount", 'traveler' );
                        break;
                    case "_st_st_booking_post_type":
                        $meta = __( "Post Type", 'traveler' );
                        break;
                    case "_st_st_booking_id":
                        $meta = __( "Booking ID", 'traveler' );
                        break;
                    case "_st_sharing":
                        $meta = __( "Sharing", 'traveler' );
                        break;
                        
                    case "_st_duration_unit":
                        $meta = __( "Duration", 'traveler' );
                        break;
                    
                    case "_st_total_price":
                        $meta = __( "Total Price", 'traveler' );
                        break;
                    case "_st_user_id":
                        $meta = __( "User ID", 'traveler' );
                        break;
                    case "_st_check_in_time":
                        $meta = __( "Check In Time", 'traveler' );
                        break;
                    case "_st_check_out_time":
                        $meta = __( "Check Out Time", 'traveler' );
                        break;
                    case "_st_check_in_timestamp":
                        $meta = __( "Check In Timestamp", 'traveler' );
                        break;
                    case "_st_check_out_timestamp":
                        $meta = __( "Check Out Timestamp", 'traveler' );
                        break;
                    case "_st_location_id_pick_up":
                        $meta = __( "Pick Up ID", 'traveler' );
                        break;
                    case "_st_location_id_drop_off":
                        $meta = __( "Drop Off ID", 'traveler' );
                        break;
                    case "_st_pick_up":
                        $meta = __( "Pick Up", 'traveler' );
                        break;
                    case "_st_drop_off":
                        $meta = __( "Drop Off", 'traveler' );
                        break;
                    case "_st_sale_price":
                        $meta = __( "Sale Price", 'traveler' );
                        break;
                    case "_st_numberday":
                        $meta = __( "Number Day", 'traveler' );
                        break;
                    case "_st_price_equipment":
                        $meta = __( "Price Equipment", 'traveler' );
                        break;
                    case "_st_distance":
                        $meta = __( "Distance", 'traveler' );
                        break;
                    case "_st_discount_type":
                        $meta = __( "Discount Type", 'traveler' );
                        break;
                    case "_st_adult_price":
                        $meta = __( "Adult Price", 'traveler' );
                        break;
                    case "_st_child_price":
                        $meta = __( "Child Price", 'traveler' );
                        break;
                    case "_st_infant_price":
                        $meta = __( "Infant Price", 'traveler' );
                        break;
                    case "_st_infant_number":
                        $meta = __( "Number Infant", 'traveler' );
                        break;
                    case "_st_type_activity":
                        $meta = __( "Type Activity", 'traveler' );
                        break;
                    case "_st_duration":
                        $meta = __( "Duration", 'traveler' );
                        break;
                    case "_st_type_tour":
                        $meta = __( "Type Tour", 'traveler' );
                        break;
                    case "daily_activity":
                        $meta = __( "Daily Activity", 'traveler' );
                        break;
                    case "daily_tour":
                        $meta = __( "Daily Tour", 'traveler' );
                        break;
                    case "st_hotel":
                        $meta = __( "Hotel", 'traveler' );
                        break;
                    case "hotel_room":
                        $meta = __( "Room", 'traveler' );
                        break;
                    case "st_rental":
                        $meta = __( "Rental", 'traveler' );
                        break;
                    case "st_cars":
                        $meta = __( "Car", 'traveler' );
                        break;
                    case "st_tours":
                        $meta = __( "Tours", 'traveler' );
                        break;
                    case "st_activity":
                        $meta = __( "Activity", 'traveler' );
                        break;
                    case "specific_date":
                        $meta = __( "Specific Date", 'traveler' );
                        break;
	                case "_st_starttime":
		                $meta = __( "Start Time", 'traveler' );
		                break;
	                case "_st_base_price":
		                $meta = __( "Base price", 'traveler' );
		                break;
	                case "_st_price_type":
		                $meta = __( "Type price", 'traveler' );
		                break;
	                case "_st_package_hotel_price":
		                $meta = __( "Hotel package price", 'traveler' );
		                break;
	                case "_st_package_activity_price":
		                $meta = __( "Activity package price", 'traveler' );
		                break;
	                case "_st_package_car_price":
		                $meta = __( "Car package price", 'traveler' );
		                break;
                    case "_st_package_flight_price":
                        $meta = __( "Flight package price", 'traveler' );
                        break;
                    case "_st_status":
                        $meta = __( "Status", 'traveler' );
                        break;
                    case "_st_location_id":
                        $meta = __( "Location Id", 'traveler' );
                        break;
                    case "_st_type_car":
                        $meta = __( "Car type", 'traveler' );
                        break;
                    case "_st_price_with_tax":
                        $meta = __( "Price with tax", 'traveler' );
                        break;
                    case "_st_price_destination":
                        $meta = __( "Price destination", 'traveler' );
                        break;
                    case "_st_data_destination":
                        $meta = __( "Data destination", 'traveler' );
                        break;
                    case "_st_wc_order_id":
                        $meta = __( "WC Order id", 'traveler' );
                        break;
                }

                return $meta;
            }
            function filter_woocommerce_order_item_get_formatted_meta_data( $meta ) {
                // Only on emails notifications
                if ( is_admin() || is_wc_endpoint_url() )
                    return $meta;
            
                foreach ( $meta as $key => $meta ) {
                    $meta[$key]->display_key = '_st_room_id';
                    $meta[$key]->display_value = 'new value';
                }
                
                return $meta;
            }
            function st_delete_order_item()
            {
                $id = isset( $_REQUEST[ 'id' ] ) ? $_REQUEST[ 'id' ] : false;

                if ( $id ) {
                    wp_delete_post( $id );
                }
                echo json_encode( [ 'status' => 1 ] );
                die;
            }

            function save_order_item()
            {
                $arg = $_REQUEST;

                $default = [
                    'item_id'     => '',
                    'item_type'   => '',
                    'item_id2'    => '',
                    'check_in'    => '',
                    'check_out'   => '',
                    'item_number' => '',
                    'item_price'  => '',
                    'order_id'    => '',
                ];

                $data = wp_parse_args( $arg, $default );
                extract( $data );

                if ( !$order_id ) {
                    echo json_encode( [
                        'status'  => 0,
                        'message' => __( 'Order ID must be specify', 'traveler' )
                    ] );
                    die;
                }

                $item_main_id = $item_id;
                switch ( $item_type ) {
                    case "st_hotel":
                        $item_main_id = $item_id2;
                        break;

                }


                $post = [
                    'post_title'  => sprintf( __( 'Order #%s', 'traveler' ), $order_id ) . ' - ' . get_the_title( $item_main_id ),
                    'post_type'   => 'st_order_item',
                    'post_status' => 'publish',
                ];

                $new_post = wp_insert_post( $post );

                if ( $new_post ) {
                    update_post_meta( $new_post, 'item_id', $item_main_id );
                    update_post_meta( $new_post, 'item_number', $item_number );
                    update_post_meta( $new_post, 'item_price', $item_price );
                    update_post_meta( $new_post, 'check_out', $check_out );
                    update_post_meta( $new_post, 'check_in', $check_in );
                    update_post_meta( $new_post, 'order_parent', $order_id );
                }

                echo json_encode( [ 'status' => 1, 'reload' => 1 ] );
                die;

            }

            function st_order_select()
            {
                $arg = $_REQUEST;

                $default = [
                    'posts_per_page' => 10,
                    'post_type'      => 'st_hotel',
                    'item_id'        => '',
                    'q'              => ''
                ];

                $data = wp_parse_args( $arg, $default );

                $query = [
                    'post_per_page' => $data[ 'posts_per_page' ],
                    'post_type'     => $data[ 'post_type' ],
                    's'             => $data[ 'q' ]
                ];
                $r     = [
                    'items'       => [],
                    'total_count' => 0
                ];

                if ( $data[ 'post_type' ] == 'hotel_room' and $data[ 'item_id' ] ) {
                    $query[ 'meta_key' ]   = 'room_parent';
                    $query[ 'meta_value' ] = $data[ 'item_id' ];
                }


                query_posts( $query );

                while ( have_posts() ) {
                    the_post();
                    $r[ 'items' ][] = [
                        'id'          => get_the_ID(),
                        'name'        => get_the_title(),
                        'description' => get_the_ID(),
                        'price'       => get_post_meta( get_the_ID(), 'price', true )
                    ];
                }
                wp_reset_query();

                echo json_encode( $r );
                die;
            }

            function admin_queue_scripts()
            {
                $screen = get_current_screen();

                if ( $screen->base == 'post' and $screen->post_type == 'st_order' ) {
                    wp_enqueue_script( 'select2' );

                    wp_enqueue_script( 'edit-orders', get_template_directory_uri() . '/js/admin/edit-order.js', [ 'jquery', 'jquery-ui-datepicker' ], null, true );


                }
            }

            function add_item_metabox()
            {
                $screens = [ 'st_order' ];

                foreach ( $screens as $screen ) {

                    add_meta_box(
                        'st_order_item_section',
                        __( 'Order Items', 'traveler' ),
                        [ $this, 'metabox_call_back' ],
                        $screen
                    );
                }
            }

            function metabox_call_back( $post )
            {
                echo balanceTags( $this->load_view( 'orders/index', null, [ 'post' => $post ] ) );
            }
        }

        new STAdminOrder();
    }
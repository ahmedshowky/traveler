<?php
    /**
     * @package    WordPress
     * @subpackage Traveler
     * @since      1.0
     *
     * Class STPaymentGateways
     *
     * Created by ShineTheme
     *
     */
    if ( !class_exists( 'STPaymentGateways' ) ) {
        class STPaymentGateways
        {
            static $_payment_gateways = [];
            protected static $_gateways_inited = false;
            static function _init()
            {
                //load abstract class
                STTraveler::load_libs( [
                    'abstract/class-abstract-payment-gateway'
                ] );   
                add_action( 'init', [ __CLASS__, '_do_add_gateway_options' ] );
            }
            static function _do_add_gateway_options()
            {
                    add_filter( 'traveler_all_settings', [ __CLASS__, '_add_gateway_options' ] );
            }
            static function _add_gateway_options( $settings = [] )
            {
                $settings[] = [
                    'id'    => 'option_pmgateway',
                    'title' => __( '<i class="fa fa-money"></i> Payment Options', 'traveler' ),
                    'settings'=>[__CLASS__,'__getOptions']
                ];
                return $settings;
            }
            public static function __getOptions()
            {
                $option_fields = [];
                $all = self::get_payment_gateways();
                if ( is_array( $all ) and !empty( $all ) ) {
                    foreach ( $all as $key => $value ) {
                        $field = $value->get_option_fields();
                        $default = [
                            [
                                'id'      => 'pm_gway_' . $key . '_tab',
                                'label'   => sprintf( __( '%s', 'traveler' ), $value->get_name() ),
                                'type'    => 'tab',
                                'section' => 'option_pmgateway'
                            ],
                            [
                                'id'      => 'pm_gway_' . $key . '_enable',
                                'label'   => sprintf( __( 'Enable %s', 'traveler' ), $value->get_name() ),
                                'type'    => 'on-off',
                                'std'     => $value->get_default_status() ? 'on' : 'off',
                                'section' => 'option_pmgateway'
                            ],
                        ];
                        $option_fields = array_merge( $option_fields, $default );
                        if ( $field and is_array( $field ) ) {
                            $option_fields = array_merge( $option_fields, $field );
                        }
                    }
                }
                return $option_fields;
            }
            protected static function init_default_gateways()
            {
                //Load default gateways
                self::_load_default_gateways();
                if ( class_exists( 'STGatewaySubmitform' ) ) {
                    self::$_payment_gateways[ 'st_submit_form' ] = new STGatewaySubmitform();
                }
                if ( class_exists( 'New_Payment' ) ) {
                    self::$_payment_gateways[ 'st_new_payment' ] = new New_Payment();
                }
            }
            static function get_payment_gateways()
            {
                if(!self::$_gateways_inited){
                    self::init_default_gateways();
                }
                $all = apply_filters( 'st_payment_gateways', self::$_payment_gateways );
                return $all;
            }
            /**
             *
             *
             * @since  1.0.1
             * @update 1.1.7
             */
            static function get_payment_gateways_html( $post_id = false, $style = false)
            {
                if(New_Layout_Helper::isNewLayout()) {
                    $all = self::get_payment_gateways();
                    if (is_array($all) and !empty($all)) {
                        $i = 1;
                        $available = [];
                        foreach ($all as $key => $value) {
                            if (method_exists($value, 'is_available') and $value->is_available()) {
                                if (!$post_id) {
                                    $post_id = STCart::get_booking_id();
                                }
                                if ($value->is_available($post_id)) {
                                    $available[$key] = $value;
                                }
                            }
                        }
                        if (!empty($available)) {
                            $i = 0;
                            if($style == 'style-2') { ?>
                                <div class="input-hidden-pay"></div>
                                <div class="payment-select">


                                    <div class="payment-choose">

                                        <ul id="input-payment">
                                            <?php foreach ($available as $key => $value) { ?>
                                                <li>
                                                    <div class="img_pay">
                                                        <img src="<?php echo esc_url($value->get_logo()); ?>" alt="" value="<?php echo esc_attr($key) ?>" data-title="<?php echo esc_html($value->get_name()); ?>">
                                                    </div>
                                                    <span class="payment-title"><?php echo esc_html($value->get_name()); ?></span>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="btn-select">
                                        <?php  $available = array_values($available); ?>
                                        <li>
                                            <div class="img_pay">
                                                <img src="<?php echo esc_url($available[0]->get_logo()); ?>" alt=""" value="<?php echo esc_url($available[0]->get_logo()); ?>">
                                                <span class="payment-title"><?php echo esc_html($available[0]->get_name()); ?></span>
                                            </div>

                                        </li>
                                    </div>

                                </div>

                                <div class="load_result">
                                    <?php
                                    $available = array_values($available);
                                    $available[0]->html();
                                    ?>
                                </div>
                                <script>

                                    var val_first = jQuery('#input-payment').find('img').attr('value');
                                    jQuery('.input-hidden-pay').html('<input type="radio" id="st_payment_gateway" name="st_payment_gateway" checked value="'+ val_first +'">');
                                    //change button stuff on click
                                    jQuery('#input-payment li').click(function(){
                                        var img = jQuery(this).find('img').attr("src");
                                        var value = jQuery(this).find('img').attr('value');
                                        var text =  jQuery(this).find('img').attr('data-title');
                                        var item = '<li><div class="img_pay"><img src="'+ img +'" alt="" /></div><span>'+ text +'</span></li>';
                                        jQuery('.btn-select').html(item);
                                        jQuery('.input-hidden-pay').html('<input type="radio" id="st_payment_gateway" name="st_payment_gateway" checked value="'+ value +'">');
                                        jQuery('#payment-gateway').attr('value', value);
                                        jQuery(".payment-choose").toggle();
                                        // load ajax html
                                        jQuery.ajax({
                                            type : "post",
                                            dataType : "json",
                                            url : '<?php echo admin_url('admin-ajax.php');?>',
                                            data : {
                                                action: "getpayhtml",
                                                value : value,
                                            },
                                            beforeSend: function(){
                                            },
                                            success: function(response) {
                                                //Làm gì đó khi dữ liệu đã được xử lý
                                                if(response.success) {
                                                    jQuery('.load_result').html(response.data);
                                                }
                                                else {
                                                    alert('Đã có lỗi xảy ra');
                                                }
                                            },
                                            error: function( jqXHR, textStatus, errorThrown ){
                                                //Làm gì đó khi có lỗi xảy ra
                                                console.log( 'The following error occured: ' + textStatus, errorThrown );
                                            }
                                        })
                                        return false;


                                    });

                                    jQuery(".btn-select").click(function(){
                                        jQuery(".payment-choose").toggle();
                                    });

                                </script>

                            <?php } else {
                                foreach ($available as $key => $value) {
                                ?>
                                <div class="payment-item <?php echo (!$i) ? 'active' : false; ?> payment-gateway payment-gateway-wrapper payment-gateway-<?php echo esc_attr($key); ?>"
                                     id="payment-gateway payment-gateway-<?php echo esc_attr($key); ?>"
                                     data-gateway="<?php echo esc_attr($key) ?>">
                                    <div class="dropdown">
                                        <div class="st-icheck">
                                            <div class="st-icheck-item">
                                                <label>
                                                    <div class="check-payment">
                                                        <input type="radio" name="st_payment_gateway"
                                                               class="payment-item-radio" <?php echo (!$i) ? 'checked' : false; ?>
                                                               value="<?php echo esc_attr($key) ?>"/>
                                                        <span class="checkmark"></span>
                                                    </div>
                                                    <span class="payment-title"><?php echo esc_html($value->get_name()); ?></span>
                                                    <img src="<?php echo esc_url($value->get_logo()); ?>"
                                                         alt="<?php echo esc_attr($value->get_name()); ?>"
                                                         class="<?php echo esc_attr($key); ?>">
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (!in_array($key, array( 'st_paypal_adaptivepayment'))) { ?>
                                            <div class="dropdown-menu">
                                                <?php $value->html(); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php
                                $i++;
                            }
                            }
                        }
                    }
                }else{
                    $all = self::get_payment_gateways();
                    if ( is_array( $all ) and !empty( $all ) ) {
                        $i         = 1;
                        $available = [];
                        foreach ( $all as $key => $value ) {
                            if ( method_exists( $value, 'is_available' ) and $value->is_available() ) {
                                if ( !$post_id ) {
                                    $post_id = STCart::get_booking_id();
                                }
                                if ( $value->is_available( $post_id ) ) {
                                    $available[ $key ] = $value;
                                }
                            }
                        }
                        if ( !empty( $available ) ) {
                            ?>
                            <div class="st-payment-tabs-wrap">
                                <ul class="st-payment-tabs clearfix">
                                    <?php
                                    $i = 0;
                                    foreach ( $available as $key => $value ) {
                                        ?>
                                        <li class="payment-gateway payment-gateway-<?php echo esc_attr( $key ); ?> <?php echo ( !$i ) ? 'active' : false; ?>"
                                            data-gateway="<?php echo esc_attr( $key ) ?>">
                                            <label class="payment-gateway-wrap">
                                                <div class="logo">
                                                    <div class="h-center">
                                                        <?php printf( '<img src="%s" alt="%s">', $value->get_logo(), $value->get_name() ) ?>
                                                    </div>
                                                </div>
                                                <h4 class="gateway-name"><?php echo esc_html( $value->get_name() ); ?></h4>
                                                <input type="radio" class="i-radio payment-item-radio"
                                                       name="st_payment_gateway" <?php echo ( !$i ) ? 'checked' : false; ?>
                                                       value="<?php echo esc_attr( $key ) ?>">
                                            </label>
                                        </li>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </ul>
                                <div class="st-payment-tab-content">
                                    <?php
                                    foreach ( $available as $key => $value ) {
                                        ?>
                                        <div class="st-tab-content" data-id="<?php echo esc_attr( $key ) ?>">
                                            <?php $value->html(); ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
            }
            static function get_payment_gateways_package_html( $post_id = false , $style = false)
            {

                if(New_Layout_Helper::isNewLayout()) {
                    
                    $all = self::get_payment_gateways();
                   
                    if (is_array($all) and !empty($all)) {
                        $i = 1;
                        $available = [];
                        foreach ($all as $key => $value) {
                            if (method_exists($value, 'is_available') and $value->is_available()) {
                                if (!$post_id) {
                                    $post_id = STCart::get_booking_id();
                                }
                                if ($value->is_available($post_id)) {
                                    $available[$key] = $value;
                                }
                            }
                        }
                        if (!empty($available)) {
                            $i = 0;
                            if($style == 'style-2'){ ?>
                               
                                <div class="input-hidden-pay"></div>
                                <div class="payment-select">
                                   
                                   
                                    <div class="payment-choose">
                                        
                                        <ul id="input-payment">
                                            <?php foreach ($available as $key => $value) { ?>    
                                                <li>
                                                    <div class="img_pay">
                                                        <img src="<?php echo esc_url($value->get_logo()); ?>" alt="" value="<?php echo esc_attr($key) ?>" data-title="<?php echo esc_html($value->get_name()); ?>">
                                                    </div>
                                                    <span class="payment-title"><?php echo esc_html($value->get_name()); ?></span>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="btn-select">
                                        <?php  $available = array_values($available); ?>
                                        <li>
                                            <div class="img_pay">
                                                <img src="<?php echo esc_url($available[0]->get_logo()); ?>" alt=""" value="<?php echo esc_url($available[0]->get_logo()); ?>">
                                                <span class="payment-title"><?php echo esc_html($available[0]->get_name()); ?></span>
                                            </div>
                                            
                                        </li>
                                    </div>
                                   
                                </div>
                     
                                <div class="load_result">
                                    <?php 
                                       $available = array_values($available);
                                       $available[0]->html();
                                    ?>
                                </div>
                                <script>
                                 
                                        var val_first = jQuery('#input-payment').find('img').attr('value');
                                        jQuery('.input-hidden-pay').html('<input type="radio" id="st_payment_gateway" name="st_payment_gateway" checked value="'+ val_first +'">');
                                        //change button stuff on click
                                        jQuery('#input-payment li').click(function(){
                                            var img = jQuery(this).find('img').attr("src");
                                            var value = jQuery(this).find('img').attr('value');
                                            var text =  jQuery(this).find('img').attr('data-title');
                                            var item = '<li><div class="img_pay"><img src="'+ img +'" alt="" /></div><span>'+ text +'</span></li>';
                                            jQuery('.btn-select').html(item);
                                            jQuery('.input-hidden-pay').html('<input type="radio" id="st_payment_gateway" name="st_payment_gateway" checked value="'+ value +'">');
                                            jQuery('#payment-gateway').attr('value', value);
                                            jQuery(".payment-choose").toggle();
                                            // load ajax html
                                            jQuery.ajax({
                                                type : "post", 
                                                dataType : "json",
                                                url : '<?php echo admin_url('admin-ajax.php');?>', 
                                                data : {
                                                    action: "getpayhtml", 
                                                    value : value,
                                                },
                                                beforeSend: function(){
                                                },
                                                success: function(response) {
                                                    //Làm gì đó khi dữ liệu đã được xử lý
                                                    if(response.success) {
                                                        jQuery('.load_result').html(response.data);
                                                    }
                                                    else {
                                                        alert('Đã có lỗi xảy ra');
                                                    }
                                                },
                                                error: function( jqXHR, textStatus, errorThrown ){
                                                    //Làm gì đó khi có lỗi xảy ra
                                                    console.log( 'The following error occured: ' + textStatus, errorThrown );
                                                }
                                            })
                                        return false;


                                        });

                                        jQuery(".btn-select").click(function(){
                                                jQuery(".payment-choose").toggle();
                                            });

                                </script>
                            <?php } else {
                                foreach ($available as $key => $value) {
                                    ?>
                                    <div class="payment-item <?php echo (!$i) ? 'active' : false; ?> payment-gateway payment-gateway-wrapper payment-gateway-<?php echo esc_attr($key); ?>"
                                        id="payment-gateway payment-gateway-<?php echo esc_attr($key); ?>"
                                        data-gateway="<?php echo esc_attr($key) ?>">
                                        <div class="dropdown">
                                            <div class="st-icheck">
                                                <div class="st-icheck-item">
                                                    <label>
                                                        <div class="check-payment">
                                                            <input type="radio" name="st_payment_gateway"
                                                                class="payment-item-radio" <?php echo (!$i) ? 'checked' : false; ?>
                                                                value="<?php echo esc_attr($key) ?>"/>
                                                            <span class="checkmark"></span>
                                                        </div>
                                                        <span class="payment-title"><?php echo esc_html($value->get_name()); ?></span>
                                                        <img src="<?php echo esc_url($value->get_logo()); ?>"
                                                            alt="<?php echo esc_attr($value->get_name()); ?>"
                                                            class="<?php echo esc_attr($key); ?>">
                                                    </label>
                                                </div>
                                            </div>
                                            <?php if (!in_array($key, array('st_submit_form', 'st_paypal_adaptivepayment'))) { ?>
                                                <div class="dropdown-menu">
                                                    <?php $value->html(); ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php
                                    $i++;
                                }
                            }
                        }
                    }
                }else{
                    $all = self::get_payment_gateways();
                    if (is_array($all) and !empty($all)) {
                        $i = 1;
                        $available = [];
                        foreach ($all as $key => $value) {
                            if (method_exists($value, 'is_available') and $value->is_available()) {
                                if (!$post_id) {
                                    $post_id = STCart::get_booking_id();
                                }
                                if ($value->is_available($post_id)) {
                                    $available[$key] = $value;
                                }
                            }
                        }
                        if (!empty($available)) {
                            $i = 0;
                            foreach ($available as $key => $value) {
                                ?>
                                <div class="payment-item <?php echo (!$i) ? 'active' : false; ?> payment-gateway payment-gateway-wrapper payment-gateway-<?php echo esc_attr($key); ?>"
                                     id="payment-gateway payment-gateway-<?php echo esc_attr($key); ?>"
                                     data-gateway="<?php echo esc_attr($key) ?>">
                                    <div class="dropdown">
                                        <div class="st-icheck">
                                            <div class="st-icheck-item">
                                                <label>
                                                    <div class="check-payment">
                                                        <input type="radio" name="st_payment_gateway"
                                                               class="payment-item-radio" <?php echo (!$i) ? 'checked' : false; ?>
                                                               value="<?php echo esc_attr($key) ?>"/>
                                                        <span class="checkmark"></span>
                                                    </div>
                                                    <span class="payment-title"><?php echo esc_html($value->get_name()); ?></span>
                                                    <img src="<?php echo esc_url($value->get_logo()); ?>"
                                                         alt="<?php echo esc_attr($value->get_name()); ?>"
                                                         class="<?php echo esc_attr($key); ?>">
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (!in_array($key, array('st_submit_form', 'st_paypal_adaptivepayment'))) { ?>
                                            <div class="dropdown-menu">
                                                <?php $value->html(); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php
                                $i++;
                            }
                        }
                    }
                }
            }
            /**
             *
             *
             * @param      $id
             * @param bool $post_id
             *
             * @return mixed
             */
            static function get_gateway( $id, $post_id = false )
            {
                $all = self::get_payment_gateways();
                if ( isset( $all[ $id ] ) ) {
                    $value = $all[ $id ];
                    if ( method_exists( $value, 'is_available' ) and $value->is_available( $post_id ) ) {
                        return $value;
                    }
                }
            }
            static function get_gatewayname( $id )
            {
                $all = self::get_payment_gateways();
                if ( isset( $all[ $id ] ) ) {
                    $value = $all[ $id ];
                    if ( method_exists( $value, 'get_name' ) ) {
                        return $value->get_name();
                    } else return $id;
                }
            }
            /**
             * Check if a gateway is allow to show the booking infomation by gived gateway id
             *
             * @param bool|FALSE $id
             *
             * @return bool
             */
            static function gateway_success_page_validate( $id = false )
            {
                $all = self::get_payment_gateways();
                if ( isset( $all[ $id ] ) ) {
                    $value = $all[ $id ];
                    if ( method_exists( $value, 'get_name' ) ) {
                        $order_code = STInput::get('order_code');
                        $order_token_code = STInput::get('order_token_code');
                        if($order_code || $order_token_code) {
                            if ($order_token_code) {
                                $order_code = STOrder::get_order_id_by_token($order_token_code);
                            }
                            $status = get_post_meta($order_code, 'status', true);
                            $result = true;
                            if ($status == 'incomplete') {
                                if ($value->is_check_complete_required()) {
                                    $r = $value->check_complete_purchase($order_code);
                                } else {
                                    $r = [
                                        'status' => true
                                    ];
                                }
                                $payment = get_post_meta($order_code, 'payment_method', true);
                                if ($r && ($payment !== 'st_submit_form') ) {
                                    if (isset($r['status'])) {
                                        if ($r['status']) {
                                            $result = true;
                                            update_post_meta($order_code, 'status', 'complete');
                                            $status = 'complete';
                                            STCart::send_mail_after_booking($order_code, true);
                                            do_action('st_booking_change_status', 'complete', $order_code, $value->getGatewayId());
                                        } elseif (isset($r['message']) and $r['message']) {
                                            $result = false;
                                            STTemplate::set_message($r['message'], 'danger');
                                        }
                                        if (isset($r['redirect_url']) and $r['redirect_url']) {
                                            echo "<script>window.location.href='" . $r['redirect_url'] . "'</script>";
                                            // die;
                                        }
                                    }else {
                                        $result = true;
                                            update_post_meta($order_code, 'status', 'incomplete');
                                            $status = 'incomplete';
                                            STCart::send_mail_after_booking($order_code, true);
                                            do_action('st_booking_change_status', 'incomplete', $order_code, $value->getGatewayId());
                                    }
                                }
                            }
                            if ($status == 'incomplete') {
                                $result = false;
                                STTemplate::set_message(__("Your payment is incomplete.", 'traveler'));
                            }
                        }else{
                            $result = false;
                            STTemplate::set_message(__("Sorry! Your payment is incomplete.", 'traveler'));
                        }
                        return $result;
                    }
                } else {
                    STTemplate::set_message( __( 'Sorry! Your Payment Gateway is not valid', 'traveler' ), 'danger' );
                }
            }
            /**
             * Process the check out
             *
             * @param $gateway
             * @param $order_id
             *
             * @return array
             */
            static function do_checkout( $gateway, $order_id )
            {
                $total = get_post_meta( $order_id, 'total_price', true );
                // check status complete first
                if ( get_post_meta( $order_id, 'status', true ) == 'complete' ) {
                    return [
                        'status'   => true,
                        'redirect' => STCart::get_success_link( $order_id )
                    ];
                }
                if ( !$gateway->stop_change_order_status() ) {
                    update_post_meta( $order_id, 'status', 'incomplete' );
                    do_action( 'st_booking_change_status', 'incomplete', $order_id, $gateway->getGatewayId() );
                }
                if ( get_post_meta( $order_id, 'payment_method', true ) !== 'st_submit_form' ) {
                    update_post_meta( $order_id, 'status', 'incomplete' );
                    do_action( 'st_booking_change_status', 'incomplete', $order_id, $gateway->getGatewayId() );
                }
                
                try {
                    $res = $gateway->do_checkout( $order_id );
                    if ( $res[ 'status' ] ) {
                        if ( !$gateway->is_check_complete_required() and !$gateway->stop_change_order_status() ) {
                            update_post_meta( $order_id, 'status', 'complete' );
                            do_action( 'st_booking_change_status', 'complete', $order_id, $gateway->getGatewayId() );
                        }

                        if ( isset($res[ 'success' ]) && $res[ 'status' ] != 'incomplete') {
                            update_post_meta( $order_id, 'status', 'complete' );
                            do_action( 'st_booking_change_status', 'complete', $order_id, $gateway->getGatewayId() );
                        }

                        if ( !isset( $res[ 'redirect' ] ) or !$res[ 'redirect' ] ) {
                            $res[ 'redirect' ] = STCart::get_success_link( $order_id );
                        }
                        STCart::send_mail_after_booking( $order_id, true );
                    } else {
                        if(isset($res['error_step']) && $res['error_step'] === 'after_get_authorize_url' ){
                            $res[ 'redirect' ] = STCart::get_success_link( $order_id );
                        }
                        if ( !isset( $res[ 'message' ] ) ) {
                            $res[ 'message' ] = false;
                        } else {
                            $res[ 'message' ] = sprintf( __( '<br>Message: %s', 'traveler' ), $res[ 'message' ] );
                        }
                        $res[ 'message' ] = sprintf( __( 'Your order has been made but we can not process the payment with %s. %s ', 'traveler' ), $gateway->get_name(), $res[ 'message' ] );
                    }
                } catch ( Exception $e ) {
                    $res[ 'redirect' ] = STCart::get_success_link( $order_id );
                    
                    $res[ 'status' ]    = 0;
                    $message            = sprintf( __( '<br>Message: %s', 'traveler' ), $e->getMessage() );
                    $res[ 'exception' ] = $e;
                    $res[ 'message' ]   = sprintf( __( 'Your order has been made but we can not process the payment with %s. %s', 'traveler' ), $gateway->get_name(), $message );
                }
                $res[ 'step' ] = 'do_checkout';
                $res[ 'order_id' ] = (int)$order_id;
                return $res;
            }
            static function package_do_checkout($gateway, $order_id){
                $res = $gateway->package_do_checkout( $order_id );
                try {
                    return $res;
                } catch ( Exception $e ) {
                    $res[ 'status' ]    = TravelHelper::st_encrypt( $order_id . 'st0' );
                    $res[ 'message' ]   = sprintf( __( 'Your order has been made but we can not process the payment with %s. %s', 'traveler' ), $gateway->get_name(), $e->getMessage() );
                }
                return $res;
            }
            static function package_completed_checkout($gateway, $order_id){
                try {
                    $res = $gateway->package_completed_checkout( $order_id );
                } catch ( Exception $e ) {
                    $res[ 'status' ]    = false;
                    $res[ 'message' ]   = sprintf( __( 'Your order has been made but we can not process the payment with %s. %s', 'traveler' ), $gateway->get_name(), $e->getMessage() );
                }
                return $res;
            }
            static function _load_default_gateways()
            {
                if ( !class_exists( 'Omnipay\Omnipay' ) ) return false;
                $path    = STTraveler::dir( 'gateways' );
                $results = scandir( $path );
                foreach ( $results as $result ) {
                    if ( $result === '.' or $result === '..' ) continue;
                    if ( is_dir( $path . '/' . $result ) ) {
                        $file = $path . '/' . $result . '/' . $result . '.php';
                        if ( file_exists( $file ) ) {
                            include_once $file;
                        }
                    }
                }
            }
        }
        STPaymentGateways::_init();
    }

<?php
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
function wbtm_add_custom_fields_text_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
  $tp               = get_post_meta($product_id,'_price',true);
  $price_arr        = get_post_meta($product_id,'wbtm_bus_prices',true);
  $new = array();
  $user = array();

  $start_stops      = sanitize_text_field($_POST['start_stops']);
  $end_stops        = sanitize_text_field($_POST['end_stops']);
  $journey_date     = sanitize_text_field($_POST['journey_date']);
  $seat_name        = wptm_array_strip($_POST['seat_name']);
  $total_seat       = sanitize_text_field($_POST['total_seat']);
  $count_seat       = count($seat_name);
  $main_fare        = wbtm_get_bus_price($start_stops,$end_stops, $price_arr);
  $total_fare       = $main_fare*$count_seat;
  $user_start_time  = sanitize_text_field($_POST['user_start_time']);
  $bus_start_time   = sanitize_text_field($_POST['bus_start_time']);
  $bus_id           = sanitize_text_field($_POST['bus_id']);


if(isset($_POST['wbtm_user_name']) || ($_POST['wbtm_user_email']) || ($_POST['wbtm_user_phone']) || ($_POST['wbtm_user_address']) || ($_POST['wbtm_user_gender'])){


  $wbtm_user_name          = $_POST['wbtm_user_name'];
  $wbtm_user_email         = $_POST['wbtm_user_email'];
  $wbtm_user_phone         = $_POST['wbtm_user_phone'];
  $wbtm_user_address       = $_POST['wbtm_user_address'];
  // $wbtm_user_gender        = "Male";
  $wbtm_user_gender        = $_POST['wbtm_user_gender'];

$count_user = count($wbtm_user_name);
  for ( $iu = 0; $iu < $count_user; $iu++ ) {
    
    if ( $wbtm_user_name[$iu] != '' ) :
      $user[$iu]['wbtm_user_name'] = stripslashes( strip_tags( $wbtm_user_name[$iu] ) );
      endif;

    if ( $wbtm_user_email[$iu] != '' ) :
      $user[$iu]['wbtm_user_email'] = stripslashes( strip_tags( $wbtm_user_email[$iu] ) );
      endif;

    if ( $wbtm_user_phone[$iu] != '' ) :
      $user[$iu]['wbtm_user_phone'] = stripslashes( strip_tags( $wbtm_user_phone[$iu] ) );
      endif;

    if ( $wbtm_user_address[$iu] != '' ) :
      $user[$iu]['wbtm_user_address'] = stripslashes( strip_tags( $wbtm_user_address[$iu] ) );
      endif;

    if ( $wbtm_user_gender[$iu] != '' ) :
      $user[$iu]['wbtm_user_gender'] = stripslashes( strip_tags( $wbtm_user_gender[$iu] ) );
      endif;

    $wbtm_form_builder_data = get_post_meta($product_id, 'wbtm_form_builder_data', true);
    if ( $wbtm_form_builder_data ) {
      foreach ( $wbtm_form_builder_data as $_field ) {
          if ( $wbtm_user_ticket_type[$iu] != '' ) :
            $user[$iu][$_field['wbtm_fbc_id']] = stripslashes( strip_tags( $_POST[$_field['wbtm_fbc_id']][$iu] ) );
            endif; 
      }
    }

}
}else{
  $user ="";
}










  
  $count = count( $seat_name );
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $seat_name[$i] != '' ) :
      $new[$i]['wbtm_seat_name'] = stripslashes( strip_tags( $seat_name[$i] ) );
      endif;
  }




  $cart_item_data['wbtm_seats'] = $new;
  $cart_item_data['wbtm_start_stops'] = $start_stops;
  $cart_item_data['wbtm_end_stops'] = $end_stops;
  $cart_item_data['wbtm_journey_date'] = $journey_date;
  $cart_item_data['wbtm_journey_time'] = $user_start_time;
  $cart_item_data['wbtm_bus_time'] = $bus_start_time;
  $cart_item_data['wbtm_total_seats'] = $total_seat;
  $cart_item_data['wbtm_passenger_info'] = $user;
  $cart_item_data['wbtm_tp'] = $total_fare;
  $cart_item_data['wbtm_bus_id'] = $bus_id;
  $cart_item_data['bus_id'] = $product_id;
  $cart_item_data['line_total'] = $total_fare;
  $cart_item_data['line_subtotal'] = $total_fare;




  return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'wbtm_add_custom_fields_text_to_cart_item', 10, 3 );



add_action( 'woocommerce_before_calculate_totals', 'wbtm_add_custom_price' );
function wbtm_add_custom_price( $cart_object ) {

    foreach ( $cart_object->cart_contents as $key => $value ) {
$eid = $value['bus_id'];
if (get_post_type($eid) == 'wbtm_bus') {      
            $cp = $value['wbtm_tp'];
            $value['data']->set_price($cp);
            $new_price = $value['data']->get_price();
    }
}
}





function wbtm_display_custom_fields_text_cart( $item_data, $cart_item ) {
$wbtm_events_extra_prices = $cart_item['wbtm_seats'];
if($wbtm_events_extra_prices){
echo "<ul class='event-custom-price'><li> Seat List:";
  foreach ( $wbtm_events_extra_prices as $field ) { 

 echo esc_attr( $field['wbtm_seat_name'] ).","; 

}
?>
</li>
<li><?php _e('Journey Date:','bus-ticket-booking-with-seat-reservation'); ?> <?php echo $cart_item['wbtm_journey_date']; ?></li>
<li><?php _e('Journey Time:','bus-ticket-booking-with-seat-reservation'); ?> <?php echo $cart_item['wbtm_journey_time']; ?></li>
<li><?php _e('Boarding Point:','bus-ticket-booking-with-seat-reservation'); ?> <?php echo $cart_item['wbtm_start_stops']; ?></li>
<li><?php _e('Dropping Point:','bus-ticket-booking-with-seat-reservation'); ?> <?php echo $cart_item['wbtm_end_stops']; ?></li>
</ul>
<?php
  return $item_data;
}
}
add_filter( 'woocommerce_get_item_data', 'wbtm_display_custom_fields_text_cart', 10, 2 );




function wbtm_add_custom_fields_text_to_order_items( $item, $cart_item_key, $values, $order ) {
$eid = $values['bus_id'];
if (get_post_type($eid) == 'wbtm_bus') { 
$wbtm_seats              = $values['wbtm_seats'];
$wbtm_passenger_info     = $values['wbtm_passenger_info'];
$wbtm_start_stops        = $values['wbtm_start_stops'];
$wbtm_end_stops          = $values['wbtm_end_stops'];
$wbtm_journey_date       = $values['wbtm_journey_date'];
$wbtm_journey_time       = $values['wbtm_journey_time'];
$wbtm_bus_start_time     = $values['wbtm_bus_time'];
$wbtm_bus_id             = $values['wbtm_bus_id'];


$seat ="";
foreach ( $wbtm_seats as $field ) {
      // $item->add_meta_data( __( esc_attr($field['wbtm_seat_name'])));
  $seat .= $field['wbtm_seat_name'].",";
} 
// .$seat =0;
$item->add_meta_data('Seats',$seat);
$item->add_meta_data('Start',$wbtm_start_stops);
$item->add_meta_data('End',$wbtm_end_stops);
$item->add_meta_data('Date',$wbtm_journey_date);
$item->add_meta_data('Time',$wbtm_journey_time);
$item->add_meta_data('_bus_id',$wbtm_bus_id);
$item->add_meta_data('_btime',$wbtm_bus_start_time);
$item->add_meta_data('_wbtm_passenger_info',$wbtm_passenger_info);


}

$item->add_meta_data('_wbtm_bus_id',$eid);
}
add_action( 'woocommerce_checkout_create_order_line_item', 'wbtm_add_custom_fields_text_to_order_items', 10, 4 );
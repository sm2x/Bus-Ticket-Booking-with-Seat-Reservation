<?php 
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

add_action( 'add_meta_boxes', 'wbtm_bus_meta_box_add' );
function wbtm_bus_meta_box_add(){
  

    add_meta_box( 'wbtm-bus-ticket-type', 'Bus Ticket Panel', 'wbtm_bus_ticket_type', 'wbtm_bus', 'normal', 'high' );

    add_meta_box( 'wbtm-bus-date', 'Bus Stops Info', 'wbtm_bus_date_meta_box_cb', 'wbtm_bus', 'normal', 'high' );

    add_meta_box( 'wbtm-bus-price', 'Bus Pricing', 'wbtm_bus_pricing_meta_box_cb', 'wbtm_bus', 'normal', 'high' );


    add_meta_box( 'wbtm-bus-info-form', 'Bus Information', 'wbtm_bus_info_meta_box', 'wbtm_bus', 'normal', 'high' );
    add_meta_box( 'wbtm-bus-operational-date', 'Operational offday settings', 'wbtm_bus_od_meta_box', 'wbtm_bus', 'normal', 'high' );

    // add_meta_box( 'wbtm-bus-reg-form', 'Passenger Registration Form', 'wbtm_bus_reg_form_meta_box_cb', 'wbtm_bus', 'normal', 'high' );
}

function wbtm_remove_post_custom_fields() {
  // remove_meta_box( 'tagsdiv-wbtm_seat' , 'wbtm_bus' , 'side' ); 
  remove_meta_box( 'wbtm_seat_typediv' , 'wbtm_bus' , 'side' ); 
  remove_meta_box( 'wbtm_bus_stopsdiv' , 'wbtm_bus' , 'side' ); 
  remove_meta_box( 'wbtm_bus_routediv' , 'wbtm_bus' , 'side' ); 
}
add_action( 'admin_menu' , 'wbtm_remove_post_custom_fields' );


function wbtm_bus_ticket_type() {
  global $post;
  $values           = get_post_custom( $post->ID );
  // $mep_bus_seat     = get_post_meta($post->ID, 'wbtm_bus_seat', true);
?>
<style type="text/css">
div#webmenu_msdd {
    width: 250px!important;
}
table#repeatable-fieldset-seat-one tr td input {
    width: auto;
    min-width: 20px;
    max-width: 60px;
}
</style>
<table style="width: 100%;margin: 30px auto;">
  <tr>
    <th><?php _e('Driver Seat Position','bus-ticket-booking-with-seat-reservation'); ?></th>
    <td>
      <?php 

if(array_key_exists('driver_seat_position', $values)){
  $position = $values['driver_seat_position'][0];
}else{
  $position = 'left';
}
      wbtm_get_driver_position($position); 
?>
    </td>
    <td>
      <label><?php _e('Total Seat Columns','bus-ticket-booking-with-seat-reservation'); ?>
        <input type="number" value='<?php if(array_key_exists('wbtm_seat_cols', $values)){ echo $values['wbtm_seat_cols'][0]; } ?>' name="seat_col" id='seat_col' style="width: 70px;" pattern="[1-9]*" inputmode="numeric"  min="0" max="">
      </label>
    </td>    
    <td>
      <label><?php _e('Total Seat Rows','bus-ticket-booking-with-seat-reservation'); ?>
        <input type="number" value='<?php if(array_key_exists('wbtm_seat_rows', $values)){ echo $values['wbtm_seat_rows'][0]; } ?>' name="seat_rows" id='seat_rows' style="width: 70px;" pattern="[1-9]*" inputmode="numeric"  min="0" max="">
      </label>
    </td>
    <td><button id="create_seat_plan"><?php _e('Create Seat Plan','bus-ticket-booking-with-seat-reservation'); ?></button></td>
  </tr>  
</table>
<div id="seat_result">
  <?php  
  if(array_key_exists('wbtm_bus_seats_info', $values)){  
  $old        = $values['wbtm_bus_seats_info'][0]; 
  $seatrows   = $values['wbtm_seat_rows'][0]; 
  $seatcols   = $values['wbtm_seat_cols'][0]; 
  $seats      = unserialize($old);
// if($old){
  ?>
<script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-seat-row' ).on('click', function() {
      var row = $( '.empty-row-seat.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-seat screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-seat-one tbody>tr:last' );
      var qtt = parseInt($('#seat_rows').val(), 10);
      $('#seat_rows').val(qtt+1);
      return false;
    });    
    $( '.remove-seat-row' ).on('click', function() {
      $(this).parents('tr').remove();
      var qtt = parseInt($('#seat_rows').val(), 10);
      $('#seat_rows').val(qtt-1);
      return false;
    });
  });
</script>
<table id="repeatable-fieldset-seat-one" width="100%">
<tbody>
<?php 
foreach ($seats as $_seats) {
?>
    <tr>
      <?php
      for ($x=1; $x <=$seatcols; $x++){
          $text_field_name = "seat" . $x;
          ?>
          <td align="center"><input type="text" value="<?php echo $_seats[$text_field_name]; ?>" name="<?php echo $text_field_name; ?>[]"  class="text"></td>
          <?php
      }
      ?>
          <td align="center"><a class="button remove-seat-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a>
            <input type="hidden" name="bus_seat_panels[]">
          </td>      
    </tr>
<?php } ?>
    <!-- empty hidden one for jQuery -->
    <tr class="empty-row-seat screen-reader-text">
      <?php
      for ($row = 1; $row <= $seatcols; $row++) {
      ?>
        <td align="center"><input type="text" value="" name="seat<?php echo $row; ?>[]"  class="text"></td>
      <?php } ?>
        <td align="center"><a class="button remove-seat-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a><input type="hidden" name="bus_seat_panels[]"></td>
    </tr>
  </tbody>
</table>
<p><a id="add-seat-row" class="button" href="#"><?php _e('Add New Seat Row','bus-ticket-booking-with-seat-reservation'); ?></a></p>
<?php } ?>
</div>
<script type="text/javascript">
  jQuery( "#create_seat_plan" ).click(function(e) {
     e.preventDefault();
     // alert('Yes');
        seat_col        = jQuery("#seat_col").val().trim();
        seat_row        = jQuery("#seat_rows").val().trim();       
        jQuery.ajax({
          type: 'POST',
          url:wbtm_ajax.wbtm_ajaxurl,
          data: {"action": "wbtm_seat_plan", "seat_col":seat_col, "seat_row":seat_row},
        beforeSend: function(){
            jQuery('#seat_result').html('<span class=search-text style="display:block;background:#ddd:color:#000:font-weight:bold;text-align:center">Creating Seat Plan...</span>');
                },          
        success: function(data)
            { 
              jQuery('#seat_result').html(data);
            }
          });
         return false;
      })
</script>
<?php
}



add_action('save_post','wbtm_bus_seat_panels_meta_save');
function wbtm_bus_seat_panels_meta_save($post_id){
    global $post; 
    if($post){
    $pid = $post->ID;
    if ($post->post_type != 'wbtm_bus'){
        return;
    }
    $seat_col             = strip_tags($_POST['seat_col']);
    $seat_row             = strip_tags($_POST['seat_rows']);
    $old = get_post_meta($post_id, 'wbtm_bus_seats_info', true);
    $new = array();
    $bus_seat_panels   = $_POST['bus_seat_panels'];
    $count             = count( $bus_seat_panels )-2;
    for ( $r = 0; $r <= $count; $r++ ) {
        for ($x=1; $x <= $seat_col; $x++ ){
            $text_field_name = "seat" . $x;         
              $new[$r][$text_field_name] = stripslashes( strip_tags($_POST[$text_field_name][$r] ));
        }
    }
  if (!empty($new) && $new != $old )
    update_post_meta( $post_id, 'wbtm_bus_seats_info', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'wbtm_bus_seats_info', $old );

  $update_seat_col      = update_post_meta( $pid, 'wbtm_seat_cols', $seat_col);
  $update_seat_row      = update_post_meta( $pid, 'wbtm_seat_rows', $seat_row);
  $driver_seat_position  = strip_tags($_POST['driver_seat_position']);
  $update_wbtm_driver_seat_position     = update_post_meta( $pid, 'driver_seat_position', $driver_seat_position);
  $update_seat_stock_status         = update_post_meta( $pid, '_sold_individually', 'yes');
}





}

// add_action('save_post','wbtm_bus_seat_panel_info_meta_save');
function wbtm_bus_seat_panel_info_meta_save($post_id){
    global $post; 
    if($post){
    $pid = $post->ID;
    if ($post->post_type != 'wbtm_bus'){
        return;
    }

    $seat_plan             = strip_tags($_POST['seat_plan']);

    $update_wbtm_seat_plan     = update_post_meta( $pid, 'seat_plan', $seat_plan);    
    $seat_col             = strip_tags($_POST['seat_column']);
    $seat_row             = strip_tags($_POST['seat_row']);
    $update_seat_col      = update_post_meta( $pid, 'wbtm_seat_col', $seat_col);
    $update_seat_row      = update_post_meta( $pid, 'wbtm_seat_row', $seat_row);
}
}



// add_action('save_post','wbtm_bus_seat_panel_meta_save');
function wbtm_bus_seat_panel_meta_save($post_id){
    global $post; 
if($post){    
    $pid = $post->ID;
    if ($post->post_type != 'wbtm_bus'){
        return;
    }
    $values = get_post_custom( $post->ID );
    $seat_col = $values['wbtm_seat_col'][0];
    $seat_row = $values['wbtm_seat_row'][0];

$seat_col_arr = explode(",",$seat_col);
$seat_row_arr = explode(",",$seat_row);
    foreach ($seat_row_arr as $seat_row) {
      foreach ($seat_col_arr as $seat_col) {
        $seat_field_name = $seat_row.$seat_col;
        $seat_field_type_name = "wbtm_seat_type_".$seat_row.$seat_col;

        update_post_meta( $pid, "wbtm_seat_".$seat_field_name, $_POST[$seat_field_name]);
        update_post_meta( $pid, "wbtm_seat_type_".$seat_field_name, $_POST[$seat_field_type_name]);
      }
   }
}
}


function wbtm_bus_date_meta_box_cb($post){
    global $post;
    $mep_event_faq  = get_post_meta($post->ID, 'wbtm_bus_next_stops', true);
    $mep_bus_bp     = get_post_meta($post->ID, 'wbtm_bus_bp_stops', true);
    $values         = get_post_custom( $post->ID );
    wp_nonce_field( 'wbtm_bus_ticket_type_nonce', 'wbtm_bus_ticket_type_nonce' );
    // echo '<pre>'; print_r( $mep_event_faq ); echo '</pre>';

  $get_terms_default_attributes = array (
            'taxonomy' => 'wbtm_bus_stops',
            'hide_empty' => false
    );
  $terms = get_terms($get_terms_default_attributes);
  if($terms){
?>



<script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-faq-row' ).on('click', function() {
      var row = $( '.empty-row-faq.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-faq screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-faq-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-faq-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });

    $( '#add-bp-row' ).on('click', function() {
      var row = $( '.empty-row-bp.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-bp screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-bp-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-bp-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
</script>

<table id="repeatable-fieldset-bp-one" width="100%">
  <tr>
    <th>Boarding Point</th>
    <th>Time</th>
    <th></th>
  </tr>
  <tbody>
  <?php
  if ( $mep_bus_bp ) :
    $count = 0;
  foreach ( $mep_bus_bp as $field ) {
  ?>
  <tr>
    <td align="center"><?php echo wbtm_get_next_bus_stops_list('wbtm_bus_bp_stops_name[]','wbtm_bus_bp_stops_name','wbtm_bus_bp_stops',$count); ?></td>
    <td align="center"><input type="text" data-clocklet name='wbtm_bus_bp_start_time[]' value="<?php if($field['wbtm_bus_bp_start_time'] != '') echo esc_attr( $field['wbtm_bus_bp_start_time'] ); ?>" class="text"></td>
    <td align="center"><a class="button remove-faq-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a></td>
  </tr>
  <?php
  $count++;
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row-bp screen-reader-text">
    <td align="center"><?php echo wbtm_get_bus_stops_list('wbtm_bus_bp_stops_name[]'); ?></td>
    <td align="center"><input type="text" data-clocklet name='wbtm_bus_bp_start_time[]' value="" class="text"></td>
    <td align="center"><a class="button remove-bp-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a></td>
  </tr>
  </tbody>
  </table>
  <p><a id="add-bp-row" class="button" href="#"><?php _e('Add More Boarding Point','bus-ticket-booking-with-seat-reservation'); ?></a></p>

<table id="repeatable-fieldset-faq-one" width="100%">
  <tr>
    <th><?php _e('Dropping Point','bus-ticket-booking-with-seat-reservation'); ?></th>
    <th><?php _e('Time','bus-ticket-booking-with-seat-reservation'); ?></th>
    <th></th>
  </tr>
  <tbody>
  <?php
  if ( $mep_event_faq ) :
    $coun = 0;
  foreach ( $mep_event_faq as $field ) {
  ?>
  <tr>
    <td align="center"><?php echo wbtm_get_next_bus_stops_list('wbtm_bus_next_stops_name[]','wbtm_bus_next_stops_name','wbtm_bus_next_stops',$coun); ?></td>
    <td align="center"><input type="text" data-clocklet name='wbtm_bus_next_end_time[]' value="<?php if($field['wbtm_bus_next_end_time'] != '') echo esc_attr( $field['wbtm_bus_next_end_time'] ); ?>" class="text"></td>
    <td align="center"><a class="button remove-faq-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a></td>
  </tr>
  <?php
  $coun++;
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row-faq screen-reader-text">
    <td align="center"><?php echo wbtm_get_bus_stops_list('wbtm_bus_next_stops_name[]'); ?></td>
    <td align="center"><input type="text" data-clocklet name='wbtm_bus_next_end_time[]' value="" class="text"></td>
    <td align="center"><a class="button remove-faq-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a></td>
  </tr>
  </tbody>
  </table>
  <p><a id="add-faq-row" class="button" href="#"><?php _e('Add More Droping Point','bus-ticket-booking-with-seat-reservation'); ?></a></p>


<label for="show-details">
  <input type="checkbox" name="show_boarding_points" value="yes" id="show-details" <?php if(array_key_exists('show_boarding_points', $values)){ if($values['show_boarding_points'][0]=='yes'){ echo 'Checked'; } } ?>> <?php _e("Don't Show Boarding and Dropping Points in Details Page.","bus-ticket-booking-with-seat-reservation"); ?>
</label>


<?php
}else{
  echo "<div style='padding: 10px 0;text-align: center;background: #d23838;color: #fff;border: 5px solid #ff2d2d;padding: 5;font-size: 16px;display: block;margin: 20px;'>Please Enter some bus stops first. <a style='color:#fff' href='".get_admin_url()."edit-tags.php?taxonomy=wbtm_bus_stops&post_type=wbtm_bus'>Click here for bus stops</a></div>";
}

}


function wbtm_bus_pricing_meta_box_cb($post){
    global $post;
    $wbtm_bus_prices  = get_post_meta($post->ID, 'wbtm_bus_prices', true);
    $values         = get_post_custom( $post->ID );
    wp_nonce_field( 'wbtm_bus_price_nonce', 'wbtm_bus_price_nonce' );
    // echo '<pre>'; print_r( $mep_event_faq ); echo '</pre>';
      $get_terms_default_attributes = array (
            'taxonomy' => 'wbtm_bus_stops',
            'hide_empty' => false
    );
  $terms = get_terms($get_terms_default_attributes);
  if($terms){
?>

<script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-price-row' ).on('click', function() {
      var row = $( '.empty-row-price.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-price screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-price-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-price-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });

  });
</script>

<table id="repeatable-fieldset-price-one" width="100%">
  <tr>
    <th><?php _e('Boarding Point','bus-ticket-booking-with-seat-reservation'); ?></th>
    <th><?php _e('Dropping Point','bus-ticket-booking-with-seat-reservation'); ?></th>
    <th><?php _e('Fare','bus-ticket-booking-with-seat-reservation'); ?></th>
    <th></th>
  </tr>
  <tbody>
  <?php
  if ( $wbtm_bus_prices ) :
    $coun = 0;
  foreach ( $wbtm_bus_prices as $field ) {
  ?>
  <tr>
    <td><?php echo wbtm_get_next_bus_stops_list('wbtm_bus_bp_price_stop[]','wbtm_bus_bp_price_stop','wbtm_bus_prices',$coun); ?></td>

    <td><?php echo wbtm_get_next_bus_stops_list('wbtm_bus_dp_price_stop[]','wbtm_bus_dp_price_stop','wbtm_bus_prices',$coun); ?></td>

    <td><input type="number" name='wbtm_bus_price[]' value="<?php if($field['wbtm_bus_price'] != '') echo esc_attr( $field['wbtm_bus_price'] ); ?>" class="text"></td>
    
    <td><a class="button remove-price-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a></td>
  </tr>
  <?php
  $coun++;
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row-price screen-reader-text">
    <td><?php echo wbtm_get_bus_stops_list('wbtm_bus_bp_price_stop[]'); ?></td>
    <td><?php echo wbtm_get_bus_stops_list('wbtm_bus_dp_price_stop[]'); ?></td>
    <td><input type="number" name='wbtm_bus_price[]' value="" class="text"></td>
    <td><a class="button remove-price-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a></td>
  </tr>
  </tbody>
  </table>
  <p><a id="add-price-row" class="button" href="#"><?php _e('Add More Price','bus-ticket-booking-with-seat-reservation'); ?></a></p>

<?php
}else{
  echo "<div style='padding: 10px 0;text-align: center;background: #d23838;color: #fff;border: 5px solid #ff2d2d;padding: 5;font-size: 16px;display: block;margin: 20px;'>Please Enter some bus stops first. <a style='color:#fff' href='".get_admin_url()."edit-tags.php?taxonomy=wbtm_bus_stops&post_type=wbtm_bus'>Click here for bus stops</a></div>";
}

}










add_action('save_post', 'wbtm_bus_pricing_save');
function wbtm_bus_pricing_save($post_id) {
  global $wpdb;

  if ( ! isset( $_POST['wbtm_bus_price_nonce'] ) ||
  ! wp_verify_nonce( $_POST['wbtm_bus_price_nonce'], 'wbtm_bus_price_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  
  $old = get_post_meta($post_id, 'wbtm_bus_prices', true);
  $new = array();

  $bp_pice_stops   = $_POST['wbtm_bus_bp_price_stop'];
  $dp_pice_stops   = $_POST['wbtm_bus_dp_price_stop'];
  $the_price       = $_POST['wbtm_bus_price'];
  
  $order_id = 0;
  $count = count( $bp_pice_stops );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $bp_pice_stops[$i] != '' ) :
      $new[$i]['wbtm_bus_bp_price_stop'] = stripslashes( strip_tags( $bp_pice_stops[$i] ) );
      endif;

    if ( $dp_pice_stops[$i] != '' ) :
      $new[$i]['wbtm_bus_dp_price_stop'] = stripslashes( strip_tags( $dp_pice_stops[$i] ) );
      endif;

    if ( $the_price[$i] != '' ) :
      $new[$i]['wbtm_bus_price'] = stripslashes( strip_tags( $the_price[$i] ) );
      endif;

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'wbtm_bus_prices', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'wbtm_bus_prices', $old );
}










add_action('save_post', 'wbtm_bus_boarding_points_save');
function wbtm_bus_boarding_points_save($post_id) {
  global $wpdb;

  if ( ! isset( $_POST['wbtm_bus_ticket_type_nonce'] ) ||
  ! wp_verify_nonce( $_POST['wbtm_bus_ticket_type_nonce'], 'wbtm_bus_ticket_type_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  
  $old = get_post_meta($post_id, 'wbtm_bus_bp_stops', true);
  $new = array();

  $bp_stops  = $_POST['wbtm_bus_bp_stops_name'];
  $start_t  = $_POST['wbtm_bus_bp_start_time'];
  


  $order_id = 0;
  $count = count( $bp_stops );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $bp_stops[$i] != '' ) :
      $new[$i]['wbtm_bus_bp_stops_name'] = stripslashes( strip_tags( $bp_stops[$i] ) );
      endif;

    if ( $start_t[$i] != '' ) :
      $new[$i]['wbtm_bus_bp_start_time'] = stripslashes( strip_tags( $start_t[$i] ) );
      endif;
  }

$bstart_time      = $new[0]['wbtm_bus_bp_start_time'];
update_post_meta( get_the_id(), 'wbtm_bus_start_time', $bstart_time );

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'wbtm_bus_bp_stops', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'wbtm_bus_bp_stops', $old );
}


add_action('save_post', 'wbtm_bus_droping_stops_save');
function wbtm_bus_droping_stops_save($post_id) {
  global $wpdb;

  if ( ! isset( $_POST['wbtm_bus_ticket_type_nonce'] ) ||
  ! wp_verify_nonce( $_POST['wbtm_bus_ticket_type_nonce'], 'wbtm_bus_ticket_type_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  
  $old = get_post_meta($post_id, 'wbtm_bus_next_stops', true);
  $new = array();

  $stops  = $_POST['wbtm_bus_next_stops_name'];
  $end_t  = $_POST['wbtm_bus_next_end_time'];
  


  $order_id = 0;
  $count = count( $stops );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $stops[$i] != '' ) :
      $new[$i]['wbtm_bus_next_stops_name'] = stripslashes( strip_tags( $stops[$i] ) );
      endif;

    if ( $end_t[$i] != '' ) :
      $new[$i]['wbtm_bus_next_end_time'] = stripslashes( strip_tags( $end_t[$i] ) );
      endif;

    $opt_name =  $post_id.str_replace(' ', '', $names[$i]);

    // update_post_meta( $post_id, "mep_xtra_$opt_name",0 );

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'wbtm_bus_next_stops', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'wbtm_bus_next_stops', $old );
}


function wbtm_bus_info_meta_box($post){
$values = get_post_custom( $post->ID );
// print_r($values);
?>

<div class='sec'>
    <label for="mep_ev_98">  
      Coach No
    <span><input id='mep_ev_98' type="text" name='wbtm_bus_no' value='<?php if(array_key_exists('wbtm_bus_no', $values)){ echo $values['wbtm_bus_no'][0]; } ?>'/>   </span></label>
</div>

<div class='sec'>
    <label for="mep_ev_99">  
      Total Seat
    <span><input id='mep_ev_99' type="text" name='wbtm_total_seat' value='<?php if(array_key_exists('wbtm_total_seat', $values)){ echo $values['wbtm_total_seat'][0]; } ?>'/>   </span></label>
</div>

<?php
}

add_action('save_post','wbtm_events_meta_save');
function wbtm_events_meta_save($post_id){
    global $post; 
if($post){    
    $pid = $post->ID;
    if ($post->post_type != 'wbtm_bus'){
        return;
    }
    $wbtm_bus_no                     = strip_tags($_POST['wbtm_bus_no']);
    $wbtm_total_seat                 = strip_tags($_POST['wbtm_total_seat']);
    $update_seat_stock_status        = update_post_meta( $pid, '_manage_stock', 'no');
    $update_price                    = update_post_meta( $pid, '_price', 0);
    $update_seat5                    = update_post_meta( $pid, 'wbtm_bus_no', $wbtm_bus_no);
    $update_seat6                    = update_post_meta( $pid, 'wbtm_total_seat', $wbtm_total_seat);
    
}
}


function wbtm_bus_od_meta_box($post){
$values = get_post_custom( $post->ID );
// print_r($values);
?>

<div class='sec'>
    <label for="od_start">  
Offday Start Date
    <span><input type="text" id='od_start' name='wbtm_od_start' value='<?php if(array_key_exists('wbtm_od_start', $values)){ echo $values['wbtm_od_start'][0]; } ?>'/>   </span></label>
</div>

<div class='sec'>
    <label for="od_end">  
Offday End date
    <span><input type="text" id='od_end' name='wbtm_od_end' value='<?php if(array_key_exists('wbtm_od_end', $values)){ echo $values['wbtm_od_end'][0]; } ?>'/>   </span></label>
</div>




<div class='sec'>
  <label for='sun'>
<input type="checkbox" id='sun' style="text-align: left;width: auto;"  name="od_sun" value='yes' <?php if(array_key_exists('od_Sun', $values)){ if($values['od_Sun'][0]=='yes'){ echo 'Checked'; } } ?>/> <?php _e('Sunday','bus-ticket-booking-with-seat-reservation'); ?>
</label>
  <label for='mon'>
<input type="checkbox" style="text-align: left;width: auto;"  name="od_mon" value='yes' id='mon' <?php if(array_key_exists('od_Mon', $values)){ if($values['od_Mon'][0]=='yes'){ echo 'Checked'; } } ?>> <?php _e('Monday','bus-ticket-booking-with-seat-reservation'); ?>
  </label>
  <label for='tue'>
<input type="checkbox" style="text-align: left;width: auto;"  name="od_tue" value='yes' id='tue' <?php if(array_key_exists('od_Tue', $values)){ if($values['od_Tue'][0]=='yes'){ echo 'Checked'; } } ?>> <?php _e('Tuesday','bus-ticket-booking-with-seat-reservation'); ?>
  </label>
  <label for='wed'>
<input type="checkbox" style="text-align: left;width: auto;"  name="od_wed" value='yes' id='wed' <?php if(array_key_exists('od_Wed', $values)){ if($values['od_Wed'][0]=='yes'){ echo 'Checked'; } } ?>> <?php _e('Wednesday','bus-ticket-booking-with-seat-reservation'); ?>
  </label>
  <label for='thu'>
<input type="checkbox" style="text-align: left;width: auto;"  name="od_thu" value='yes' id='thu' <?php if(array_key_exists('od_Thu', $values)){ if($values['od_Thu'][0]=='yes'){ echo 'Checked'; } } ?>> <?php _e('Thursday','bus-ticket-booking-with-seat-reservation'); ?>
  </label>
  <label for='fri'>
<input type="checkbox" style="text-align: left;width: auto;"  name="od_fri" value='yes' id='fri' <?php if(array_key_exists('od_Fri', $values)){ if($values['od_Fri'][0]=='yes'){ echo 'Checked'; } } ?>> <?php _e('Friday','bus-ticket-booking-with-seat-reservation'); ?>
 </label>
  <label for='sat'>
<input type="checkbox" style="text-align: left;width: auto;"  name="od_sat" value='yes' id='sat' <?php if(array_key_exists('od_Sat', $values)){ if($values['od_Sat'][0]=='yes'){ echo 'Checked'; } } ?>> <?php _e('Saturday','bus-ticket-booking-with-seat-reservation'); ?>
</label>
</div>
<?php
}


add_action('save_post','wbtm_bus_od_info_save');
function wbtm_bus_od_info_save($post_id){
global $post; 
if($post){    
    $pid = $post->ID;
    if ($post->post_type != 'wbtm_bus'){
        return;
    }
    $wbtm_od_start         = strip_tags($_POST['wbtm_od_start']);
    $wbtm_od_end           = strip_tags($_POST['wbtm_od_end']);
    $od_sun                = strip_tags($_POST['od_sun']);
    $od_mon                = strip_tags($_POST['od_mon']);
    $od_tue                = strip_tags($_POST['od_tue']);
    $od_wed                = strip_tags($_POST['od_wed']);
    $od_thu                = strip_tags($_POST['od_thu']);
    $od_fri                = strip_tags($_POST['od_fri']);
    $od_sat                = strip_tags($_POST['od_sat']);
    $show_boarding_points  = strip_tags($_POST['show_boarding_points']);
    $update_virtual         = update_post_meta( $pid, '_virtual', 'yes');
    $update_wbtm_od_start   = update_post_meta( $pid, 'wbtm_od_start', $wbtm_od_start);
    $update_wbtm_od_end     = update_post_meta( $pid, 'wbtm_od_end', $wbtm_od_end);
    $update_wbtm_od_sun     = update_post_meta( $pid, 'od_Sun', $od_sun);
    $update_wbtm_od_mon     = update_post_meta( $pid, 'od_Mon', $od_mon);
    $update_wbtm_od_tue     = update_post_meta( $pid, 'od_Tue', $od_tue);
    $update_wbtm_od_wed     = update_post_meta( $pid, 'od_Wed', $od_wed);
    $update_wbtm_od_thu     = update_post_meta( $pid, 'od_Thu', $od_thu);
    $update_wbtm_od_fri     = update_post_meta( $pid, 'od_Fri', $od_fri);
    $update_wbtm_od_sat     = update_post_meta( $pid, 'od_Sat', $od_sat);
    $update_wbtm_show_boarding_points   = update_post_meta( $pid, 'show_boarding_points', $show_boarding_points);
    
}
}
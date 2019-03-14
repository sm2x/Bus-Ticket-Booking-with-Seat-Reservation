<?php
function wbtm_seat_global($start,$date){
	$seats = get_post_meta(get_the_id(),'wbtm_bus_seats_info',true);
    $current_driver_position = get_post_meta(get_the_id(),'driver_seat_position',true);
    $seatrows 				 = get_post_meta(get_the_id(),'wbtm_seat_rows',true);
    $seatcols 				 = get_post_meta(get_the_id(),'wbtm_seat_cols',true);
    if($current_driver_position){
        $current_driver = $current_driver_position;
    }else{
        $current_driver = 'driver_right';
    }
?>

<?php 
     $start  = isset( $_GET['bus_start_route'] ) ? strip_tags($_GET['bus_start_route']) : '';
     $end    = isset( $_GET['bus_end_route'] ) ? strip_tags($_GET['bus_end_route']) : '';
     $bus_bp_array = get_post_meta(get_the_id(),'wbtm_bus_bp_stops',true);
     $bus_dp_array = get_post_meta(get_the_id(),'wbtm_bus_next_stops',true);
     $bp_time = wbtm_get_bus_start_time($start, $bus_bp_array);
     $dp_time = wbtm_get_bus_end_time($end, $bus_dp_array);
  if(wbtm_buffer_time_check($bp_time,$date) == 'yes'){
  ?>
       
<div class="bus-seat-panel">
  <img src="<?php echo plugin_dir_url( __FILE__ ).'images/'.$current_driver.'.png'; ?>">
   <table class="bus-seats" width="300" border="1" style="width: 211px;
    border: 0px solid #ddd;">
<?php 
foreach ($seats as $_seats) {
?>
    <tr class="seat<?php echo get_the_id(); ?>_lists ">
      <?php
      for ($x=1; $x <=$seatcols; $x++){
          $text_field_name 		= "seat" . $x;
          $seat_name   			= $_seats[$text_field_name];
          $get_seat_status    	= wbtm_get_seat_status($_seats[$text_field_name],$date,get_the_id(),$start);
          if($get_seat_status) {
            $seat_status        = $get_seat_status[0]->status;
          }else{
            $seat_status        = 0; 
           }
          ?>
          <td align="center">
			<?php if($_seats[$text_field_name]){ ?>
              <?php if( $seat_status == 1 ) { ?> <span class="booked-seat"><?php echo $seat_name; ?></span>
              <?php } elseif($seat_status==2) { ?><span class="confirmed-seat"><?php echo $seat_name; ?></span>
              <?php } else { ?> 				
          	<a data-seat='<?php echo $_seats[$text_field_name]; ?>' id='seat<?php echo get_the_id(); ?>_<?php echo $_seats[$text_field_name]; ?>' data-sclass='Economic' class='seat<?php echo get_the_id(); ?>_blank blank_seat'>
          		<?php echo $_seats[$text_field_name]; ?></a>
			<?php } } ?>
          </td>
          <?php
      }
      ?>     
    </tr>
<?php } ?>
</table>
</div>
<?php
}else{

?>
 <tr>
  <td colspan="10" style="text-align: center;"><?php _e('No Bus Found, Try Another Date.','bus-ticket-booking-with-seat-reservation'); ?></td>
  </tr>

<?php
}

}
?>
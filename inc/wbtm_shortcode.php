<?php
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.


add_shortcode( 'wbtm-bus-list', 'wbtm_bus_list' );
function wbtm_bus_list($atts, $content=null){
        $defaults = array(
            "cat"                   => "0",
            "show"                  => "20",
        );
        $params                     = shortcode_atts($defaults, $atts);
        $cat                        = $params['cat'];
        $show                       = $params['show'];
ob_start();
 

$paged = get_query_var("paged")?get_query_var("paged"):1;
if($cat>0){
     $args_search_qqq = array (
                     'post_type'        => array( 'wbtm_bus' ),
                     'paged'            => $paged,
                     'posts_per_page'   => $show,
                      'tax_query'       => array(
                                array(
                                        'taxonomy'  => 'wbtm_bus_cat',
                                        'field'     => 'term_id',
                                        'terms'     => $cat
                                    )
                        )

                );
 }else{
     $args_search_qqq = array (
                     'post_type'        => array( 'wbtm_bus' ),
                     'paged'             => $paged,
                     'posts_per_page'   => $show

                );  
 }

    $loop = new WP_Query( $args_search_qqq );
?>
<div class="wbtm-bus-list-sec">
    
<?php 
    while ($loop->have_posts()) {
    $loop->the_post(); 
    $bp_arr = get_post_meta(get_the_id(),'wbtm_bus_bp_stops',true); 
    $dp_arr = get_post_meta(get_the_id(),'wbtm_bus_next_stops',true);
    $price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);
    $total_dp = count($dp_arr)-1;
    $term = get_the_terms(get_the_id(),'wbtm_bus_cat');
?>

<div class="wbtm-bus-lists">
    <div class="bus-thumb">
        <?php the_post_thumbnail('full'); ?>
    </div>
    <h2><?php the_title(); ?></h2>
    <ul>
        <li><strong><?php _e('Type:','bus-ticket-booking-with-seat-reservation'); ?></strong> <?php echo $term[0]->name; ?></li>
        <li><strong><?php _e('Bus No:','bus-ticket-booking-with-seat-reservation'); ?></strong> <?php echo get_post_meta(get_the_id(),'wbtm_bus_no',true); ?></li>
        <li><strong><?php _e('Start From:','bus-ticket-booking-with-seat-reservation'); ?></strong> <?php echo $start = $bp_arr[0]['wbtm_bus_bp_stops_name'];; ?> </li>
        <li><strong><?php _e('End at:','bus-ticket-booking-with-seat-reservation'); ?></strong> <?php echo $end = $dp_arr[$total_dp]['wbtm_bus_next_stops_name'];; ?> </li>
        <li><strong><?php _e('Fare:','bus-ticket-booking-with-seat-reservation'); ?></strong> <?php echo get_woocommerce_currency_symbol().wbtm_get_bus_price($start,$end, $price_arr); ?> </li>
    </ul>
    <a href="<?php the_permalink(); ?>" class='btn wbtm-bus-list-btn'><?php _e('Book Now','bus-ticket-booking-with-seat-reservation'); ?></a>
</div>
<?php
}
?>
<div class="row">
    <div class="col-md-12"><?php
    $pargs = array(
        "current"=>$paged,
        "total"=>$loop->max_num_pages
    );
    echo "<div class='pagination-sec'>".paginate_links($pargs)."</div>";
    ?>  
    </div>
</div>
</div>
<?php
$content = ob_get_clean();
return $content;
}

add_shortcode( 'wbtm-bus-search', 'wbtm_bus_search' );
function wbtm_bus_search($atts, $content=null){
        $defaults = array(
            "cat"                   => "0"
        );
        $params                     = shortcode_atts($defaults, $atts);
        $cat                        = $params['cat'];
ob_start();

$start  = isset( $_GET['bus_start_route'] ) ? strip_tags($_GET['bus_start_route']) : '';
$end    = isset( $_GET['bus_end_route'] ) ? strip_tags($_GET['bus_end_route']) : '';
$date   = isset( $_GET['j_date'] ) ? strip_tags($_GET['j_date']) : date('Y-m-d');
$rdate  = isset( $_GET['r_date'] ) ? strip_tags($_GET['r_date']) : date('Y-m-d');
$today = date('Y-m-d');
$the_day = date('D', strtotime($date));
$od_name = 'od_'.$the_day;
?>
<?php do_action( 'woocommerce_before_single_product' ); ?>
<div class="wbtm-search-form-sec">
    <form action="" method="get">
   <?php wbtm_bus_search_fileds($start,$end,$date,$rdate); //do_action('wbtm_search_fields'); ?>
    </form>
</div>



<div class="wbtm-search-result-list">
<?php 
if(isset($_GET['bus_start_route']) && ($_GET['bus_end_route']) && ($_GET['j_date'])){
    
?>
 <div class="selected_route">
     <strong><?php _e('Route','bus-ticket-booking-with-seat-reservation'); ?></strong>
    <?php printf( '<span>%s <i class="fa fa-long-arrow-right"></i> %s<span>', $start, $end ); ?> <strong><?php _e('Date:','bus-ticket-booking-with-seat-reservation'); ?></strong> <?php echo date('D, d M Y', strtotime($date)); ?> 
</div>
<table class="bus-search-list">
    <thead>
        <tr>
            <th></th>
            <th><?php _e('Bus Name','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('DEPARTING','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('COACH NO','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('STARTING','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('END','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('FARE','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('TYPE','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('ARRIVAL','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('SEATS AVAILABLE','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('VIEW','bus-ticket-booking-with-seat-reservation'); ?></th>
        </tr>
    </thead>
    <tbody>
<?php

         $args_search_qqq = array (
                     'post_type'        => array( 'wbtm_bus' ),
                     'posts_per_page'   => -1,
                     'order'             => 'ASC',
                     'orderby'           => 'meta_value', 
                     'meta_key'          => 'wbtm_bus_start_time',                      
                     'meta_query'    => array(
                        'relation' => 'AND',
                        array(
                            'key'       => 'wbtm_bus_bp_stops',
                            'value'     => $start,
                            'compare'   => 'LIKE',
                        ),
                      
                        array(
                            'key'       => 'wbtm_bus_next_stops',
                            'value'     => $end,
                            'compare'   => 'LIKE',
                        ),
                    )                     

                );  
 

    $loop = new WP_Query($args_search_qqq);
    while ($loop->have_posts()) {
    $loop->the_post();
    $values = get_post_custom( get_the_id() );
    $term = get_the_terms(get_the_id(),'wbtm_bus_cat');
    // print_r($term);
    $total_seat = $values['wbtm_total_seat'][0];
    $sold_seat = wbtm_get_available_seat(get_the_id(),$date);
    $available_seat = ($total_seat - $sold_seat);
    $price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);  
    $bus_bp_array = get_post_meta(get_the_id(),'wbtm_bus_bp_stops',true);
    $bus_dp_array = get_post_meta(get_the_id(),'wbtm_bus_next_stops',true); 
    $bp_time = wbtm_get_bus_start_time($start, $bus_bp_array);
    $dp_time = wbtm_get_bus_end_time($end, $bus_dp_array);
    $od_start_date  = get_post_meta(get_the_id(),'wbtm_od_start',true);  
    $od_end_date    = get_post_meta(get_the_id(),'wbtm_od_end',true);
    $od_range = wbtm_check_od_in_range($od_start_date, $od_end_date, $date);
    $oday           = get_post_meta(get_the_id(),$od_name,true);     
if($od_range =='no'){
if($oday !='yes'){
?>


<?php
     if(wbtm_buffer_time_check($bp_time,$date) == 'yes'){
 ?>
        
        <tr class="<?php echo wbtm_find_product_in_cart(get_the_id()); ?>">
            <td><div class="bus-thumb-list"><?php the_post_thumbnail('thumb'); ?></div></td>
            <td><?php the_title();?></td>
            <td><?php echo $start; ?></td>
            <td><?php echo $values['wbtm_bus_no'][0]; ?></td>
            <td><?php echo date('h:i A', strtotime($bp_time)); ?></td>
            <td><?php echo $end; ?></td>
            <td><?php echo get_woocommerce_currency_symbol(); ?><?php echo wbtm_get_bus_price($start,$end, $price_arr); ?></td>
            <td><?php echo $term[0]->name; ?></td>
            <td><?php echo date('h:i A', strtotime($dp_time)); ?></td>
            <td align="center"><span class='available-seat'><?php echo $available_seat; ?></span></td>
            <td><button id="view_panel_<?php echo get_the_id(); ?>" class='view-seat-btn'><?php _e('View Seats','bus-ticket-booking-with-seat-reservation'); ?></button></td>
        </tr>

   <?php }else{ 
      $i = 0; 
      $i++;
     if($i==1) break;
    ?>
 
  <tr>
  <td colspan="10" style="text-align: center;"><?php _e('No Bus Found, Try Another Date.','bus-ticket-booking-with-seat-reservation'); ?></td>
  </tr>

    <?php } ?>

        <tr style='display: none;' class="admin-bus-details" id="admin-bus-details<?php echo get_the_id(); ?>">
            <td colspan="11">
        <?php
            $bus_meta           = get_post_custom(get_the_id());
            $seat_col           = $bus_meta['wbtm_seat_col'][0];
            $seat_row           = $bus_meta['wbtm_seat_row'][0];
            $next_stops_arr     =  get_post_meta(get_the_id(), 'wbtm_bus_next_stops', true);
            $wbtm_bus_bp_stops  =  get_post_meta(get_the_id(), 'wbtm_bus_bp_stops', true);
            $seat_col_arr       = explode(",",$seat_col);
            $seat_row_arr       = explode(",",$seat_row);
            $seat_column        = count($seat_col_arr);
            $count              = 1;
            // $fare   = $bus_meta['wbtm_bus_route_fare'][0];

            $start  = isset( $_GET['bus_start_route'] ) ? sanitize_text_field($_GET['bus_start_route']) : '';
            $end    = isset( $_GET['bus_end_route'] ) ? sanitize_text_field($_GET['bus_end_route']) : '';
            $date   = isset( $_GET['j_date'] ) ? sanitize_text_field($_GET['j_date']) : date('Y-m-d');
            $term = get_the_terms(get_the_id(),'wbtm_bus_cat');
            $price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);  

            if($seat_column==4){
                $seat_style = 2;
            }elseif ($seat_column==3) {
                # code...
                $seat_style = 1;
            }else{
                $seat_style = 999;
            }
    ?>
<div class="wbtm-content-wrappers">
    <div>
    <?php wbtm_bus_seat_plan(wbtm_get_this_bus_seat_plan(),$start,$date); ?>
       <div class="bus-info-sec">
            <?php 
            $price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);
            $fare = wbtm_get_bus_price($start,$end, $price_arr);
            ?>
            <form action="" method='post'>
                <div class="top-search-section">                    
                    <div class="leaving-list">
                        <input type="hidden"  name='journey_date' class="text" value='<?php echo $date; ?>'/>
                        <input type="hidden" name='start_stops' value="<?php echo $start; ?>" class="hidden"/>
                        <input type='hidden' value='<?php echo $end; ?>' name='end_stops'/>
                        <h6><?php _e('Route','bus-ticket-booking-with-seat-reservation'); ?></h6>
                        <div class="selected_route">
                            <?php printf( '<span>%s <i class="fa fa-long-arrow-right"></i> %s<span>', $start, $end ); ?>
                             (<?php echo get_woocommerce_currency_symbol(); ?><?php echo wbtm_get_bus_price($start,$end, $price_arr); ?>)
                        </div>
                    </div>                    
                    <div class="leaving-list">
                        <h6>Date</h6>
                        <div class="selected_date">
                            <?php printf( '<span>%s</span>', date( 'jS F, Y', strtotime( $date ) ) ); ?>
                        </div>
                    </div>   
                    <div class="leaving-list">
                        <h6><?php _e('Start & Arrival Time','bus-ticket-booking-with-seat-reservation'); ?></h6>
                        <div class="selected_date">
                            <?php  
                                $bus_bp_array = get_post_meta(get_the_id(),'wbtm_bus_bp_stops',true);
                                $bus_dp_array = get_post_meta(get_the_id(),'wbtm_bus_next_stops',true);
                                $bp_time = wbtm_get_bus_start_time($start, $bus_bp_array);
                                $dp_time = wbtm_get_bus_end_time($end, $bus_dp_array);
                                echo date('h:i A', strtotime($bp_time)).' <i class="fa fa-long-arrow-right"></i> '.date('h:i A', strtotime($dp_time));
                            ?>
                        <input type="hidden" value="<?php echo date('h:i A', strtotime($bp_time)); ?>" name="user_start_time" id='user_start_time<?php echo get_the_id(); ?>'>
                        <input type="hidden" name="bus_start_time" value="<?php echo date('h:i A', strtotime($bp_time)); ?>" id='bus_start_time'>                            
                        </div>
                    </div>                                    
                </div>
                <div class="seat-selected-list-fare">
                    <table class="selected-seat-list<?php echo get_the_id(); ?>">
                        <tr class='list_head<?php echo get_the_id(); ?>'>
                            <th><?php _e('Seat No','bus-ticket-booking-with-seat-reservation'); ?></th>
                            <th><?php _e('Fare','bus-ticket-booking-with-seat-reservation'); ?></th>
                            <th><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></th>
                        </tr>
                        <tr>
                            <td align="center"><?php _e('Total','bus-ticket-booking-with-seat-reservation'); ?> <span id='total_seat<?php echo get_the_id(); ?>_booked'></span><input type="hidden" value="" id="tq<?php echo get_the_id(); ?>" name='total_seat' class="number"/></td>
                            
                            <td align="center"><input type="hidden" value="" id="tfi<?php echo get_the_id(); ?>" class="number"/><span id="totalFare<?php echo get_the_id(); ?>"></span></td><td></td>
                        </tr>
                    </table>
                    <div id="divParent<?php echo get_the_id(); ?>"></div>
                    <input type="hidden" name="bus_id" value="<?php echo get_the_id(); ?>">
                    <button id='bus-booking-btn<?php echo get_the_id(); ?>' type="submit" name="add-to-cart" value="<?php echo esc_attr(get_the_id()); ?>" class="single_add_to_cart_button button alt btn-mep-event-cart"><?php _e('Book Now','bus-ticket-booking-with-seat-reservation'); ?></button>
                </div>
            </form>
        </div>




    </div>

<script>
jQuery(document).ready(function ($) {

$('#bus-booking-btn<?php echo get_the_id(); ?>').hide();

    $(document).on('remove_selection<?php echo get_the_id(); ?>', function( e, seatNumber ) {

        $( '#selected_list<?php echo get_the_id(); ?>_'    + seatNumber ).remove();
        $( '#seat<?php echo get_the_id(); ?>_'             + seatNumber ).removeClass('seat<?php echo get_the_id(); ?>_booked');
        $( '#seat<?php echo get_the_id(); ?>_'             + seatNumber ).removeClass('seat_booked');

        wbt_calculate_total();
        wbt_update_passenger_form();
    })

    $(document).on('click', '.seat<?php echo get_the_id(); ?>_booked', function () {
        // $( document.body ).trigger( 'remove_selection<?php echo get_the_id(); ?>', [ $(this).data("seat") ] );
    })

    $(document).on('click', '.remove-seat-row<?php echo get_the_id(); ?>', function () {
        $( document.body ).trigger( 'remove_selection<?php echo get_the_id(); ?>', [ $(this).data("seat") ] );
    });

    jQuery('#start_stops<?php echo get_the_id(); ?>').on('change', function () {
        var start_time = jQuery(this).find(':selected').data('start');
        jQuery('#user_start_time<?php echo get_the_id(); ?>').val(start_time);;
    });



    jQuery(".seat<?php echo get_the_id(); ?>_blank").on('click', function () {

        if( $(this).hasClass('seat<?php echo get_the_id(); ?>_booked') ) {
            
            $( document.body ).trigger( 'remove_selection<?php echo get_the_id(); ?>', [ $(this).data("seat") ] );
            return;
        }
        jQuery(this).addClass('seat<?php echo get_the_id(); ?>_booked');
        jQuery(this).addClass('seat_booked');

        var seat<?php echo get_the_id(); ?>_name   = jQuery(this).data("seat");
        var seat<?php echo get_the_id(); ?>_class  = jQuery(this).data("sclass");
        var fare        = <?php echo $fare; ?>;
        var foo         = "<tr id='selected_list<?php echo get_the_id(); ?>_" + seat<?php echo get_the_id(); ?>_name + "'><td align=center><input type='hidden' name='seat_name[]' value='" + seat<?php echo get_the_id(); ?>_name + "'/>" + seat<?php echo get_the_id(); ?>_name + "</td><td align=center><input type='hidden' name='bus_fare<?php echo get_the_id(); ?>' value=" + fare + "><?php echo get_woocommerce_currency_symbol(); ?>" + fare + "</td><td align=center><a class='button remove-seat-row<?php echo get_the_id(); ?>' data-seat='" + seat<?php echo get_the_id(); ?>_name + "'>X</a></td></tr>";
        
        jQuery(foo).insertAfter('.list_head<?php echo get_the_id(); ?>');

        var total_fare  = jQuery('.bus_fare<?php echo get_the_id(); ?>').val();
        var rowCount    = jQuery('.selected-seat-list<?php echo get_the_id(); ?> tr').length - 2;
        var totalFare   = (rowCount * fare);

        jQuery('#total_seat<?php echo get_the_id(); ?>_booked').html(rowCount);
        jQuery('#tq<?php echo get_the_id(); ?>').val(rowCount);
        jQuery('#totalFare<?php echo get_the_id(); ?>').html("<?php echo get_woocommerce_currency_symbol(); ?>" + totalFare);
        jQuery('#tfi<?php echo get_the_id(); ?>').val("<?php echo get_woocommerce_currency_symbol(); ?>" + totalFare);
if(totalFare>0){
    jQuery('#bus-booking-btn<?php echo get_the_id(); ?>').show();

}
// alert(totalFare);
        wbt_update_passenger_form();
    });

    function wbt_calculate_total(){

        var fare        = <?php echo $fare; ?>;
        var rowCount    = jQuery('.selected-seat-list<?php echo get_the_id(); ?> tr').length - 2;
        var totalFare   = (rowCount * fare);

        jQuery('#total_seat<?php echo get_the_id(); ?>_booked').html(rowCount);
        jQuery('#tq<?php echo get_the_id(); ?>').val(rowCount);
        jQuery('#totalFare<?php echo get_the_id(); ?>').html("<?php echo get_woocommerce_currency_symbol(); ?>" + totalFare);
        jQuery('#tfi<?php echo get_the_id(); ?>').val(totalFare);
if(totalFare==0){
    jQuery('#bus-booking-btn<?php echo get_the_id(); ?>').hide();

}
// alert(totalFare);
    }

    function wbt_update_passenger_form(){

        var input       = jQuery('#tq<?php echo get_the_id(); ?>').val() || 0;
        var children    = jQuery('#divParent<?php echo get_the_id(); ?> > div').size() || 0;

        if (input < children) {
            jQuery('#divParent<?php echo get_the_id(); ?>').empty();
            children = 0;
        }

        for (var i = children + 1; i <= input; i++) {

            jQuery('#divParent<?php echo get_the_id(); ?>').append(
                jQuery('<div/>')
                .attr("id", "newDiv" + i)
                .html("<?php do_action('wbtm_reg_fields'); ?>")
            );
        }
    }



jQuery( "#view_panel_<?php echo get_the_id(); ?>" ).click(function() {
  jQuery( "#admin-bus-details<?php echo get_the_id(); ?>" ).slideToggle( "slow", function() {
    // Animation complete.
  });
});


});
</script>   

</div>
</td>
        </tr>
<?php
}
} 
}
wp_reset_query();

?>
</tbody>
</table>
<div class="bus-list-mobile">
<?php

         $args_search_qqq = array (
                     'post_type'        => array( 'wbtm_bus' ),
                     'posts_per_page'   => -1,
                     'meta_query'    => array(
                        'relation' => 'AND',
                        array(
                            'key'       => 'wbtm_bus_bp_stops',
                            'value'     => $start,
                            'compare'   => 'LIKE',
                        ),
                      
                        array(
                            'key'       => 'wbtm_bus_next_stops',
                            'value'     => $end,
                            'compare'   => 'LIKE',
                        ),
                    )                     

                );  
 

    $loop = new WP_Query($args_search_qqq);
    while ($loop->have_posts()) {
    $loop->the_post();
    $values = get_post_custom( get_the_id() );
    $term = get_the_terms(get_the_id(),'wbtm_bus_cat');
    // print_r($term);
    $total_seat = $values['wbtm_total_seat'][0];
    $sold_seat = wbtm_get_available_seat(get_the_id(),$date);
    $available_seat = ($total_seat - $sold_seat);

$price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);    
$bus_bp_array = get_post_meta(get_the_id(),'wbtm_bus_bp_stops',true);
$bus_dp_array = get_post_meta(get_the_id(),'wbtm_bus_next_stops',true); 
$bp_time = wbtm_get_bus_start_time($start, $bus_bp_array);
$dp_time = wbtm_get_bus_end_time($end, $bus_dp_array);

$od_start_date  = get_post_meta(get_the_id(),'wbtm_od_start',true);  
$od_end_date    = get_post_meta(get_the_id(),'wbtm_od_end',true);
$od_range = wbtm_check_od_in_range($od_start_date, $od_end_date, $date);
$oday           = get_post_meta(get_the_id(),$od_name,true);     
if($od_range =='no'){
if($oday !='yes'){
?>

<div class="bus-list-item-mobile">
    <ul>
    <li>
        <?php the_post_thumbnail('medium'); ?>
    </li>       
        <li>
            <strong><?php _e('Bus Name','bus-ticket-booking-with-seat-reservation'); ?></strong>
            <p><?php echo get_the_title(); ?></p>
        </li>
        <li>
            <strong><?php _e('DEPARTING','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo $start; ?></p>
        </li>
        <li>
            <strong><?php _e('COACH NO','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo $values['wbtm_bus_no'][0]; ?></p>
        </li>
        <li>
            <strong><?php _e('STARTING','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo date('h:i A', strtotime($bp_time)); ?></p>
        </li>
        <li>
            <strong><?php _e('END','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo $end; ?></p>
        </li>
        <li>
            <strong><?php _e('FARE','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo get_woocommerce_currency_symbol(); ?><?php echo wbtm_get_bus_price($start,$end, $price_arr); ?></p>
        </li>
        <li>
            <strong><?php _e('TYPE','bus-ticket-booking-with-seat-reservation'); ?></strong>
            <p><?php echo $term[0]->name; ?></p>
        </li>
        <li>
            <strong><?php _e('ARRIVAL','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo date('h:i A', strtotime($dp_time)); ?></p>
        </li>
        <li>
            <strong><?php _e('SEATS AVAILABLE','bus-ticket-booking-with-seat-reservation'); ?>  </strong>
<p><?php echo $available_seat; ?></p>
        </li>
        <li class="mobile-view-btn">
            <a href="<?php the_permalink(); ?>?bus_start_route=<?php echo $start; ?>&bus_end_route=<?php echo $end; ?>&j_date=<?php echo $date; ?>"><?php _e('View','bus-ticket-booking-with-seat-reservation'); ?></a>
        </li>
    </ul>
</div>
<?php
}
}
}
wp_reset_query();

?>
</div>

<?php } ?>



<?php 
if(isset($_GET['bus_start_route']) && ($_GET['bus_end_route']) && ($_GET['r_date'])){
    if($rdate>$date){

$the_day = date('D', strtotime($rdate));
$od_name = 'od_'.$the_day;
?>
 <div class="selected_route">
     <strong><?php _e('Route','bus-ticket-booking-with-seat-reservation'); ?></strong>
    <?php printf( '<span>%s <i class="fa fa-long-arrow-right"></i> %s<span>', $end, $start ); ?> <strong><?php _e('Date:','bus-ticket-booking-with-seat-reservation'); ?></strong> <?php echo date('D, d M Y', strtotime($rdate)); ?> 
 </div>
<table class="bus-search-list">
    <thead>
        <tr>
            <th></th>
            <th><?php _e('Bus Name','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('DEPARTING','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('COACH NO','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('STARTING','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('END','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('FARE','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('TYPE','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('ARRIVAL','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('SEATS AVAILABLE','bus-ticket-booking-with-seat-reservation'); ?></th>
            <th><?php _e('VIEW','bus-ticket-booking-with-seat-reservation'); ?></th>
        </tr>
    </thead>
    <tbody>
<?php

         $args_search_rrr = array (
                     'post_type'        => array( 'wbtm_bus' ),
                     'posts_per_page'   => -1,
                     'order'             => 'ASC',
                     'orderby'           => 'meta_value', 
                     'meta_key'          => 'wbtm_bus_start_time',                      
                     'meta_query'    => array(
                        'relation' => 'AND',
                        array(
                            'key'       => 'wbtm_bus_bp_stops',
                            'value'     => $end,
                            'compare'   => 'LIKE',
                        ),
                      
                        array(
                            'key'       => 'wbtm_bus_next_stops',
                            'value'     => $start,
                            'compare'   => 'LIKE',
                        ),
                    )                     

                );  
 

    $loopr = new WP_Query($args_search_rrr);
    while ($loopr->have_posts()) {
    $loopr->the_post();
    $values = get_post_custom( get_the_id() );
    $term = get_the_terms(get_the_id(),'wbtm_bus_cat');
    // print_r($term);
    $total_seat = $values['wbtm_total_seat'][0];
    $sold_seat = wbtm_get_available_seat(get_the_id(),$rdate);
    $available_seat = ($total_seat - $sold_seat);

$price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);    
$bus_bp_array = get_post_meta(get_the_id(),'wbtm_bus_bp_stops',true);
$bus_dp_array = get_post_meta(get_the_id(),'wbtm_bus_next_stops',true); 
$bp_time = wbtm_get_bus_start_time($end, $bus_bp_array);
$dp_time = wbtm_get_bus_end_time($start, $bus_dp_array);

$od_start_date  = get_post_meta(get_the_id(),'wbtm_od_start',true);  
$od_end_date    = get_post_meta(get_the_id(),'wbtm_od_end',true);
$od_range = wbtm_check_od_in_range($od_start_date, $od_end_date, $rdate);
$oday           = get_post_meta(get_the_id(),$od_name,true);     
if($od_range =='no'){
if($oday !='yes'){
?>
        <tr class="<?php echo wbtm_find_product_in_cart(get_the_id()); ?>">
            <td><div class="bus-thumb-list"><?php the_post_thumbnail('thumb'); ?></div></td>
            <td><?php the_title(); ?></td>
            <td><?php echo $end; ?></td>
            <td><?php echo $values['wbtm_bus_no'][0]; ?></td>
            <td><?php echo date('h:i A', strtotime($bp_time)); ?></td>
            <td><?php echo $start; ?></td>
            <td><?php echo get_woocommerce_currency_symbol(); ?><?php echo wbtm_get_bus_price($end,$start, $price_arr); ?></td>
            <td><?php echo $term[0]->name; ?></td>
            <td><?php echo date('h:i A', strtotime($dp_time)); ?></td>
            <td align="center"><span class='available-seat'><?php echo $available_seat; ?></span></td>
            <td><button id="view_panel_<?php echo get_the_id(); ?>" class='view-seat-btn'>View Seats</button></td>
        </tr>
        <tr style='display: none;' class="admin-bus-details" id="admin-bus-details<?php echo get_the_id(); ?>">
            <td colspan="11">
                <?php
                    $bus_meta           = get_post_custom(get_the_id());
                    $seat_col           = $bus_meta['wbtm_seat_col'][0];
                    $seat_row           = $bus_meta['wbtm_seat_row'][0];
                    $next_stops_arr     =  get_post_meta(get_the_id(), 'wbtm_bus_next_stops', true);
                    $wbtm_bus_bp_stops  =  get_post_meta(get_the_id(), 'wbtm_bus_bp_stops', true);
                    $seat_col_arr       = explode(",",$seat_col);
                    $seat_row_arr       = explode(",",$seat_row);
                    $seat_column = count($seat_col_arr);
                    $count  = 1;
$term = get_the_terms(get_the_id(),'wbtm_bus_cat');
$price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);  

if($seat_column==4){
    $seat_style = 2;
}elseif ($seat_column==3) {
    # code...
    $seat_style = 1;
}else{
    $seat_style = 999;
}
    ?>
<div class="wbtm-content-wrappers">
    <div >
    <?php wbtm_bus_seat_plan(wbtm_get_this_bus_seat_plan(),$end,$rdate); ?>

       <div class="bus-info-sec">
        <?php 
        $price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);
        $fare = wbtm_get_bus_price($end,$start, $price_arr);
        ?>
            <form action="" method='post'>
                <div class="top-search-section">                    
                    <div class="leaving-list">
                        <input type="hidden"  name='journey_date' class="text" value='<?php echo $rdate; ?>'/>
                        <input type="hidden" name='start_stops' value="<?php echo $end; ?>" class="hidden"/>
                        <input type='hidden' value='<?php echo $start; ?>' name='end_stops'/>
                        <h6><?php _e('Route','bus-ticket-booking-with-seat-reservation'); ?></h6>
                        <div class="selected_route">
                            <?php printf( '<span>%s <i class="fa fa-long-arrow-right"></i> %s<span>', $end, $start ); ?>
                             (<?php echo get_woocommerce_currency_symbol(); ?><?php echo wbtm_get_bus_price($end,$start, $price_arr); ?>)
                        </div>
                    </div>                    
                    <div class="leaving-list">
                        <h6><?php _e('Date','bus-ticket-booking-with-seat-reservation'); ?></h6>
                        <div class="selected_date">
                            <?php printf( '<span>%s</span>', date( 'jS F, Y', strtotime( $rdate ) ) ); ?>
                        </div>
                    </div>   
                    <div class="leaving-list">
                        <h6><?php _e('Start & Arrival Time','bus-ticket-booking-with-seat-reservation'); ?></h6>
                        <div class="selected_date">
                            <?php  
                                $bus_bp_array = get_post_meta(get_the_id(),'wbtm_bus_bp_stops',true);
                                $bus_dp_array = get_post_meta(get_the_id(),'wbtm_bus_next_stops',true);
                                $bp_time = wbtm_get_bus_start_time($end, $bus_bp_array);
                                $dp_time = wbtm_get_bus_end_time($start, $bus_dp_array);
                                echo date('h:i A', strtotime($bp_time)).' <i class="fa fa-long-arrow-right"></i> '.date('h:i A', strtotime($dp_time));
                            ?>
                        <input type="hidden" value="<?php echo date('h:i A', strtotime($bp_time)); ?>" name="user_start_time" id='user_start_time<?php echo get_the_id(); ?>'>
                        <input type="hidden" name="bus_start_time" value="<?php echo date('h:i A', strtotime($bp_time)); ?>" id='bus_start_time'>                            
                        </div>
                    </div>                                    
                </div>
                <div class="seat-selected-list-fare">
                    <table class="selected-seat-list<?php echo get_the_id(); ?>">
                        <tr class='list_head<?php echo get_the_id(); ?>'>
                            <th><?php _e('Seat No','bus-ticket-booking-with-seat-reservation'); ?></th>
                            <th><?php _e('Fare','bus-ticket-booking-with-seat-reservation'); ?></th>
                            <th><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></th>
                        </tr>
                        <tr>
                            <td align="center"><?php _e('Total','bus-ticket-booking-with-seat-reservation'); ?> <span id='total_seat<?php echo get_the_id(); ?>_booked'></span><input type="hidden" value="" id="tq<?php echo get_the_id(); ?>" name='total_seat' class="number"/></td>
                            
                            <td align="center"><input type="hidden" value="" id="tfi<?php echo get_the_id(); ?>" class="number"/><span id="totalFare<?php echo get_the_id(); ?>"></span></td><td></td>
                        </tr>
                    </table>
                    <div id="divParent<?php echo get_the_id(); ?>"></div>
                    <input type="hidden" name="bus_id" value="<?php echo get_the_id(); ?>">
                    <button id='bus-booking-btn<?php echo get_the_id(); ?>' type="submit" name="add-to-cart" value="<?php echo esc_attr(get_the_id()); ?>" class="single_add_to_cart_button button alt btn-mep-event-cart"><?php _e('Book Now','bus-ticket-booking-with-seat-reservation'); ?></button>
                </div>
            </form>
        </div>
    </div>
<script>
jQuery(document).ready(function ($) {

$('#bus-booking-btn<?php echo get_the_id(); ?>').hide();

    $(document).on('remove_selection<?php echo get_the_id(); ?>', function( e, seatNumber ) {

        $( '#selected_list<?php echo get_the_id(); ?>_'    + seatNumber ).remove();
        $( '#seat<?php echo get_the_id(); ?>_'             + seatNumber ).removeClass('seat<?php echo get_the_id(); ?>_booked');
        $( '#seat<?php echo get_the_id(); ?>_'             + seatNumber ).removeClass('seat_booked');

        wbt_calculate_total();
        wbt_update_passenger_form();
    })

    $(document).on('click', '.seat<?php echo get_the_id(); ?>_booked', function () {
        // $( document.body ).trigger( 'remove_selection<?php echo get_the_id(); ?>', [ $(this).data("seat") ] );
    })

    $(document).on('click', '.remove-seat-row<?php echo get_the_id(); ?>', function () {
        $( document.body ).trigger( 'remove_selection<?php echo get_the_id(); ?>', [ $(this).data("seat") ] );
    });

    jQuery('#start_stops<?php echo get_the_id(); ?>').on('change', function () {
        var start_time = jQuery(this).find(':selected').data('start');
        jQuery('#user_start_time<?php echo get_the_id(); ?>').val(start_time);;
    });



    jQuery(".seat<?php echo get_the_id(); ?>_blank").on('click', function () {

        if( $(this).hasClass('seat<?php echo get_the_id(); ?>_booked') ) {
            
            $( document.body ).trigger( 'remove_selection<?php echo get_the_id(); ?>', [ $(this).data("seat") ] );
            return;
        }
        jQuery(this).addClass('seat<?php echo get_the_id(); ?>_booked');
        jQuery(this).addClass('seat_booked');

        var seat<?php echo get_the_id(); ?>_name   = jQuery(this).data("seat");
        var seat<?php echo get_the_id(); ?>_class  = jQuery(this).data("sclass");
        var fare        = <?php echo $fare; ?>;
        var foo         = "<tr id='selected_list<?php echo get_the_id(); ?>_" + seat<?php echo get_the_id(); ?>_name + "'><td align=center><input type='hidden' name='seat_name[]' value='" + seat<?php echo get_the_id(); ?>_name + "'/>" + seat<?php echo get_the_id(); ?>_name + "</td><td align=center><input type='hidden' name='bus_fare<?php echo get_the_id(); ?>' value=" + fare + "><?php echo get_woocommerce_currency_symbol(); ?>" + fare + "</td><td align=center><a class='button remove-seat-row<?php echo get_the_id(); ?>' data-seat='" + seat<?php echo get_the_id(); ?>_name + "'>X</a></td></tr>";
        
        jQuery(foo).insertAfter('.list_head<?php echo get_the_id(); ?>');

        var total_fare  = jQuery('.bus_fare<?php echo get_the_id(); ?>').val();
        var rowCount    = jQuery('.selected-seat-list<?php echo get_the_id(); ?> tr').length - 2;
        var totalFare   = (rowCount * fare);

        jQuery('#total_seat<?php echo get_the_id(); ?>_booked').html(rowCount);
        jQuery('#tq<?php echo get_the_id(); ?>').val(rowCount);
        jQuery('#totalFare<?php echo get_the_id(); ?>').html("<?php echo get_woocommerce_currency_symbol(); ?>" + totalFare);
        jQuery('#tfi<?php echo get_the_id(); ?>').val("<?php echo get_woocommerce_currency_symbol(); ?>" + totalFare);
if(totalFare>0){
    jQuery('#bus-booking-btn<?php echo get_the_id(); ?>').show();

}
// alert(totalFare);
        wbt_update_passenger_form();
    });

    function wbt_calculate_total(){

        var fare        = <?php echo $fare; ?>;
        var rowCount    = jQuery('.selected-seat-list<?php echo get_the_id(); ?> tr').length - 2;
        var totalFare   = (rowCount * fare);

        jQuery('#total_seat<?php echo get_the_id(); ?>_booked').html(rowCount);
        jQuery('#tq<?php echo get_the_id(); ?>').val(rowCount);
        jQuery('#totalFare<?php echo get_the_id(); ?>').html("<?php echo get_woocommerce_currency_symbol(); ?>" + totalFare);
        jQuery('#tfi<?php echo get_the_id(); ?>').val(totalFare);
if(totalFare==0){
    jQuery('#bus-booking-btn<?php echo get_the_id(); ?>').hide();

}
// alert(totalFare);
    }

    function wbt_update_passenger_form(){

        var input       = jQuery('#tq<?php echo get_the_id(); ?>').val() || 0;
        var children    = jQuery('#divParent<?php echo get_the_id(); ?> > div').size() || 0;

        if (input < children) {
            jQuery('#divParent<?php echo get_the_id(); ?>').empty();
            children = 0;
        }

        for (var i = children + 1; i <= input; i++) {

            jQuery('#divParent<?php echo get_the_id(); ?>').append(
                jQuery('<div/>')
                .attr("id", "newDiv" + i)
                .html("<?php do_action('wbtm_reg_fields'); ?>")
            );
        }
    }



jQuery( "#view_panel_<?php echo get_the_id(); ?>" ).click(function() {
  jQuery( "#admin-bus-details<?php echo get_the_id(); ?>" ).slideToggle( "slow", function() {
    // Animation complete.
  });
});


});
</script>   

</div>
</td>
        </tr>
<?php
}
}
}
wp_reset_query();

?>
</tbody>
</table>
<div class="bus-list-mobile">
<?php

         $args_search_qqq = array (
                     'post_type'        => array( 'wbtm_bus' ),
                     'posts_per_page'   => -1,
                     'meta_query'    => array(
                        'relation' => 'AND',
                        array(
                            'key'       => 'wbtm_bus_bp_stops',
                            'value'     => $end,
                            'compare'   => 'LIKE',
                        ),
                      
                        array(
                            'key'       => 'wbtm_bus_next_stops',
                            'value'     => $start,
                            'compare'   => 'LIKE',
                        ),
                    )                     

                );  
 

    $loop = new WP_Query($args_search_qqq);
    while ($loop->have_posts()) {
    $loop->the_post();
    $values = get_post_custom( get_the_id() );
    $term = get_the_terms(get_the_id(),'wbtm_bus_cat');
    // print_r($term);
    $total_seat = $values['wbtm_total_seat'][0];
    $sold_seat = wbtm_get_available_seat(get_the_id(),$date);
    $available_seat = ($total_seat - $sold_seat);

$price_arr = get_post_meta(get_the_id(),'wbtm_bus_prices',true);    
$bus_bp_array = get_post_meta(get_the_id(),'wbtm_bus_bp_stops',true);
$bus_dp_array = get_post_meta(get_the_id(),'wbtm_bus_next_stops',true); 
$bp_time = wbtm_get_bus_start_time($end, $bus_bp_array);
$dp_time = wbtm_get_bus_end_time($start, $bus_dp_array);

$od_start_date  = get_post_meta(get_the_id(),'wbtm_od_start',true);  
$od_end_date    = get_post_meta(get_the_id(),'wbtm_od_end',true);
$od_range = wbtm_check_od_in_range($od_start_date, $od_end_date, $rdate);
$oday           = get_post_meta(get_the_id(),$od_name,true);     
if($od_range =='no'){
if($oday !='yes'){
?>

<div class="bus-list-item-mobile">
    <ul>
    <li>
        <?php the_post_thumbnail('medium'); ?>
    </li>       
        <li>
            <strong><?php _e('Bus Name','bus-ticket-booking-with-seat-reservation'); ?></strong>
            <p><?php echo get_the_title(); ?></p>
        </li>
        <li>
            <strong><?php _e('DEPARTING','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo $end; ?></p>
        </li>
        <li>
            <strong><?php _e('COACH NO','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo $values['wbtm_bus_no'][0]; ?></p>
        </li>
        <li>
            <strong><?php _e('STARTING','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo date('h:i A', strtotime($bp_time)); ?></p>
        </li>
        <li>
            <strong><?php _e('END','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo $start; ?></p>
        </li>
        <li>
            <strong><?php _e('FARE','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo get_woocommerce_currency_symbol(); ?><?php echo wbtm_get_bus_price($end,$start, $price_arr); ?></p>
        </li>
        <li>
            <strong><?php _e('TYPE','bus-ticket-booking-with-seat-reservation'); ?></strong>
            <p><?php echo $term[0]->name; ?></p>
        </li>
        <li>
            <strong><?php _e('ARRIVAL','bus-ticket-booking-with-seat-reservation'); ?></strong>
<p><?php echo date('h:i A', strtotime($dp_time)); ?></p>
        </li>
        <li>
            <strong><?php _e('SEATS AVAILABLE','bus-ticket-booking-with-seat-reservation'); ?> </strong>
<p><?php echo $available_seat; ?></p>
        </li>
        <li class="mobile-view-btn">
            <a href="<?php the_permalink(); ?>?bus_start_route=<?php echo $start; ?>&bus_end_route=<?php echo $end; ?>&j_date=<?php echo $date; ?>"><?php _e('View','bus-ticket-booking-with-seat-reservation'); ?></a>
        </li>
    </ul>
</div>
<?php
}
}
}
wp_reset_query();

?>
</div>

<?php } } ?>




</div>
<?php
$content = ob_get_clean();
return $content;
}


add_shortcode( 'wbtm-bus-search-form', 'wbtm_bus_search_form' );
function wbtm_bus_search_form($atts, $content=null){
        $defaults = array(
            "cat"                   => "0"
        );
        $params                     = shortcode_atts($defaults, $atts);
        $cat                        = $params['cat'];
ob_start();
 
$start  = isset( $_GET['bus_start_route'] ) ? strip_tags($_GET['bus_start_route']) : '';
$end    = isset( $_GET['bus_end_route'] ) ? strip_tags($_GET['bus_end_route']) : '';
$date   = isset( $_GET['j_date'] ) ? strip_tags($_GET['j_date']) : date('Y-m-d');
$r_date     = isset( $_GET['r_date'] ) ? strip_tags($_GET['r_date']) : date('Y-m-d');

?>
<div class="wbtm-search-form-fields-sec">
    <h2>BUY TICKET</h2>
    <form action="<?php echo get_site_url(); ?>/bus-search-list/" method="get">
        <?php wbtm_bus_search_fileds($start,$end,$date,$r_date); //do_action('wbtm_search_fields'); ?>
    </form>
</div>
<?php
$content = ob_get_clean();
return $content;
}
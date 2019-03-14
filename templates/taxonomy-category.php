<?php 
get_header();
the_post();
$term_id = get_queried_object()->term_id;
// print_r(get_queried_object());
?>
<div class="mep-events-wrapper">
<div class="wbtm-bus-list-sec">
<div class="wbtm_cat-details">
	<h1><?php echo get_queried_object()->name; ?></h1>
	<p><?php echo get_queried_object()->description; ?></p>
</div>
<?php
     $args_search_qqq = array (
                     'post_type'        => array( 'wbtm_bus' ),
                     'posts_per_page'   => -1,
                      'tax_query'       => array(
								array(
							            'taxonomy'  => 'wbtm_bus_cat',
							            'field'     => 'term_id',
							            'terms'     => $term_id
							        )
                        )

                );
	 $loop = new WP_Query( $args_search_qqq );
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
		<li><strong>Type:</strong> <?php echo $term[0]->name; ?></li>
		<li><strong>Bus No:</strong> <?php echo get_post_meta(get_the_id(),'wbtm_bus_no',true); ?></li>
		<li><strong>Total Seat:</strong> <?php echo get_post_meta(get_the_id(),'wbtm_total_seat',true); ?> </li>
		<li><strong>Start From:</strong> <?php echo $start = $bp_arr[0]['wbtm_bus_bp_stops_name'];; ?> </li>
		<li><strong>End at:</strong> <?php echo $end = $dp_arr[$total_dp]['wbtm_bus_next_stops_name'];; ?> </li>
		<li><strong>Fare:</strong> <?php echo get_woocommerce_currency_symbol().wbtm_get_bus_price($start,$end, $price_arr); ?> </li>
	</ul>
	<a href="<?php the_permalink(); ?>" class='btn wbtm-bus-list-btn'><?php _e('Book Now','bus-ticket-booking-with-seat-reservation'); ?></a>
</div>
<?php
}
?>
</div>
<?php
get_footer();
?>
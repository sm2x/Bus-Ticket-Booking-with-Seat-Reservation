<?php
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
function wbtm_bus_cpt_tax(){


	$labels = array(
		'name'                       => _x( 'Bus Category','bus-ticket-booking-with-seat-reservation' ),
		'singular_name'              => _x( 'Bus Category','bus-ticket-booking-with-seat-reservation' ),
		'menu_name'                  => __( 'Category', 'bus-ticket-booking-with-seat-reservation' ),
		'all_items'                  => __( 'All Bus Category', 'bus-ticket-booking-with-seat-reservation' ),
		'parent_item'                => __( 'Parent Category', 'bus-ticket-booking-with-seat-reservation' ),
		'parent_item_colon'          => __( 'Parent Category:', 'bus-ticket-booking-with-seat-reservation' ),
		'new_item_name'              => __( 'New Category Name', 'bus-ticket-booking-with-seat-reservation' ),
		'add_new_item'               => __( 'Add New Category', 'bus-ticket-booking-with-seat-reservation' ),
		'edit_item'                  => __( 'Edit Category', 'bus-ticket-booking-with-seat-reservation' ),
		'update_item'                => __( 'Update Category', 'bus-ticket-booking-with-seat-reservation' ),
		'view_item'                  => __( 'View Category', 'bus-ticket-booking-with-seat-reservation' ),
		'separate_items_with_commas' => __( 'Separate Category with commas', 'bus-ticket-booking-with-seat-reservation' ),
		'add_or_remove_items'        => __( 'Add or remove Category', 'bus-ticket-booking-with-seat-reservation' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'bus-ticket-booking-with-seat-reservation' ),
		'popular_items'              => __( 'Popular Category', 'bus-ticket-booking-with-seat-reservation' ),
		'search_items'               => __( 'Search Category', 'bus-ticket-booking-with-seat-reservation' ),
		'not_found'                  => __( 'Not Found', 'bus-ticket-booking-with-seat-reservation' ),
		'no_terms'                   => __( 'No Category', 'bus-ticket-booking-with-seat-reservation' ),
		'items_list'                 => __( 'Category list', 'bus-ticket-booking-with-seat-reservation' ),
		'items_list_navigation'      => __( 'Category list navigation', 'bus-ticket-booking-with-seat-reservation' ),
	);

	$args = array(
		'hierarchical'          => true,
		"public" 				=> true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'bus-category' ),
	);
register_taxonomy('wbtm_bus_cat', 'wbtm_bus', $args);





	$seat_type_labels = array(
		'singular_name'              => _x( 'Seat Type','bus-ticket-booking-with-seat-reservation' ),
		'name'                       => _x( 'Seat Type','bus-ticket-booking-with-seat-reservation' ),
	);

	$seat_type_args = array(
		'hierarchical'          => true,
		"public" 				=> true,
		'labels'                => $seat_type_labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'seat-type' ),
	);
// register_taxonomy('wbtm_seat_type', 'wbtm_bus', $seat_type_args);






	$bus_stops_labels = array(
		'singular_name'              => _x( 'Bus Stops','bus-ticket-booking-with-seat-reservation' ),
		'name'                       => _x( 'Bus Stops','bus-ticket-booking-with-seat-reservation' ),
	);

	$bus_stops_args = array(
		'hierarchical'          => true,
		"public" 				=> true,
		'labels'                => $bus_stops_labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'bus-stops' ),
	);
register_taxonomy('wbtm_bus_stops', 'wbtm_bus', $bus_stops_args);




	$bus_route_labels = array(
		'singular_name'              => _x( 'Bus Route','bus-ticket-booking-with-seat-reservation' ),
		'name'                       => _x( 'Bus Route','bus-ticket-booking-with-seat-reservation' ),
	);

	$bus_route_args = array(
		'hierarchical'          => true,
		"public" 				=> true,
		'labels'                => $bus_route_labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'bus-route' ),
	);
// register_taxonomy('wbtm_bus_route', 'wbtm_bus', $bus_route_args);

}
add_action("init","wbtm_bus_cpt_tax",10);
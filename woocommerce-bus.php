<?php
/**
* Plugin Name: Bus Ticket Booking with Seat Reservation
* Plugin URI: http://mage-people.com
* Description: A Complete Bus Ticketig System for WordPress & WooCommerce
* Version: 1.5.5
* Author: MagePeople Team
* Author URI: http://www.mage-people.com/
* Text Domain: bus-ticket-booking-with-seat-reservation
* Domain Path: /languages/
*/ 


if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

// Language Load
add_action( 'init', 'wbtm_language_load');
function wbtm_language_load(){
    $plugin_dir = basename(dirname(__FILE__))."/languages/";
    load_plugin_textdomain( 'bus-ticket-booking-with-seat-reservation', false, $plugin_dir );
}


// function to create passenger list table        
function wbtm_booking_list_table_create() {
global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = $wpdb->prefix . 'wbtm_bus_booking_list';
  $sql = "CREATE TABLE $table_name (
    booking_id int(15) NOT NULL AUTO_INCREMENT,
    order_id int(9) NOT NULL,  
    bus_id int(9) NOT NULL, 
    user_id int(9) NOT NULL, 
    boarding_point varchar(55) NOT NULL,
    next_stops text NOT NULL,      
    droping_point varchar(55) NOT NULL, 
    user_name varchar(55) NOT NULL, 
    user_email varchar(55) NOT NULL, 
    user_phone varchar(55) NOT NULL, 
    user_gender varchar(55) NOT NULL, 
    user_address text NOT NULL, 
    bus_start varchar(55) NOT NULL, 
    user_start varchar(55) NOT NULL, 
    seat varchar(55) NOT NULL,
    bus_fare int(9) NOT NULL DEFAULT 0,
    journey_date date DEFAULT '0000-00-00' NOT NULL,    
    booking_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,    
    status int(1) NOT NULL,  
    ticket_status int(1) NOT NULL,  
    PRIMARY KEY  (booking_id)
  ) $charset_collate;";
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'wbtm_booking_list_table_create');


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
require_once(dirname(__FILE__) . "/inc/class/mep_settings_api.php");
require_once(dirname(__FILE__) . "/inc/admin_setting_panel.php");
require_once(dirname(__FILE__) . "/inc/wbtm_cpt.php");
require_once(dirname(__FILE__) . "/inc/wbtm_tax.php");
require_once(dirname(__FILE__) . "/inc/wbtm_bus_ticket_meta.php");
require_once(dirname(__FILE__) . "/inc/wbtm_extra_price.php");
require_once(dirname(__FILE__) . "/inc/wbtm_shortcode.php");
require_once(dirname(__FILE__) . "/inc/wbtm_enque.php");
require_once(dirname(__FILE__) . "/templates/seat-template/seat_plan.php");


/**
 * Run code only once
 */
function wbtm_update_databas_once() {
global $wpdb;
$table_name = $wpdb->prefix."wbtm_bus_booking_list";
    if ( get_option( 'wbtm_update_db_once_01' ) != 'completed' ) {
        $args_search_qqq = array (
              'post_type'        => array( 'wbtm_bus' ),
              'posts_per_page'   => -1
           );  
        $loop = new WP_Query($args_search_qqq);
        while ($loop->have_posts()) {
          $loop->the_post();
          $wbtm_bus_bp      = get_post_meta(get_the_id(), 'wbtm_bus_bp_stops', true);
          $start_t          = $wbtm_bus_bp[0]['wbtm_bus_bp_start_time'];
          $bstart_time      = $start_t;
          update_post_meta( get_the_id(), 'wbtm_bus_start_time', $bstart_time );
        }
                 
        $table = $wpdb->prefix."wbtm_bus_booking_list";

        $myCustomer = $wpdb->get_row( sprintf("SELECT * FROM %s LIMIT 1", $table) );

        if(!isset($myCustomer->ticket_status)) {
            $wpdb->query( sprintf( "ALTER TABLE %s ADD ticket_status INT NOT NULL DEFAULT 1 AFTER status", $table) );
        }
        update_option( 'wbtm_update_db_once_01', 'completed' );
    }
    if ( get_option( 'wbtm_update_db_once_02' ) != 'completed' ) {

                 
        $table = $wpdb->prefix."wbtm_bus_booking_list";

        $myCustomer = $wpdb->get_row( sprintf("SELECT * FROM %s LIMIT 1", $table) );

        if(!isset($myCustomer->next_stops)) {
            $wpdb->query( sprintf( "ALTER TABLE %s ADD next_stops text NOT NULL AFTER boarding_point", $table) );
        }
        update_option( 'wbtm_update_db_once_02', 'completed' );
    } 

if ( !empty(get_option( 'wbtm_update_db_once_15' )) && get_option( 'wbtm_update_db_once_15' ) != 'completed' ) {  
        $table = $wpdb->prefix."wbtm_bus_booking_list";
        $myCustomer = $wpdb->get_row( sprintf("SELECT * FROM %s LIMIT 1", $table) );
        if(!isset($myCustomer->bus_fare)) {
            $wpdb->query( sprintf( "ALTER TABLE %s ADD bus_fare INT NOT NULL DEFAULT 0 AFTER seat", $table) );
        }

  $sql = 'SELECT * FROM '.$table_name;
  $q_results = $wpdb->get_results($sql) or die(mysql_error());
  foreach( $q_results as $_result ) {
     $price_arr = get_post_meta($_result->bus_id,'wbtm_bus_prices',true);
     $start = $_result->boarding_point;
     $end = $_result->droping_point;
     $booking_id = $_result->booking_id;
     $price = wbtm_get_bus_price($start,$end, $price_arr);

     $wpdb->query( $wpdb->prepare("UPDATE $table_name 
                SET bus_fare = %d 
             WHERE booking_id = %d",$price, $booking_id)
     );
  }
update_option( 'wbtm_update_db_once_15', 'completed' );

}  
}
add_action( 'admin_init', 'wbtm_update_databas_once' );



function wbtm_get_all_stops_after_this($bus_id,$val,$end){

    $start_stops = get_post_meta($bus_id,'wbtm_bus_bp_stops',true);
    $all_stops = array();
    foreach ($start_stops as $_start_stops) {
    $all_stops[] = $_start_stops['wbtm_bus_bp_stops_name'];
  }

$pos        = array_search($val, $all_stops);
$pos2       = array_search($end, $all_stops);
unset($all_stops[$pos]);
unset($all_stops[$pos2]);
return $all_stops;

}



// Function to get page slug
function wbtm_get_page_by_slug($slug) {
    if ($pages = get_pages())
        foreach ($pages as $page)
            if ($slug === $page->post_name) return $page;
    return false;
}


// Cretae pages on plugin activation
function wbtm_page_create() {

        if (! wbtm_get_page_by_slug('bus-search')) {
            $bus_search_page = array(
            'post_type' => 'page',
            'post_name' => 'bus-search',
            'post_title' => 'Bus Search',
            'post_content' => '[wbtm-bus-search]',
            'post_status' => 'publish',
            );

            wp_insert_post($bus_search_page);
        }

        if (! wbtm_get_page_by_slug('view-ticket')) {
            $view_ticket_page = array(
            'post_type' => 'page',
            'post_name' => 'view-ticket',
            'post_title' => 'View Ticket',
            'post_content' => '[wbtm-view-ticket]',
            'post_status' => 'publish',
            );

            wp_insert_post($view_ticket_page);
        } 
}
register_activation_hook(__FILE__,'wbtm_page_create');










// Class for Linking with Woocommerce with Bus Pricing 
add_action('plugins_loaded', 'wbtm_load_wc_class');
function wbtm_load_wc_class() {
  if ( class_exists('WC_Product_Data_Store_CPT') ) {
   class WBTM_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {
    public function read( &$product ) {

        $product->set_defaults();

        if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || ! in_array( $post_object->post_type, array( 'wbtm_bus', 'product' ) ) ) { // change birds with your post type
            throw new Exception( __( 'Invalid product.', 'woocommerce' ) );
        }

        $id = $product->get_id();

        $product->set_props( array(
            'name'              => $post_object->post_title,
            'slug'              => $post_object->post_name,
            'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
            'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
            'status'            => $post_object->post_status,
            'description'       => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id'         => $post_object->post_parent,
            'menu_order'        => $post_object->menu_order,
            'reviews_allowed'   => 'open' === $post_object->comment_status,
        ) );

        $this->read_attributes( $product );
        $this->read_downloads( $product );
        $this->read_visibility( $product );
        $this->read_product_data( $product );
        $this->read_extra_data( $product );
        $product->set_object_read( true );
    }

    /**
     * Get the product type based on product ID.
     *
     * @since 3.0.0
     * @param int $product_id
     * @return bool|string
     */
    public function get_product_type( $product_id ) {
        $post_type = get_post_type( $product_id );
        if ( 'product_variation' === $post_type ) {
            return 'variation';
        } elseif ( in_array( $post_type, array( 'wbtm_bus', 'product' ) ) ) { // change birds with your post type
            $terms = get_the_terms( $product_id, 'product_type' );
            return ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
        } else {
            return false;
        }
    }
}




add_filter( 'woocommerce_data_stores', 'wbtm_woocommerce_data_stores' );
function wbtm_woocommerce_data_stores ( $stores ) {     
      $stores['product'] = 'WBTM_Product_Data_Store_CPT';
      return $stores;
  }

  } else {

    add_action('admin_notices', 'wc_not_loaded');

  }
}


add_action('woocommerce_before_checkout_form', 'wbtm_displays_cart_products_feature_image');
function wbtm_displays_cart_products_feature_image() {
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $item = $cart_item['data'];
    }
}



add_action('restrict_manage_posts', 'wbtm_filter_post_type_by_taxonomy');
function wbtm_filter_post_type_by_taxonomy() {
  global $typenow;
  $post_type = 'wbtm_bus'; // change to your post type
  $taxonomy  = 'wbtm_bus_cat'; // change to your taxonomy
  if ($typenow == $post_type) {
    $selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
    $info_taxonomy = get_taxonomy($taxonomy);
    wp_dropdown_categories(array(
      'show_option_all' => __("Show All {$info_taxonomy->label}"),
      'taxonomy'        => $taxonomy,
      'name'            => $taxonomy,
      'orderby'         => 'name',
      'selected'        => $selected,
      'show_count'      => true,
      'hide_empty'      => true,
    ));
  };
}




add_filter('parse_query', 'wbtm_convert_id_to_term_in_query');
function wbtm_convert_id_to_term_in_query($query) {
  global $pagenow;
  $post_type = 'wbtm_bus'; // change to your post type
  $taxonomy  = 'wbtm_bus_cat'; // change to your taxonomy
  $q_vars    = &$query->query_vars;

  if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
    $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
    $q_vars[$taxonomy] = $term->slug;
  }

}



// Redirect to Checkout after successfuly event registration
// add_filter ('woocommerce_add_to_cart_redirect', 'wbtm_bus_ticket_redirect_to_checkout');
function wbtm_bus_ticket_redirect_to_checkout() {
    global $woocommerce;
    $checkout_url = wc_get_checkout_url();
    return $checkout_url;
}






function wbtm_load_bus_templates($template) {
    global $post;
  if ($post->post_type == "wbtm_bus"){
          $template_name = 'single-bus.php';
          $template_path = 'mage-bus-ticket/';
          $default_path = plugin_dir_path( __FILE__ ) . 'templates/'; 
          $template = locate_template( array($template_path . $template_name) );
        if ( ! $template ) :
          $template = $default_path . $template_name;
        endif;
    return $template;
  }
    return $template;
}
add_filter('single_template', 'wbtm_load_bus_templates');





add_filter('template_include', 'wbtm_taxonomy_set_template');
function wbtm_taxonomy_set_template( $template ){

    if( is_tax('wbtm_bus_cat')){
        $template = plugin_dir_path( __FILE__ ).'templates/taxonomy-category.php';
    }    

    return $template;
}




function wbtm_get_bus_ticket_order_metadata($id,$part){
global $wpdb;
$table_name = $wpdb->prefix . 'woocommerce_order_itemmeta';
$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE order_item_id=$id" );

foreach ( $result as $page )
{
  if (strpos($page->meta_key, '_') !== 0) {
   echo wbtm_get_string_part($page->meta_key,$part).'<br/>';
 }
}

}





function wbtm_get_seat_type($name){
  global $post;
$values = get_post_custom( $post->ID );
$seat_name = $name;
if(array_key_exists($seat_name, $values)){
$type_name = $values[$seat_name][0];
}else{
  $type_name = '';
}

  $get_terms_default_attributes = array (
            'taxonomy' => 'wbtm_seat_type', //empty string(''), false, 0 don't work, and return 
            'hide_empty' => false, //can be 1, '1' too
    );
  $terms = get_terms($get_terms_default_attributes);
if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
  ob_start();
  ?>
    <select name="<?php echo $name; ?>" class='seat_type select2'>
      <?php
    foreach ( $terms as $term ) {
      ?>
        <option value="<?php echo $term->name; ?>" <?php if($type_name==$term->name){ echo "Selected"; } ?> ><?php echo $term->name; ?></option>
        <?php
    }
    ?>
    </select>
  <?php
  
}
$content = ob_get_clean();
return $content;
}




function wbtm_get_bus_route_list( $name, $value = '' ) {
    
    global $post;
    if($post){
    $values     = get_post_custom( $post->ID );
  }else{
    $values ='';
  }


if(is_array($values) && array_key_exists($name, $values)){
    $seat_name  = $name;
    $type_name  = $values[$seat_name][0];
  }else{
    $type_name='';
  }
    $terms      = get_terms( array (
        // 'taxonomy' => 'wbtm_bus_route',
        'taxonomy' => 'wbtm_bus_stops',
        'hide_empty' => false,
    ) );

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ob_start(); ?>

        <select name="<?php echo $name; ?>" class='seat_type select2'>
            
            <option value="">Please Select</option>
            
            <?php foreach ( $terms as $term ) : 
                    
                $selected = $type_name == $term->name ? 'selected' : '';
                
                if( ! empty( $value ) ) $selected = $term->name == $value ? 'selected' : '';
                printf( '<option %s value="%s">%s</option>', $selected, $term->name, $term->name );

            endforeach; ?>

        </select>

    <?php endif;

    return ob_get_clean();
}


function wbtm_get_bus_stops_list($name){
  global $post;
$values = get_post_custom( $post->ID );
$seat_name = $name;
if(array_key_exists($seat_name, $values)){
$type_name = $values[$seat_name][0];
}else{
  $type_name = '';
}

  $get_terms_default_attributes = array (
            'taxonomy' => 'wbtm_bus_stops', //empty string(''), false, 0 don't work, and return 
            'hide_empty' => false, //can be 1, '1' too
    );
  $terms = get_terms($get_terms_default_attributes);
if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
  ob_start();
  ?>
    <select name="<?php echo $name; ?>" class='seat_type select2'>
      <option value=""><?php _e('Please Select','bus-ticket-booking-with-seat-reservation'); ?></option>
      <?php
    foreach ( $terms as $term ) {
      ?>
        <option value="<?php echo $term->name; ?>" <?php if($type_name==$term->name){ echo "Selected"; } ?> ><?php echo $term->name; ?></option>
        <?php
    }
    ?>
    </select>
  <?php
  
}
$content = ob_get_clean();
return $content;
}



function wbtm_get_next_bus_stops_list($name,$data,$list,$coun){
  global $post;
$values = get_post_custom( $post->ID );
$nxt_arr = get_post_meta($post->ID, $list, true);
// print_r($nxt_arr);
$seat_name = $name;
$type_name = $nxt_arr[$coun][$data];

  $get_terms_default_attributes = array (
            'taxonomy' => 'wbtm_bus_stops', //empty string(''), false, 0 don't work, and return 
            'hide_empty' => false, //can be 1, '1' too
    );
  $terms = get_terms($get_terms_default_attributes);
if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
  ob_start();
  ?>
    <select name="<?php echo $name; ?>" class='seat_type select2'>
      <option value=""><?php _e('Please Select','bus-ticket-booking-with-seat-reservation'); ?></option>
      <?php
    foreach ( $terms as $term ) {
      ?>
        <option value="<?php echo $term->name; ?>" <?php if($type_name==$term->name){ echo "Selected"; } ?> ><?php echo $term->name; ?></option>
        <?php
    }
    ?>
    </select>
  <?php
  
}
$content = ob_get_clean();
return $content;
}



function wbtm_get_bus_price($start,$end, $array) {
   foreach ($array as $key => $val) {
       if ($val['wbtm_bus_bp_price_stop'] === $start && $val['wbtm_bus_dp_price_stop'] === $end ) {
           return $val['wbtm_bus_price'];
           // return $key;
       }
   }
   return null;
}



function wbtm_get_bus_start_time($start, $array) {
   foreach ($array as $key => $val) {
       if ($val['wbtm_bus_bp_stops_name'] === $start ) {
           return $val['wbtm_bus_bp_start_time'];
           // return $key;
       }
   }
   return null;
}



function wbtm_get_bus_end_time($end, $array) {
   foreach ($array as $key => $val) {
       if ($val['wbtm_bus_next_stops_name'] === $end ) {
           return $val['wbtm_bus_next_end_time'];
           // return $key;
       }
   }
   return null;
}

//add_action('wbtm_search_fields','wbtm_bus_search_fileds');
function wbtm_bus_search_fileds($start,$end,$date,$r_date){
    ob_start();
    ?>
    <div class="search-fields">
      <div class="fields-li">
          <label>
             <i class="fa fa-map-marker" aria-hidden="true"></i> <?php _e('From','bus-ticket-booking-with-seat-reservation'); ?>
          <?php echo wbtm_get_bus_route_list( 'bus_start_route', $start ); ?></label>
          </div>
      <div class="fields-li">
          <label>
             <i class="fa fa-map-marker" aria-hidden="true"></i> <?php _e('To:','bus-ticket-booking-with-seat-reservation'); ?>
          <?php echo wbtm_get_bus_route_list( 'bus_end_route', $end ); ?>
          </label>
          </div>
      <div class="fields-li">
          <label for='j_date'>
            <i class="fa fa-calendar" aria-hidden="true"></i>  <?php _e('Date of Journey:','bus-ticket-booking-with-seat-reservation'); ?>
          <input type="text" id="j_date" name="j_date" value="<?php echo $date; ?>">
          </label>
          </div>
      <div class="fields-li return-date-sec">
          <label for='r_date'>
            <i class="fa fa-calendar" aria-hidden="true"></i> <?php _e('Return Date:','bus-ticket-booking-with-seat-reservation'); ?>
          <input type="text" id="r_date" name="r_date" value="<?php echo $r_date; ?>">
          </label>
      </div> 

    <?php 
      if(isset($_GET['bus-r'])){
      $busr = strip_tags($_GET['bus-r']);
      }else{
      $busr = 'oneway';
      }
      ?>

     <div class="fields-li">
       <div class="search-radio-sec">
          <label for="oneway"><input type="radio" <?php if($busr=='oneway'){ echo 'checked'; } ?> id='oneway' name="bus-r" value='oneway'> <?php _e('One Way','bus-booking-manager'); ?></label>
          <label for="return_date"><input type="radio" <?php if($busr=='return'){ echo 'checked'; } ?> id='return_date' name="bus-r" value='return'> <?php _e('Return','bus-booking-manager'); ?></label>
        </div>
      <button type="submit"><i class='fa fa-search'></i> <?php _e('Search Buses','bus-ticket-booking-with-seat-reservation'); ?></button>
      </div>
    </div>
 <script>
      <?php if(isset($_GET['bus-r']) && $_GET['bus-r']=='oneway'){ ?>
        jQuery('.return-date-sec').hide();
      <?php }elseif(isset($_GET['bus-r']) && $_GET['bus-r']=='return'){ ?>
        jQuery('.return-date-sec').show();
      <?php }else{ ?>
        jQuery('.return-date-sec').hide();
      <?php } ?>
        jQuery('#oneway').on('click', function () {
          jQuery('.return-date-sec').hide();
        }); 
        jQuery('#return_date').on('click', function () {
          jQuery('.return-date-sec').show();
        });      
    </script>  


    <?php
    $content = ob_get_clean();
    echo $content;
}


function wbtm_get_seat_status($seat,$date,$bus_id,$start){
global $wpdb;
  $table_name = $wpdb->prefix."wbtm_bus_booking_list";
  $total_mobile_users = $wpdb->get_results( "SELECT status FROM $table_name WHERE seat='$seat' AND journey_date='$date' AND bus_id = $bus_id AND ( boarding_point ='$start' OR next_stops LIKE '%$start%' ) ORDER BY booking_id DESC Limit 1 " );
  return $total_mobile_users;
}


function wbtm_get_available_seat($bus_id,$date){
  global $wpdb;
    $table_name = $wpdb->prefix."wbtm_bus_booking_list";
  $total_mobile_users = $wpdb->get_var( "SELECT COUNT(booking_id) FROM $table_name WHERE bus_id=$bus_id AND journey_date='$date' AND (status=2 OR status=1)" );
  return $total_mobile_users;
}

function wbtm_get_order_meta($item_id,$key){
global $wpdb;
  $table_name = $wpdb->prefix."woocommerce_order_itemmeta";
  $sql = 'SELECT meta_value FROM '.$table_name.' WHERE order_item_id ='.$item_id.' AND meta_key="'.$key.'"';
  $results = $wpdb->get_results($sql) or die(mysql_error());
  foreach( $results as $result ) {
     $value = $result->meta_value;
  }
  return $value;
}



function wbtm_get_order_seat_check($bus_id,$order_id,$seat,$bus_start,$date){
  global $wpdb;
    $table_name = $wpdb->prefix."wbtm_bus_booking_list";
  $total_mobile_users = $wpdb->get_var( "SELECT COUNT(booking_id) FROM $table_name WHERE bus_id=$bus_id AND order_id = $order_id AND seat ='$seat' AND bus_start = '$bus_start' AND journey_date='$date'" );
  return $total_mobile_users;
}




add_action('woocommerce_order_status_changed', 'wbtm_bus_ticket_seat_management', 10, 4);
function wbtm_bus_ticket_seat_management( $order_id, $from_status, $to_status, $order ) {
global $wpdb;
   // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 

   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $product_id = $item_values->get_product_id(); 
        $item_data = $item_values->get_data();
        $product_id = $item_data['product_id'];
        $item_quantity = $item_values->get_quantity();
        $product = get_page_by_title( $item_data['name'], OBJECT, 'wbtm_bus' );
        $event_name = $item_data['name'];
        $event_id = $product->ID;
        $item_id = $item_id;
    // $item_data = $item_values->get_data();
  $wbtm_bus_id             = wbtm_get_order_meta($item_id,'_wbtm_bus_id');
if (get_post_type($wbtm_bus_id) == 'wbtm_bus') { 


$user_id            = $order_meta['_customer_user'][0];
$bus_id             = wbtm_get_order_meta($item_id,'_bus_id');
$user_info_arr      = wbtm_get_order_meta($item_id,'_wbtm_passenger_info');
$seat               = wbtm_get_order_meta($item_id,'Seats');
$start              = wbtm_get_order_meta($item_id,'Start');
$end                = wbtm_get_order_meta($item_id,'End');
$j_date             = wbtm_get_order_meta($item_id,'Date');
$j_time             = wbtm_get_order_meta($item_id,'Time');
$bus_id             = wbtm_get_order_meta($item_id,'_bus_id');
$b_time             = wbtm_get_order_meta($item_id,'_btime');
$seats              = explode(",",$seat);
$usr_inf            = unserialize($user_info_arr);
$counter            = 0;
$next_stops         = maybe_serialize(wbtm_get_all_stops_after_this($bus_id,$start,$end));
$price_arr          = get_post_meta($bus_id,'wbtm_bus_prices',true);
$fare               = wbtm_get_bus_price($start,$end, $price_arr);

  foreach ($seats as $_seats) {
    if(!empty($_seats)){

      if($usr_inf[$counter]['wbtm_user_name']){
        $user_name = $usr_inf[$counter]['wbtm_user_name'];
      }else{
        $user_name = "";
      }
      if($usr_inf[$counter]['wbtm_user_email']){
        $user_email = $usr_inf[$counter]['wbtm_user_email'];
      }else{
        $user_email = "";
      }
      if($usr_inf[$counter]['wbtm_user_phone']){
        $user_phone = $usr_inf[$counter]['wbtm_user_phone'];
      }else{
        $user_phone = "";
      }
      if($usr_inf[$counter]['wbtm_user_address']){
        $user_address = $usr_inf[$counter]['wbtm_user_address'];
      }else{
        $user_address = "";
      }
      if($usr_inf[$counter]['wbtm_user_gender']){
        $user_gender = $usr_inf[$counter]['wbtm_user_gender'];
      }else{
        $user_gender = "";
      }            

$check_before_add = wbtm_get_order_seat_check($bus_id,$order_id,$_seats,$b_time,$j_date);
if($check_before_add == 0){

    $table_name = $wpdb->prefix . 'wbtm_bus_booking_list';
    $add_datetime = date("Y-m-d h:i:s");
     $bi = $wpdb->insert( 
        $table_name, 
        array( 
            'order_id'        => $order_id, 
            'bus_id'          => $bus_id,
            'user_id'         => $user_id,
            'boarding_point'  => $start,
            'next_stops'      => $next_stops,
            'droping_point'   => $end,
            'user_name'       => $user_name,
            'user_email'      => $user_email,
            'user_phone'      => $user_phone,
            'user_gender'     => $user_gender,
            'user_address'    => $user_address,
            'bus_start'       => $b_time,
            'user_start'      => $j_time,
            'seat'            => $_seats,
            'bus_fare'        => $fare,
            'journey_date'    => $j_date,
            'booking_date'    => $add_datetime,
            'status'          => 0,
            'ticket_status'   => 1
        ), 
        array( 
            '%d', 
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',            
            '%s',
            '%s',
            '%d',
            '%d'
        ) 
     );
    }
    }
    $counter++;
  }




















if($order->has_status( 'processing' ) || $order->has_status( 'pending' ) || $order->has_status( 'on-hold' ) ) {
    $status = 1;
    $table_name = $wpdb->prefix . 'wbtm_bus_booking_list';
    $wpdb->query( $wpdb->prepare("UPDATE $table_name 
                SET status = %d 
             WHERE order_id = %d
             AND bus_id = %d",$status, $order_id, $bus_id)
    );
  
}


if($order->has_status( 'cancelled' )) {
    $status = 3;
    $table_name = $wpdb->prefix . 'wbtm_bus_booking_list';
    $wpdb->query( $wpdb->prepare("UPDATE $table_name 
                SET status = %d 
             WHERE order_id = %d
             AND bus_id = %d",$status, $order_id, $bus_id)
    );

}



if( $order->has_status( 'completed' )) {

    $status = 2;
    $table_name = $wpdb->prefix . 'wbtm_bus_booking_list';
    $wpdb->query( $wpdb->prepare("UPDATE $table_name 
                SET status = %d 
             WHERE order_id = %d
             AND bus_id = %d",$status, $order_id, $bus_id)
    );

}
}
}
  }

function wptm_array_strip($string, $allowed_tags = NULL)
{
    if (is_array($string))
    {
        foreach ($string as $k => $v)
        {
            $string[$k] = wptm_array_strip($v, $allowed_tags);
        }
        return $string;
    }
    return strip_tags($string, $allowed_tags);
}

function wbtm_find_product_in_cart($id) {
 
    $product_id = $id;
    $in_cart = false;
 
foreach( WC()->cart->get_cart() as $cart_item ) {
   $product_in_cart = $cart_item['product_id'];
   if ( $product_in_cart === $product_id ) $in_cart = true;
}
 
    if ( $in_cart ) {
      return 'into-cart';
    }else{
      return 'not-in-cart';
    }
}




}else{
function wbtm_admin_notice_wc_not_active() {
  $class = 'notice notice-error';
  $message = __( 'Bus Ticket Booking with Seat Reservation Plugin is Dependent on WooCommerce, But currently WooCommerce is not Active. Please Active WooCommerce plugin first', 'bus-ticket-booking-with-seat-reservation' );
  printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}
add_action( 'admin_notices', 'wbtm_admin_notice_wc_not_active' );
}


function wbtm_check_od_in_range($start_date, $end_date, $j_date){
  // Convert to timestamp
  $start_ts = strtotime($start_date);
  $end_ts = strtotime($end_date);
  $user_ts = strtotime($j_date);

  // Check that user date is between start & end
   if(($user_ts >= $start_ts) && ($user_ts <= $end_ts)){
    return 'yes';
   }else{
    return 'no';
   }
}



function wbtm_get_seat_plan($current_plan){
?>
     <select name="seat_plan" id="webmenu">
        <option value="seat_plan_1" <?php if($current_plan=='seat_plan_1'){ echo 'Selected'; } ?> data-image="<?php echo plugin_dir_url( __FILE__ ).'images/seat-panel-double.png'; ?>"></option>
        <option value="seat_plan_2" <?php if($current_plan=='seat_plan_2'){ echo 'Selected'; } ?> data-image="<?php echo plugin_dir_url( __FILE__ ).'images/seat-panel-single-left.png'; ?>"></option>
        <option value="seat_plan_3" <?php if($current_plan=='seat_plan_3'){ echo 'Selected'; } ?> data-image="<?php echo plugin_dir_url( __FILE__ ).'images/seat-panel-single-right.png'; ?>"></option>
        <?php do_action('wbtm_after_seat_plan_dd'); ?>
      </select>
  <?php
}

function wbtm_buffer_time_check($bp_time,$date){
     $bus_buffer_time = bus_get_option( 'bus_buffer_time', 'general_setting_sec', 0 );
     date_default_timezone_set(get_option('timezone_string'));
     $bus_start_time = date('H:i', strtotime($bp_time));
     $start_bus = $date.' '.$bus_start_time;
     $current_date_time = date('Y-m-d H:i');
     $bus_starting_time = strtotime($start_bus)/3600;
     $today_current_time = strtotime($current_date_time)/3600;
     $diff = $bus_starting_time - $today_current_time;
     if($diff >= $bus_buffer_time){
      return 'yes';
    }else{
      return 'no';
    }
}


function wbtm_get_driver_position($current_plan){
?>
      <select name="driver_seat_position">
        <option <?php if($current_plan=='driver_left'){ echo 'Selected'; } ?> value="driver_left"><?php _e('Left','bus-ticket-booking-with-seat-reservation'); ?></option>
        <option <?php if($current_plan=='driver_right'){ echo 'Selected'; } ?> value="driver_right"><?php _e('Right','bus-ticket-booking-with-seat-reservation'); ?></option>
        <?php do_action('wbtm_after_driver_position_dd'); ?>
      </select>
  <?php
}


function wbtm_bus_seat_plan($current_plan,$start,$date){

  $global_plan = get_post_meta(get_the_id(),'wbtm_bus_seats_info',true);
if(!empty($global_plan)){
  wbtm_seat_global($start,$date);
}else{
  if($current_plan=='seat_plan_1'){
    wbtm_seat_plan_1($start,$date);
  }
  if($current_plan=='seat_plan_2'){
    wbtm_seat_plan_2($start,$date);
  }
  if($current_plan=='seat_plan_3'){
    wbtm_seat_plan_3($start,$date);
  }
}


}


function wbtm_get_this_bus_seat_plan(){
$current_plan = get_post_meta(get_the_id(),'seat_plan',true);
$bus_meta           = get_post_custom(get_the_id());
$seat_col           = $bus_meta['wbtm_seat_col'][0];
$seat_col_arr       = explode(",",$seat_col);
$seat_column        = count($seat_col_arr);
if($current_plan){
    $current_seat_plan = $current_plan;
}else{
  if($seat_column==4){
    $current_seat_plan = 'seat_plan_1';
  }else{
    $current_seat_plan = 'seat_plan_2';
  }
  }
  return $current_seat_plan;
}



function wbtm_seat_plan(){
  $seat_col = strip_tags($_POST['seat_col']);
  $seat_row = strip_tags($_POST['seat_row']);
?>

<div>
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
    for ($x = 1; $x <= $seat_row; $x++) {
    ?>
    <tr>
    <?php
    for ($row = 1; $row <= $seat_col; $row++) {
    ?>
      <td align="center"><input type="text" value="" name="seat<?php echo $row; ?>[]"  class="text"></td>
    <?php } ?>
          <td align="center"><a class="button remove-seat-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a>
            <input type="hidden" name="bus_seat_panels[]">
          </td>
    </tr>
    <?php
    }
   ?>
    <!-- empty hidden one for jQuery -->
    <tr class="empty-row-seat screen-reader-text">
    <?php
    for ($row = 1; $row <= $seat_col; $row++) {
    ?>
      <td align="center"><input type="text" value="" name="seat<?php echo $row; ?>[]"  class="text"></td>
    <?php } ?>
      <td align="center"><a class="button remove-seat-row" href="#"><?php _e('Remove','bus-ticket-booking-with-seat-reservation'); ?></a><input type="hidden" name="bus_seat_panels[]"></td>
    </tr>
  </tbody>
</table>
  <p><a id="add-seat-row" class="button" href="#"><?php _e('Add New Seat Row','bus-ticket-booking-with-seat-reservation'); ?></a></p>
</div>
<?php
  die();
}
add_action('wp_ajax_wbtm_seat_plan', 'wbtm_seat_plan');
add_action('wp_ajax_nopriv_wbtm_seat_plan', 'wbtm_seat_plan');
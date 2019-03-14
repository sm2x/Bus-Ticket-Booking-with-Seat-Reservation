<?php 
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

// Enqueue Scripts for admin dashboard
add_action('admin_enqueue_scripts', 'wbtm_bus_admin_scripts');
function wbtm_bus_admin_scripts() {
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-core');   
    wp_enqueue_style('wbtm-clocklet-style',plugin_dir_url( __DIR__ ).'css/clocklet.css',array());
    wp_enqueue_style('mep-admin-style',plugin_dir_url( __DIR__ ).'css/admin_style.css',array());
    wp_enqueue_style('mep-jquery-ui-style',plugin_dir_url( __DIR__ ).'css/jquery-ui.css',array());    
    wp_enqueue_style('wbtm-dropdown-image-style',plugin_dir_url( __DIR__ ).'css/dd.css',array());    

 wp_enqueue_script('wbtm-select2-lib',plugin_dir_url( __DIR__ ).'js/select2.full.min.js',array('jquery','jquery-ui-core'),1,true); 

 wp_enqueue_script('wbtm-clocklet-lib',plugin_dir_url( __DIR__ ).'js/clocklet.js',array('jquery','jquery-ui-core'),1,true);
 wp_enqueue_script('jquery-image-dropdown-lib',plugin_dir_url( __DIR__ ).'js/jquery-dd.min.js',array('jquery','jquery-ui-core'),1,true);
    wp_enqueue_script('gmap-scripts',plugin_dir_url( __DIR__ ).'js/mkb-admin.js',array('jquery','jquery-ui-core'),1,true);
    wp_enqueue_style ('wbtm-select2-style-cdn',"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css",null,1); 
       wp_enqueue_style ('font-awesome-css-cdn',"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css",null,1); 
}



function wbtm_add_admin_scripts( $hook ) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'wbtm_bus' === $post->post_type ) { 
             wp_enqueue_style('wbtm-jquery-ui-style',plugin_dir_url( __DIR__ ).'css/jquery-ui.css',array());
        
        }
    }
       
}
add_action( 'admin_enqueue_scripts', 'wbtm_add_admin_scripts', 10, 1 );




// Datepicker code for admin dashboard load in footer section
add_action('admin_footer','wbtm_admin_footer_script',10,99);
add_action('wp_footer','wbtm_admin_footer_script',10,99);
function wbtm_admin_footer_script(){
  ?>
<script type="text/javascript">
jQuery(document).ready(function($){
      jQuery( "#j_date" ).datepicker({
        dateFormat: "yy-mm-dd",
        minDate:0
      });
      jQuery( "#r_date" ).datepicker({
        dateFormat: "yy-mm-dd",
        minDate:0
      });
      jQuery( "#ja_date" ).datepicker({
        dateFormat: "yy-mm-dd"
      });
      jQuery(".the_select select").select2();
    });
</script>
  <?php
}



// Select2 code for admin dashboard load in footer section
add_action('wp_footer','wbtm_admin_footer_select_2_script',10,99);
function wbtm_admin_footer_select_2_script(){
  ?>
<script type="text/javascript">
jQuery(document).ready(function($){
      jQuery(".select2, #boarding_point, #drp_point").select2();
    });
</script>
  <?php
}







// Enqueue Scripts for frontend
add_action('wp_enqueue_scripts', 'wbtm_bus_enqueue_scripts');
function wbtm_bus_enqueue_scripts() {
   wp_enqueue_script('jquery');
   wp_enqueue_script('jquery-ui-datepicker');
   wp_enqueue_script('jquery-ui-core');   
   wp_enqueue_script('jquery-ui-accordion');
   wp_enqueue_style('wbtm-jquery-ui-style',plugin_dir_url( __DIR__ ).'css/jquery-ui.css',array());
    wp_enqueue_script('wbtm-select2-lib',plugin_dir_url( __DIR__ ).'js/select2.full.min.js',array('jquery','jquery-ui-core'),1,false); 

   wp_enqueue_style('wbtm-bus-style',plugin_dir_url( __DIR__ ).'css/style.css',array());


   wp_enqueue_style ('font-awesome-css-cdn',"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css",null,1); 

   wp_enqueue_style ('wbtm-select2-style-cdn',"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css",null,1); 
}


function wbtm_ajax_call_url(){
    wp_localize_script('jquery', 'wbtm_ajax', array( 'wbtm_ajaxurl' => admin_url( 'admin-ajax.php')));
}
add_action('admin_enqueue_scripts','wbtm_ajax_call_url');
add_action('wp_enqueue_scripts','wbtm_ajax_call_url');
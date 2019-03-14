// jQuery(".select2").select2();

jQuery(document).ready(function($){

      jQuery( "#od_start" ).datepicker({
        dateFormat: "yy-mm-dd",
        minDate:0
      });
      jQuery( "#od_end" ).datepicker({
        dateFormat: "yy-mm-dd"
        // minDate:0
      });

try {
jQuery("#webmenu").msDropDown();
} catch(e) {
alert(e.message);
}


    });



// $(document).ready(function(e) {

// });
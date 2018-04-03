/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */

jQuery(document).ready( function($) {
    "use strict";

    function toggle_product_editor_single_product(){
        if( $('#_ywsbs_subscription').prop('checked')){
            $('.ywsbs_price_is_per, .ywsbs_max_length').show();
        }else{
            $('.ywsbs_price_is_per, .ywsbs_max_length').hide();
        }
    }
    $('#_ywsbs_subscription').on('change', function(){
        toggle_product_editor_single_product();
    });
    toggle_product_editor_single_product();

    $('#_ywsbs_price_time_option').on('change', function(){
        $('.ywsbs_max_length .description span').text( $(this).val() );
        var selected = $(this).find(':selected'),
            max_value = selected.data('max');
        $('.ywsbs_max_length .description .max-l').text( max_value );
    });

});
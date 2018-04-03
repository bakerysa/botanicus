<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');


class WOOCS_FIXED_AMOUNT {

    protected $key="";
            
    public function __construct() {
    }

    public function admin_footer() {
	wp_enqueue_script('chosen-drop-down', WOOCS_LINK . 'js/chosen/chosen.jquery.min.js', array('jquery'));
	wp_enqueue_style('chosen-drop-down', WOOCS_LINK . 'js/chosen/chosen.min.css');
	wp_enqueue_script('woocs-fixed', WOOCS_LINK . 'js/fixed.js', array('jquery'));
	wp_enqueue_style('woocs-fixed', WOOCS_LINK . 'css/fixed.css');
    }
    public function get_value($post_id, $code, $type) {
	//echo $post_id.'+++_woocs_' . $type . '_price_' . strtoupper($code).'<br />';
	return get_post_meta($post_id, '_woocs_' . $type . $this->key . strtoupper($code), true);
    }

    public function is_exists($post_id, $code, $type) {
	$is = false;
	$val = $this->get_value($post_id, $code, $type);
	if (floatval($val) > 0 OR (int) $val === -1) {
	    $is = true;
	}
	return $is;
    }
        
    public function is_empty($post_id, $code, $type) {
	$is = false;
	$val = $this->get_value($post_id, $code, $type);
	if ((int) $val === -1) {
	    $is = true;
	}
	return $is;
    }

    public function render_html($pagepath, $data = array()) {
	@extract($data);
	ob_start();
	include($pagepath);
	return ob_get_clean();
    }

}



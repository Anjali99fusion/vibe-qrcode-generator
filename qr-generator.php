<?php
/*
Plugin Name: Vibe QR Code Generator
Plugin URI: http://www.VibeThemes.com
Description: custom plugin 
Version: 1.0
Author: vibethemes
Author URI: http://www.VibeThemes.com
Text Domain: wplms-qrgnr
Domain Path: /languages/
*/

/**
 * Register important functions for WPLMS
 *
 * @author      VibeThemes
 * @category    Admin
 * @package     Initialization
 * @version     2.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

class Vibe_qrcode_generator{

    public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new Vibe_qrcode_generator();

        return self::$instance;
    }

    private function __construct(){
    	if(!function_exists('vibe_get_option')){
    		//theme not active
    		return;
    	}
    	add_action('wp_enqueue_scripts',array($this,'wplms_enqueue_head'));

        add_action('wplms_validate_certificate',array($this,'cache_user_id_course_id'),10,2);
        add_action('wplms_certificate_before_full_content',array($this,'cache_template_id'));
    	add_action('wplms_certificate_after_content',array($this,'show_qr'));
    }

    function wplms_enqueue_head(){
    	global $post;
    	$certificate_page=vibe_get_option('certificate_page');
    	if($post->post_type == 'certificate' || ($post->post_type == 'page' && $post->ID == $certificate_page) || (function_exists('bp_is_profile') && bp_is_profile()) ){
    		wp_enqueue_script( 'qrcode-js', plugins_url('jquery.qrcode.min.js',__FILE__) ,array('jquery'),1);
    	}

    }

    function cache_user_id_course_id($user_id,$course_id){
        $this->course_id = $course_id;
        $this->user_id = $user_id;
    }

    function cache_template_id(){
        global $post;
        $this->template_id = $post->ID;
    }

    function show_qr(){

        $certificate_code =  apply_filters('wplms_certificate_code',$this->template_id.'-'.$this->course_id.'-'.$this->user_id,$this->course_id,$this->user_id);
       
    	?>
    	<div id="qrcode"></div>
        <script>
    	jQuery(document).ready(function(){
    		jQuery('#qrcode').qrcode("<?php echo $certificate_code; ?>");
    	});
    	</script>
    	<?php
    }  

}

Vibe_qrcode_generator::init();
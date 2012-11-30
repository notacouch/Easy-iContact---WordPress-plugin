<?php
/*
Plugin Name: Easy iContact

Description: Makes seamless integration with iContact point and click.
Version: 0.3
Author: Ben McFadden
Author URI: http://benmcfadden.com
URI: https://github.com/mcfadden/Easy-iContact---WordPress-plugin

*/
?>
<?php
//Enable Shortcodes in widgets:
add_filter('widget_text', 'do_shortcode');

function easy_icontact_menu() {
	add_options_page('Easy iContact Options', 'Easy iContact', 'manage_options', 'easyicontact', 'easy_icontact_options_page');
}
add_action('admin_menu', 'easy_icontact_menu');

function easy_icontact_options_page() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

?>
<div class="wrap">
  <h2>East iContact</h2>
  Easy iContact configuration.
<form action="options.php" method="post">
<?php settings_fields('easy_icontact_options'); ?>
<?php do_settings_sections('easy_icontact'); ?>
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
<h3>Shortcode settings</h3>
  <p>Shortcode: [easyicontact]</p>
  <p>Shortcode optons:<br />
  0 == false<br />
  1 == true</p>
  <ul>
    <li><strong>confirm_email</strong> (1 or 0) default: <em>true (1)</em></li>
    <li><strong>first_name</strong> (1 or 0) default: <em>true (1)</em></li>
    <li><strong>last_name</strong> (1 or 0) default: <em>true (1)</em></li>
    <!--<li><strong>table</strong> put the items in a table (1 or 0) default: <em>true (1)</em> NOTE: Not yet implemented</li>-->
    <li><strong>ajax</strong> (1 or 0) default: <em>true (1)</em> NOTE: while this setting is implemented, when not using AJAX to process requests, it may respond a bit strange. This feature may be removed in future versions.</li>
    <li><strong>validation</strong> (1 or 0) default: <em>true (1)</em></li>
    <li><strong>label_type</strong> ('label' or 'value') Create HTML labels or insert the lable as the default value of a field. If value is chosen, upon click, the default value is removed. default: <em>&quot;label&quot;</em></li>
    <li><strong>submit_image</strong> path/URL - If set to a value other than false, it will be used as the path/URL to a submit button image. Relative paths are relative from the <a href="http://codex.wordpress.org/Function_Reference/bloginfo" target="_blank">stylesheet_directory</a> (<em><?php bloginfo('stylesheet_directory'); ?></em>). Absolute paths and URLs are used as-is. URLs must begin with "http://" or "https://".  If submit_image is set, submit_text is used as the alt text. Default: <em>false (0)</em></li>
    <li><strong>submit_text</strong> text - Will show on the submit button if submit_image is false. If submit_image is used, submit_text is used as the alt text for submit_image. Default: &quot;Sign up!&quot;</li>
	<li><strong>callback_function</strong> function name - This JavaScript function will be called upon successful submit of the form. It is called immediately after the success message is displayed. Checks to make sure the function is defined.</li>
  </ul>
  <p>Example Shortcode:</p>
  <pre>[easyicontact confirm_email='0' last_name='0' 'submit_text='Sign me up!' label_type='value' ]</pre>
  <h3>Example CSS</h3>
  <p>You'll have to add this to your template's CSS file. In future versions, I may enable a custom CSS option, but for now you have to put it in your template's CSS file and edit there.</p>
  <pre>
    /* EasyiContact */

/* Wrapper div around the form element */
div#easyicontact_wrapper{ 

}
/* Text input fields */
div#easyicontact_wrapper input[type=text]{
  background-color: #FFFFFF;
  border: 1px solid #CCCCCC;
  width: 140px;
  height: 15px;
  color: #000000;
  padding: 2px;
}
/* Text input field that failed validation */
div#easyicontact_wrapper input.validation-error{
  border: 1px solid #FF0000;
}
/* Text input field with default text (when label_type='value' ) */
div#easyicontact_wrapper input.default{
  color: #CCCCCC;
}
/* Image submit */
div#easyicontact_wrapper input.submit-image{
  background-color: transparent;
  color: #000000;
}
/* Button submit */
div#easyicontact_wrapper input.submit-button{
  color: #000000;
}
  </pre>
</div>
<?php
}

add_action('admin_init', 'easy_icontact_admin_init');
function easy_icontact_admin_init(){
  wp_enqueue_script('jquery');

  register_setting( 'easy_icontact_options', 'easy_icontact_options', 'easy_icontact_options_validate' );
  
  add_settings_section('easy_icontact_main', 'iContact Account Settings', 'easy_icontact_section_text', 'easy_icontact');
  add_settings_field('easy_icontact_listid', 'List ID', 'easy_icontact_setting_listid', 'easy_icontact', 'easy_icontact_main');
  add_settings_field('easy_icontact_specialid', 'Special ID<br />(Should be the same as ListID)', 'easy_icontact_setting_specialid', 'easy_icontact', 'easy_icontact_main');
  add_settings_field('easy_icontact_specialidvalue', 'Special ID Value', 'easy_icontact_setting_specialidvalue', 'easy_icontact', 'easy_icontact_main');
  add_settings_field('easy_icontact_clientid', 'Client ID', 'easy_icontact_setting_clientid', 'easy_icontact', 'easy_icontact_main');
  add_settings_field('easy_icontact_formid', 'Form ID', 'easy_icontact_setting_formid', 'easy_icontact', 'easy_icontact_main');
  
  add_settings_section('easy_icontact_fields', 'Field Labels', 'easy_icontact_fields_section', 'easy_icontact');
  add_settings_field('easy_icontact_fields_fname_label', 'First Name:', 'easy_icontact_setting_fields_fname_label', 'easy_icontact', 'easy_icontact_fields');
  add_settings_field('easy_icontact_fields_lname_label', 'Last Name:', 'easy_icontact_setting_fields_lname_label', 'easy_icontact', 'easy_icontact_fields');
  add_settings_field('easy_icontact_fields_email_label', 'Email Address:', 'easy_icontact_setting_fields_email_label', 'easy_icontact', 'easy_icontact_fields');
  add_settings_field('easy_icontact_fields_confirm_email_label', 'Confirm Email Address:', 'easy_icontact_setting_fields_confirm_email_label', 'easy_icontact', 'easy_icontact_fields');
  
  add_settings_section('easy_icontact_messages', 'Response Messages', 'easy_icontact_response_section', 'easy_icontact');
  add_settings_field('easy_icontact_success_message', 'Success Message (HTML)', 'easy_icontact_setting_success_message', 'easy_icontact', 'easy_icontact_messages');
  add_settings_field('easy_icontact_error_message', 'Error Message (HTML)', 'easy_icontact_setting_error_message', 'easy_icontact', 'easy_icontact_messages');
}

function easy_icontact_section_text() {
  ?>
  <script type="text/javascript">
    function parse_html(){
      html = jQuery('textarea#raw_html').val();
      
      listid = html.split('"listid" value="', 2);
      if(listid == html){
        alert("Error: listid not found in provided HTML");
        return;
      }
      if(listid[1].split('">', 1) == listid){
        alert("Error: invalid listid found in provided HTML");
        return;
      }
      jQuery('input#easy_icontact_listid').val(listid[1].split('">', 1));
      
      specialid = html.split('name="specialid:', 2);
      if(specialid == html){
        alert("Error: specialid not found in provided HTML");
        return;
      }
      if(specialid[1].split('" value="', 1) == specialid){
        alert("Error: invalid specialid found in provided HTML");
        return;
      }
      jQuery('input#easy_icontact_specialid').val(specialid[1].split('" value="', 1));
      
      specialidvalue = html.split('name="specialid:', 2);
      if(specialidvalue == html){
        alert("Error: specialidvalue not found in provided HTML");
        return;
      }
      if(specialidvalue[1].split('" value="', 2) == specialidvalue){
        alert("Error: invalid specialidvalue found in provided HTML");
        return;
      }
      specialidvalue = specialidvalue[1].split('" value="', 2);
      if(specialidvalue[1].split('">', 1) == specialidvalue){
        alert("Error: invalid specialidvalue found in provided HTML");
        return;
      }
      jQuery('input#easy_icontact_specialidvalue').val(specialidvalue[1].split('">', 1));
      
      clientid = html.split('"clientid" value="', 2);
      if(clientid == html){
        alert("Error: clientid not found in provided HTML");
        return;
      }
      if(clientid[1].split('">', 1) == clientid){
        alert("Error: invalid clientid found in provided HTML");
        return;
      }
      jQuery('input#easy_icontact_clientid').val(clientid[1].split('">', 1));
      
      formid = html.split('"formid" value="', 2);
      if(formid == html){
        alert("Error: formid not found in provided HTML");
        return;
      }
      if(formid[1].split('">', 1) == formid){
        alert("Error: invalid formid found in provided HTML");
        return;
      }
      jQuery('input#easy_icontact_formid').val(formid[1].split('">', 1));
      
      
      
      
    }
  </script>
  <p>Please enter in the details from your sign-up form, or paste the generated form in the textarea below:</p>
  <textarea id="raw_html" name="raw_html" cols="80" rows="5">Paste the HTML code from iContact into this box, and then click "Parse Code" below. Be sure to click "Save Changes" when you are done.</textarea><br />
  <input type="button" name="parse" value="Parse Code" onClick="parse_html()" />
  <?php

  }

function easy_icontact_setting_listid(){
  $options = get_option('easy_icontact_options');
  echo "<input id='easy_icontact_listid' name='easy_icontact_options[listid]' size='40' type='text' value='" . $options['listid'] . "' />";
}

function easy_icontact_setting_specialid(){
  $options = get_option('easy_icontact_options');
  echo "<input id='easy_icontact_specialid' name='easy_icontact_options[specialid]' size='40' type='text' value='" . $options['specialid'] . "' />";
}

function easy_icontact_setting_specialidvalue(){
  $options = get_option('easy_icontact_options');
  echo "<input id='easy_icontact_specialidvalue' name='easy_icontact_options[specialidvalue]' size='40' type='text' value='" . $options['specialidvalue'] . "' />";
}

function easy_icontact_setting_clientid(){
  $options = get_option('easy_icontact_options');
  echo "<input id='easy_icontact_clientid' name='easy_icontact_options[clientid]' size='40' type='text' value='" . $options['clientid'] . "' />";
}

function easy_icontact_setting_formid(){
  $options = get_option('easy_icontact_options');
  echo "<input id='easy_icontact_formid' name='easy_icontact_options[formid]' size='40' type='text' value='" . $options['formid'] . "' />";
}

function easy_icontact_fields_section(){
  ?>
    <p>Enter the text you would like to use to identify the fields.</p>
  <?php
}
function easy_icontact_setting_fields_fname_label(){
  $options = get_option('easy_icontact_options');
  if(!isset($options['fname_label']) || empty($options['fname_label'])){
    $options['fname_label'] = "First Name";
  }
  echo "<input id='easy_icontact_fields_fname_label' name='easy_icontact_options[fname_label]' size='40' type='text' value='" . $options['fname_label'] . "' />";
}
function easy_icontact_setting_fields_lname_label(){
  $options = get_option('easy_icontact_options');
  if(!isset($options['lname_label']) || empty($options['lname_label'])){
    $options['lname_label'] = "Last Name";
  }
  echo "<input id='easy_icontact_fields_lname_label' name='easy_icontact_options[lname_label]' size='40' type='text' value='" . $options['lname_label'] . "' />";
}
function easy_icontact_setting_fields_email_label(){
  $options = get_option('easy_icontact_options');
  if(!isset($options['email_label']) || empty($options['email_label'])){
    $options['email_label'] = "Email Address";
  }
  echo "<input id='easy_icontact_fields_email_label' name='easy_icontact_options[email_label]' size='40' type='text' value='" . $options['email_label'] . "' />";
}
function easy_icontact_setting_fields_confirm_email_label(){
  $options = get_option('easy_icontact_options');
  if(!isset($options['confirm_email_label']) || empty($options['confirm_email_label'])){
    $options['confirm_email_label'] = "Comfirm Email Address";
  }
  echo "<input id='easy_icontact_fields_confirm_email_label' name='easy_icontact_options[confirm_email_label]' size='40' type='text' value='" . $options['confirm_email_label'] . "' />";
}

function easy_icontact_response_section(){
  ?>
    <p>Enter the response HTML you would like displayed.</p>
  <?php
}

function easy_icontact_setting_success_message(){
  $options = get_option('easy_icontact_options');
  if(!isset($options['success_message']) || empty($options['success_message'])){
    $options['success_message'] = "<h2>Thank you!</h2>\n<p>You have successfully signed up to receive email updates</p>";
  }
  echo "<textarea id='easy_icontact_success_message' name='easy_icontact_options[success_message]' cols='80' rows='5' type='text'>" . $options['success_message'] . "</textarea>";
}

function easy_icontact_setting_error_message(){
  $options = get_option('easy_icontact_options');
  if(!isset($options['error_message']) || empty($options['error_message'])){
    $options['error_message'] = "<h2>Oops!</h2>\n<p>Something went wrong! Please try again later;</p>";
  }
  echo "<textarea id='easy_icontact_error_message' name='easy_icontact_options[error_message]' cols='80' rows='5' type='text'>" . $options['error_message'] . "</textarea>";
}

function easy_icontact_options_validate($input) {
  $newinput['listid'] = trim($input['listid']);
  $newinput['specialid'] = trim($input['specialid']);
  $newinput['specialidvalue'] = trim($input['specialidvalue']);
  $newinput['clientid'] = trim($input['clientid']);
  $newinput['formid'] = trim($input['formid']);
  $newinput['fname_label'] = trim($input['fname_label']);
  $newinput['lname_label'] = trim($input['lname_label']);
  $newinput['email_label'] = trim($input['email_label']);
  $newinput['confirm_email_label'] = trim($input['confirm_email_label']);
  $newinput['success_message'] = trim($input['success_message']);
  $newinput['error_message'] = trim($input['error_message']);
  /*if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['text_string'])) {
    $newinput['text_string'] = '';
  }*/
  return $newinput;
}

// [bartag foo="foo-value"]
function easyicontacttag_func( $atts ) {
	extract( shortcode_atts( array(
    'confirm_email' => true,
    'first_name' => true,
    'last_name' => true,
    'table' => true, //not yet implemented
    'ajax' => true,
    'validation' => true,
    'label_type' => 'label',
    'submit_image' => false,
    'submit_text' => "Sign up!",
	'callback_function' => false
	), $atts ) );
  $options = get_option('easy_icontact_options');
  
  $output = '';
  //$output .= print_r($options, true); //DEBUG
  if(true == (bool)$validation || true == (bool)$ajax){
    global $add_jquery;
    $add_jquery = true;
    $javascript = '';
    $javascript .= '
    <script type="text/javascript">
      //jQuery(document).ready(function(jQuery){
        jQuery("form#easyicontact").submit(function(fse){
          
          ';
    if(true == (bool)$validation){
      $javascript .= '
        /* Validation */
        var error = new Boolean(false);
      ';

      $javascript .= '
      if(jQuery("input#fields_email").val() == \'\' || jQuery("input#fields_email").val() == jQuery("input#fields_email")[0].defaultValue ){
        jQuery("input#fields_email").addClass("validation-error").parents("div.control-group").addClass("error");
        error = true;
      }else{
        jQuery("#fields_email").removeClass("validation-error").parents("div.control-group").removeClass("error");
      }
      ';
      
      if(true == (bool)$confirm_email){
        $javascript .= '
        if(jQuery("input#fields_confirm_email").val() == \'\' || jQuery("input#fields_confirm_email").val() != jQuery("input#fields_email").val() ){
          jQuery("input#fields_confirm_email").addClass("validation-error");
          error = true;
        }else{
          jQuery("#fields_confirm_email").removeClass("validation-error");
        }
        ';
      }
      
      if(true == (bool)$first_name){
        $javascript .= '
        if(jQuery("input#fields_fname").val() == \'\' || jQuery("input#fields_fname").val() == jQuery("input#fields_fname")[0].defaultValue ){
          jQuery("input#fields_fname").addClass("validation-error");
          error = true;
        }else{
          jQuery("#fields_fname").removeClass("validation-error");
        }
        ';
      }
      
      if(true == (bool)$last_name){
        $javascript .= '
        if(jQuery("input#fields_lname").val() == \'\' || jQuery("input#fields_lname").val() == jQuery("input#fields_lname")[0].defaultValue ){
          jQuery("input#fields_lname").addClass("validation-error");
          error = true;
        }else{
          jQuery("#fields_lname").removeClass("validation-error");
        }
        ';
      }
      
      $javascript .= '
        if(false != error){
          return false;
        }
      ';
      
    }
    if(true == (bool)$ajax){
      $javascript .= '
        /* Ajax submission code here */
         //Submit form
         jQuery.post(
         "./", 
         jQuery("#easyicontact").serialize() + "&ajax=true",
           function(data){
             var data_str       = String(data);
             var error_text     = data_str.replace(/^error:/,"");
             var success_text   = data_str.replace(/^success:/, "");
             var error          = false;
             var success        = false;
             var use_str        = data_str;
             if ( error_text !== data_str ) {
               error    = true;
               use_str  = error_text;
             }
             if ( success_text !== data_str ) {
               success  = true;
               use_str  = success_text;
             }
             var html_body = (!error) ? "div#easyicontact_wrapper":"div#easyicontact_response";
             if ( error ) {
               jQuery("#easyicontact_wrapper").find("div.control-group").addClass("error");
               jQuery("#easyicontact_wrapper").find("input,button").attr("disabled", false);
             }
             jQuery(html_body).html(use_str);
             ';
             if(false != $callback_function){
               $callback_function = rtrim($callback_function, "(); ") . '()';
               $output .= '
               if(window.' . $callback_function . '){
                 ' . $callback_function . ';
               }
               ';
             }
   	$javascript .= '
           } 
         );
         // Disable user elements while we submit
         jQuery(this).find("input,button").attr("disabled", true);
         return false;
      ';
    }
    $javascript .= '
        });
        jQuery("#easyicontact input[type=text]").focus(function(){
          if (jQuery(this).val() == this.defaultValue) {
            jQuery(this).val("").removeClass("default"); 
          }
		  jQuery(this).removeClass("validation-error");
        });
        jQuery("#easyicontact input[type=text]").blur(function(){
          if (jQuery(this).val() == "") {
            jQuery(this).val(this.defaultValue).addClass("default"); 
          }
        });
        
        
      //});
    </script>';
  }
  
  $output .= '
  <div id="easyicontact_wrapper">
    <div id="easyicontact_response"></div>
    <form name="easyicontact" id="easyicontact" method="POST" action="">';
    if(true == (bool)$first_name){
      if('value' != $label_type){
        $output .= '<label for="fields_fname">' . $options['fname_label'] . '</label>';
      }
      $output .= '<input type="text" name="fields_fname" id="fields_fname" ';
      if('value' == $label_type){
        $output .= 'class="default" value="' . $options['fname_label'] . '" ';
      }
      $output .= ' />';
    }
    
    if(true == (bool)$last_name){
      if('value' != $label_type){
        $output .= '<label for="fields_lname">' . $options['lname_label'] . '</label>';
      }
      $output .= '<input type="text" name="fields_lname" id="fields_lname" ';
      if('value' == $label_type){
        $output .= 'class="default" value="' . $options['lname_label'] . '" ';
      }
      $output .= ' />';
    }
    
    if('value' != $label_type){
      $output .= '<label for="fields_email">' . $options['email_label'] . '</label>';
    }
    $output .= '<div class="control-group"><div class="input-prepend input-append"><span class="add-on"><i class="icon-envelope"></i></span>';
    $output .= '<input type="text" name="fields_email" id="fields_email" ';
      
      if(isset($_GET['fields_email'])){
        $output .= 'value="' . $_GET['fields_email'] . '" ';
      }elseif('value' == $label_type){
        $output .= 'class="default" value="' . $options['email_label'] . '" ';
      }
      
    $output .= '/></div></div>';
      
    if(true == (bool)$confirm_email){
      if('value' != $label_type){
        $output .= '<label for="fields_confirm_email">' . $options['confirm_email_label'] . '</label>';
      }
      $output .= '<input type="text" name="fields_confirm_email" id="fields_confirm_email" ';
      if('value' == $label_type){
        $output .= 'class="default" value="' . $options['confirm_email_label'] . '" ';
      }
      $output .= ' />';
    }
      
      $output .= '<input type="hidden" name="easyicontact" value="true" />';
      
      if(false != (bool)$submit_image){
        if(false !== stripos($submit_image, 'http://') || false !== stripos($submit_image, 'https://')){ //URL
          $output .= '<input class="submit-image" type="image" alt="' . $submit_text . '" src="' . $submit_image . '" />';
        }elseif(0 == strpos($submit_image, '/')){ //Absolute Path
          $output .= '<input class="submit-image" type="image" alt="' . $submit_text . '" src="' . $submit_image . '" />';
        }else{ //Relative Path
          $output .= '<input class="submit-image" type="image" alt="' . $submit_text . '" src="' . get_bloginfo('stylesheet_directory') . '/' . ltrim($submit_image, '/') . '" />';
        }
      }else{
        $output .= '<button class="btn btn-warning submit-button" type="submit">' . $submit_text . '</button>';
      }
      
      $output .='
    </form>
  </div>';
  $output .= $javascript;

	return $output;
}
add_shortcode( 'easyicontact', 'easyicontacttag_func' );

/* FAILED attempte to load jQuery in the footer
add_action('wp_footer', 'print_my_script');
function print_my_script() {
	global $add_jquery;
 
	if ( ! $add_jquery )
		return;
 
	wp_print_scripts('jquery');
}
/**/


add_action('template_redirect', 'add_jquery');
function add_jquery() {
	wp_enqueue_script('jquery');
}

add_action('init', 'easyicontact_process_request');
function easyicontact_process_request(){
  if(true == $_POST['easyicontact']){
    $options = get_option('easy_icontact_options');
    
    $url = "http://app.icontact.com/icp/signup.php";
    
    if ( ! is_email($_POST['fields_email']) ) {
      exit('error:<p>Please enter a valid email address.</p>');
    }
    
    $post_data = array(
      'source' => $_SERVER['REQUEST_URI'],
      'listid' => $options['listid'],
      'specialid:' . $options['specialid'] => $options['specialidvalue'],
      'clientid' => $options['clientid'],
      'formid' => $options['formid'],
      'reallistid' => '1',
      'doubleopt' => '0',
      'fields_email' => $_POST['fields_email'],
      'fields_fname' => $_POST['fields_fname'],
      'fields_lname' => $_POST['fields_lname']
    );
    $response = wp_remote_post( $url, array(
        'method' => 'POST',
        'timeout' => 5,
        'redirection' => 5,
        'httpversion' => 1.0,
        'blocking' => true,
        'headers' => array(),
        'body' => $post_data,
        'cookies' => array()
      )
    );
    
    if( is_wp_error( $response ) ) {
      echo 'error:'.$options['error_message'];
    } else {
      echo 'success:'.$options['success_message'];
    }
    exit();
  }
}

// Minimum Bootstrap CSS reference, remember to replace icon URLs and fix the comment block below
/*
<style type="text/css">
/ *!
 * Bootstrap v2.2.1
 *
 * Copyright 2012 Twitter, Inc
 * Licensed under the Apache License v2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Designed and built with all the love in the world @twitter by @mdo and @fat.
 * /
.clearfix{*zoom:1;}.clearfix:before,.clearfix:after{display:table;content:"";line-height:0;}
.clearfix:after{clear:both;}
.hide-text{font:0/0 a;color:transparent;text-shadow:none;background-color:transparent;border:0;}
.input-block-level{display:block;width:100%;min-height:30px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}
.label,.badge{display:inline-block;padding:2px 4px;font-size:11.844px;font-weight:bold;line-height:14px;color:#ffffff;vertical-align:baseline;white-space:nowrap;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#999999;}
.label{-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;}
.badge{padding-left:9px;padding-right:9px;-webkit-border-radius:9px;-moz-border-radius:9px;border-radius:9px;}
a.label:hover,a.badge:hover{color:#ffffff;text-decoration:none;cursor:pointer;}
.label-important,.badge-important{background-color:#b94a48;}
.label-important[href],.badge-important[href]{background-color:#953b39;}
.label-warning,.badge-warning{background-color:#f89406;}
.label-warning[href],.badge-warning[href]{background-color:#c67605;}
.label-success,.badge-success{background-color:#468847;}
.label-success[href],.badge-success[href]{background-color:#356635;}
.label-info,.badge-info{background-color:#3a87ad;}
.label-info[href],.badge-info[href]{background-color:#2d6987;}
.label-inverse,.badge-inverse{background-color:#333333;}
.label-inverse[href],.badge-inverse[href]{background-color:#1a1a1a;}
.btn .label,.btn .badge{position:relative;top:-1px;}
.btn-mini .label,.btn-mini .badge{top:0;}
form{margin:0 0 20px;}
fieldset{padding:0;margin:0;border:0;}
legend{display:block;width:100%;padding:0;margin-bottom:20px;font-size:21px;line-height:40px;color:#333333;border:0;border-bottom:1px solid #e5e5e5;}legend small{font-size:15px;color:#999999;}
label,input,button,select,textarea{font-size:14px;font-weight:normal;line-height:20px;}
input,button,select,textarea{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;}
label{display:block;margin-bottom:5px;}
select,textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="url"],input[type="search"],input[type="tel"],input[type="color"],.uneditable-input{display:inline-block;height:20px;padding:4px 6px;margin-bottom:10px;font-size:14px;line-height:20px;color:#555555;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;vertical-align:middle;}
input,textarea,.uneditable-input{width:206px;}
textarea{height:auto;}
textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="url"],input[type="search"],input[type="tel"],input[type="color"],.uneditable-input{background-color:#ffffff;border:1px solid #cccccc;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);-webkit-transition:border linear .2s, box-shadow linear .2s;-moz-transition:border linear .2s, box-shadow linear .2s;-o-transition:border linear .2s, box-shadow linear .2s;transition:border linear .2s, box-shadow linear .2s;}textarea:focus,input[type="text"]:focus,input[type="password"]:focus,input[type="datetime"]:focus,input[type="datetime-local"]:focus,input[type="date"]:focus,input[type="month"]:focus,input[type="time"]:focus,input[type="week"]:focus,input[type="number"]:focus,input[type="email"]:focus,input[type="url"]:focus,input[type="search"]:focus,input[type="tel"]:focus,input[type="color"]:focus,.uneditable-input:focus{border-color:rgba(82, 168, 236, 0.8);outline:0;outline:thin dotted \9;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(82,168,236,.6);-moz-box-shadow:inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(82,168,236,.6);box-shadow:inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(82,168,236,.6);}
input[type="radio"],input[type="checkbox"]{margin:4px 0 0;*margin-top:0;margin-top:1px \9;line-height:normal;cursor:pointer;}
input[type="file"],input[type="image"],input[type="submit"],input[type="reset"],input[type="button"],input[type="radio"],input[type="checkbox"]{width:auto;}
select,input[type="file"]{height:30px;*margin-top:4px;line-height:30px;}
select{width:220px;border:1px solid #cccccc;background-color:#ffffff;}
select[multiple],select[size]{height:auto;}
select:focus,input[type="file"]:focus,input[type="radio"]:focus,input[type="checkbox"]:focus{outline:thin dotted #333;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px;}
.uneditable-input,.uneditable-textarea{color:#999999;background-color:#fcfcfc;border-color:#cccccc;-webkit-box-shadow:inset 0 1px 2px rgba(0, 0, 0, 0.025);-moz-box-shadow:inset 0 1px 2px rgba(0, 0, 0, 0.025);box-shadow:inset 0 1px 2px rgba(0, 0, 0, 0.025);cursor:not-allowed;}
.uneditable-input{overflow:hidden;white-space:nowrap;}
.uneditable-textarea{width:auto;height:auto;}
input:-moz-placeholder,textarea:-moz-placeholder{color:#999999;}
input:-ms-input-placeholder,textarea:-ms-input-placeholder{color:#999999;}
input::-webkit-input-placeholder,textarea::-webkit-input-placeholder{color:#999999;}
.radio,.checkbox{min-height:20px;padding-left:20px;}
.radio input[type="radio"],.checkbox input[type="checkbox"]{float:left;margin-left:-20px;}
.controls>.radio:first-child,.controls>.checkbox:first-child{padding-top:5px;}
.radio.inline,.checkbox.inline{display:inline-block;padding-top:5px;margin-bottom:0;vertical-align:middle;}
.radio.inline+.radio.inline,.checkbox.inline+.checkbox.inline{margin-left:10px;}
.input-mini{width:60px;}
.input-small{width:90px;}
.input-medium{width:150px;}
.input-large{width:210px;}
.input-xlarge{width:270px;}
.input-xxlarge{width:530px;}
input[class*="span"],select[class*="span"],textarea[class*="span"],.uneditable-input[class*="span"],.row-fluid input[class*="span"],.row-fluid select[class*="span"],.row-fluid textarea[class*="span"],.row-fluid .uneditable-input[class*="span"]{float:none;margin-left:0;}
.input-append input[class*="span"],.input-append .uneditable-input[class*="span"],.input-prepend input[class*="span"],.input-prepend .uneditable-input[class*="span"],.row-fluid input[class*="span"],.row-fluid select[class*="span"],.row-fluid textarea[class*="span"],.row-fluid .uneditable-input[class*="span"],.row-fluid .input-prepend [class*="span"],.row-fluid .input-append [class*="span"]{display:inline-block;}
input,textarea,.uneditable-input{margin-left:0;}
.controls-row [class*="span"]+[class*="span"]{margin-left:20px;}
input.span12, textarea.span12, .uneditable-input.span12{width:926px;}
input.span11, textarea.span11, .uneditable-input.span11{width:846px;}
input.span10, textarea.span10, .uneditable-input.span10{width:766px;}
input.span9, textarea.span9, .uneditable-input.span9{width:686px;}
input.span8, textarea.span8, .uneditable-input.span8{width:606px;}
input.span7, textarea.span7, .uneditable-input.span7{width:526px;}
input.span6, textarea.span6, .uneditable-input.span6{width:446px;}
input.span5, textarea.span5, .uneditable-input.span5{width:366px;}
input.span4, textarea.span4, .uneditable-input.span4{width:286px;}
input.span3, textarea.span3, .uneditable-input.span3{width:206px;}
input.span2, textarea.span2, .uneditable-input.span2{width:126px;}
input.span1, textarea.span1, .uneditable-input.span1{width:46px;}
.controls-row{*zoom:1;}.controls-row:before,.controls-row:after{display:table;content:"";line-height:0;}
.controls-row:after{clear:both;}
.controls-row [class*="span"],.row-fluid .controls-row [class*="span"]{float:left;}
.controls-row .checkbox[class*="span"],.controls-row .radio[class*="span"]{padding-top:5px;}
input[disabled],select[disabled],textarea[disabled],input[readonly],select[readonly],textarea[readonly]{cursor:not-allowed;background-color:#eeeeee;}
input[type="radio"][disabled],input[type="checkbox"][disabled],input[type="radio"][readonly],input[type="checkbox"][readonly]{background-color:transparent;}
.control-group.warning>label,.control-group.warning .help-block,.control-group.warning .help-inline{color:#c09853;}
.control-group.warning .checkbox,.control-group.warning .radio,.control-group.warning input,.control-group.warning select,.control-group.warning textarea{color:#c09853;}
.control-group.warning input,.control-group.warning select,.control-group.warning textarea{border-color:#c09853;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);}.control-group.warning input:focus,.control-group.warning select:focus,.control-group.warning textarea:focus{border-color:#a47e3c;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #dbc59e;-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #dbc59e;box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #dbc59e;}
.control-group.warning .input-prepend .add-on,.control-group.warning .input-append .add-on{color:#c09853;background-color:#fcf8e3;border-color:#c09853;}
.control-group.error>label,.control-group.error .help-block,.control-group.error .help-inline{color:#b94a48;}
.control-group.error .checkbox,.control-group.error .radio,.control-group.error input,.control-group.error select,.control-group.error textarea{color:#b94a48;}
.control-group.error input,.control-group.error select,.control-group.error textarea{border-color:#b94a48;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);}.control-group.error input:focus,.control-group.error select:focus,.control-group.error textarea:focus{border-color:#953b39;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #d59392;-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #d59392;box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #d59392;}
.control-group.error .input-prepend .add-on,.control-group.error .input-append .add-on{color:#b94a48;background-color:#f2dede;border-color:#b94a48;}
.control-group.success>label,.control-group.success .help-block,.control-group.success .help-inline{color:#468847;}
.control-group.success .checkbox,.control-group.success .radio,.control-group.success input,.control-group.success select,.control-group.success textarea{color:#468847;}
.control-group.success input,.control-group.success select,.control-group.success textarea{border-color:#468847;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);}.control-group.success input:focus,.control-group.success select:focus,.control-group.success textarea:focus{border-color:#356635;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #7aba7b;-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #7aba7b;box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #7aba7b;}
.control-group.success .input-prepend .add-on,.control-group.success .input-append .add-on{color:#468847;background-color:#dff0d8;border-color:#468847;}
.control-group.info>label,.control-group.info .help-block,.control-group.info .help-inline{color:#3a87ad;}
.control-group.info .checkbox,.control-group.info .radio,.control-group.info input,.control-group.info select,.control-group.info textarea{color:#3a87ad;}
.control-group.info input,.control-group.info select,.control-group.info textarea{border-color:#3a87ad;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);}.control-group.info input:focus,.control-group.info select:focus,.control-group.info textarea:focus{border-color:#2d6987;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #7ab5d3;-moz-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #7ab5d3;box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 6px #7ab5d3;}
.control-group.info .input-prepend .add-on,.control-group.info .input-append .add-on{color:#3a87ad;background-color:#d9edf7;border-color:#3a87ad;}
input:focus:required:invalid,textarea:focus:required:invalid,select:focus:required:invalid{color:#b94a48;border-color:#ee5f5b;}input:focus:required:invalid:focus,textarea:focus:required:invalid:focus,select:focus:required:invalid:focus{border-color:#e9322d;-webkit-box-shadow:0 0 6px #f8b9b7;-moz-box-shadow:0 0 6px #f8b9b7;box-shadow:0 0 6px #f8b9b7;}
.form-actions{padding:19px 20px 20px;margin-top:20px;margin-bottom:20px;background-color:#f5f5f5;border-top:1px solid #e5e5e5;*zoom:1;}.form-actions:before,.form-actions:after{display:table;content:"";line-height:0;}
.form-actions:after{clear:both;}
.help-block,.help-inline{color:#595959;}
.help-block{display:block;margin-bottom:10px;}
.help-inline{display:inline-block;*display:inline;*zoom:1;vertical-align:middle;padding-left:5px;}
.input-append,.input-prepend{margin-bottom:5px;font-size:0;white-space:nowrap;}.input-append input,.input-prepend input,.input-append select,.input-prepend select,.input-append .uneditable-input,.input-prepend .uneditable-input,.input-append .dropdown-menu,.input-prepend .dropdown-menu{font-size:14px;}
.input-append input,.input-prepend input,.input-append select,.input-prepend select,.input-append .uneditable-input,.input-prepend .uneditable-input{position:relative;margin-bottom:0;*margin-left:0;vertical-align:top;-webkit-border-radius:0 4px 4px 0;-moz-border-radius:0 4px 4px 0;border-radius:0 4px 4px 0;}.input-append input:focus,.input-prepend input:focus,.input-append select:focus,.input-prepend select:focus,.input-append .uneditable-input:focus,.input-prepend .uneditable-input:focus{z-index:2;}
.input-append .add-on,.input-prepend .add-on{display:inline-block;width:auto;height:20px;min-width:16px;padding:4px 5px;font-size:14px;font-weight:normal;line-height:20px;text-align:center;text-shadow:0 1px 0 #ffffff;background-color:#eeeeee;border:1px solid #ccc;}
.input-append .add-on,.input-prepend .add-on,.input-append .btn,.input-prepend .btn{vertical-align:top;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;}
.input-append .active,.input-prepend .active{background-color:#a9dba9;border-color:#46a546;}
.input-prepend .add-on,.input-prepend .btn{margin-right:-1px;}
.input-prepend .add-on:first-child,.input-prepend .btn:first-child{-webkit-border-radius:4px 0 0 4px;-moz-border-radius:4px 0 0 4px;border-radius:4px 0 0 4px;}
.input-append input,.input-append select,.input-append .uneditable-input{-webkit-border-radius:4px 0 0 4px;-moz-border-radius:4px 0 0 4px;border-radius:4px 0 0 4px;}.input-append input+.btn-group .btn,.input-append select+.btn-group .btn,.input-append .uneditable-input+.btn-group .btn{-webkit-border-radius:0 4px 4px 0;-moz-border-radius:0 4px 4px 0;border-radius:0 4px 4px 0;}
.input-append .add-on,.input-append .btn,.input-append .btn-group{margin-left:-1px;}
.input-append .add-on:last-child,.input-append .btn:last-child{-webkit-border-radius:0 4px 4px 0;-moz-border-radius:0 4px 4px 0;border-radius:0 4px 4px 0;}
.input-prepend.input-append input,.input-prepend.input-append select,.input-prepend.input-append .uneditable-input{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;}.input-prepend.input-append input+.btn-group .btn,.input-prepend.input-append select+.btn-group .btn,.input-prepend.input-append .uneditable-input+.btn-group .btn{-webkit-border-radius:0 4px 4px 0;-moz-border-radius:0 4px 4px 0;border-radius:0 4px 4px 0;}
.input-prepend.input-append .add-on:first-child,.input-prepend.input-append .btn:first-child{margin-right:-1px;-webkit-border-radius:4px 0 0 4px;-moz-border-radius:4px 0 0 4px;border-radius:4px 0 0 4px;}
.input-prepend.input-append .add-on:last-child,.input-prepend.input-append .btn:last-child{margin-left:-1px;-webkit-border-radius:0 4px 4px 0;-moz-border-radius:0 4px 4px 0;border-radius:0 4px 4px 0;}
.input-prepend.input-append .btn-group:first-child{margin-left:0;}
input.search-query{padding-right:14px;padding-right:4px \9;padding-left:14px;padding-left:4px \9;margin-bottom:0;-webkit-border-radius:15px;-moz-border-radius:15px;border-radius:15px;}
.form-search .input-append .search-query,.form-search .input-prepend .search-query{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;}
.form-search .input-append .search-query{-webkit-border-radius:14px 0 0 14px;-moz-border-radius:14px 0 0 14px;border-radius:14px 0 0 14px;}
.form-search .input-append .btn{-webkit-border-radius:0 14px 14px 0;-moz-border-radius:0 14px 14px 0;border-radius:0 14px 14px 0;}
.form-search .input-prepend .search-query{-webkit-border-radius:0 14px 14px 0;-moz-border-radius:0 14px 14px 0;border-radius:0 14px 14px 0;}
.form-search .input-prepend .btn{-webkit-border-radius:14px 0 0 14px;-moz-border-radius:14px 0 0 14px;border-radius:14px 0 0 14px;}
.form-search input,.form-inline input,.form-horizontal input,.form-search textarea,.form-inline textarea,.form-horizontal textarea,.form-search select,.form-inline select,.form-horizontal select,.form-search .help-inline,.form-inline .help-inline,.form-horizontal .help-inline,.form-search .uneditable-input,.form-inline .uneditable-input,.form-horizontal .uneditable-input,.form-search .input-prepend,.form-inline .input-prepend,.form-horizontal .input-prepend,.form-search .input-append,.form-inline .input-append,.form-horizontal .input-append{display:inline-block;*display:inline;*zoom:1;margin-bottom:0;vertical-align:middle;}
.form-search .hide,.form-inline .hide,.form-horizontal .hide{display:none;}
.form-search label,.form-inline label,.form-search .btn-group,.form-inline .btn-group{display:inline-block;}
.form-search .input-append,.form-inline .input-append,.form-search .input-prepend,.form-inline .input-prepend{margin-bottom:0;}
.form-search .radio,.form-search .checkbox,.form-inline .radio,.form-inline .checkbox{padding-left:0;margin-bottom:0;vertical-align:middle;}
.form-search .radio input[type="radio"],.form-search .checkbox input[type="checkbox"],.form-inline .radio input[type="radio"],.form-inline .checkbox input[type="checkbox"]{float:left;margin-right:3px;margin-left:0;}
.control-group{margin-bottom:10px;}
legend+.control-group{margin-top:20px;-webkit-margin-top-collapse:separate;}
.form-horizontal .control-group{margin-bottom:20px;*zoom:1;}.form-horizontal .control-group:before,.form-horizontal .control-group:after{display:table;content:"";line-height:0;}
.form-horizontal .control-group:after{clear:both;}
.form-horizontal .control-label{float:left;width:160px;padding-top:5px;text-align:right;}
.form-horizontal .controls{*display:inline-block;*padding-left:20px;margin-left:180px;*margin-left:0;}.form-horizontal .controls:first-child{*padding-left:180px;}
.form-horizontal .help-block{margin-bottom:0;}
.form-horizontal input+.help-block,.form-horizontal select+.help-block,.form-horizontal textarea+.help-block{margin-top:10px;}
.form-horizontal .form-actions{padding-left:180px;}
.btn{display:inline-block;*display:inline;*zoom:1;padding:4px 12px;margin-bottom:0;font-size:14px;line-height:20px;*line-height:20px;text-align:center;vertical-align:middle;cursor:pointer;color:#333333;text-shadow:0 1px 1px rgba(255, 255, 255, 0.75);background-color:#f5f5f5;background-image:-moz-linear-gradient(top, #ffffff, #e6e6e6);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));background-image:-webkit-linear-gradient(top, #ffffff, #e6e6e6);background-image:-o-linear-gradient(top, #ffffff, #e6e6e6);background-image:linear-gradient(to bottom, #ffffff, #e6e6e6);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);border-color:#e6e6e6 #e6e6e6 #bfbfbf;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#e6e6e6;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);border:1px solid #bbbbbb;*border:0;border-bottom-color:#a2a2a2;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;*margin-left:.3em;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);-moz-box-shadow:inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);box-shadow:inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);}.btn:hover,.btn:active,.btn.active,.btn.disabled,.btn[disabled]{color:#333333;background-color:#e6e6e6;*background-color:#d9d9d9;}
.btn:active,.btn.active{background-color:#cccccc \9;}
.btn:first-child{*margin-left:0;}
.btn:hover{color:#333333;text-decoration:none;background-color:#e6e6e6;*background-color:#d9d9d9;background-position:0 -15px;-webkit-transition:background-position 0.1s linear;-moz-transition:background-position 0.1s linear;-o-transition:background-position 0.1s linear;transition:background-position 0.1s linear;}
.btn:focus{outline:thin dotted #333;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px;}
.btn.active,.btn:active{background-color:#e6e6e6;background-color:#d9d9d9 \9;background-image:none;outline:0;-webkit-box-shadow:inset 0 2px 4px rgba(0,0,0,.15), 0 1px 2px rgba(0,0,0,.05);-moz-box-shadow:inset 0 2px 4px rgba(0,0,0,.15), 0 1px 2px rgba(0,0,0,.05);box-shadow:inset 0 2px 4px rgba(0,0,0,.15), 0 1px 2px rgba(0,0,0,.05);}
.btn.disabled,.btn[disabled]{cursor:default;background-color:#e6e6e6;background-image:none;opacity:0.65;filter:alpha(opacity=65);-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;}
.btn-large{padding:11px 19px;font-size:17.5px;-webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;}
.btn-large [class^="icon-"],.btn-large [class*=" icon-"]{margin-top:2px;}
.btn-small{padding:2px 10px;font-size:11.9px;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;}
.btn-small [class^="icon-"],.btn-small [class*=" icon-"]{margin-top:0;}
.btn-mini{padding:1px 6px;font-size:10.5px;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;}
.btn-block{display:block;width:100%;padding-left:0;padding-right:0;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}
.btn-block+.btn-block{margin-top:5px;}
input[type="submit"].btn-block,input[type="reset"].btn-block,input[type="button"].btn-block{width:100%;}
.btn-primary.active,.btn-warning.active,.btn-danger.active,.btn-success.active,.btn-info.active,.btn-inverse.active{color:rgba(255, 255, 255, 0.75);}
.btn{border-color:#c5c5c5;border-color:rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.25);}
.btn-primary{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#006dcc;background-image:-moz-linear-gradient(top, #0088cc, #0044cc);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));background-image:-webkit-linear-gradient(top, #0088cc, #0044cc);background-image:-o-linear-gradient(top, #0088cc, #0044cc);background-image:linear-gradient(to bottom, #0088cc, #0044cc);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0044cc', GradientType=0);border-color:#0044cc #0044cc #002a80;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#0044cc;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-primary:hover,.btn-primary:active,.btn-primary.active,.btn-primary.disabled,.btn-primary[disabled]{color:#ffffff;background-color:#0044cc;*background-color:#003bb3;}
.btn-primary:active,.btn-primary.active{background-color:#003399 \9;}
.btn-warning{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#faa732;background-image:-moz-linear-gradient(top, #fbb450, #f89406);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#fbb450), to(#f89406));background-image:-webkit-linear-gradient(top, #fbb450, #f89406);background-image:-o-linear-gradient(top, #fbb450, #f89406);background-image:linear-gradient(to bottom, #fbb450, #f89406);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffbb450', endColorstr='#fff89406', GradientType=0);border-color:#f89406 #f89406 #ad6704;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#f89406;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-warning:hover,.btn-warning:active,.btn-warning.active,.btn-warning.disabled,.btn-warning[disabled]{color:#ffffff;background-color:#f89406;*background-color:#df8505;}
.btn-warning:active,.btn-warning.active{background-color:#c67605 \9;}
.btn-danger{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#da4f49;background-image:-moz-linear-gradient(top, #ee5f5b, #bd362f);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#bd362f));background-image:-webkit-linear-gradient(top, #ee5f5b, #bd362f);background-image:-o-linear-gradient(top, #ee5f5b, #bd362f);background-image:linear-gradient(to bottom, #ee5f5b, #bd362f);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b', endColorstr='#ffbd362f', GradientType=0);border-color:#bd362f #bd362f #802420;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#bd362f;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-danger:hover,.btn-danger:active,.btn-danger.active,.btn-danger.disabled,.btn-danger[disabled]{color:#ffffff;background-color:#bd362f;*background-color:#a9302a;}
.btn-danger:active,.btn-danger.active{background-color:#942a25 \9;}
.btn-success{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#5bb75b;background-image:-moz-linear-gradient(top, #62c462, #51a351);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#51a351));background-image:-webkit-linear-gradient(top, #62c462, #51a351);background-image:-o-linear-gradient(top, #62c462, #51a351);background-image:linear-gradient(to bottom, #62c462, #51a351);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff62c462', endColorstr='#ff51a351', GradientType=0);border-color:#51a351 #51a351 #387038;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#51a351;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-success:hover,.btn-success:active,.btn-success.active,.btn-success.disabled,.btn-success[disabled]{color:#ffffff;background-color:#51a351;*background-color:#499249;}
.btn-success:active,.btn-success.active{background-color:#408140 \9;}
.btn-info{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#49afcd;background-image:-moz-linear-gradient(top, #5bc0de, #2f96b4);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#5bc0de), to(#2f96b4));background-image:-webkit-linear-gradient(top, #5bc0de, #2f96b4);background-image:-o-linear-gradient(top, #5bc0de, #2f96b4);background-image:linear-gradient(to bottom, #5bc0de, #2f96b4);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5bc0de', endColorstr='#ff2f96b4', GradientType=0);border-color:#2f96b4 #2f96b4 #1f6377;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#2f96b4;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-info:hover,.btn-info:active,.btn-info.active,.btn-info.disabled,.btn-info[disabled]{color:#ffffff;background-color:#2f96b4;*background-color:#2a85a0;}
.btn-info:active,.btn-info.active{background-color:#24748c \9;}
.btn-inverse{color:#ffffff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#363636;background-image:-moz-linear-gradient(top, #444444, #222222);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#444444), to(#222222));background-image:-webkit-linear-gradient(top, #444444, #222222);background-image:-o-linear-gradient(top, #444444, #222222);background-image:linear-gradient(to bottom, #444444, #222222);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff444444', endColorstr='#ff222222', GradientType=0);border-color:#222222 #222222 #000000;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#222222;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-inverse:hover,.btn-inverse:active,.btn-inverse.active,.btn-inverse.disabled,.btn-inverse[disabled]{color:#ffffff;background-color:#222222;*background-color:#151515;}
.btn-inverse:active,.btn-inverse.active{background-color:#080808 \9;}
button.btn,input[type="submit"].btn{*padding-top:3px;*padding-bottom:3px;}button.btn::-moz-focus-inner,input[type="submit"].btn::-moz-focus-inner{padding:0;border:0;}
button.btn.btn-large,input[type="submit"].btn.btn-large{*padding-top:7px;*padding-bottom:7px;}
button.btn.btn-small,input[type="submit"].btn.btn-small{*padding-top:3px;*padding-bottom:3px;}
button.btn.btn-mini,input[type="submit"].btn.btn-mini{*padding-top:1px;*padding-bottom:1px;}
.btn-link,.btn-link:active,.btn-link[disabled]{background-color:transparent;background-image:none;-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;}
.btn-link{border-color:transparent;cursor:pointer;color:#0088cc;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;}
.btn-link:hover{color:#005580;text-decoration:underline;background-color:transparent;}
.btn-link[disabled]:hover{color:#333333;text-decoration:none;}
[class^="icon-"],[class*=" icon-"]{display:inline-block;width:14px;height:14px;*margin-right:.3em;line-height:14px;vertical-align:text-top;background-image:url("../img/glyphicons-halflings.png");background-position:14px 14px;background-repeat:no-repeat;margin-top:1px;}
.icon-white,.nav-pills>.active>a>[class^="icon-"],.nav-pills>.active>a>[class*=" icon-"],.nav-list>.active>a>[class^="icon-"],.nav-list>.active>a>[class*=" icon-"],.navbar-inverse .nav>.active>a>[class^="icon-"],.navbar-inverse .nav>.active>a>[class*=" icon-"],.dropdown-menu>li>a:hover>[class^="icon-"],.dropdown-menu>li>a:hover>[class*=" icon-"],.dropdown-menu>.active>a>[class^="icon-"],.dropdown-menu>.active>a>[class*=" icon-"],.dropdown-submenu:hover>a>[class^="icon-"],.dropdown-submenu:hover>a>[class*=" icon-"]{background-image:url("../img/glyphicons-halflings-white.png");}
.icon-glass{background-position:0 0;}
.icon-music{background-position:-24px 0;}
.icon-search{background-position:-48px 0;}
.icon-envelope{background-position:-72px 0;}
.icon-heart{background-position:-96px 0;}
.icon-star{background-position:-120px 0;}
.icon-star-empty{background-position:-144px 0;}
.icon-user{background-position:-168px 0;}
.icon-film{background-position:-192px 0;}
.icon-th-large{background-position:-216px 0;}
.icon-th{background-position:-240px 0;}
.icon-th-list{background-position:-264px 0;}
.icon-ok{background-position:-288px 0;}
.icon-remove{background-position:-312px 0;}
.icon-zoom-in{background-position:-336px 0;}
.icon-zoom-out{background-position:-360px 0;}
.icon-off{background-position:-384px 0;}
.icon-signal{background-position:-408px 0;}
.icon-cog{background-position:-432px 0;}
.icon-trash{background-position:-456px 0;}
.icon-home{background-position:0 -24px;}
.icon-file{background-position:-24px -24px;}
.icon-time{background-position:-48px -24px;}
.icon-road{background-position:-72px -24px;}
.icon-download-alt{background-position:-96px -24px;}
.icon-download{background-position:-120px -24px;}
.icon-upload{background-position:-144px -24px;}
.icon-inbox{background-position:-168px -24px;}
.icon-play-circle{background-position:-192px -24px;}
.icon-repeat{background-position:-216px -24px;}
.icon-refresh{background-position:-240px -24px;}
.icon-list-alt{background-position:-264px -24px;}
.icon-lock{background-position:-287px -24px;}
.icon-flag{background-position:-312px -24px;}
.icon-headphones{background-position:-336px -24px;}
.icon-volume-off{background-position:-360px -24px;}
.icon-volume-down{background-position:-384px -24px;}
.icon-volume-up{background-position:-408px -24px;}
.icon-qrcode{background-position:-432px -24px;}
.icon-barcode{background-position:-456px -24px;}
.icon-tag{background-position:0 -48px;}
.icon-tags{background-position:-25px -48px;}
.icon-book{background-position:-48px -48px;}
.icon-bookmark{background-position:-72px -48px;}
.icon-print{background-position:-96px -48px;}
.icon-camera{background-position:-120px -48px;}
.icon-font{background-position:-144px -48px;}
.icon-bold{background-position:-167px -48px;}
.icon-italic{background-position:-192px -48px;}
.icon-text-height{background-position:-216px -48px;}
.icon-text-width{background-position:-240px -48px;}
.icon-align-left{background-position:-264px -48px;}
.icon-align-center{background-position:-288px -48px;}
.icon-align-right{background-position:-312px -48px;}
.icon-align-justify{background-position:-336px -48px;}
.icon-list{background-position:-360px -48px;}
.icon-indent-left{background-position:-384px -48px;}
.icon-indent-right{background-position:-408px -48px;}
.icon-facetime-video{background-position:-432px -48px;}
.icon-picture{background-position:-456px -48px;}
.icon-pencil{background-position:0 -72px;}
.icon-map-marker{background-position:-24px -72px;}
.icon-adjust{background-position:-48px -72px;}
.icon-tint{background-position:-72px -72px;}
.icon-edit{background-position:-96px -72px;}
.icon-share{background-position:-120px -72px;}
.icon-check{background-position:-144px -72px;}
.icon-move{background-position:-168px -72px;}
.icon-step-backward{background-position:-192px -72px;}
.icon-fast-backward{background-position:-216px -72px;}
.icon-backward{background-position:-240px -72px;}
.icon-play{background-position:-264px -72px;}
.icon-pause{background-position:-288px -72px;}
.icon-stop{background-position:-312px -72px;}
.icon-forward{background-position:-336px -72px;}
.icon-fast-forward{background-position:-360px -72px;}
.icon-step-forward{background-position:-384px -72px;}
.icon-eject{background-position:-408px -72px;}
.icon-chevron-left{background-position:-432px -72px;}
.icon-chevron-right{background-position:-456px -72px;}
.icon-plus-sign{background-position:0 -96px;}
.icon-minus-sign{background-position:-24px -96px;}
.icon-remove-sign{background-position:-48px -96px;}
.icon-ok-sign{background-position:-72px -96px;}
.icon-question-sign{background-position:-96px -96px;}
.icon-info-sign{background-position:-120px -96px;}
.icon-screenshot{background-position:-144px -96px;}
.icon-remove-circle{background-position:-168px -96px;}
.icon-ok-circle{background-position:-192px -96px;}
.icon-ban-circle{background-position:-216px -96px;}
.icon-arrow-left{background-position:-240px -96px;}
.icon-arrow-right{background-position:-264px -96px;}
.icon-arrow-up{background-position:-289px -96px;}
.icon-arrow-down{background-position:-312px -96px;}
.icon-share-alt{background-position:-336px -96px;}
.icon-resize-full{background-position:-360px -96px;}
.icon-resize-small{background-position:-384px -96px;}
.icon-plus{background-position:-408px -96px;}
.icon-minus{background-position:-433px -96px;}
.icon-asterisk{background-position:-456px -96px;}
.icon-exclamation-sign{background-position:0 -120px;}
.icon-gift{background-position:-24px -120px;}
.icon-leaf{background-position:-48px -120px;}
.icon-fire{background-position:-72px -120px;}
.icon-eye-open{background-position:-96px -120px;}
.icon-eye-close{background-position:-120px -120px;}
.icon-warning-sign{background-position:-144px -120px;}
.icon-plane{background-position:-168px -120px;}
.icon-calendar{background-position:-192px -120px;}
.icon-random{background-position:-216px -120px;width:16px;}
.icon-comment{background-position:-240px -120px;}
.icon-magnet{background-position:-264px -120px;}
.icon-chevron-up{background-position:-288px -120px;}
.icon-chevron-down{background-position:-313px -119px;}
.icon-retweet{background-position:-336px -120px;}
.icon-shopping-cart{background-position:-360px -120px;}
.icon-folder-close{background-position:-384px -120px;}
.icon-folder-open{background-position:-408px -120px;width:16px;}
.icon-resize-vertical{background-position:-432px -119px;}
.icon-resize-horizontal{background-position:-456px -118px;}
.icon-hdd{background-position:0 -144px;}
.icon-bullhorn{background-position:-24px -144px;}
.icon-bell{background-position:-48px -144px;}
.icon-certificate{background-position:-72px -144px;}
.icon-thumbs-up{background-position:-96px -144px;}
.icon-thumbs-down{background-position:-120px -144px;}
.icon-hand-right{background-position:-144px -144px;}
.icon-hand-left{background-position:-168px -144px;}
.icon-hand-up{background-position:-192px -144px;}
.icon-hand-down{background-position:-216px -144px;}
.icon-circle-arrow-right{background-position:-240px -144px;}
.icon-circle-arrow-left{background-position:-264px -144px;}
.icon-circle-arrow-up{background-position:-288px -144px;}
.icon-circle-arrow-down{background-position:-312px -144px;}
.icon-globe{background-position:-336px -144px;}
.icon-wrench{background-position:-360px -144px;}
.icon-tasks{background-position:-384px -144px;}
.icon-filter{background-position:-408px -144px;}
.icon-briefcase{background-position:-432px -144px;}
.icon-fullscreen{background-position:-456px -144px;}
</style>
*/

?>
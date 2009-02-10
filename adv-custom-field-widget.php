<?php
/*
PLUGIN NAME: Advanced Custom Field Widget
PLUGIN URI: http://athena.outer-reaches.com/wp/index.php/projects/advanced-custom-field-widget
DESCRIPTION: Displays the values of specified <a href="http://codex.wordpress.org/Using_Custom_Fields">custom field</a> keys, allowing post- and page-specific meta content in your sidebar. This plugin started life as a plaintxt.org experiment for WordPress by Scott Wallick, but I needed (or wanted) it to do more, so I've created this version which has more functionality than the original.
AUTHOR: Christina Louise Warne
AUTHOR URI: http://athena.outer-reaches.com/
VERSION: 0.3

------------------------------------------------------------------------------------------------------------
Version History:-

Version Date      Author                 Description
======= ========= ====================== ======================================
0.3     23-Dec-08 Christina Louise Warne ADDED - Add custom field '<KEY>-linkto' and specify a page ID to
                                           have the current page load the specified cfield content from the
										   specified page.  This takes priority over acfw-linkto.
------- --------- ---------------------- --------------------------------------										 
0.2     05-Dec-08 Christina Louise Warne FIX - Widget was not displaying content for pages (only posts)
										 ADDED - Add custom field 'acfw-linkto' and specify a page ID to
										   have the current page load it's fields from the specified page
------- --------- ---------------------- --------------------------------------										 
0.1     Nov 2008  Scott Allan Wallick    Original Version
                  with heavy mods by
				  Christina Louise Warne
------------------------------------------------------------------------------------------------------------

ADVANCED CUSTOM FIELD WIDGET
by Christina Louise Warne (aka AthenaOfDelphi), http://athena.outer-reaches.com/
from The Outer Reaches, http://www.outer-reaches.com/

Based on the original CUSTOM FIELD WIDGET,  by SCOTT ALLAN WALLICK, http://scottwallick.com/
from PLAINTXT.ORG, http://www.plaintxt.org/.

ADVANCED CUSTOM FIELD WIDGET is free software: you can redistribute it
and/or modify it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 3 of
the License, or (at your option) any later version.

ADVANCED CUSTOM FIELD WIDGET is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty
of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for details.

You should have received a copy of the GNU General Public License
along with ADVANCED CUSTOM FIELD WIDGET.
If not, see www.gnu.org/licenses/

*/

// Function for the Advanced Custom Field Widget
function wp_widget_adv_custom_field( $args, $widget_args = 1 ) {
	// Get hold of the global WP database object
	global $wpdb;
	
	// Let's begin our widget.
	extract( $args, EXTR_SKIP );
	// Our widgets are stored with a numeric ID, process them as such
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	// We'll need to get our widget data by offsetting for the default widget
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	// Offset for this widget
	extract( $widget_args, EXTR_SKIP );
	// We'll get the options and then specific options for our widget further below
	$options = get_option('widget_adv_custom_field');
	// If we don't have the widget by its ID, then what are we doing?
	if ( !isset($options[$number]) )
		return;
	
	// We'll use the standard filters from widgets.php for consistency
	$ckey  = $options[$number]['key'];
	$skey  = $options[$number]['skey'];
		
	$title = apply_filters( 'widget_title', $options[$number]['title'] );

	$text  = apply_filters( 'widget_text', $options[$number]['text'] );
	$pretext = apply_filters( 'widget_text', $options[$number]['pretext'] );
	$posttext = apply_filters( 'widget_text', $options[$number]['posttext'] );
	$cvalue = "";
	$fixedtext1 = "";
	$fixedtext2 = "";
	
	// Let's set a global to retrieve the post ID
	global $post;
			
	// Are we on a single post page (i.e. a blog entry or a page) or not?
	if (is_single()||is_page()) {
		// Look first for say 'externallinks-linkto' allowing us to link up a single
		// field
        $linkto = get_post_meta( $post->ID, $ckey.'-linkto',true);
		
		if (empty($linkto)) {
		    // If <KEY>-linkto is empty, look for the general acfw-linkto custom field
			$linkto = get_post_meta( $post->ID, 'acfw-linkto',true);	
		}
		
		// If we have a source page ID (from the linkto fields) then set the data source ID to
		// that, otherwise set the date source ID to the current post ID
		if (!empty($linkto))
		{
			$srcpost=$linkto;
		}
		else
		{
			$srcpost=$post->ID;
		}
		
		// Load the data from the target page
		$cvalue=get_post_meta( $srcpost, $ckey, true );
			
	    // Check to see if we read anything, if we didn't
		if (empty($cvalue)) {
			// Try loading the main content from the secondary field
			$cvalue = get_post_meta( $post->ID, $skey, true );
		}
	
		// Read the randomisation settings from the configuration
		$dorandom=!empty($options[$number]['dorandomsingle']);
		
		// We are on a single page, and we have some content, so we need to cancel the randomisation
		if (!empty($cvalue)) {
			unset($dorandom);
		}
	} else {
		// We are on a multi post page, so get the randomisation setting and clean $cvalue
		$dorandom=!empty($options[$number]['dorandomother']);
		$cvalue="";
	}
			
	// Load our fixedtext1 and 2 with their 'ALWAYS' values
	$fixedtext1=$options[$number]['fixedtext1a'];
	$fixedtext2=$options[$number]['fixedtext2a'];
		
	// If we are loading random content
	if ($dorandom) {
		// Randomise our main content
		$randomlist=$wpdb->get_results(
			"SELECT
				m.meta_id,
				m.meta_value 
			FROM
				$wpdb->postmeta m,
				$wpdb->posts p
			WHERE
				(p.id=m.post_id) and
				(p.post_status='publish') and 
				(m.meta_key='$ckey')
			ORDER BY rand()
			LIMIT 1");
			
		if ($randomlist) {
			foreach ($randomlist as $metarec) {
					$cvalue=$metarec->meta_value;
			}
		}
			
		if (!empty($cvalue)) {
			// We have some main content, so load our random fixed text if we haven't already been loaded with the 'ALWAYS' option
			if (empty($fixedtext1)) {
				$fixedtext1=$options[$number]['fixedtext1r'];
			}
			if (empty($fixedtext2)) {
				$fixedtext2=$options[$number]['fixedtext2r'];
			}
		}
	}
	else
	{	
		if (empty($cvalue)) {
			// Load our 'no main content' fixed text items if they aren't loaded with the 'ALWAYS' options
			if (empty($fixedtext1)) {
				$fixedtext1=$options[$number]['fixedtext1n'];
			}
			if (empty($fixedtext2)) {
				$fixedtext2=$options[$number]['fixedtext2n'];
			}
		} else {
			// Load our 'main content' fixed text items if they aren't loaded with the 'ALWAYS' options
			if (empty($fixedtext1)) {
				$fixedtext1=$options[$number]['fixedtext1m'];
			}
			if (empty($fixedtext2)) {
				$fixedtext2=$options[$number]['fixedtext2m'];
			}
		}
	}
		
	// Apply the widget text filters to our fixed text fields (if they are present)
	if (!empty($fixedtext1)) {
		$fixedtext1 = apply_filters( 'widget_text', $fixedtext1 );
	}
	if (!empty($fixedtext2)) {
		$fixedtext2 = apply_filters( 'widget_text', $fixedtext2 );
	}
		
	if ( !empty($cvalue) || !empty($fixedtext1) || !empty($fixedtext2) ) {
		// Yes? Then let's make a widget. Open it.
		echo $before_widget;
		// Our widget title field is optional; if we have some, show it
		if ( $title ) {
			echo "\n$before_title $title $after_title";
		}
		
		// We have some fixed text, so show it
		if ( $fixedtext1 ) {
			echo $fixedtext1;
		}
		
		// We have some main content, so show it and the other related items
		if ( $cvalue ) {
			// Our widget text field is optional; if we have some, show it
			if ( $text ) {
				echo "\n<div class='textwidget'>\n$text\n</div>\n";
			}
		
			// If we have pretext, show it
			if ( $pretext ) {
				echo $pretext;
			}
			
			// Let's apply our filters to the custom field value
			$cvalue = apply_filters( 'adv_custom_field_value', $cvalue );
			// Echo the matching custom field value, filtered nicely
			echo "\n<div class='advcustomvalue'>\n$cvalue\n</div>\n";
		
			if ( $posttext ) {
				echo $posttext;
			}
		}
			
		// We have some fixed text, so show it
		if ( $fixedtext2 ) {
			echo $fixedtext2;
		}
			
		// Close our widget.
		echo $after_widget;
	}
	// And we're finished with the actual widget
}

// Function for the Advanced Custom Field Widget options panels
function wp_widget_adv_custom_field_control($widget_args) {
	// Establishes what widgets are registered, i.e., in use
	global $wp_registered_widgets;
	// We shouldn't update, i.e., process $_POST, if we haven't updated
	static $updated = false;
	// Our widgets are stored with a numeric ID, process them as such
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	// We can process the data by numeric ID, offsetting for the '1' default
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	// Complete the offset with the widget data
	extract( $widget_args, EXTR_SKIP );
	// Get our widget options from the databse
	$options = get_option('widget_adv_custom_field');
	// If our array isn't empty, process the options as an array
	if ( !is_array($options) )
		$options = array();
	// If we haven't updated (a global variable) and there's no $_POST data, no need to run this
	if ( !$updated && !empty($_POST['sidebar']) ) {
		// If this is $_POST data submitted for a sidebar
		$sidebar = (string) $_POST['sidebar'];
		// Let's konw which sidebar we're dealing with so we know if that sidebar has our widget
		$sidebars_widgets = wp_get_sidebars_widgets();
		// Now we'll find its contents
		if ( isset($sidebars_widgets[$sidebar]) ) {
			$this_sidebar =& $sidebars_widgets[$sidebar];
		} else {
			$this_sidebar = array();
		}
		// We must store each widget by ID in the sidebar where it was saved
		foreach ( $this_sidebar as $_widget_id ) {
			// Process options only if from a Widgets submenu $_POST
			if ( 'wp_widget_adv_custom_field' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				// Set the array for the widget ID/options
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				// If we have submitted empty data, don't store it in an array.
				if ( !in_array( "adv-custom-field-$widget_number", $_POST['widget-id'] ) )
					unset($options[$widget_number]);
			}
		}
		// If we are returning data via $_POST for updated widget options, save for each widget by widget ID
		foreach ( (array) $_POST['widget-adv-custom-field'] as $widget_number => $widget_adv_custom_field ) {
			// If the $_POST data has values for our widget, we'll save them
			if ( !isset($widget_adv_custom_field['key']) && isset($options[$widget_number]) )
				continue;
			// Create variables from $_POST data to save as array below
			$key   = strip_tags(stripslashes($widget_adv_custom_field['key']));
			$skey  = strip_tags(stripslashes($widget_adv_custom_field['skey']));
			$title = strip_tags(stripslashes($widget_adv_custom_field['title']));
			// For the optional text, let's carefully process submitted data
			if ( current_user_can('unfiltered_html') ) {
				$text = stripslashes($widget_adv_custom_field['text']);
				$pretext = stripslashes($widget_adv_custom_field['pretext']);
				$posttext = stripslashes($widget_adv_custom_field['posttext']);
				
				$fixedtext1r = stripslashes($widget_adv_custom_field['fixedtext1r']);
				$fixedtext1m = stripslashes($widget_adv_custom_field['fixedtext1m']);
				$fixedtext1n = stripslashes($widget_adv_custom_field['fixedtext1n']);
				$fixedtext1a = stripslashes($widget_adv_custom_field['fixedtext1a']);
				$fixedtext2r = stripslashes($widget_adv_custom_field['fixedtext2r']);
				$fixedtext2m = stripslashes($widget_adv_custom_field['fixedtext2m']);
				$fixedtext2n = stripslashes($widget_adv_custom_field['fixedtext2n']);
				$fixedtext2a = stripslashes($widget_adv_custom_field['fixedtext2a']);
				
				$dorandomsingle = stripslashes($widget_adv_custom_field['dorandomsingle']);
				$dorandomother  = stripslashes($widget_adv_custom_field['dorandomother']);
			} else {
				$text = stripslashes(wp_filter_post_kses($widget_adv_custom_field['text']));
				$pretext = stripslashes(wp_filter_post_kses($widget_adv_custom_field['pretext']));
				$posttext = stripslashes(wp_filter_post_kses($widget_adv_custom_field['posttext']));
				
				$fixedtext1r = stripslashes(wp_filter_post_kses($widget_adv_custom_field['fixedtext1r']));
				$fixedtext1m = stripslashes(wp_filter_post_kses($widget_adv_custom_field['fixedtext1m']));
				$fixedtext1n = stripslashes(wp_filter_post_kses($widget_adv_custom_field['fixedtext1n']));
				$fixedtext1a = stripslashes(wp_filter_post_kses($widget_adv_custom_field['fixedtext1a']));
				$fixedtext2r = stripslashes(wp_filter_post_kses($widget_adv_custom_field['fixedtext2r']));
				$fixedtext2m = stripslashes(wp_filter_post_kses($widget_adv_custom_field['fixedtext2m']));
				$fixedtext2n = stripslashes(wp_filter_post_kses($widget_adv_custom_field['fixedtext2n']));
				$fixedtext2a = stripslashes(wp_filter_post_kses($widget_adv_custom_field['fixedtext2a']));
				
				$dorandomsingle = stripslashes(wp_filter_post_kses($widget_adv_custom_field['dorandomsingle']));
				$dorandomother  = stripslashes(wp_filter_post_kses($widget_adv_custom_field['dorandomother']));
			}
			
			// We're saving as an array, so save the options as such
			$options[$widget_number] = compact( 
				// Standard fields from original custom field widget
				'key', 'title', 'text',
				// Extended fields for advanced version
				'skey',
				'pretext', 'posttext', 'dorandomsingle', 'dorandomother',
				'fixedtext1r', 'fixedtext1m', 'fixedtext1n', 'fixedtext1a',
				'fixedtext2r', 'fixedtext2m', 'fixedtext2n', 'fixedtext2a'
				);
		}
		// Update our options in the database
		update_option( 'widget_adv_custom_field', $options );
		// Now we have updated, let's set the variable to show the 'Saved' message
		$updated = true;
	}
	// Variables to return options in widget menu below; first, if
	if ( -1 == $number ) {
		$key            = '';
		$skey           = '';
		$title  		= '';
		$text   		= '';
		$pretext 		= '';
		$posttext       = '';		
		$fixedtext1n    = '';
		$fixedtext1m    = '';
		$fixedtext1r    = '';
		$fixedtext1a    = '';
		$fixedtext2n    = '';
		$fixedtext2m    = '';
		$fixedtext2r    = '';
		$fixedtext2a    = '';
		$dorandomsingle = '';
		$dorandomother  = '';
		
		$number = '%i%';
	// Otherwise, this widget has stored options to return
	} else {
		$key            = attribute_escape($options[$number]['key']);
		$skey           = attribute_escape($options[$number]['skey']);
		$title  		= attribute_escape($options[$number]['title']);
		$text           = format_to_edit($options[$number]['text']);
		$pretext  		= format_to_edit($options[$number]['pretext']);
		$posttext 		= format_to_edit($options[$number]['posttext']);
		$fixedtext1n    = format_to_edit($options[$number]['fixedtext1n']);
		$fixedtext1m    = format_to_edit($options[$number]['fixedtext1m']);
		$fixedtext1r    = format_to_edit($options[$number]['fixedtext1r']);
		$fixedtext1a    = format_to_edit($options[$number]['fixedtext1a']);
		$fixedtext2n    = format_to_edit($options[$number]['fixedtext2n']);
		$fixedtext2m    = format_to_edit($options[$number]['fixedtext2m']);
		$fixedtext2r    = format_to_edit($options[$number]['fixedtext2r']);
		$fixedtext2a    = format_to_edit($options[$number]['fixedtext2a']);
		$dorandomsingle = !empty($options[$number]['dorandomsingle']);
		$dorandomother  = !empty($options[$number]['dorandomother']);
	}
	// Our actual widget options panel
?>
	<p><?php printf( __( 'Enter the custom field key <a href="%s">[?]</a>  to locate in single posts/pages. When found, the corresponding value is displayed along with widget title and text (if provided).', 'cf_widget' ), 'http://codex.wordpress.org/Using_Custom_Fields' ) ?></p>
	<p>
		<label for="adv-custom-field-key-<?php echo $number; ?>"><?php _e( 'Primary Custom Field Key (required - Used for randomised content):', 'cf_widget' ) ?></label>
		<input id="adv-custom-field-key-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][key]" class="code widefat" type="text" value="<?php echo $key; ?>" /><br />
		<?php _e( 'The <strong>key</strong> must match <em>exactly</em> as in posts/pages.', 'cf_widget' ) ?>
	</p>
	<p>
		<label for="adv-custom-field-skey-<?php echo $number; ?>"><?php _e( 'Secondary Custom Field Key (optional - Used if no content for primary on single post pages):', 'cf_widget' ) ?></label>
		<input id="adv-custom-field-skey-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][skey]" class="code widefat" type="text" value="<?php echo $skey; ?>" /><br />
		<?php _e( 'The <strong>key</strong> must match <em>exactly</em> as in posts/pages.', 'cf_widget' ) ?>
	</p>
	<p>
		<label for="adv-custom-field-title-<?php echo $number; ?>"><?php _e( 'Widget Title (optional):', 'cf_widget' ) ?></label>
		<input id="adv-custom-field-title-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][title]" class="widefat" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="adv-custom-field-text-<?php echo $number; ?>"><?php _e( 'Widget Text (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-text-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][text]" class="widefat" rows="5" cols="20"><?php echo $text; ?></textarea>
	</p>
	
	<table border="0" align="center">
	<tr><td>
	<p>
		<label for="adv-custom-field-pretext-<?php echo $number; ?>"><?php _e( 'Widget Pretext (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-pretext-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][pretext]" class="widefat" rows="5" cols="20"><?php echo $pretext; ?></textarea>
	</p>
	</td><td>
	<p>
		<label for="adv-custom-field-posttext-<?php echo $number; ?>"><?php _e( 'Widget Posttext (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-posttext-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][posttext]" class="widefat" rows="5" cols="20"><?php echo $posttext; ?></textarea>
	</p>
	</td></tr>
	
	<!-- Fixed Text 1 -->
	<tr><td>
	<p>
		<label for="adv-custom-field-fixedtext1a-<?php echo $number; ?>"><?php _e( 'Fixed Text 1 Always (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-fixedtext1a-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][fixedtext1a]" class="widefat" rows="5" cols="20"><?php echo $fixedtext1a; ?></textarea>
	</p>
	</td><td>
	<p>
		<label for="adv-custom-field-fixedtext1m-<?php echo $number; ?>"><?php _e( 'Fixed Text 1 Content Found (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-fixedtext1m-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][fixedtext1m]" class="widefat" rows="5" cols="20"><?php echo $fixedtext1m; ?></textarea>
	</p>
	</td></tr>
	<tr><td>
	<p>
		<label for="adv-custom-field-fixedtext1n-<?php echo $number; ?>"><?php _e( 'Fixed Text 1 No Content (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-fixedtext1n-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][fixedtext1n]" class="widefat" rows="5" cols="20"><?php echo $fixedtext1n; ?></textarea>
	</p>
	</td><td>
	<p>
		<label for="adv-custom-field-fixedtext1r-<?php echo $number; ?>"><?php _e( 'Fixed Text 1 Random Content (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-fixedtext1r-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][fixedtext1r]" class="widefat" rows="5" cols="20"><?php echo $fixedtext1r; ?></textarea>
	</p>
	</td></tr>
	
	<!-- Fixed Text 2 -->
	<tr><td>
	<p>
		<label for="adv-custom-field-fixedtext2a-<?php echo $number; ?>"><?php _e( 'Fixed Text 2 Always (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-fixedtext2a-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][fixedtext2a]" class="widefat" rows="5" cols="20"><?php echo $fixedtext2a; ?></textarea>
	</p>
	</td><td>
	<p>
		<label for="adv-custom-field-fixedtext2m-<?php echo $number; ?>"><?php _e( 'Fixed Text 2 Content Found (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-fixedtext2m-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][fixedtext2m]" class="widefat" rows="5" cols="20"><?php echo $fixedtext2m; ?></textarea>
	</p>
	</td></tr>
	<tr><td>
	<p>
		<label for="adv-custom-field-fixedtext2n-<?php echo $number; ?>"><?php _e( 'Fixed Text 2 No Content (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-fixedtext2n-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][fixedtext2n]" class="widefat" rows="5" cols="20"><?php echo $fixedtext2n; ?></textarea>
	</p>
	</td><td>
	<p>
		<label for="adv-custom-field-fixedtext2r-<?php echo $number; ?>"><?php _e( 'Fixed Text 2 Random Content (optional):', 'cf_widget' ) ?></label>
		<textarea id="adv-custom-field-fixedtext2r-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][fixedtext2r]" class="widefat" rows="5" cols="20"><?php echo $fixedtext2r; ?></textarea>
	</p>
	</td></tr>
	</table>
		
	<p>
		<label for="adv-custom-field-dorandomsingle-<?php echo $number; ?>"><?php _e( 'Show random on single post pages:', 'cf_widget' ) ?></label>
		<input type="checkbox" id="adv-custom-field-dorandomsingle-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][dorandomsingle]" <?php if ($dorandomsingle) echo "checked"; ?>>
	</p>
	<p>
		<label for="adv-custom-field-dorandomother-<?php echo $number; ?>"><?php _e( 'Show random on other pages:', 'cf_widget' ) ?></label>
		<input type="checkbox" id="adv-custom-field-dorandomother-<?php echo $number; ?>" name="widget-adv-custom-field[<?php echo $number; ?>][dorandomother]" <?php if ($dorandomother) echo "checked"; ?>>
		<input type="hidden" name="widget-adv-custom-field[<?php echo $number; ?>][submit]" value="1" />
	</p>	
	
<?php
	// And we're finished with our widget options panel
}
// Function to add widget option table when activating this plugin
function wp_widget_adv_custom_field_activation() {
	add_option( 'widget_adv_custom_field', '', '', 'yes' );
}
// Function to delete widget option table when deactivating this plugin
function wp_widget_adv_custom_field_deactivation() {
	delete_option('widget_adv_custom_field');
}
// Function to initialize the Custom Field Widget: the widget and widget options panel
function wp_widget_adv_custom_field_register() {
	// Do we have options? If so, get info as array
	if ( !$options = get_option('widget_adv_custom_field') )
		$options = array();
	// Variables for our widget
	$widget_ops = array(
			'classname'   => 'widget_adv_custom_field',
			'description' => __( 'Display page/post custom field value for a set key', 'cf_widget' )
		);
	// Variables for our widget options panel
	$control_ops = array(
			'width'   => 750,
			'height'  => 400,
			'id_base' => 'adv-custom-field'
		);
	// Variable for out widget name
	$name = __( 'Adv. Custom Field', 'cf_widget' );
	// Assume we have no widgets in play.
	$id = false;
	// Since we're dealing with multiple widgets, we much register each accordingly
	foreach ( array_keys($options) as $o ) {
		// Per Automattic: "Old widgets can have null values for some reason"
		if ( !isset($options[$o]['title']) || !isset($options[$o]['text']) || !isset($options[$o]['pretext']) || !isset($options[$o]['posttext']) )
			continue;
			
		// Automattic told me not to translate an ID. Ever.
		$id = "adv-custom-field-$o"; // "Never never never translate an id" See?
		// Register the widget and then the widget options menu
		wp_register_sidebar_widget( $id, $name, 'wp_widget_adv_custom_field', $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, 'wp_widget_adv_custom_field_control', $control_ops, array( 'number' => $o ) );
	}
	// Create a generic widget if none are in use
	if ( !$id ) {
		// Register the widget and then the widget options menu
		wp_register_sidebar_widget( 'adv-custom-field-1', $name, 'wp_widget_adv_custom_field', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'adv-custom-field-1', $name, 'wp_widget_adv_custom_field_control', $control_ops, array( 'number' => -1 ) );
	}
}
// Adds filters to custom field values to prettify like other content
add_filter( 'adv_custom_field_value', 'convert_chars' );
add_filter( 'adv_custom_field_value', 'stripslashes' );
add_filter( 'adv_custom_field_value', 'wptexturize' );
// When activating/deactivating, run the appropriate function
register_activation_hook( __FILE__, 'wp_widget_adv_custom_field_activation' );
register_deactivation_hook( __FILE__, 'wp_widget_adv_custom_field_deactivation' );
// Allow localization, if applicable
load_plugin_textdomain('cf_widget');
// Initializes the function to make our widget(s) available
add_action( 'init', 'wp_widget_adv_custom_field_register' );
// Fin.
?>
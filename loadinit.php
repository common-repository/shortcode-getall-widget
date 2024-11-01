<?php
/*
Plugin Name:    Shortcode Get All Widget
Description:    Insert a entire widget area (Any sidebar) into your page without any line of code.
Author:         Bhupesh Kushwaha
Author URI:     https://github.com/bhupeshbk
Version:        1.0.0
Text Domain:    shortcode-getall-widget
Domain Path:    /languages

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class Shortcode_getall_widget {

	public function __construct() {
		if ( is_admin() ) {
			add_action( 'init', array(  $this, 'setup_for_tinymce_editor' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'get_shortcode_parameters' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'get_shortcode_parameters' ) );
		}
	}

	function setup_for_tinymce_editor() {
		// Check if the logged in WordPress User can edit Posts or Pages		
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Check if the logged in WordPress User has the Visual Editor enabled
		if ( get_user_option( 'rich_editing' ) !== 'true' ) {
			return;
		}

		// Setup some filters
		add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ) );
		add_filter( 'mce_buttons', array( &$this, 'add_tinymce_toolbar_button' ) );		
	}

	function add_tinymce_plugin( $plugin_array ) {
		$plugin_array['getAllwidgetShortcode'] = plugin_dir_url( __FILE__ ).'vendor/tinymce.js';
		return $plugin_array;
	}
	function add_tinymce_toolbar_button( $buttons ) {
		array_push( $buttons, '|', 'getAllwidgetShortcode' );
		return $buttons;
	}


	function get_shortcode_parameters() {
		global $wp_registered_widgets;

		if ( empty ( $GLOBALS['wp_widget_factory'] ) )
        return;

    	### $widgets = array_keys( $GLOBALS['wp_widget_factory']->widgets );
    	$widget = $GLOBALS['wp_widget_factory']->widgets ;
    	$widgets = array();
    	foreach ($widget as $key => $value) {
    		$widgets[] = array(
							'id' => $key,
							'title' =>  $value,
						);
    	}

    	wp_localize_script( 'editor', 'getAllwidgetShortcode', array(
			'widgets' => $widgets,
		) );
	}
}

$shortcode_getall_widget = new Shortcode_getall_widget;

// [Shortcodewidget widget="bar"]
function Shortcodewidget_func( $atts ) {
 
    global $wp_widget_factory;
    
    extract(shortcode_atts(array(
        'widget' => FALSE
    ), $atts));
    
    $widget = esc_html($widget);
    
    if (!is_a($wp_widget_factory->widgets[$widget], 'WP_Widget')):

        $wp_class = 'WP_Widget_'.ucwords(strtolower($class));
        
        if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):

            #return '<p>'.sprintf(__("%s: Widget not found."),'<strong>'.$class.'</strong>').'</p>';
            return '<p>[Shortcodewidget widget="'.$widget.'"]</p>';

        else:

            $class = $wp_class;

        endif;

    endif;
    
    ob_start();
    $instance = null;

    the_widget($widget, $instance, array('widget_id'=>'arbitrary-instance-'.$widget,
        'before_widget' => '<div class="Shortcodewidget">',
        'after_widget' => '</div>',
        'before_title' => '<h1>',
        'after_title' => '</h1>'
    ));

    $result = ob_get_contents();

    ob_end_clean();

    return $result;
}
add_shortcode( 'Shortcodewidget', 'Shortcodewidget_func' );
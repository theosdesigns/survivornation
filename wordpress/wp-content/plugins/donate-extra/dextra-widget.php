<?php

function widget_donateextra_init() {

	if ( !function_exists('wp_register_sidebar_widget') )
		return;

	function widget_donateextraform($args) {
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_donateextraform');
		$title = $options['title'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		DonateExtraForm();
		echo $after_widget;
	}
	function widget_donateextratotal($args) {
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_donateextratotal');
		$title = $options['title'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		echo '<p>';DonateExtraTotal(); echo '</p>';
		echo $after_widget;
	}
	function widget_donateextrawall($args) {
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_donateextrawall');
		$title = $options['title'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		DonateExtraWall();
		echo $after_widget;
	}


	function widget_donateextraform_control() {
		$options = get_option('widget_donateextraform');
		if ( !is_array($options) )
			$options = array('title'=>'Donate');
		if ( $_POST['donateextraf-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['donateextraf-title']));
			update_option('widget_donateextraform', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		echo '<p style="text-align:right;"><label for="donateextraf-title">' . __('Title:') . ' <input style="width: 200px;" id="donateextraf-title" name="donateextraf-title" type="text" value="'.$title.'" /></label></p>';
		echo '<input type="hidden" id="donateextraf-submit" name="donateextraf-submit" value="1" />';
	}
	function widget_donateextratotal_control() {
		$options = get_option('widget_donateextratotal');
		if ( !is_array($options) )
			$options = array('title'=>'Total Donations');
		if ( $_POST['donateextrat-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['donateextrat-title']));
			update_option('widget_donateextratotal', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		echo '<p style="text-align:right;"><label for="donateextrat-title">' . __('Title:') . ' <input style="width: 200px;" id="donateextrat-title" name="donateextrat-title" type="text" value="'.$title.'" /></label></p>';
		echo '<input type="hidden" id="donateextrat-submit" name="donateextrat-submit" value="1" />';
	}
	function widget_donateextrawall_control() {
		$options = get_option('widget_donateextrawall');
		if ( !is_array($options) )
			$options = array('title'=>'Recognition Wall');
		if ( $_POST['donateextraw-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['donateextraw-title']));
			update_option('widget_donateextrawall', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		echo '<p style="text-align:right;"><label for="donateextraw-title">' . __('Title:') . ' <input style="width: 200px;" id="donateextraw-title" name="donateextraw-title" type="text" value="'.$title.'" /></label></p>';
		echo '<input type="hidden" id="donateextraw-submit" name="donateextraw-submit" value="1" />';
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	
	wp_register_sidebar_widget(
    'donate_extra_1',        // your unique widget id
    'Donate Extra Form',          // widget name
    'widget_donateextraform',  // callback function
    array(                  // options
        'description' => 'Provides the Donate Extra Form Widget',
		'title' => 'Donate Extra Form'
    )
);
	wp_register_sidebar_widget(
    'donate_extra_2',        // your unique widget id
    'Donate Extra Total',          // widget name
    'widget_donateextratotal',  // callback function
    array(                  // options
        'description' => 'Provides the Donate Extra Total Widget',
		'title' => 'Donate Extra Total'
    )
);
	wp_register_sidebar_widget(
    'donate_extra_3',        // your unique widget id
    'Donate Extra Wall',          // widget name
    'widget_donateextrawall',  // callback function
    array(                  // options
        'description' => 'Provides the Donate Extra Wall Widget',
		'title' => 'Donate Extra Wall'
    )
);


	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
    
	wp_register_widget_control(
    'donate_extra_1',        // your unique widget id
    'Donate Extra Form',          // widget name
    'widget_donateextraform_control',  // callback function
    array(                  // options
        'height' => '300',
		'width' => '100',
		'title' => 'Donate Extra Form'
    )
);
	wp_register_widget_control(
    'donate_extra_2',        // your unique widget id
    'Donate Extra Total',          // widget name
    'widget_donateextratotal_control',  // callback function
    array(                  // options
        'height' => '300',
		'width' => '100',
		'title' => 'Donate Extra Total'
    )
);
	wp_register_widget_control(
    'donate_extra_3',        // your unique widget id
    'Donate Extra Wall',          // widget name
    'widget_donateextrawall_control',  // callback function
    array(                  // options
        'height' => '300',
		'width' => '100',
		'title' => 'Donate Extra Wall'
    )
);
	
	}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_donateextra_init');
?>

<?php
/*
 * Plugin Name: NGO-list
 * Plugin URI: https://ngo-portal.org
 * Description: This plugin shows a list of the portals member sites, including external pages and event pages. Should be activated on the portal site.
 * Version: 1.3.4
 * Author: George Bredberg
 * Author URI: https://datagaraget.se
 * Text Domain: ngo-list
 * Domain Path: /languages
 * License GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html */

 /*
 Use [site-list] to show a list of public sites
 Use [search-site] to show a search form to search sites.
 */
 
//Enables the Link Manager that existed in WordPress until version 3.5.
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

// Load translation
add_action( 'plugins_loaded', 'ngol_load_plugin_textdomain' );
function ngol_load_plugin_textdomain() {
	load_plugin_textdomain( 'ngo-list', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/* At activation, create link categories */
// This is not translated since it's created when plugin registers, and then it won't change.
// FIX: the term can't be changed, then the rest of the script will not work. Should redo this to use the slug, so at least the term is translatable.
//register_activation_hook( __FILE__, 'ngol_create_link_cat' );

// Debug
add_action( 'plugins_loaded', 'ngol_create_link_cat' );

function ngol_create_link_cat() {
	$parent_term = term_exists( 'link_category' ); // array is returned if taxonomy is given
	$parent_term_id = $parent_term['term_id']; // get numeric term id
	$eventpages=__( 'Event pages', 'ngo-list' );

	wp_insert_term(
		$eventpages, // the term
		'link_category', // the taxonomy
		array(
			'description'=> __( 'Links to sites in the portal that should be listed as eventpages.', 'ngo-list' ),
			'slug' => 'eventpage',
			'parent'=> $parent_term_id
		)
	);
	$externalpages=__( 'External sites', 'ngo-list' );
	wp_insert_term(
//		'Extern', // the term
		$externalpages,
		'link_category', // the taxonomy
		array(
			'description'=> __('Links to external sites', 'ngo-list' ),
			'slug' => 'extern',
			'parent'=> $parent_term_id
		)
	);
}

/////////////////////////////////////////////////////////////////////////////////////////
// Check to see if mandatory link-categories exists. If not, warn the user.            //
// Link-categories are created at plugin activation, but could be deleted by the user. //
/////////////////////////////////////////////////////////////////////////////////////////
add_action( 'admin_init', 'ngol_check_link_cat' );
function ngol_check_link_cat() {
	global $wpdb;
	$table_terms = $wpdb->prefix . 'terms';
	$table_taxonomy = $wpdb->prefix . 'term_taxonomy';

	$link_categories = $wpdb->get_results(
		"
		SELECT slug
		FROM $table_terms
		WHERE term_id
		IN (
			SELECT term_id
			FROM $table_taxonomy
			WHERE taxonomy =  'link_category'
		)
		LIMIT 0 , 30
		"
	);

	// Create a variable that holds all the link categories and warn if ours are not there among them
	$result = array();
	foreach ( $link_categories as $link_category ) {
		$result[] = $link_category->slug;
		}
		if (!in_array("eventpage", $result)) {
			?><div class='error'><p><?php _e('The plugin NGO-list requires that a link-category with the slug "eventpage" exists. Links you create for events pages should be added to this category. Go to your portal-page Links - Link Categories and create it or deactivate and activate this plugin to automatically create it.', 'ngo-list');?></p></div><?php
		}
		if (!in_array("extern", $result)) {
			?><div class='error'><p><?php _e('The plugin NGO-list requires that a link-category with the slug "extern" exists. Links you create for external web sites should be put in to this category. Go to your portal-page Links - Link Categories and create it or deactivate and activate this plugin to automatically create it.', 'ngo-list');?></p></div><?php
		}
}
// Done complaining about missing link-categories...

//////////////////
// Search NGO:s //
//////////////////
// FIX: update filtered result in page, now it's only showed in the selectbox
add_shortcode('search-site', 'ngol_search_ngo');

function ngol_search_ngo(){ ?>
	<script type="text/javascript">
	function showResult(str) {
		// Get current directory as url
		var pluginUrl = '<?= plugin_dir_url( __FILE__ ) ?>';
		// window.alert(pluginUrl+"livesearch.php");
	  if (str.length==0) {
		document.getElementById("livesearch").innerHTML="";
		document.getElementById("livesearch").style.border="0px";
		return;
	  }
	  if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	  } else {  // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	  xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		  document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
		  document.getElementById("livesearch").style.border="1px solid #A5ACB2";
		}
	  }
	  xmlhttp.open("GET",pluginUrl+"livesearch.php?q="+str,true);
	  xmlhttp.send();
	}
	</script>

	<form>
	<input type="text" size="30" placeholder="<?php _e('Write & click on the desired NGO.', 'ngo-list');?>" onkeyup="showResult(this.value)">
	<div id="livesearch"></div>
	</form>
	<?php
}

//////////////////////////////////////////////////////////////////
// Create the shortcode to list the NGO:s and call the function //
//////////////////////////////////////////////////////////////////
add_shortcode( 'site-list', 'ngol_display_ngo_list' );
	if ( ! is_admin() ) {
		add_filter( 'widget_text', 'do_shortcode' );
	}

// Create the function to list the NGO:s
function ngol_display_ngo_list() {

	echo '<div><ul>';
	?><h2><?php _e('Web sites', 'ngo-list');?>:</h2><?php
	echo '</ul></div>';
		// Create sitelist from public sites
		$args = array(
		'spam'			=>	0,
		'deleted'		=>	0,
		'archived'	=>	0,
		'public'		=>	1,
		'orderby' 	=>	'path',
		);

		$sites = get_sites($args);
		
if(!empty($sites)){
    ?><ul class="sitelist"><?php
    foreach($sites as $site){
        $details = get_blog_details($site->blog_id);
        if($details != false){
            $addr = $details->siteurl;
            $name = $details->blogname;
            if(!(($site->blog_id == 1)/* &&($show_main != 1)*/)){
                ?>
                <li>
                    <a class="sitelist" href="<?php echo $addr; ?>"><?php echo $name;?></a>
                </li>
                <?php
            }
        }
    }
    ?></ul><?php
}

	/* Check for image to indicate external link */
	// Get link to stylesheet
	$extlink = get_stylesheet_directory_uri();

	// Check if valid image is in theme/images folder
	$located = locate_template( 'images/ico_extern.png' );
	if( !empty( $located ) ) {
		$ico_extern = $extlink . '/images/ico_extern.png';
	}
	// Else use the picture in the plugin folder.
	// Get path to plugin folder
	elseif(function_exists('plugin_dir_url')){
		$ico_extern = plugin_dir_url( __FILE__ ) . 'images/ico_extern.png';
	} else {
		_e('This is a Wordpress error... The file wp-includes/plugin.php is probably not loaded..', 'ngo-list');
	}

	// Get external sites and event sites
	// Find out what the category with slug "extern" is called
	global $wpdb;
	$table_terms = $wpdb->prefix . 'terms';
	$extern_names = $wpdb->get_results(
		"
		SELECT name
		FROM $table_terms
		WHERE slug = 'extern'
		LIMIT 0, 1
		"
	);

	// Now get the bookmarks with this name
	$result = array();
	foreach ( $extern_names as $extern_name ) {

		$external_links = get_bookmarks( array(
			'orderby'        => 'name',
			'order'          => 'ASC',
			'category_name' => $extern_name->name,
			'hide_invisible' => 1
		));
	}
	
	// Find out what the category with slug "eventpage" is called
	$eventpage_names = $wpdb->get_results(
	"
	SELECT name
	FROM $table_terms
	WHERE slug = 'eventpage'
	LIMIT 0,1
	"
	);

	//Now get the bookmarks with this name
	$result = array();
	foreach ( $eventpage_names as $eventpage_name ) {

	$event_links = get_bookmarks( array(
		'orderby'        => 'name',
		'order'          => 'ASC',
		'category_name' => $eventpage_name->name,
		'hide_invisible' => 1,
	));
	}

	// Echo out the lists of bookmarks
	echo '<div><ul class="sitelist">';

	if( !empty( $external_links ) ) {
		?><h2><?php _e('External websites', 'ngo-list');?>:</h2><?php

		foreach ( $external_links as $external_link ) {
			echo '<li><a class="sitelist" target="_blank" href="'.$external_link->link_url.'">'.$external_link->link_name.'&nbsp;<img style="display: inline; !important" src="'.$ico_extern.'"/></a></li>';
		}
	}

	if( !empty( $event_links ) ) {
		?><h2><?php _e('Event Pages', 'ngo-list')?>:</h2><?php
	
		foreach ( $event_links as $event_link ) {
			printf( '<li><a class="sitelist" href="%s">%s</a></li>', $event_link->link_url, $event_link->link_name );
		}
	}
	echo '</ul></div>';
}

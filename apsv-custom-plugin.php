<?php
/**
 * @package APSV_Custom_Plugin
 * @version 1.0
 */
/*
Plugin Name: APSV Custom Plugin
Description: Test Plugin for ASPV - ETIST UPM - Spain
Author: Nacho & Pedro
Version: 1.0
*/

if (!function_exists('apsv_custom_plugin_add_menus')) :
function apsv_custom_plugin_add_menus()
{
 add_menu_page('APSV Custom Plugin', 'Feed Sources', 'manage_options', __FILE__, 'feed_sources_admin_setting');
 add_submenu_page(__FILE__, 'New Feed', 'New Feed', 'manage_options', 'new_feed_source','new_feed_source');
}
endif;

add_action('admin_menu', 'apsv_custom_plugin_add_menus');


if (!function_exists('feed_sources_admin_setting')) :
function feed_sources_admin_setting()
{

?>
	<div class="wrap">
    <?php    echo "<h2>" . __( 'APSV Custom Plugin Settings', 'wordpress' ) . "</h2>"; ?>
    </div>
<?php

}
endif;

if (!function_exists('new_feed_source')) :
function new_feed_source()
{

    if($_POST['wpieeexsp_new_feed_hidden'] == 'Y') {
        feed_sources_admin_setting();
    } else {
        //Normal page display
    


?>

 <div class="wrap">
    <?php    echo "<h2>" . __( 'New Feed', 'wordpress' ) . "</h2>"; ?>
     
    <form name="wpieeexsp_new_feed_form" method="post" action="<?php save_post ?>">
        <input type="hidden" name="wpieeexsp_new_feed_hidden" value="Y">
        <?php    echo "<h4>" . __( 'New Feed Settings', 'wordpress' ) . "</h4>"; ?>
        <p><?php _e("Feed Name: " ); feed_name_box_content();?></p>
        <p><?php _e("All Fields: " ); feed_allfields_box_content();?></p>
        <hr />         
     
        <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Create Feed', 'wordpress' ) ?>" />
        </p>
    </form>
</div>

<?php
    $args = array(
      'post_type' => 'apsv_feed',
      'tax_query' => array(
        array(
          'field' => 'slug',
        )
      )
    );
    $products = new WP_Query( $args );
    if( $products->have_posts() ) {
      while( $products->have_posts() ) {
        $products->the_post();
        ?>
        <?php
  // If we are in a loop we can get the post ID easily
  $price = get_post_meta( get_the_ID(), 'product_price', true );

  // To get the price of a random product we will need to know the ID
  $price = get_post_meta( $product_id, 'product_price', true );
?>
          <h1><?php the_title() ?></h1>
          <div class='content'>
            <?php the_content() ?>
          </div>
        <?php
      }
    }
    else {
      echo 'Oh ohm no products!';
    }
  ?>



<?php
}
}

endif;


add_action( 'init', 'create_feed_post_type' );
function create_feed_post_type() {
  register_post_type( 'apsv_feed',
    array(
      'labels' => array(
        'name' => __( 'IEEE Feeds' ),
        'singular_name' => __( 'IEEE Feed' )
      ),
      'public' => true,
      'has_archive' => false,
      'rewrite' => array('slug' => 'ieee_feeds'),
       'show_ui' => true,
    )
  );
}

add_action( 'init', 'create_feed_item_post_type' );
function create_feed_item_post_type() {
  register_post_type( 'apsv_feed_item',
    array(
      'labels' => array(
        'name' => __( 'IEEE Feed Items' ),
        'singular_name' => __( 'IEEE Feed Item' )
      ),
      'public' => true,
      'has_archive' => false,
      'rewrite' => array('slug' => 'ieee_feed_items'),
      'show_ui' => true,
    )
  );
}

add_action( 'add_meta_boxes', 'feed_name_box' );
function feed_name_box() {
    add_meta_box( 
        'feed_name_box',
        __( 'Name', 'wordpress' ),
        'feed_name_box_content',
        'apsv_feed',
        'side',
        'high'
    );
}
function feed_name_box_content( $post ) {
  wp_nonce_field( plugin_basename( __FILE__ ), 'feed_name_box_content_nonce' );
  echo '<label for="feed_name"></label>';
  echo '<input type="text" id="feed_name" name="feed_name" placeholder="" />';
}

add_action( 'add_meta_boxes', 'feed_allfields_box' );
function feed_allfields_box() {
    add_meta_box( 
        'feed_allfields_box',
        __( 'All Fields', 'wordpress' ),
        'feed_allfields_box_content',
        'apsv_feed',
        'side',
        'high'
    );
}
function feed_allfields_box_content( $post ) {
  echo '<label for="feed_allfields"></label>';
  echo '<input type="text" id="feed_allfields" name="feed_allfields" placeholder="" />';
}

add_action( 'save_post', 'feed_box_save' );
function feed_box_save( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
  return;

  if ( !wp_verify_nonce( $_POST['feed_name_box_content_nonce'], plugin_basename( __FILE__ ) ) )
  return;

  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
    return;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
    return;
  }
  $feed_name = $_POST['feed_name'];
  $feed_allfields = $_POST['feed_allfields'];
  update_post_meta( $post_id, 'feed_name', $feed_name );
  update_post_meta( $post_id, 'feed_allfields', $feed_allfields );
}



add_action( 'add_meta_boxes', 'feed_item_name_box' );
function feed_item_name_box() {
    add_meta_box( 
        'feed_item_name_box',
        __( 'Name', 'wordpress' ),
        'feed_item_name_box_content',
        'apsv_feed_item',
        'side',
        'high'
    );
}


function custom_plugin_form_shortcode() {
	$form = '<form action="" method="post">';
	$form .= '<table style="border:none;" cellpadding="5">';
	$form .= '<tr style="border:none;"><td style="border:none;">Author</td><td style="border:none;"><input type="text" name="author"></td></tr>';
	$form .= '<tr style="border:none;"><td style="border:none;">Title</td><td style="border:none;"><input type="text" name="title"></td></tr>';
	$form .= '<tr style="border:none;"><td style="border:none;">Abstract</td><td style="border:none;"><input type="text" name="abstract"></td></tr>';
	$form .= '</table>';
	$form .= '<input type="submit">';
	$form .= '</form><p>';

	echo $form;
	if(isset($_POST)){
		$query = 'http://ieeexplore.ieee.org/gateway/ipsSearch.jsp?';
		$data=array();
		if(!empty($_POST['author'])) {
    			$data['au'] = $_POST['author'];
		}
		if(!empty($_POST['title'])) {
   			$data['ti'] = $_POST['title'];
		}
		if(!empty($_POST['abstract'])) {
   			$data['ab'] = $_POST['abstract'];
		}
		$query .= http_build_query($data);
		if (($response_xml_data = file_get_contents($query))===false){
    			echo "Error fetching XML\n";
		} else {
   			libxml_use_internal_errors(true);
   			$data = simplexml_load_string($response_xml_data);
   			if (!$data) {
       				foreach(libxml_get_errors() as $error) {
           				echo "\t", $error->message;
       				}
   			} else {
				$count = 1;
				foreach($data->document as $doc) {
					echo '<p align="justify">';
      					echo '<font size="3"><a target="_blank" href="' . $doc->pdf . '">' . $count . '. ' . $doc->title . '</a></font><br>';
					echo '<strong>Authors:</strong> ' . $doc->authors . '<br>';
					echo '<strong>Abstract:</strong> ' . $doc->abstract . '<br>';
					$count++;
				}
   			}
		}
	}
}

add_shortcode( 'custom_plugin_form', 'custom_plugin_form_shortcode');

// Add the ability to display the content block in a reqular post using a shortcode
function custom_plugin_results_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'text' => ''
	), $atts ) );
	if ($text != '') {
		$query = 'http://ieeexplore.ieee.org/gateway/ipsSearch.jsp?querytext=' . $text;
		if (($response_xml_data = file_get_contents($query))===false){
    			echo "Error fetching XML\n";
		} else {
   			libxml_use_internal_errors(true);
   			$data = simplexml_load_string($response_xml_data);
   			if (!$data) {
       				foreach(libxml_get_errors() as $error) {
           				echo "\t", $error->message;
       				}
   			} else {
				$count = 1;
				foreach($data->document as $doc) {
					$abstract = substr($doc->abstract, 0, 300);
					echo '<p align="justify">';
      					echo '<font size="3"><a target="_blank" href="' . $doc->pdf . '">' . $count . '. ' . $doc->title . '</a></font><br>';
					echo '<strong>Authors:</strong> ' . $doc->authors . '<br>';
					echo '<strong>Abstract:</strong> ' . $abstract . '...<br>';
					$count++;
				}
   			}
		}
	}

}
add_shortcode( 'custom_plugin_results', 'custom_plugin_results_shortcode' );




?>

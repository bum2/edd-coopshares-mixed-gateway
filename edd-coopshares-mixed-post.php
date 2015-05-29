<?php

function cs_mixed_text_callback ( $args, $post_id ) {
	$value = get_post_meta( $post_id, $args['id'], true );
	if ( $value != "" ) {
		$value = get_post_meta( $post_id, $args['id'], true );
	}else{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$output = "<tr valign='top'> \n".
		" <th scope='row'> " . $args['name'] . " </th> \n" .
		" <td><input type='text' class='regular-text' id='" . $args['id'] . "'" .
		" name='" . $args['id'] . "' value='" .  $value   . "' />\n" .
		" <label for='" . $args['id'] . "'> " . $args['desc'] . "</label>" .
		"</td></tr>";

	return $output;
}

function cs_mixed_rich_editor_callback ( $args, $post_id ) {
	$value = get_post_meta( $post_id, $args['id'], true );
	if ( $value != "" ) {
		$value = get_post_meta( $post_id, $args['id'], true );
	}else{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
	$output = "<tr valign='top'> \n".
		" <th scope='row'> " . $args['name'] . " </th> \n" .
		" <td>";
		ob_start();
		wp_editor( stripslashes( $value ) , $args['id'], array( 'textarea_name' => $args['id'] ) );
	$output .= ob_get_clean();

	$output .= " <label for='" . $args['id'] . "'> " . $args['desc'] . "</label>" .
		"</td></tr>\n";

	return $output;
}


/**
 * Updates when saving post
 *
 */
function coopshares_mixed_post_save( $post_id ) {

	if ( ! isset( $_POST['post_type']) || 'download' !== $_POST['post_type'] ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;

	$fields = coopshares_mixed_post_fields();

	foreach ($fields as $field) {
		update_post_meta( $post_id, $field['id'],  $_REQUEST[$field['id']] );
	}
}
add_action( 'save_post', 'coopshares_mixed_post_save' );


/**
 * Display sidebar metabox in saving post
 *
 */
function coopshares_mixed_print_meta_box ( $post ) {

	if ( get_post_type( $post->ID ) != 'download' ) return;

	?>
	<div class="wrap">
		<div id="tab_container_local">
			<table class="form-table">
				<?php
					$fields = coopshares_mixed_post_fields();
					foreach ($fields as $field) {
						if ( $field['type'] == 'text'){
							echo cs_mixed_text_callback( $field, $post->ID );
						}elseif ( $field['type'] == 'rich_editor' ) {
							echo cs_mixed_rich_editor_callback( $field, $post->ID ) ;
						}
					}
				?>

			</table>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
}

function coopshares_mixed_show_post_fields ( $post) {
	//print_r($post);
	add_meta_box( 'coopshares_mixed_'.$post->ID, __( "Coopshares-Mixed Settings", 'edd-coopshares-mixed'), "coopshares_mixed_print_meta_box", 'download', 'normal', 'high');

}
add_action( 'submitpost_box', 'coopshares_mixed_show_post_fields' );

function coopshares_mixed_post_fields () {

	$cs_mixed_gateway_settings = array(
		// bumbum
		/*array(
			'id' => 'coopshares_mixed_post_receipt',
			'name' => __( 'Coopshares-Mixed Receipt Text', 'edd-coopshares-mixed' ),
			'desc' => __('The html to add to the Receipt page, once registered the payment via Coopshares-Mixed', 'edd-coopshares-mixed'),// . '<br/>' . edd_get_emails_tags_list()  ,
			'type' => 'rich_editor',
		),*/
		//
		array(
			'id' => 'coopshares_mixed_post_from_email',
			'name' => __( 'Coopshares-Mixed Email From', 'edd-coopshares-mixed' ),
			'desc' => __( 'The remitent email for the notification to the user', 'edd-coopshares-mixed' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => get_bloginfo( 'admin_email' )
		),
		array(
			'id' => 'coopshares_mixed_post_subject_mail',
			'name' => __( 'Coopshares-Mixed Email Subject', 'edd-coopshares-mixed' ),
			'desc' => __( 'The subject of the email sended to the user (can use email tags)', 'edd-coopshares-mixed' ),//  . '<br/>' . edd_get_emails_tags_list(),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id' => 'coopshares_mixed_post_body_mail',
			'name' => __( 'Coopshares-Mixed Email Body', 'edd-coopshares-mixed' ),
			'desc' => __('The email body when using Coopshares-Mixed (using the email tags below)', 'edd-coopshares-mixed') . '<br/>' . edd_get_emails_tags_list()  ,
			'type' => 'rich_editor',
		),
	);

	return $cs_mixed_gateway_settings;
}

?>
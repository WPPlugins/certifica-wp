<?php
/*
Plugin Name: Certifica WP
Plugin URI: https://certificawp.com.br/
Description: Plugin para gerar e validar certificados dos seus cursos.
Version: 1.17
Author: Lucas Moreira
Author URI: http://lucasmoreira.com.br/
*/

function CWP_activation() {
	add_option('cwp_username_option','display_name');
}
register_activation_hook(__FILE__,'CWP_activation');

function CWP_deactivation() {
	delete_option('cwp_username_option');
}
register_deactivation_hook(__FILE__,'CWP_deactivation');

function cwp_page_options(){
    add_submenu_page(
    	'options-general.php',
    	'Certifica WP',
    	'Certifica WP',
    	'manage_options',
    	'certifica-wp/options.php'
	);
}
add_action('admin_menu', 'cwp_page_options');

function CWP_get_certificate($atts) {
	$defaults = shortcode_atts(array(
		'key' => '',
	), $atts);

	if ( is_user_logged_in() ) {

		$userinfo = wp_get_current_user();

		$user['name'] = $userinfo->user_login;
		$user['email'] = $userinfo->user_email;
		// $user['firstname'] = $userinfo->user_firstname;
		// $user['lastname'] = $userinfo->user_lastname;
		// $user['displayname'] = $userinfo->display_name;
		// $user['ID'] = $userinfo->ID;

		$the_name = get_option('cwp_username_option');

		if ( $the_name == 'display_name' && !empty($userinfo->display_name) )
			$user_certificate = $userinfo->display_name;

		elseif ( $the_name == 'user_fullname' && !empty($userinfo->user_firstname) && !empty($userinfo->user_lastname) )
			$user_certificate = $userinfo->user_firstname.' '.$userinfo->user_lastname;

		elseif ( $the_name == 'user_firstname' && !empty($userinfo->user_firstname) )
			$user_certificate = $userinfo->user_firstname;

		elseif ( $the_name == 'user_login' && !empty($userinfo->user_login) )
			$user_certificate = $userinfo->user_login;

		else
			$user_certificate = $userinfo->user_login;

		$course_name = get_bloginfo();

		$local = array('127.0.0.1','192.56.1.30');
		( in_array($_SERVER['HTTP_HOST'], $local) ) ?
			$url_endpoint = 'http://192.56.1.30/certificawp/public/' :
			$url_endpoint = 'http://certificawp.com.br/';

		$get_url = $url_endpoint.'getCertificate/'.$defaults['key'].'?user_name='.urlencode($user_certificate).'&user_email='.urlencode($user['email']);

		$return  = '<link type="text/css" rel="stylesheet" href="'.plugin_dir_url(__FILE__).'certificawp.css">';
		$return .= '<div class="certifica_wrapper">';

		if ( !wp_script_is('jquery') ) {
			$return .= '
				<script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
				<script>
					console.log("CWP_debug: jQuery is loaded by CWP plugin");
				</script>';
			// wp_enqueue_script('jquery');
		} else {
			$return .= '
				<script>
					console.log("CWP_debug: jQuery was loaded previously");
				</script>';
		}

		$return .= '
			<script type="text/javascript">
				jQuery(function() {
				    jQuery(".certificawp_btn_download").attr("href","'.$get_url.'");
				    jQuery(".certificawp_btn_obter").click(function() {
				        jQuery(".certificawp_popup_overlay_container").fadeIn();
				    });
				    jQuery(".certificawp_popup_overlay, .certificawp_link_close_popup").click(function() {
				        jQuery(".certificawp_popup_overlay_container").fadeOut();
				    });
				});
			</script>';

		$return .= '
			<div class="certificawp_popup_overlay_container">
				<div class="certificawp_popup_overlay"></div>
				<div class="certificawp_popup">
					<h1>Aqui está o seu certificado!</h1>
					Clique no botão abaixo para salvar o certificado em seu computador.
					<div class="separator"></div>
					<a href="#" target="_self" class="certificawp_btn_download">Fazer download do certificado</a>
					<div class="separator"></div>
					<small>
						<div class="cwp_footer">
							<div class="left">
								<a href="javascript:void(null)" class="certificawp_link_close_popup">Fechar esta mensagem</a>
							</div>
							<div class="right">
								<a href="http://certificawp.com.br?href=plugin" class="certificawp_link_close_popup" target="_blank">CertificaWP</a>
							</div>
						</div>
					</small>
				</div>
			</div>';
		$return .= '<button type="button" class="certificawp_btn_obter" data-file="URL aqui" target="_blank">Solicitar certificado</button>';
	} else
		$return .= 'Você precisa estar logado para obter o certificado.';

	$return .= '</div>';

	return $return;
}
add_shortcode('certificawp_obter','CWP_get_certificate');

function CWP_validate_certificate() {

	$local = array('127.0.0.1','192.56.1.30');
	( in_array($_SERVER['HTTP_HOST'], $local) ) ?
		$url_endpoint = 'http://192.56.1.30/certificawp/public/validateCertificate' :
		$url_endpoint = 'http://certificawp.com.br/validateCertificate';

	$return  = '<link type="text/css" rel="stylesheet" href="'.plugin_dir_url(__FILE__).'certificawp.css">';
	$return .= '<script>function goValidator() { jQuery("#linkValidator").attr("href","'.$url_endpoint.'?c=" + jQuery("#CWP_validator").val() ); }</script>';
	$return .= '<div class="certifica_wrapper">';
	// $return .= '<form method="post" action="'.$url_endpoint.'">';
	$return .= '<form>';
	// $return .= '<input type="text" name="validator" class="certificawp_btn_validar" placeholder="Código de validação" /><br>';
	$return .= '<input type="text" name="validator" id="CWP_validator" class="certificawp_btn_validar" placeholder="Código de validação" /><br>';
	// $return .= '<button type="submit" class="certificawp_btn_validar_submit">Validar</button>';
	$return .= '<a href="#" id="linkValidator" onclick="goValidator()" class="certificawp_btn_validar_submit" target="_blank">Validar Certificado</a>';
	$return .= '</form>';
	$return .= '</div>';

	return $return;
}
add_shortcode('certificawp_validar','CWP_validate_certificate');
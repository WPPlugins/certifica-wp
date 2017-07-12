<?php
if ( isset($_POST['cwp_username_option']) ) {

	if ( !empty($_POST['cwp_username_option']) ) {

		update_option('cwp_username_option',trim($_POST['cwp_username_option']));
		?>
		<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
			<p>
				<strong>Configurações salvas.</strong>
			</p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dispensar este aviso.</span></button>
		</div>
		<?php

	} else
		update_option('cwp_username_option','display_name');
}
?>

<div class="wrap">
	<h1>Certifica WP</h1>

	<p>
		Cada usuário pode ser identificado de várias formas dentro do Wordpress.<br>
		Selecione abaixo o nome que deverá aparecer no certificado dos usuários. 
	</p>
	<form method="post" action="<?php $PHP_SELF; ?>">
		<p>
			<select name="cwp_username_option">
				<option value="display_name" <?php if ( get_option('cwp_username_option') == 'display_name' ) echo 'selected="selected"'; ?>>Nome de exibição</option>
				<option value="user_fullname" <?php if ( get_option('cwp_username_option') == 'user_fullname' ) echo 'selected="selected"'; ?>>Nome + Sobrenome</option>
				<option value="user_firstname" <?php if ( get_option('cwp_username_option') == 'user_firstname' ) echo 'selected="selected"'; ?>>Primeiro nome</option>
				<option value="user_login" <?php if ( get_option('cwp_username_option') == 'user_login' ) echo 'selected="selected"'; ?>>Login do usuário</option>
			</select>
		</p>
		<?php submit_button(); ?>
	</form>
	<p>
		* Se a opção escolhida estiver vazia, o valor padrão será a imediatamente abaixo.
	</p>
	<p>
		<b>Por exemplo:</b>
		<br><br>
		Se você selecionou a opção "Nome + Sobrenome" e, ao gerar o certificado, o usuário não tiver estes dois dados preenchidos, o Primeiro nome será utilizado.<br>
		Se o Primeiro nome também estiver vazio, será utilizado o login do usuário.
	</p>
</div>
<?php
require("functions.php");
loadsrcs();
mnp_display_nav(); ?>
<div class="container">
	<div class="row">
		<h1><?php echo MNP_PLUGIN_NAME ?></h1>
		<?php settings_errors(); ?>
		<hr>
		<div class="settingsgrid">
			<div class="w70">
				<div class="tab-content">
					<div id="tab-1" class="tab-pane active">

						<div class="selector">
							<h2>Налаштування</h2>
						<select id="selectorseting">
							<option value="allsettings">Всі налаштування</option>
							<option value="basesettings" selected>Базові налаштування</option>
							<option value="additional">Додаткові налаштування</option>
							<option value="autosettings">Налаштування автоматизації</option>
							<option value="forjuridical">Для юридичних осіб</option>
						</select>
					</div>
					<hr>

						<form method="post" action="options.php">
							<?php
								settings_fields( 'morkvanp_options_group' );
								do_settings_sections( 'morkvanp_plugin' );
								submit_button();
							?>
						</form>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		  <?php require 'card.php' ; ?>
		</div>
	</div>
</div>

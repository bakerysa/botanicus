<?php

/**
 * The Administration interface
 *
 */
class OM4_Typekit_Admin {
	
	private $typekitInstance;
	
	/**
	 * Class Constructor
	 *
	 * @param OM4_Typekit instance
	 */
	public function __construct(& $instance) {
		global $wpdb;
		
		$this->typekitInstance = $instance;
		
		add_action('admin_menu', array($this, 'AdminMenu'));

		add_filter( 'plugin_action_links_' . str_replace( 'typekit-admin.php', 'typekit.php', plugin_basename( __FILE__ ) ), array( $this, 'ActionLinks' ) );
	}
	
	/**
	 * Set up the Admin Settings menu
	 */
	public function AdminMenu() {
		add_options_page(__('Typekit Fonts', 'typekit-fonts-for-wordpress'), __('Typekit Fonts', 'typekit-fonts-for-wordpress'), 'manage_options', basename(__FILE__), array($this, 'AdminPage'));
	}

	/**
	 * Add "Settings" link to the plugin's action links on the plugins screen.
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function ActionLinks( $links ) {
		$plugin_links = array(
				'<a href="' . admin_url( 'options-general.php?page=typekit-admin.php' ) . '">' . __( 'Settings', 'typekit-fonts-for-wordpress' ) . '</a>'
		);

		return array_merge( $plugin_links, $links );
	}
	
	/**
	 * Display the admin settings page
	 */
	public function AdminPage() {
		?>
		<div class="wrap typekitsettings">
		<style type="text/css">
			.typekitsettings label { font-weight: bold; vertical-align: top; padding-right: 1em; }
			.typekitsettings li p { margin: 1em 0em; }
			.typekitsettings p code { margin: 0.5em; padding: 0.5em; display: block; }
			.typekitsettings code.inline { margin: 0; padding: 0.2em; display: inline; }
			.typekitsettings textarea { width: 90%; font-family: Courier, Fixed, monospace; }
			.typekitsettings .indent { margin-left: 2em; }
		</style>
		<?php
		if (isset($_POST['submit']) && check_admin_referer('typekit-fonts-for-wordpress-save-settings') && current_user_can('manage_options')) {
			// settings page has been submitted

			if ( isset( $_POST['kitid'] ) && isset( $_POST['method'] ) ) {

			    $this->typekitInstance->ParseKitID(stripslashes($_POST['kitid']));
				$this->typekitInstance->ParseEmbedMethod(stripslashes($_POST['method']));

				$id = $this->typekitInstance->GetAccountID();
				if ($id == '') {
					// embed code is empty
					?>
					<div id="error" class="error"><p>
					<?php
					$instructions = sprintf( __(' Please <a href="%s">click here for instructions</a> on how to obtain your Typekit embed code.', 'typekit-fonts-for-wordpress'), '#getembedcode');
					if (strlen($_POST['kitid'])) {
						// an embed code has been submitted, but was rejected
						printf(__('Invalid Typekit Kit ID. %s', 'typekit-fonts-for-wordpress'), $instructions);
					} else {
						// no kit ID was submitted
						printf(__('You must enter your Typekit Kit ID. %s', 'typekit-fonts-for-wordpress'), $instructions);
					}
					?>
					</p></div>
					<?php
				} else {
					// ensure the Typekit account ID maps to a valid JS file on Typekit's servers (ie doesn't return a 404 error)
					$url = sprintf($this->typekitInstance->embedcodeurl, $id);
					$response = wp_remote_head( $url );

					if ( 404 == wp_remote_retrieve_response_code( $response ) ) {
						?>
						<div id="error" class="error"><p>
							<?php printf( __( 'Your Typekit Kit ID may be incorrect because  <a href="%1$s" target="_blank">%1$s</a> does not exist. Please verify that your Typekit Kit ID correct. If you have just published your kit, please try again in a few minutes.', 'typekit-fonts-for-wordpress'), esc_url( $url ) ); ?>
						</p></div>
						<?php
					}
				}
			}
			if (isset($_POST['css'])) {
				$this->typekitInstance->SetCSSRules(stripslashes($_POST['css']));
			}
			$this->typekitInstance->SaveSettings();
			?>
			<div id="message" class="updated fade"><p><?php _e('Settings saved.', 'typekit-fonts-for-wordpress'); ?></p></div>
			<?php
		}
		?>
		<form method="post">
		<?php wp_nonce_field('typekit-fonts-for-wordpress-save-settings'); ?>
		<h2><?php _e('Typekit Fonts for WordPress Settings', 'typekit-fonts-for-wordpress'); ?></h2>
		<p><?php _e('Typekit offer a service that allows you to select from a range of hundreds of high quality fonts for your WordPress website. The fonts are applied using the font-face standard, so they are standards compliant, fully licensed and accessible.', 'typekit-fonts-for-wordpress'); ?></p>
		<p><?php _e('To use this plugin you need to sign up with Typekit, and then configure the following options.', 'typekit-fonts-for-wordpress'); ?></p>
		<h3><?php _e('Register with Typekit', 'typekit-fonts-for-wordpress'); ?></h3>
		<ol>
			<li><?php printf( __('Go to <a href="%s" target="blank">typekit.com</a> and register for an account.', 'typekit-fonts-for-wordpress'), 'https://typekit.com/'); ?></li>
			<li><?php _e('Choose a few fonts to add to your account and Publish them.', 'typekit-fonts-for-wordpress'); ?></li>
			<li id="getembedcode"><?php _e('Go to the Kit Editor (link at the top right of the screen) then click on the Embed Code option.', 'typekit-fonts-for-wordpress'); ?></li>
		</ol>
		<h3><?php _e('Plugin Configuration', 'typekit-fonts-for-wordpress'); ?></h3>
		<ol start="4">
			<li><?php _e('Enter your Typekit Kit ID (shown at the bottom of the Embed Code screen).', 'typekit-fonts-for-wordpress'); ?><br />
				<p class="option"><label for="kitid"><?php _e('Typekit Kit ID:', 'typekit-fonts-for-wordpress'); ?></label> <input type="text" name="kitid" value="<?php echo esc_attr( $this->typekitInstance->GetAccountID() ); ?>" /><br />
			</li>
            <li><?php _e('Choose your Preferred Embed Method.', 'typekit-fonts-for-wordpress'); ?><br />
				<p class="option"><label for="method"><?php _e('Embed Method:', 'typekit-fonts-for-wordpress'); ?></label>
                    <select name="method">
                        <option value="css"<?php echo selected( $this->typekitInstance->GetEmbedMethod(), 'css' ); ?>><?php _e('CSS Link (Simplest)', 'typekit-fonts-for-wordpress'); ?></option>
                        <option value="js"<?php echo selected( $this->typekitInstance->GetEmbedMethod(), 'js' ); ?>><?php _e('Javascript (Advanced)', 'typekit-fonts-for-wordpress'); ?></option>
                    </select>
			</li>

			<li><?php _e('You can add selectors using the Typekit Kit Editor. Alternatively you can define your own CSS rules in your own style sheet or using the Custom CSS Rules field below (technical note: these CSS rules will be embedded in the header of each page). Look at the advanced examples shown in the Typekit editor for ideas.', 'typekit-fonts-for-wordpress'); ?>
				<p class="option"><label for="css"><?php _e('Custom CSS Rules:', 'typekit-fonts-for-wordpress'); ?></label> <textarea name="css" rows="10" cols="80"><?php echo esc_textarea( $this->typekitInstance->GetCSSRules() ); ?></textarea><br />
				<a href="#help-css"><?php _e('Click here for help on CSS', 'typekit-fonts-for-wordpress'); ?></a>
				</p>
			</li>
		</ol>
		
		<p class="submit"><input name="submit" type="submit" value="<?php _e('Save Settings', 'typekit-fonts-for-wordpress'); ?>" class="button-primary" /></p>
		</form>
		<h3 id="help"><?php _e('Help', 'typekit-fonts-for-wordpress'); ?></h3>
		<h4 id="help-fontsnotshowing"><?php _e('Fonts not showing?', 'typekit-fonts-for-wordpress'); ?></h4>
			<ol>
				<li><?php _e('Have you created your Typekit account, added fonts to it and <strong>pressed Publish</strong>? Fonts aren\'t available until they are published.', 'typekit-fonts-for-wordpress'); ?></li>
				<li><?php _e('Have you <strong>waited a few minutes</strong> to allow Typekit time to send your fonts out around the world? Grab a cup of coffee and try again soon.', 'typekit-fonts-for-wordpress'); ?></li>
				<li><?php _e('Have you <strong>added CSS rules</strong> to display your fonts? If in doubt, just try the H2 rule shown in the example and see if that works for you.', 'typekit-fonts-for-wordpress'); ?></li>
			</ol>
		<h4 id="help-css"><?php _e('CSS', 'typekit-fonts-for-wordpress'); ?></h4>
			<p><?php _e('You can use CSS selectors to apply your new typekit fonts. The settings for this plugin allow you to add new CSS rules to your website to activate Typekit fonts. If you are using fonts for more than just a few elements, you may find it easier to manage this way. And using your own CSS rules is a good way to access different font weights.', 'typekit-fonts-for-wordpress'); ?></p>
			<p><?php _e('There are many options for using CSS, but here are a few common scenarios. Note: we\'ve used proxima-nova for our examples, you\'ll need to change proxima-nova to the name of your chosen font from Typekit - your added font names will be visible in the Kit Editor.', 'typekit-fonts-for-wordpress'); ?></p>
			<h5><?php _e('Headings'); ?></h5>
			<p>
				<?php _e('If you want your Typekit fonts to be used for H2 headings, add a rule like this to your CSS Rules field:', 'typekit-fonts-for-wordpress'); ?>
				<code>h2 { font-family: "proxima-nova-1","proxima-nova-2",sans-serif; }</code>
				<?php _e('(and you can add similar rules if you want to target other headings such as H3)', 'typekit-fonts-for-wordpress'); ?>
			</p>
			<h5><?php _e('Sidebar Headings'); ?></h5>
			<p>
				<?php _e('If you want your Typekit fonts to be used for sidebar H2 headings, add a rule like this to your CSS Rules field:', 'typekit-fonts-for-wordpress'); ?>
				<code>#sidebar h2 { font-family: "proxima-nova-1","proxima-nova-2",sans-serif; }</code>
			</p>
			<h5><?php _e('Font Weights', 'typekit-fonts-for-wordpress'); ?></h5>
			<p><?php _e('If your Kit contains more than one weight and/or style for a particular font, you need to use numeric <code class="inline">font-weight</code> values in your CSS rules to map to a font\'s weights.', 'typekit-fonts-for-wordpress'); ?></p>
            <p><?php _e('Typekit fonts have been assigned values from 100 to 900 based on information from the font\'s designer. Web browsers also do some guessing as to which weight it should display if the specific value isn\'t present. Say your font has 100, 300 and 900. If you set your text with <code class="inline">font-weight: 400</code>, it will choose the most appropriate (300 in this case).<br />Note: A <code class="inline">font-weight</code> value of 400 corresponds to <code class="inline">font-weight: normal;</code>', 'typekit-fonts-for-wordpress'); ?></p>
            <p><?php printf(__('See <a href="%s">this help article</a> for more details.', 'typekit-fonts-for-wordpress'), 'http://getsatisfaction.com/typekit/topics/how_do_i_use_alternate_weights_and_styles'); ?></p>
		<h4 id="help-css-advanced"><?php _e('Advanced targetting of fonts with CSS selectors', 'typekit-fonts-for-wordpress'); ?></h4>
			<p>
				<?php _e('You can target your fonts to specific parts of your website if you know a bit more about your current WordPress theme and where the font family is specified. All WordPress themes have a style.css file, and if you know how to check that you should be able to see the selectors in use. Or you can install Chris Pederick\'s Web Developer Toolbar for Firefox and use the CSS, View CSS option to see all the CSS rules in use for your theme. When you find the selectors that are used for font-family, you can create a rule just for that selector to override that rule.', 'typekit-fonts-for-wordpress'); ?>
				<?php _e('For example, if your theme has this CSS rule:', 'typekit-fonts-for-wordpress'); ?>
				<code>body { font-family: Arial, Helvetica, Sans-Serif; }</code>
				<?php _e('you could create this rule to apply your new font to the body of your website:', 'typekit-fonts-for-wordpress'); ?>
				<code>body { font-family: "proxima-nova-1","proxima-nova-2", sans-serif; }</code>
			</p>
		<h4 id="help-css-external"><?php _e('Where to go to get help', 'typekit-fonts-for-wordpress'); ?></h4>
			<p class="indent">
				<?php printf( __('<a href="%s" target="_blank">Typekit Support</a>', 'typekit-fonts-for-wordpress'), 'http://getsatisfaction.com/typekit/' ); ?>
				<br /><?php printf( __('<a href="%s" target="_blank">Sitepoint CSS Forums</a>', 'typekit-fonts-for-wordpress'), 'http://www.sitepoint.com/forums/forumdisplay.php?f=53' ); ?>
				<br /><?php printf( __('<a href="%s" target="_blank">W3Schools CSS Help</a>', 'typekit-fonts-for-wordpress'), 'http://www.w3schools.com/CSS/default.asp' ); ?>
			</p>
		</div>
		<?php 
	}
}
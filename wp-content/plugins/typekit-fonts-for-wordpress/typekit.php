<?php
/*
Plugin Name: Typekit Fonts for WordPress
Plugin URI: https://om4.com.au/plugins/typekit-fonts-for-wordpress-plugin/
Description: Use a range of hundreds of high quality fonts on your WordPress website by integrating the <a href="http://typekit.com">Typekit</a> font service into your WordPress blog.
Version: 1.9.0
Author: OM4
Author URI: https://om4.com.au/plugins/
Text Domain: typekit-fonts-for-wordpress
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*  Copyright 2009-2017 OM4 (email : plugins@om4.com.au)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class OM4_Typekit {
	
	private $dbVersion = 1;
	
	private $installedVersion;
	
	private $dirname;
	
	private $optionName = 'OM4_Typekit';
	
	private $admin;

	public $embedcode_advanced = '<script>
  (function(d) {
    var config = {
      kitId: \'%1$s\',
      scriptTimeout: 3000,
      async: %2$s
    },
    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src=\'https://use.typekit.net/\'+config.kitId+\'.js\';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
  })(document);
</script>';

	public $embedcode_css = '<link rel="stylesheet" href="https://use.typekit.net/%s.css">';

	public $kitid_regexp = '#([a-z0-9]*)#i';

	/**
	 * The format for the Typekit CSS file URL. Used in HTTP requests to verify that the URL doesn't produce a 404 error
	 * 
	 * @var string
	 */
	public $embedcodeurl = 'https://use.typekit.net/%s.css';

	const EMBED_METHOD_CSS = 'css';

	const EMBED_METHOD_JAVASCRIPT = 'js';

	/*
	 * Default settings
	 */
	private $settings = array(
		'id'=> '',
		'method' => self::EMBED_METHOD_CSS,
		'css' => '',
		'async' => '',
	);
	
	/**
	 * Class Constructor
	 *
	 */
	public function __construct() {
		
		// Store the name of the directory that this plugin is installed in
		$this->dirname = str_replace('/typekit.php', '', plugin_basename(__FILE__));

		register_activation_hook(__FILE__, array($this, 'Activate'));

		add_action('init', array($this, 'Initialise'));

		add_action('plugins_loaded', array($this, 'LoadDomain'));
		
		add_action('wp_head', array($this, 'HeaderCode'), 99);

		$data = get_option($this->optionName);
		if (is_array($data)) {
			$this->installedVersion = intval($data['version']);
			$this->settings = $data['settings'];
		}

	}
	
	/**
	 * Load up the relevant language pack if we're using WordPress in a different language.
	 */
	public function LoadDomain() {
		load_plugin_textdomain( 'typekit-fonts-for-wordpress' );
	}
	
	/**
	 * Plugin Activation Tasks
	 *
	 */
	public function Activate() {
		// There aren't really any installation tasks at the moment
		if (!$this->installedVersion) {
			$this->installedVersion = $this->dbVersion;
			$this->SaveSettings();
		}
	}
	
	/**
	 * Performs any upgrade tasks if required
	 *
	 */
	public function CheckVersion() {
		if ($this->installedVersion != $this->dbVersion) {
			// Upgrade tasks
			if ($this->installedVersion == 0) {
				$this->installedVersion++;
			}
			$this->SaveSettings();
		}
	}
	
	/**
	 * Initialise the plugin.
	 * Set up the admin interface if necessary
	 */
	public function Initialise() {
		
		$this->CheckVersion();
		
		if (is_admin()) {
			// WP Dashboard
			require_once('typekit-admin.php');
			$this->admin = new OM4_Typekit_Admin( $this );
		}
	}
	
	/**
	 * Saves the plugin's settings to the database
	 */
	public function SaveSettings() {
		$data = array_merge(array('version' => $this->installedVersion), array('settings' => $this->settings));
		update_option($this->optionName, $data);
	}
	
	/*
	 * Retrieve the Typekit embed code if the unique account id has been set
	 * @return string The typekit embed code if the unique account ID has been set, otherwise an empty string
	 */
	public function GetEmbedCode() {
		if ( '' != $id = $this->GetAccountID() ) {

		    switch( $this->GetEmbedMethod() ) {
                case self::EMBED_METHOD_CSS:
                    return sprintf( $this->embedcode_css, $id );
                    break;
                case self::EMBED_METHOD_JAVASCRIPT:
                    $async = $this->GetAsync() ? 'true' : 'false';
                    return sprintf( $this->embedcode_advanced, $id, $async );
                    break;
            }
		}
		return '';
	}
	
	/**
	 * Get the stored Typekit Account/Kit ID
	 * @return string The account ID if it has been specified, otherwise an empty string
	 */
	public function GetAccountID() {
		if (strlen($this->settings['id'])) return $this->settings['id'];
		return '';
	}

	/**
	 * Get the stored value for the async parameter.
	 *
	 * Defaults to true.
	 *
	 * @return bool
	 */
	public function GetAsync() {
		if ( isset( $this->settings['async'] ) && false === $this->settings['async'] ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get the stored value for the embed method.
	 *
	 * @return bool
	 */
	public function GetEmbedMethod() {
		if ( isset( $this->settings['method'] ) ) {
			return $this->settings['method'];
		} else {
		    // No embed method chosen, so default to the JS method
            return self::EMBED_METHOD_JAVASCRIPT;
		}
	}

	public function ParseKitID( $id ) {
        if ( preg_match( $this->kitid_regexp, $id, $matches ) && 2 == sizeof( $matches ) ) {
            $this->settings['id'] = $matches[0];
        } else {
            $this->settings['id'] = '';
        }
    }

    public function ParseEmbedMethod( $method ) {
	    if ( $method == self::EMBED_METHOD_JAVASCRIPT ) {
	        $this->settings['method'] = self::EMBED_METHOD_JAVASCRIPT;
        } else {
	        $this->settings['method'] = self::EMBED_METHOD_CSS;
	        $this->settings['async'] = '';
        }
    }

	
	/*
	 * Retrieve the custom CSS rules
	 * @return string The custom CSS rules
	 */
	public function GetCSSRules() {
		return $this->settings['css'];
	}
	
	/**
	 * Parse and save the custom css rules.
	 * The input is santized by stripping all HTML tags
	 * @param string CSS code
	 */
	public function SetCSSRules($code) {
		$this->settings['css'] = '';
		$code = strip_tags($code);
		if (strlen($code)) $this->settings['css'] = $code;
	}
	
	/**
	 * Display the plugin's javascript and css code in the site's header
	 */
	public function HeaderCode() {
?>

<!-- BEGIN Typekit Fonts for WordPress -->
<?php
		echo $this->GetEmbedCode();
		
		if (strlen($this->settings['css'])) {
		?>

<style type="text/css">
<?php echo $this->settings['css']; ?>
</style>
<?php
		}
?>

<!-- END Typekit Fonts for WordPress -->

<?php
	}

}

if(defined('ABSPATH') && defined('WPINC')) {
	if (!isset($GLOBALS["OM4_Typekit"])) {
		$GLOBALS["OM4_Typekit"] = new OM4_Typekit();
	}
}

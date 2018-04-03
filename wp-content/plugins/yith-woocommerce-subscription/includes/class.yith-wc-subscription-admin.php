<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWSBS_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH WooCommerce Subscription
 *
 * @class   YITH_WC_Subscription_Admin
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YITH_WC_Subscription_Admin' ) ) {

    class YITH_WC_Subscription_Admin {

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Subscription_Admin
         */

        protected static $instance;

        /**
         * @var $_panel Panel Object
         */
        protected $_panel;

        /**
         * @var $_premium string Premium tab template file name
         */
        protected $_premium = 'premium.php';

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = 'http://yithemes.com/themes/plugins/yith-woocommerce-subscription/';

        /**
         * @var string Panel page
         */
        protected $_panel_page = 'yith_woocommerce_subscription';

        /**
         * @var string Doc Url
         */
        public $doc_url = 'https://yithemes.com/docs-plugins/yith-woocommerce-subscription/';

        public $cpt_obj_subscriptions;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Subscription_Admin
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function __construct() {

            $this->create_menu_items();

            //Add action links
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWSBS_DIR . '/' . basename( YITH_YWSBS_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

            //custom styles and javascripts
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11);

            //product editor
            add_filter( 'product_type_options', array( $this, 'add_type_options' ) );

            //custom fields for single product
            add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_custom_fields_for_single_products' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_custom_fields_for_single_products' ), 10, 2 );

        }

	    /**
	     * Add a product type option in single product editor
	     *
	     * @access public
	     *
	     * @param $types
	     *
	     * @return array
	     * @since 1.0.0
	     */
        public function add_type_options( $types ) {
            $types['ywsbs_subscription'] = array(
                'id'            => '_ywsbs_subscription',
                'wrapper_class' => 'show_if_simple',
                'label'         => __( 'Subscription', 'yith-woocommerce-subscription' ),
                'description'   => __( 'Create a subscription for this product', 'yith-woocommerce-subscription' ),
                'default'       => 'no'
            );
            return $types;
        }

        /**
         * Add custom fields for single product
         *
         * @since   1.0.0
         * @author  Emanuela Castorina
         * @return  void
         */
        public function add_custom_fields_for_single_products() {
            global $thepostid;

	        $product = wc_get_product( $thepostid );

            echo '<div class="options_group">';

            $_ywsbs_price_is_per           = yit_get_prop( $product, '_ywsbs_price_is_per' );
            $_ywsbs_price_time_option      = yit_get_prop( $product, '_ywsbs_price_time_option' );
            $_ywsbs_max_length             = yit_get_prop( $product, '_ywsbs_max_length' );

            $max_lengths = ywsbs_get_max_length_period();
            ?>
            <h3 class="ywsbs_price_is_per"><?php _e('Subscription Settings','yith-woocommerce-subscription') ?></h3>
            <div class="options_group show_if_simple">
                <p class="form-field ywsbs_price_is_per">
                    <label for="_ywsbs_price_is_per"><?php _e( 'Price is per', 'yith-woocommerce-subscription' ); ?></label>
                    <input type="text" class="short" name="_ywsbs_price_is_per" id="_ywsbs_price_is_per" value="<?php echo esc_attr( $_ywsbs_price_is_per ); ?>" style="float: left; width:15%;" />
                    <select id="_ywsbs_price_time_option" name="_ywsbs_price_time_option" class="select" style="margin-left: 3px;">
                        <?php foreach ( ywsbs_get_time_options() as $key => $value ):
                            $select = selected( $_ywsbs_price_time_option, $key, false );
                            echo '<option value="' . $key . '" ' . $select . ' data-max="'.$max_lengths[$key].'">' . $value . '</option>';
                        endforeach;
                        ?>
                    </select>
                </p>

                <p class="form-field ywsbs_max_length">
                    <label for="_ywsbs_max_length"><?php _e( 'Max length:', 'yith-woocommerce-subscription' ); ?>
                        <a href="#" class="tips" data-tip="<?php _e( 'Leave it empty for unlimited subscription', 'yith-woocommerce-subscription' ) ?>"> [?]</a></label>
                    <input type="text" class="short" name="_ywsbs_max_length" id="_ywsbs_max_length" value="<?php echo esc_attr( $_ywsbs_max_length ); ?>" style="float: left; width:15%; " />
                    <span class="description"><span><?php echo $time_opt = ( $_ywsbs_price_time_option ) ? $_ywsbs_price_time_option : 'days'; ?></span> <?php printf(__('(Max: <span class="max-l">%d</span>)', 'yith-woocommerce-subscription'), $max_lengths[$time_opt]) ?></span>
                </p>

            </div>
            </div>
<?php

        }

	    /**
	     * Save custom fields for single product
	     *
	     * @since   1.0.0
	     * @author  Emanuela Castorina
	     *
	     * @param $post_id
	     * @param $post
	     *
	     * @return void
	     */
	    public function save_custom_fields_for_single_products( $post_id, $post ) {

		    $product = wc_get_product( $post_id );
		    $args    = array();

		    $args['_ywsbs_subscription'] = isset( $_POST['_ywsbs_subscription'] ) ? 'yes' : 'no';

		    if ( isset( $_POST['_ywsbs_price_is_per'] ) ) {
			    $args['_ywsbs_price_is_per'] = $_POST['_ywsbs_price_is_per'];
		    }


		    if ( isset( $_POST['_ywsbs_price_time_option'] ) ) {
			    $args['_ywsbs_price_time_option'] = $_POST['_ywsbs_price_time_option'];
		    }

		    if ( isset( $_POST['_ywsbs_price_time_option'] ) && isset( $_POST['_ywsbs_max_length'] ) ) {
			    $max_length                = ywsbs_validate_max_length( $_POST['_ywsbs_max_length'], $_POST['_ywsbs_price_time_option'] );
			    $args['_ywsbs_max_length'] = $max_length;
		    }

		    if ( $args ) {
			    yit_save_prop( $product, $args );
		    }

	    }


        /**
         * Enqueue styles and scripts
         *
         * @access public
         * @return void
         * @since 1.0.0
         */
        public function enqueue_styles_scripts() {
            wp_enqueue_style( 'yith_ywsbs_backend', YITH_YWSBS_ASSETS_URL . '/css/backend.css', YITH_YWSBS_VERSION );
            wp_enqueue_script( 'yith_ywsbs_admin', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-admin' . YITH_YWSBS_SUFFIX . '.js', array( 'jquery' ), YITH_YWSBS_VERSION, true );
            wp_enqueue_script( 'jquery-blockui', YITH_YWSBS_ASSETS_URL . '/js/jquery.blockUI.min.js', array( 'jquery' ), false, true );
            wp_localize_script( 'yith_ywsbs_admin', 'yith_ywsbs_admin', array(
                'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
                'block_loader'            => apply_filters( 'yith_ywsbs_block_loader_admin', YITH_YWSBS_ASSETS_URL . '/images/block-loader.gif' ),
            )
            );
        }



        /**
         * Create Menu Items
         *
         * Print admin menu items
         *
         * @since  1.0
         * @author Emanuela Castorina
         */

        private function create_menu_items() {

            // Add a panel under YITH Plugins tab
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
            add_action( 'yith_ywsbs_subscriptions_tab', array( $this, 'subscriptions_tab' ) );
            add_action( 'yith_ywsbs_premium_tab', array( $this, 'premium_tab' ) );
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      /YIT_Plugin_Panel_WooCommerce class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */

        public function register_panel() {

            if ( !empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = array(
                'general'   => __( 'Settings', 'yith-woocommerce-subscription' ),
                'subscriptions'     => __( 'Subscriptions', 'yith-woocommerce-subscription' ),
                'premium' => __( 'Premium Version',  'yith-woocommerce-subscription' )
            );

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => __( 'Subscription', 'yith-woocommerce-subscription' ),
                'menu_title'       => __( 'Subscription', 'yith-woocommerce-subscription' ),
                'capability'       => 'manage_options',
                'parent'           => '',
                'parent_page'      => 'yit_plugin_panel',
                'links'            => $this->get_panel_sidebar_link(),
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_YWSBS_DIR . '/plugin-options'
            );

            /* === Fixed: not updated theme  === */
            if ( !class_exists( 'YIT_Plugin_Panel' ) ) {
                require_once( YITH_YWSBS_DIR.'/plugin-fw/lib/yit-plugin-panel.php' );
            }
            if ( !class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( YITH_YWSBS_DIR.'/plugin-fw/lib/yit-plugin-panel-wc.php' );
            }


            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );


        }

        


        /**
         * Premium Tab Template
         *
         * Load the premium tab template on admin page
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */

        public function premium_tab() {
            $premium_tab_template = YITH_YWSBS_TEMPLATE_PATH . '/admin/' . $this->_premium;

            if ( file_exists( $premium_tab_template ) ) {
                include_once( $premium_tab_template );
            }
        }


        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed
         * @use      plugin_action_links_{$plugin_file_name}
         */

        public function action_links( $links ) {

            $links[] = '<a href="' . admin_url( "admin.php?page={$this->_panel_page}" ) . '">' . __( 'Settings', 'yith-woocommerce-subscription' ) . '</a>';
            if ( defined( 'YITH_YWSBS_FREE_INIT' ) ) {
                $links[] = '<a href="' . $this->get_premium_landing_uri() . '" target="_blank">' . __( 'Premium Version', 'yith-woocommerce-subscription' ) . '</a>';
            }

            return $links;
        }


        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      plugin_row_meta
         */

        public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

            if ( defined( 'YITH_YWSBS_INIT' ) && YITH_YWSBS_INIT == $plugin_file ) {
                $plugin_meta[] = '<a href="' . $this->doc_url . '" target="_blank">' . __( 'Plugin Documentation', 'yith-woocommerce-subscription' ) . '</a>';
            }
            return $plugin_meta;
        }



        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri(){
            return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing . '?refer_id=1030585';
        }

        /**
         * Subscriptions List Table
         *
         * Load the subscriptions on admin page
         *
         * @return   void
         * @since    1.0
         * @author   Emanuela Castorina
         */
        public function subscriptions_tab() {
            $this->cpt_obj_subscriptions = new YITH_YWSBS_Subscriptions_List_Table();

            $subscriptions_tab = YITH_YWSBS_TEMPLATE_PATH . '/admin/subscriptions-tab.php';

            if ( file_exists( $subscriptions_tab ) ) {
                include_once( $subscriptions_tab );
            }
        }


        /**
         * Add the widget of "Important Links" inside the admin sidebar
         * @return array
         */
        public function get_panel_sidebar_link() {
            return array(
                array(
                    'url'   => $this->doc_url,
                    'title' => __( 'Plugin Documentation', 'yith-woocommerce-subscription' )
                ),
                array(
                    'url'   => $this->_premium_landing,
                    'title' => __( 'Discovery premium version', 'yith-woocommerce-subscription' )
                ),
                array(
                    'url'   => $this->_premium_landing.'#tab-free_vs_premium_tab',
                    'title' => __( 'Free Vs Premium', 'yith-woocommerce-subscription' )
                ),
                array(
                    'url'   => 'http://plugins.yithemes.com/yith-woocommerce-subscription/product/diablo-3/?preview',
                    'title' => __( 'Premium live demo', 'yith-woocommerce-subscription' )
                ),
                array(
                    'url'   => 'https://wordpress.org/support/plugin/yith-woocommerce-subscription',
                    'title' => __( 'WordPress support forum', 'yith-woocommerce-subscription' )
                ),
                array(
                    'url'   => $this->doc_url . '/changelog-free',
                    'title' => sprintf( __( 'Changelog (current version %s)', 'yith-woocommerce-subscription' ), YITH_YWSBS_VERSION )
                ),
            );
        }

    }
}

/**
 * Unique access to instance of YITH_WC_Subscription_Admin class
 *
 * @return \YITH_WC_Subscription_Admin
 */
function YITH_WC_Subscription_Admin() {
    return YITH_WC_Subscription_Admin::get_instance();
}

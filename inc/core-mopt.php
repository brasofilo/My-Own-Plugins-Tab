<?php
/**
 * Main class 
 * 
 * @plugin My Own Plugins Tab
 * 
 */

# Busted!
!defined( 'ABSPATH' ) AND exit(
        "<pre>Hi there! I'm just part of a plugin, 
            <h1>&iquest;what exactly are you looking for?" );

class B5F_My_Own_Plugins_Tab
{
	/**
	 * Plugin instance.
	 * @type object
	 */
	protected static $instance = NULL;

	/**
	 * URL to this plugin's directory.
	 * @type string
	 */
	public $plugin_url;

	/**
	 * Path to this plugin's directory.
	 * @type string
	 */
	public $plugin_path;
    
    /**
     * Hold the plugin settings from SettingsClass
     * @var array
     */
    private $options;
    
	/**
	 * Total number of plugins to jQuery fix.
	 * @type string
	 */
	public $all_count;
    
    
	/**
	 * Number of plugins of user
	 * @type integer
	 */
    private $my_plugins_count;
	
	/**
	 * Div id to be fixed in jQuery/CSS
	 * @type integer
	 */
    private $fix_div;
    
    /**
     * Authors to filter
     */
    private $authors = array();
	
    /**
	 * Constructor. Intentionally left empty and public.
	 *
	 * @see plugin_setup()
	 * @since 2012.09.12
	 */
	public function __construct() {}
		

	/**
	 * Access this plugin's working instance.
	 *
	 * @wp-hook plugins_loaded
	 * @since   2012.09.13
	 * @return  object of this class
	 */
	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
	}

    
	/**
	 * Plugin start
	 *
	 * @wp-hook plugins_loaded
	 * @return  void
	 */
	public function plugin_setup()
	{
        global $pagenow;
        $check_pages = array( 'plugins.php', 'update.php', 'update-core.php' );
        if( !in_array( $pagenow, $check_pages ) )
            return;
        
        
        # Basics, main folder one level up
		$this->plugin_url    = plugins_url( '/', dirname( __FILE__ ) );
		$this->plugin_path   = plugin_dir_path( dirname( __FILE__ ) ); 
        
        
        # Plugin settings
        include_once __DIR__ . '/settings-mopt.php';
        $settings = new B5F_MOPT_Settings();
        
        
        # Get plugin options
        $this->options = $settings->get_options();
        
        
        # Plugin is active in MS **and** is marked as do not show in subsites
        if( !is_network_admin() && is_plugin_active_for_network(plugin_basename( B5F_MOPT_FILE )) && !(boolean)$this->options['subsites'] ) 
            return;
        
        $this->whose_plugins();

        
        # Upper tab
        $hook_views = is_network_admin() ? '-network' : '';
		add_filter( "views_plugins$hook_views", array( $this, 'add_row_links' ) );

        
        # Add icon to our plugins
        $hook_actions = is_network_admin() ? 'network_admin_' : '';
		add_action( "{$hook_actions}plugin_action_links", array( $this, 'add_plugin_icon' ), 1, 4 );
        
        
        # Do, do the bugaloo
        add_action( 'load-plugins.php', array( $this, 'filter_our_plugins' ) );
        
        
        # Self hosted updates
        include_once __DIR__ . '/plugin-update-dispatch.php';
        $icon = !empty( $this->options['icon'] )
            ? $this->strip_slashes_recursive( $this->options['icon'] ) : '&hearts;';
        new B5F_General_Updater_and_Plugin_Love(array( 
            'repo' => 'My-Own-Plugins-Tab', 
            'user' => 'brasofilo',
            'plugin_file' => B5F_MOPT_FILE,
            'donate_text' => 'Buy me a beer',
            'donate_icon' => "<span  class='mopt-icon'>$icon </span>",
            'donate_link' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=JNJXKWBYM9JP6&lc=US&item_name=Rodolfo%20Buaiz&item_number=Plugin%20donation&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted'
        ));	

   }

   
    
	/**
	 * Add icon if plugin is ours.
	 *
	 * @wp-hook *_plugin_action_links
	 * @return array
	 */
	public function add_plugin_icon( $actions, $plugin_file, $plugin_data, $context )
	{
        $our_screen = isset( $_GET['plugin_status'] ) 
            && in_array( $_GET['plugin_status'], array('my_own_plugins','not_mine' ) );
        
        if( !$this->is_ours( $plugin_data['Author'] ) || $our_screen )
            return $actions;
        
        $in = !empty( $this->options['icon'] )
            ? $this->strip_slashes_recursive( $this->options['icon'] ) : '&#xf113;';
        $in = '<span title="Mine" class="mopt-icon">' . $in . '</span>';
        array_unshift( $actions, $in );
		return $actions;
	}

    
    /**
     * 
	 * Add links to the row All|Active|Inactive|etc
     * 
     * @wp-hook views_plugins
	 */
	public function add_row_links( $tabs )
	{
        # Our plugins
        if( !empty( $this->options['authors'] ) )
        {
            $my_count = !empty( $this->my_plugins_count ) 
                ? ' ('.$this->my_plugins_count.')' 
                : '';
            $url = 'plugins.php?plugin_status=my_own_plugins';
            $tabs['my_own_plugins'] = sprintf(
                '<a href="%s">%s%s</a>',
                is_network_admin() ? network_admin_url( $url ) : admin_url( $url ),
                __( 'Mine' ),
                $my_count
            );
        }
        
        # Show other folks plugins separetedly 
        if( $this->options['others'] )
        {
            $their_count = !empty( $this->my_plugins_count ) 
                ? ' ('.($this->all_count - $this->my_plugins_count).')' 
                : '';
            $url = 'plugins.php?plugin_status=not_mine';
            $tabs['not_mine'] = sprintf(
                '<a href="%s">%s%s</a>',
                is_network_admin() ? network_admin_url( $url ) : admin_url( $url ),
                __( 'Not mine' ),
                $their_count
            );
        }
        
		return $tabs;
	}


    /**
     * Run on plugins.php screen
     * 
     * @wp-hook load-$pagenow
     */
    public function filter_our_plugins()
    {
        add_filter( 'all_plugins', array( $this, 'count_filter_plugins' ) );
        
        if( 
            isset( $_GET['plugin_status'] ) 
            && in_array( $_GET['plugin_status'], array('my_own_plugins','not_mine' ) ) 
            )
        {
            $this->fix_div = $_GET['plugin_status'];
            add_action( 'admin_footer', array( $this, 'fix_css' ) );
            add_filter( 'mtt_disable_plugins_coloring', '__return_false' );
        }
    }
    
    
   /**
    * Count plugins and filter if necessary
    *
    * @return  array    List of plugins
    * @wp-hook all_plugins
    */
   public function count_filter_plugins( $plugins )
   {
       $this->all_count = count( $plugins );
       $count = 0;
       foreach ( $plugins as $name => $data ) 
       { 
           $ours = $this->is_ours( $data['Author'] );
            if( $ours )
                $count++;
            if( isset( $_GET['plugin_status'] ) )
            {
                switch( $_GET['plugin_status'] )
                {
                    case 'my_own_plugins':
                        if( !$ours )
                           unset( $plugins[ $name ] );
                    break;
                    case 'not_mine':
                        if( $ours )
                           unset( $plugins[ $name ] );
                    break;
                }
            }
                   
       }
       $this->my_plugins_count = $count;
       return $plugins;
   }
   
   
   
    /**
     * Swap current tab classes
     * 
     * @return void
     */
    public function fix_css()
    {
        $total = '(' . $this->all_count . ')';
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {   
                $('li.all a').removeClass('current');
                $('li.<?php echo $this->fix_div; ?> a').addClass('current');
                $('li.all .count').text('<?php echo $total; ?>');
            });             
        </script>
        <?php
    }

    
   
   /**
    * Build authors array
    */
    private function whose_plugins()
    {
        $authors = isset( $this->options['authors'] ) ? $this->options['authors'] : false;

        if( $authors )
        {
            $authors = str_replace( ' ', '', $authors );
            $this->authors = explode( ',', $authors );
        }
    }

    
    /**
     * Breaks a name by whitespaces (after making it lowercase)
     * and uses this as an array to compare with our list
     * 
     * http://stackoverflow.com/a/10693696/1287812
     * 
     * @param  string   $author
     * @return boolean  Found author
     */
    private function is_ours( $author )
    {
        $haystacks = preg_split( "/\b/", strtolower( $author ) );
        $intersect = array_intersect( $haystacks, $this->authors );
        return ( count( $intersect ) > 0 );
    }


    /**
     * Prepares text output
     * 
     * @param string $str
     * @return string
     */
    private function strip_slashes_recursive( $str )
    {
        $str = html_entity_decode( stripslashes($str) );
        return $str;
    }
    
    
    

}
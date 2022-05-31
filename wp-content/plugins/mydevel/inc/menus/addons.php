<?php

if ( ! defined('ABSPATH')) exit;  // if direct access

class WPMD_Addons_Submenu {

	private $parent_menu_slug;
	private $menu_slug;
	private $menu_page;

	public function __construct( $parent_menu_slug ) {
		$this->parent_menu_slug = $parent_menu_slug;
		$this->menu_slug = 'devel';
		$this->menu_page = new MyDevel_Addons_Menu_Page();
	}

	public function init() {
		$this->add_submenu();
		add_action(
			'admin_print_styles-amp_page_' . $this->menu_slug,
			array( $this, 'amp_options_styles' )
		);
	}

	private function add_submenu() {
		add_submenu_page(
			$this->parent_menu_slug,
			__( 'AMP Analytics Options', 'amp' ),
			__( 'Analytics', 'amp' ),
			'manage_options',
			$this->menu_slug,
			array( $this->menu_page, 'render' )
		);

	}

	public function amp_options_styles() {
		?>
		<style>
			.analytics-data-container #delete {
				background: red;
				border-color: red;
				text-shadow: 0 0 0;
				margin: 0 5px;
			}
			.amp-analytics-options.notice {
				width: 300px;
			}
		</style>;

		<?php
	}

}



class MyDevel_Addons_Menu_Page {

	public function addons_data($addons_data = array()){

  		$addons_data_new = array(
  			'verified-users'=>array(	'title'=>'Verified users',
  										'version'=>'1.0.0',
  										'price'=>'19',
  										'content'=>'',
  										'item_link'=>'https://www.pickplugins.com/item/user-profile-verified-users/',
  										'thumb'=>DP_PLUGIN_URL.'assets/admin/images/addons/verified-users.png',
  			),


  			'user-directory'=>array(	'title'=>'User directory',
  										'version'=>'1.0.0',
  										'price'=>'19',
  										'content'=>'',
  										'item_link'=>'https://www.pickplugins.com/item/user-profile-user-directory/',
  										'thumb'=>DP_PLUGIN_URL.'assets/admin/images/addons/user-directory.png',
  			),

  			'message'=>array(	'title'=>'Message',
  										'version'=>'1.0.0',
  										'price'=>'0',
  										'content'=>'',
  										'item_link'=>'#',
  										'thumb'=>DP_PLUGIN_URL.'assets/admin/images/addons/message.png',
  			),
  		);
//dpl($addons_data_new);
//d($this);
  		$addons_data = array_merge($addons_data_new,$addons_data);

  		//$addons_data = apply_filters('WPMD_filters_addons_data', $addons_data);

  		return $addons_data;


  		}

  public function qa_addons_html(){

    		$html = '';

    		$addons_data = $this->addons_data();

    		foreach($addons_data as $key=>$values){

    			$html.= '<div class="single '.$key.'">';
    			$html.= '<div class="thumb"><a href="'.$values['item_link'].'"><img src="'.$values['thumb'].'" /></a></div>';
    			$html.= '<div class="title"><a href="'.$values['item_link'].'">'.$values['title'].'</a></div>';
    			$html.= '<div class="content">'.$values['content'].'</div>';
    			$html.= '<div class="meta version"><b>'.__('Version:', DP_TEXTDOMAIN).'</b> '.$values['version'].'</div>';

    			if($values['price']==0){

    				$price = __('Free', DP_TEXTDOMAIN);
    				}
    			else{
    				$price = '$'.$values['price'];

    				}
    			$html.= '<div class="meta price"><b>'.__('Price:', DP_TEXTDOMAIN).'</b> '.$price.'</div>';
    			$html.= '<div class="meta download"><a href="'.$values['item_link'].'">'.__('Download', DP_TEXTDOMAIN).'</a></div>';
    			$html.= '</div>';
    		}

    		$html.= '';

    		return $html;
  }

	public function render() {
		?>
    <div class="wrap">
    	<div id="icon-tools" class="icon32"><br></div>
      <?php echo "<h2>".sprintf(__('%s - Addons', DP_TEXTDOMAIN), 'Devel')."</h2>";?>
    		<div class="mydevel-addons">
    			<?php
					dpm('blablabla');
          print $this->qa_addons_html();
          ?>
        </div>
    </div>
		<?php
	}
}


class class_mydevel_addons{

  const ICON_BASE64_SVG = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PHN2ZyB3aWR0aD0iNjJweCIgaGVpZ2h0PSI2MnB4IiB2aWV3Qm94PSIwIDAgNjIgNjIiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+ICAgICAgICA8dGl0bGU+QU1QLUJyYW5kLUJsYWNrLUljb248L3RpdGxlPiAgICA8ZGVzYz5DcmVhdGVkIHdpdGggU2tldGNoLjwvZGVzYz4gICAgPGRlZnM+PC9kZWZzPiAgICA8ZyBpZD0iYW1wLWxvZ28taW50ZXJuYWwtc2l0ZSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+ICAgICAgICA8ZyBpZD0iQU1QLUJyYW5kLUJsYWNrLUljb24iIGZpbGw9IiMwMDAwMDAiPiAgICAgICAgICAgIDxwYXRoIGQ9Ik00MS42Mjg4NjY3LDI4LjE2MTQzMzMgTDI4LjYyNDM2NjcsNDkuODAzNTY2NyBMMjYuMjY4MzY2Nyw0OS44MDM1NjY3IEwyOC41OTc1LDM1LjcwMTY2NjcgTDIxLjM4MzgsMzUuNzEwOTY2NyBDMjEuMzgzOCwzNS43MTA5NjY3IDIxLjMxNTYsMzUuNzEzMDMzMyAyMS4yODM1NjY3LDM1LjcxMzAzMzMgQzIwLjYzMzYsMzUuNzEzMDMzMyAyMC4xMDc2MzMzLDM1LjE4NzA2NjcgMjAuMTA3NjMzMywzNC41MzcxIEMyMC4xMDc2MzMzLDM0LjI1ODEgMjAuMzY3LDMzLjc4NTg2NjcgMjAuMzY3LDMzLjc4NTg2NjcgTDMzLjMyOTEzMzMsMTIuMTY5NTY2NyBMMzUuNzI0NCwxMi4xNzk5IEwzMy4zMzYzNjY3LDI2LjMwMzUgTDQwLjU4NzI2NjcsMjYuMjk0MiBDNDAuNTg3MjY2NywyNi4yOTQyIDQwLjY2NDc2NjcsMjYuMjkzMTY2NyA0MC43MDE5NjY3LDI2LjI5MzE2NjcgQzQxLjM1MTkzMzMsMjYuMjkzMTY2NyA0MS44Nzc5LDI2LjgxOTEzMzMgNDEuODc3OSwyNy40NjkxIEM0MS44Nzc5LDI3LjczMjYgNDEuNzc0NTY2NywyNy45NjQwNjY3IDQxLjYyNzgzMzMsMjguMTYwNCBMNDEuNjI4ODY2NywyOC4xNjE0MzMzIFogTTMxLDAgQzEzLjg3ODcsMCAwLDEzLjg3OTczMzMgMCwzMSBDMCw0OC4xMjEzIDEzLjg3ODcsNjIgMzEsNjIgQzQ4LjEyMDI2NjcsNjIgNjIsNDguMTIxMyA2MiwzMSBDNjIsMTMuODc5NzMzMyA0OC4xMjAyNjY3LDAgMzEsMCBMMzEsMCBaIiBpZD0iRmlsbC0xIj48L3BhdGg+ICAgICAgICA8L2c+ICAgIDwvZz48L3N2Zz4=';

  private $menu_page;
	private $menu_slug;

  public function __construct() {

    $this->menu_page = new MyDevel_Addons_Menu_Page();
		$this->menu_slug = 'devel';
    $this->parent_slug = 'admin.php?page=devel';
    //add_action('admin_init', array( $this, 'register_settings' ));
    add_action('admin_menu', array( $this, 'menu_items' ));

    // add_action(
		// 	'admin_print_styles-amp_page_' . $this->menu_slug,
		// 	array( $this, 'amp_options_styles' )
		// );


  }

  public function amp_options_styles() {
    ?>
    <style>
      .analytics-data-container #delete {
        background: red;
        border-color: red;
        text-shadow: 0 0 0;
        margin: 0 5px;
      }
      .amp-analytics-options.notice {
        width: 300px;
      }
    </style>;

    <?php
  }


  public function menu_items() {
		// add_menu_page(
		// 	__( 'Devel Addons', 'amp' ),
		// 	__( 'Devel', 'amp' ),
		// 	'manage_options',
		// 	$this->menu_slug,
		// 	array( $this->menu_page, 'render' ),
		// 	self::ICON_BASE64_SVG
		// );
    //
    add_submenu_page(
			$this->menu_slug,
			__( 'Devel Addons', 'amp' ),
			__( 'Addons', 'amp' ),
			'manage_options',
			'devel-addons',
			array( $this->menu_page, 'render' )
		);

    //
    //
    // add_menu_page('My Custom Page', 'My Custom Page', 'manage_options', 'my-top-level-slug');
    // add_submenu_page( 'my-top-level-slug', 'My Custom Page', 'My Custom Page',
    //     'manage_options', 'my-top-level-slug');
    // add_submenu_page( 'my-top-level-slug', 'My Custom Submenu Page', 'My Custom Submenu Page',
    //     'manage_options', 'my-secondary-slug');

		$this->remove_toplevel_menu_item();
	}


  // Helper function to avoid having the top-level menu as
	// the first menu item
	function remove_toplevel_menu_item() {
		// global $submenu;
		// if ( isset( $submenu['devel'][0] ) ) {
		// 	unset( $submenu['devel'][0] );
		// }
	}
}

new class_mydevel_addons();

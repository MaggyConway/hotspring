<?php

if ( ! defined('ABSPATH')) exit;  // if direct access

class md_settings_menu_page {

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

		<div class="header-wrap">
         <h1>Ajax Load More <span>3.2.0</span>
         <em>A powerful plugin to add infinite scroll functionality to your website.</em>
         </h1><div id="abb-settings-changed" class="notice notice-warning" style="display: none;"><p><strong>You've changed some settings.</strong> Make sure you <a href="#" title="Save settings" class="abb-settings-changed-save">save</a> them before you leave.</p></div>
      </div>

	   <div class="cnkt-main">

   	   	   	<div class="group share-alm" style="display: none !important;">
				<div class="dotted">
      	   	<h2 style="padding: 0; margin: 0 0 20px;">
	      	   	<img draggable="false" class="emoji" alt="👋" src="https://s.w.org/images/core/emoji/2.2.1/svg/1f44b.svg"> &nbsp;Thanks for installing Ajax Load More 3.0!	      	   </h2>
	      	   <p>Version 3 is a big step forward for Ajax Load More and I really hope you like the changes and new features - be sure to check out the new <a href="admin.php?page=ajax-load-more-extensions">Extensions</a> section for 1-click installs of all currently available extensions for Ajax Load More.</p>
				</div>
   	   	<p>Please consider helping <a href="https://twitter.com/KaptonKaos" target="_blank">me</a> widen the reach of Ajax Load More by sharing with your networks.</p>

				<ul class="share">
					<li class="twitter">
						<a target="blank" title="Share on Twitter" href="//twitter.com/home?status=I'm infinite scrolling with Ajax Load More for %23WordPress - https://connekthq.com/plugins/ajax-load-more/" class="share-twitter"><i class="fa fa-twitter"></i> Twitter</a>
					</li>
					<li class="facebook">
						<a target="blank" title="Share on Facebook" href="//facebook.com/share.php?u=https://connekthq.com/plugins/ajax-load-more/" class="share-facebook"><i class="fa fa-facebook"></i> Facebook</a>
					</li>
				</ul>

            <div class="clear"></div>

            <a href="javascript: void(0);" class="dismiss" id="alm_dismiss_sharing" title="Don't show me this again!">×</a>

	   	</div>


	   	<div class="group">
   	   	      	      			<form action="options.php" method="post" id="alm_OptionsForm">
					<input type="hidden" name="option_page" value="alm-setting-group"><input type="hidden" name="action" value="update"><input type="hidden" id="_wpnonce" name="_wpnonce" value="7fe2a9fa29"><input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=ajax-load-more"><h2>Global Settings</h2>
<p>Customize the user experience of Ajax Load More by updating the fields below.</p><table class="form-table"><tbody><tr><th scope="row">Container Type</th><td><input type="radio" id="_alm_container_type_one" name="alm_settings[_alm_container_type]" value="1" checked="checked"><label for="_alm_container_type_one">&lt;ul&gt; <span>&lt;!-- Ajax Posts Here --&gt;</span> &lt;/ul&gt;</label><br><input type="radio" id="_alm_container_type_two" name="alm_settings[_alm_container_type]" value="2"><label for="_alm_container_type_two">&lt;div&gt; <span>&lt;!-- Ajax Posts Here --&gt;</span> &lt;/div&gt;</label><label style="cursor: default !important"><span style="display:block">You can modify the container type when building a shortcode.</span></label></td></tr><tr><th scope="row">Container Classes</th><td><label for="alm_settings[_alm_classname]">Add classes to Ajax Load More container - classes are applied globally and will appear with every instance of Ajax Load More. <span style="display:block">You can also add classes when building a shortcode.</span></label><br><input type="text" id="alm_settings[_alm_classname]" name="alm_settings[_alm_classname]" value="" placeholder="posts listing etc..."> </td></tr><tr><th scope="row">Disable CSS</th><td><input type="hidden" name="alm_settings[_alm_disable_css]" value="0"><input type="checkbox" id="alm_disable_css_input" name="alm_settings[_alm_disable_css]" value="1"><label for="alm_disable_css_input">I want to use my own CSS styles.<br><span style="display:block;"><i class="fa fa-file-text-o"></i> &nbsp;<a href="http://caldera/wp-content/plugins/ajax-load-more/core/dist/css/ajax-load-more.css" target="blank">View Ajax Load More CSS</a></span></label></td></tr><tr><th scope="row">Button/Loading Style</th><td><label for="s2id_autogen1">Select an Ajax loading style - you can choose between a <strong>Button</strong> or <strong>Infinite Scroll</strong>.<br><span style="display:block">Selecting an Infinite Scroll style will remove the click interaction and load content on scroll <u>only</u>.</span></label><div class="select2-container" id="s2id_alm_settings_btn_color"><a href="javascript:void(0)" onclick="return false;" class="select2-choice" tabindex="-1">   <span>Default</span><abbr class="select2-search-choice-close" style="display:none;"></abbr>   <div><b></b></div></a><input class="select2-focusser select2-offscreen" type="text" id="s2id_autogen1"><div class="select2-drop select2-with-searchbox" style="display:none">   <div class="select2-search">       <input type="text" autocomplete="off" class="select2-input">   </div>   <ul class="select2-results">   </ul></div></div><select id="alm_settings_btn_color" name="alm_settings[_alm_btn_color]" class="select2-offscreen" tabindex="-1"><optgroup label="Button"><option value="default" class="alm-color default" selected="selected">Default</option><option value="blue" class="alm-color blue">Blue</option><option value="green" class="alm-color green">Green</option><option value="purple" class="alm-color purple">Purple</option><option value="grey" class="alm-color grey">Grey</option></optgroup><optgroup label="Infinite Scroll (No Button)"><option value="infinite classic" class="infinite classic">Classic</option><option value="infinite skype" class="infinite skype">Skype</option><option value="infinite ring" class="infinite ring">Circle Fill</option><option value="infinite fading-blocks" class="infinite fading-blocks">Fading Blocks</option><option value="infinite fading-circles" class="infinite fading-circles">Fading Circles</option><option value="infinite chasing-arrows" class="infinite chasing-arrows">Chasing Arrows</option></optgroup></select><div class="clear"></div><div class="ajax-load-more-wrap core default"><span>Preview</span><button class="alm-load-more-btn loading" disabled="disabled">Older Posts</button></div></td></tr><tr><th scope="row">Button Classes</th><td><label for="alm_settings[_alm_btn_classname]">Add classes to your <strong>Load More</strong> button.</label><input type="text" class="btn-classes" id="alm_settings[_alm_btn_classname]" name="alm_settings[_alm_btn_classname]" value="" placeholder="button rounded listing etc...">     <script>

		// Check if Disable CSS  === true
		if(jQuery('input#alm_disable_css_input').is(":checked")){
	      jQuery('select#alm_settings_btn_color').parent().parent().hide(); // Hide button color
         //jQuery('input.btn-classes').parent().parent().hide(); // Hide Button Classes
    	}
    	jQuery('input#alm_disable_css_input').change(function() {
    		var el = jQuery(this);
	      if(el.is(":checked")) {
	      	el.parent().parent('tr').next('tr').hide(); // Hide button color
	      	//el.parent().parent('tr').next('tr').next('tr').hide(); // Hide Button Classes
	      }else{
	      	el.parent().parent('tr').next('tr').show(); // show button color
	      	//el.parent().parent('tr').next('tr').next('tr').show(); // show Button Classes
	      }
	   });

    </script>
	</td></tr><tr><th scope="row">Ajax Security</th><td><input type="hidden" name="alm_settings[_alm_nonce_security]" value="0"><input type="checkbox" name="alm_settings[_alm_nonce_security]" id="_alm_nonce_security" value="1"><label for="_alm_nonce_security">Enable <a href="https://codex.wordpress.org/WordPress_Nonces" target="_blank">WP nonce</a> verification to help protect URLs against certain types of misuse, malicious or otherwise on each Ajax Load More query.</label></td></tr><tr><th scope="row">Top of Page</th><td><input type="hidden" name="alm_settings[_alm_scroll_top]" value="0"><input type="checkbox" name="alm_settings[_alm_scroll_top]" id="_alm_scroll_top" value="1"><label for="_alm_scroll_top">On initial page load, move the user's browser window to the top of the screen.<span style="display:block">This <u>may</u> help prevent the loading of unnecessary posts.</span></label></td></tr></tbody></table><h2>Admin Settings</h2>
<p>The following settings affect the WordPress admin area only.</p><table class="form-table"><tbody><tr><th scope="row">Dynamic Content</th><td><input type="hidden" name="alm_settings[_alm_disable_dynamic]" value="0"><input type="checkbox" name="alm_settings[_alm_disable_dynamic]" id="_alm_disable_dynamic" value="1"><label for="_alm_disable_dynamic">Disable dynamic population of categories, tags and authors in the Shortcode Builder.<span style="display:block">Recommended if you have an extraordinary number of categories, tags and/or authors.</span></label></td></tr><tr><th scope="row">Editor Button</th><td><input type="hidden" name="alm_settings[_alm_hide_btn]" value="0"><input type="checkbox" id="alm_hide_btn" name="alm_settings[_alm_hide_btn]" value="1"><label for="alm_hide_btn">Hide shortcode button in WYSIWYG editor.</label></td></tr><tr><th scope="row">Error Notices</th><td><input type="hidden" name="alm_settings[_alm_error_notices]" value="0"><input type="checkbox" name="alm_settings[_alm_error_notices]" id="_alm_error_notices" value="1" checked="checked"><label for="_alm_error_notices">Display error messaging regarding repeater template updates in the browser console.</label></td></tr></tbody></table>					<div class="save-in-progress"></div>
   			</form>

	   	</div>
	   </div>
	   <div class="cnkt-sidebar">
						<div class="cta padding-bottom resources">
	<h3>Resources</h3>
	<div class="cta-inner">
   	<ul>
   		<li><a target="blank" href="https://connekthq.com/plugins/ajax-load-more/"><i class="fa fa-mouse-pointer"></i> Ajax Load More Demo Site</a></li>
   		<li><a target="blank" href="https://connekthq.com/plugins/ajax-load-more/docs/"><i class="fa fa-pencil"></i> Documentation</a></li>
   		<li><a target="blank" href="http://wordpress.org/support/plugin/ajax-load-more"><i class="fa fa-question-circle"></i> Plugin Support and Issues</a></li>
   		<li><a target="blank" href="https://wordpress.org/support/view/plugin-reviews/ajax-load-more"><i class="fa fa-star"></i> Reviews</a></li>
   		<li><a target="blank" href="http://twitter.com/ajaxloadmore"><i class="fa fa-twitter"></i> Twitter</a></li>
   		<li><a target="blank" href="http://facebook.com/ajaxloadmore"><i class="fa fa-facebook"></i> Facebook</a></li>
   		<li><a target="blank" href="https://github.com/dcooney/wordpress-ajax-load-more"><i class="fa fa-github"></i> Github</a></li>
   	</ul>
   </div>
	<a href="https://wordpress.org/plugins/ajax-load-more/" target="blank" class="visit"><i class="fa fa-wordpress"></i> WordPress Repository</a>
</div>						<div class="cta dyk">
	<h3>Did You Know?</h3>

		<div class="cta-inner padding-bottom">
   	<img src="http://caldera/wp-content/plugins/ajax-load-more/admin/img/add-ons/cache-add-on.jpg"><br>
   	<p class="addon-intro">You can cache your server requests with Ajax Load More!</p>
   	<p>The <a target="blank" style="font-weight: 600;" href="https://connekthq.com/plugins/ajax-load-more/add-ons/cache/?utm_source=WP%20Admin&amp;utm_medium=ALM%20DYK&amp;utm_campaign=Cache">Cache</a> add-on creates static HTML files of Ajax Load More requests then delivers those static files to your visitors.</p>
   	<a target="blank" class="visit" href="https://connekthq.com/plugins/ajax-load-more/add-ons/cache/?utm_source=WP%20Admin&amp;utm_medium=ALM%20DYK&amp;utm_campaign=Cache"><i class="fa fa-chevron-circle-right"></i> Learn More</a>
   </div>




</div>			<div class="cta padding-bottom">
	<h3>Add-ons</h3>
	<div class="cta-inner">
	   <p style="padding-bottom: 10px;">Ajax Load More offers a variety of unique <a href="admin.php?page=ajax-load-more-add-ons">add-ons</a> that will extend and enhance the core functionality of the plugin.</p>
	   <p>Add-ons can be purchased individually or in a <a href="https://connekthq.com/plugins/ajax-load-more/add-ons/bundle/?utm_source=WP%20Admin&amp;utm_medium=ALM%20Dashboard&amp;utm_campaign=Bundle" target="_blank">bundle</a> which gives you access all of the Ajax Load More add-ons at over 50% off the regular price!</p>
	</div>
	<a href="admin.php?page=ajax-load-more-add-ons" class="visit"><i class="fa fa-chevron-circle-right"></i> View Add-ons</a>
</div>						<div class="cta">
	<h3>Other Plugins</h3>
	<div class="cta-inner">
   	<ul class="project-listing">
   		<li>
      		<a target="blank" href="https://connekthq.com/plugins/broadcast/">
      		   <img src="http://caldera/wp-content/plugins/ajax-load-more/admin/img/logos/broadcast-48x48.png" alt="">
      		   <strong>Broadcast</strong>
      		   <span>Manage and display WordPress call to actions with Broadcast.</span>
      		</a>
         </li>
   		<li>
      		<a target="blank" href="https://connekthq.com/plugins/easy-query/">
      		   <img src="http://caldera/wp-content/plugins/ajax-load-more/admin/img/logos/eq-48x48.png" alt="">
      		   <strong>Easy Query</strong>
      		   <span>Build and display WordPress queries without touching a single line of code.</span>
      		</a>
         </li>
   		<li>
   		   <a target="blank" href="https://connekthq.com/plugins/instant-images/">
      		   <img src="http://caldera/wp-content/plugins/ajax-load-more/admin/img/logos/instant-images-48x48.png" alt="">
      		   <strong>Instant Images</strong>
      		   <span>Upload Unsplash.com photos directly to your media library without leaving WordPress.</span>
   		   </a>
   		</li>
   		<li>
   		   <a target="blank" href="https://connekthq.com/plugins/velocity/">
      		   <img src="http://caldera/wp-content/plugins/ajax-load-more/admin/img/logos/velocity-48x48.png" alt="">
      		   <strong>Velocity</strong>
      		   <span>Improve website performance by lazy loading and customizing your embedded media with Velocity.</span>
   		   </a>
   		</li>
   	</ul>
	</div>
</div>

	   </div>
	</div>
		<?php
	}
}


class wpmd_settings{

  const ICON_BASE64_SVG = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiI+PHBhdGggZmlsbD0iIzQ0NDQ0NCIgZD0iTTExLjA2NyAxMC40MjNsLTQuODE3IDUuODYzIDQuODE3IDUuODYyIDEuMTM5LTEuODc0LTMuMjQ2LTMuOTg5IDMuMjQ2LTMuOTg5ek0xMy4xNzUgMjIuMDA4aDIuMTE0bDMuMzYxLTExLjQ3N2gtMi4xMTV6TTIwLjkzMyAxMC40MjNsLTEuMTM5IDEuODc0IDMuMjQ2IDMuOTg5LTMuMjQ2IDMuOTg5IDEuMTM5IDEuODc0IDQuODE3LTUuODYyeiI+PC9wYXRoPjwvc3ZnPg==';

  private $menu_page;
	private $menu_slug;

  public function __construct() {

    $this->menu_page = new md_settings_menu_page();
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
		add_menu_page(
			__( 'Devel Addons', 'amp' ),
			__( 'Devel', 'amp' ),
			'manage_options',
			$this->menu_slug,
			array( $this->menu_page, 'render' ),
			self::ICON_BASE64_SVG
		);
    //
    add_submenu_page(
			$this->menu_slug,
			__( 'Devel Addons', 'amp' ),
			__( 'Settings', 'amp' ),
			'manage_options',
			$this->menu_slug,
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

new wpmd_settings();

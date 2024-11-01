<?php
/*
Plugin Name: Facebook Likebutton for Wordpress
Plugin URI: http://maschinendeck.fliks.com/
Description:  Adds Facebook Like Button
Version: 0.3
Author: Fliks GmbH
Author URI: http://maschinendeck.fliks.com/
License: GNU Lesser General Public License
*/


/*
Usage: 	

	Activae plugin and configure in admin-panel.
	You can use the following code to display the facebook iframe:
		<?php $wp_fb_likebutton->showButton(); ?>

Changes:

	2010-04-22	Bugfix: no facebook meta on homepage.
	2010-04-22	added possibility to show like button on homepage.
	2010-04-22	init
	
Licence:		

This plugin is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this plugin; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/	


class WPFacebookLikeButton
{
	private $_plink = null; 
	private $_layout = 'standard';
	private $_showFaces = 'true'; 
	private $_font = 'arial';
	private $_width = '450'; 
	private $_height = '70';
	private $_action = 'like';
	private $_colorscheme = 'light';
	private $_og_title = null;
	private $_og_image = null;
	private $_showonposts = true;
	private $_showonpages = false;
	private $_showonhome = false;
	
	
	
	public function __construct()
	{
		add_action('admin_menu', array($this, 'generateMenu'));
	}
	
	
	
	/**
	 * Generated sub menu entry. 
	 * 
	 */
	public function generateMenu() 
	{
		// add_submenu_page('edit.php', 'FacebookLikeButton', 'FacebookLikeButton', 5, __FILE__, array($this, 'main'));
		add_options_page('FacebookLikeButton', 'FacebookLikeButton', 5, __FILE__, array($this, 'main'));
	}
	
	
	
	/**
	 * Dispatch action in admin panel.
	 */
	public function main()
	{
		$action = isset($_GET['form_action']) ? $_GET['form_action'] : null; 
		
		switch ($action) 
		{
			case 'update': 
				$this->_update()->_showForm();
				break; 
		
			case 'list': 
			default: 
				$this->_showForm();
				break;
		}
	}
	
	
	
	/**
	 * Installs Plugin.
	 * 
	 * @return WPFacebookLikeButton
	 */
	public function install()
	{
		// Add options
		add_option('wp_facebook_likebutton_layout', $this->_layout);
		add_option('wp_facebook_likebutton_showfaces', $this->_showFaces);
		add_option('wp_facebook_likebutton_width', $this->_width);
		add_option('wp_facebook_likebutton_height', $this->_height);
		add_option('wp_facebook_likebutton_action', $this->_action);
		add_option('wp_facebook_likebutton_font', $this->_font);
		add_option('wp_facebook_likebutton_colorscheme', $this->colorscheme);
		add_option('wp_facebook_likebutton_showonposts', $this->_showonposts);
		add_option('wp_facebook_likebutton_showonpages', $this->_showonpages);
		add_option('wp_facebook_likebutton_showonhome', $this->_showonhome);
		
		return $this;
	}
	
	
	/**
	 * Print out Like Button
	 * 
	 * @param Enum $type ('iframe')
	 * @return WPFacebookLikeButton
	 */
	public function showButton($type = 'iframe')
	{
		$this->_setup(); 
		
		switch ($type) {
			case 'iframe': 
				print $this->createIframe();
				break; 
				
			default: 
				print 'Type: ' . $type . ' not implemented.';
				break;
		}
		
		return $this;
	}	
	
	public function showMetaTags()
	{
		$this->_setup();
		if(is_single() === true || is_page() === true)
		{			
			echo sprintf("\n".'<meta property="og:title" content="%s" />'."\n", $this->_og_title);
		}
	}	

	/**
	 * Update options.
	 */
	private function _update()
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST')
		{
			return $this;
		}
		
		if (isset($_POST['layout'])) {
			update_option('wp_facebook_likebutton_layout', $_POST['layout']);
		}
		
		if (isset($_POST['showfaces'])) {
			update_option('wp_facebook_likebutton_showfaces', $_POST['showfaces']);
		}
		
		if (isset($_POST['width'])) {
			update_option('wp_facebook_likebutton_width', $_POST['width']);
		}
		
		if (isset($_POST['height'])) {
			update_option('wp_facebook_likebutton_height', $_POST['height']);
		}
		
		if (isset($_POST['action'])) {
			update_option('wp_facebook_likebutton_action', $_POST['action']);
		}
		
		if (isset($_POST['font'])) {
			update_option('wp_facebook_likebutton_font', $_POST['font']);
		}
		
		if (isset($_POST['colorscheme'])) {
			update_option('wp_facebook_likebutton_colorscheme', $_POST['colorscheme']);
		}

		if (isset($_POST['showonposts'])) {
			update_option('wp_facebook_likebutton_showonposts', true);
		}
		else
		{
			update_option('wp_facebook_likebutton_showonposts', false);
		}

		if (isset($_POST['showonpages'])) {
			update_option('wp_facebook_likebutton_showonpages', true);
		}
		else
		{
			update_option('wp_facebook_likebutton_showonpages', false);
		}

		if (isset($_POST['showonhome'])) {
			update_option('wp_facebook_likebutton_showonhome', true);
		}
		else
		{
			update_option('wp_facebook_likebutton_showonhome', false);
		}
		
		return $this;
	}
	
	
	
	/**
	 * List options as form
	 */
	private function _showForm()
	{
		$this->_setup();
		?>
			<div class="wrap">
				<h2>Facebook Like-Button Options</h2>
				<h3>Plugin Settings</h3>
				<form action="options-general.php?page=wp_facebook_likebutton/wp_facebook_likebutton.php&form_action=update" method="post">
				<fieldset>
					<legend>Confugure your like-button iframe</legend>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><label for="layout">Layout</label></th>
							<td>
								<select name="layout" id="layout">
									<option value="standard"<?php if ($this->_layout=='standard') print ' selected="selected"';?>>Standard</option>
									<option value="button_count"<?php if ($this->_layout=='button_count') print ' selected="selected"';?>>Button Count</option>
								</select>
							</td>

						</tr>
						<tr valign="top">
							<th scope="row"><label for="showfaces">show Faces</label></th>
							<td>
								<select name="showfaces" id="layout">
									<option value="true"<?php if ($this->_showFaces=='true') print ' selected="selected"';?>>True</option>
									<option value="false"<?php if ($this->_showFaces=='false') print ' selected="selected"';?>>False</option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="action">Action</label></th>
							<td>
								<select name="action" id="action">
									<option value="like"<?php if ($this->_action=='like') print ' selected="selected"';?>>Like</option>
									<option value="recommend"<?php if ($this->_action=='recommend') print ' selected="selected"';?>>Recommend</option>
								</select>
							</td>
						</tr>
					
						<tr valign="top">
							<th scope="row"><label for="font">Font</label></th>
							<td>
								<select name="font" id="font">
									<option value="arial"<?php if ($this->_font=='arial') print ' selected="selected"';?>>Arial</option>
									<option value="lucida+grande"<?php if ($this->_font=='lucida+grande') print ' selected="selected"';?>>Lucida Grande</option>
									<option value="segoe+ui"<?php if ($this->_font=='segoe+ui') print ' selected="selected"';?>>Wegoe UI</option>
									<option value="tahoma"<?php if ($this->_font=='tahoma') print ' selected="selected"';?>>Tahoma</option>
									<option value="trebuchet+ms"<?php if ($this->_font=='trebuchet+ms') print ' selected="selected"';?>>Trebuchet MS</option>
									<option value="verdana"<?php if ($this->_font=='verdana') print ' selected="selected"';?>>Verdana</option>
								</select>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row"><label for="font">Color Scheme</label></th>
							<td>
								<select name="colorscheme" id="colorscheme">
									<option value="light"<?php if ($this->_colorscheme=='light') print ' selected="selected"';?>>Light</option>
									<option value="dark"<?php if ($this->_colorscheme=='dark') print ' selected="selected"';?>>Dark</option>
								</select>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="width">Width</label></th>
							<td><input type="text" name="width" id="width" value="<?php print $this->_width; ?>" /></td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="height">Height</label></th>
							<td><input type="text" name="height" id="height" value="<?php print $this->_height; ?>" /></td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="showonposts">Show under posts?</label></th>
							<td><input type="checkbox" name="showonposts" id="showonposts"<?php if($this->_showonposts == true): ?> checked="checked"<?php endif; ?> /></td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="showonpages">Show on pages?</label></th>
							<td><input type="checkbox" name="showonpages" id="showonpages"<?php if($this->_showonpages == true): ?> checked="checked"<?php endif; ?> /></td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="showonpages">Show on homepage?</label></th>
							<td><input type="checkbox" name="showonhome" id="showonhome"<?php if($this->_showonhome == true): ?> checked="checked"<?php endif; ?> /></td>
						</tr>
					</table>
				</fieldset>

				<p class="submit">
					<input type="submit" value="Save" class="button-primary" />
					<input type="hidden" name="id" value="<?php print $this->id; ?>" />
				</p>

				</form>

				<p>
					<strong>Notice:</strong><br />
					You can use the following code to manually display the facebook iframe in your templates:<br /><br />
					<em><?php echo htmlentities('<?php $wp_fb_likebutton->showButton(); ?>'); ?></em>
				</p>
			</div>
			<?php

		return $this;
	}

	
	/**
	 * Setup values.
	 * 
	 * @return WPFacebookLikeButton
	 */
	private function _setup()
	{
		global $post; 
		
	
		$this->_og_title = $post->post_title;		
		$this->_og_image = null;
		
		$server = get_option('siteurl');
		$path = (isset($_SERVER['REDIRECT_URL'])) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
		$this->_plink = urlencode($server.$path);
		$this->_layout = get_option('wp_facebook_likebutton_layout');
		$this->_showFaces = get_option('wp_facebook_likebutton_showfaces');
		$this->_width = get_option('wp_facebook_likebutton_width');
		$this->_height = get_option('wp_facebook_likebutton_height');
		$this->_font = get_option('wp_facebook_likebutton_font');
		$this->_action = get_option('wp_facebook_likebutton_action');
		$this->_colorscheme = get_option('wp_facebook_likebutton_colorscheme');
		$this->_showonposts = get_option('wp_facebook_likebutton_showonposts');
		$this->_showonpages = get_option('wp_facebook_likebutton_showonpages');
		$this->_showonhome = get_option('wp_facebook_likebutton_showonhome');

		return $this;
	}
	
	
	
	/**
	 * Returns iframe for Like Button.
	 * 
	 * @return String
	 */
	public function createIframe($content = null)
	{
		$this->_plink = urlencode(get_permalink());
		
		$frame = sprintf(
			'<iframe src="http://www.facebook.com/plugins/like.php?href=%s&amp;layout=%s&amp;show_faces=%s&amp;width=%d&amp;action=%s&amp;font=%s;&amp;colorscheme=%s" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:%dpx; height:%dpx"></iframe>', 
			$this->_plink, 
			$this->_layout, 
			$this->_showFaces, 
			$this->_width,
			$this->_action, 
			$this->_font,
			$this->_colorscheme,
			$this->_width,
			$this->_height
		);

		if($content === null)
		{
			return $frame;
		}
		
		if(is_single() === true && $this->_showonposts == true)
		{			
			return $content . $frame;
		}
		elseif(is_page() === true && $this->_showonpages == true)
		{
			return $content . $frame;
		}
		elseif(is_home() === true && $this->_showonhome == true)
		{			
			return $content . $frame;
		}
		else
		{
			return $content;
		}
	}
}


$wp_fb_likebutton = new WPFacebookLikeButton();

// register install function:
register_activation_hook(__FILE__, array($wp_fb_likebutton, 'install'));
add_action('wp_head', array($wp_fb_likebutton, 'showMetaTags'));
add_action('the_content', array($wp_fb_likebutton, 'createIframe'));
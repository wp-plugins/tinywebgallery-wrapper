<?php
/* 
Plugin Name: TinyWebGallery wrapper
Plugin URI: http://www.tinywebgallery.com
Version: 2.3.4
Text Domain: twg-wrapper
Domain Path: /languages
Author: Michael Dempfle
Author URI: http://www.tinywebgallery.com
Description: This plugin includes TinyWebGallery as shortcode in an advanced iframe and offers a TWG random image widget
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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if (!class_exists("twgWrapper")) {
    class twgWrapper {
        var $adminOptionsName = "twgWrapperAdminOptions";

        //
        // class constructor
        //
        function twgWrapper() {
        }

        function init() {
            $this->getAdminOptions();
        }

        function activate() {
            $this->getAdminOptions();
        }

        function iframe_defaults() {
            $iframeAdminOptions = array(
                'securitykey' => sha1(session_id()),
                'twg_url' => 'http://www.tinywebgallery.com/demo/index.php', 'width' => '100%',
                'height' => '700', 'scrolling' => 'no', 'marginwidth' => '0', 'marginheight' => '0',
                'frameborder' => '0', 'transparency' => 'true', 'skin' => 'true', 'addalbum' => 'false',
                'twg_admin_url' => 'http://www.tinywebgallery.com/demo/admin/index.php',
                'twg_admin_user' => '', 'twg_admin_pw' => '', 'show_menu_link' => 'false',
                'include_lytebox_css' => 'true', 'content_id' => '', 'content_styles' => '',
                'hide_elements' => '', 'class' => '', 'shortcode_attributes' => 'true',
                'url_forward_parameter' => 'twg_album,twg_show,twg_random,twg_random_display');
            return $iframeAdminOptions;
        }

        function getAdminOptions() {
            $iframeAdminOptions = twgWrapper::iframe_defaults();
            $devOptions = get_option("twgWrapperAdminOptions");
            if (!empty($devOptions)) {
                foreach ($devOptions as $key => $option)
                    $iframeAdminOptions[$key] = $option;
            }
            update_option("twgWrapperAdminOptions", $iframeAdminOptions);
            return $iframeAdminOptions;
        }

        function loadLanguage() {
            load_plugin_textdomain('twg-wrapper', false, dirname(plugin_basename(__FILE__)) . '/languages/');
            wp_enqueue_script('jquery');
        }

        /* CSS for the admin area */
        function addAdminHeaderCode($hook) {
            if( $hook != 'settings_page_tinywebgallery-wrapper' && $hook != 'toplevel_page_tinywebgallery-wrapper') 
         		    return;
            echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/tinywebgallery-wrapper/css/twg.css" />' . "\n";

        }

        function param($param, $content = null) {
            $value = isset($_GET[$param]) ? $_GET[$param] : '';
            return esc_html($value);
        }

        function do_iframe_script($atts) {
            $options = get_option("twgWrapperAdminOptions");
            // defaults
            extract(array('securitykey' => 'not set',
                'twg_url' => $options['twg_url'], 'height' => $options['height'], 'width' => $options['width'], 'frameborder' => $options['frameborder'],
                'scrolling' => $options['scrolling'], 'marginheight' => $options['marginheight'], 'marginwidth' => $options['marginwidth'],
                'transparency' => $options['transparency'], 'skin' => $options['skin'], 'addalbum' => $options['addalbum'],
                'include_lytebox_css' => $options['include_lytebox_css'], 'content_id' => $options['content_id'],
                'content_styles' => $options['content_styles'], 'hide_elements' => $options['hide_elements'],
                'class' => $options['class'], 'url_forward_parameter' => $options['url_forward_parameter'], $atts));
            // read the shortcode attributes
            if ($options['shortcode_attributes'] == 'true') {
                extract(shortcode_atts(array('securitykey' => 'not set',
                    'twg_url' => $options['twg_url'], 'height' => $options['height'], 'width' => $options['width'], 'frameborder' => $options['frameborder'],
                    'scrolling' => $options['scrolling'], 'marginheight' => $options['marginheight'], 'marginwidth' => $options['marginwidth'],
                    'transparency' => $options['transparency'], 'skin' => $options['skin'], 'addalbum' => $options['addalbum'],
                    'include_lytebox_css' => $options['include_lytebox_css'], 'content_id' => $options['content_id'],
                    'content_styles' => $options['content_styles'], 'hide_elements' => $options['hide_elements'],
                    'class' => $options['class'], 'url_forward_parameter' => $options['url_forward_parameter']), $atts));
            } else {
                // only the secrity key is read.
                extract(shortcode_atts(array('securitykey' => 'not set'), $atts));
            }

            echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/tinywebgallery-wrapper/css/twg.css" />' . "\n";
            if ($options['securitykey'] != $securitykey) {
                echo '<div class="errordiv">' . __('An invalid security key was specified. Please use at least the following shortcode:<br>[twg securitykey="&lt;your security key - see settings&gt;"]', 'twg-wrapper') . '</div>';
                return;
            } else {

                // add parameters
                if ($url_forward_parameter != '') {
                    $sep = "&amp;";
                    if (strpos($twg_url, '?') === false) {
                        $sep = '?';
                    }
                    $parameters = explode(",", $url_forward_parameter);
                    foreach ($parameters as $parameter) {
                        if (strpos($twg_url, 'twg_album') !== false && $parameter == 'twg_album') { // album is set
                            continue;
                        }
                        $read_param_esc = $this->param($parameter);
                        if ($read_param_esc != '') {
                            $twg_url .= $sep . $parameter . "=" . $read_param_esc;
                            $sep = "&amp;";
                        }
                    }
                }

                // add options
                if (is_user_logged_in() && $addalbum == 'true') {
                    // check if parameters already there and if twg_album is set the album is not added again.
                    $sep = "&amp;";
                    if (strpos($twg_url, '?') === false) {
                        $sep = '?';
                    }
                    if (strpos($twg_url, 'twg_album') === false) { // album is not set
                        global $user_login;
                        get_currentuserinfo();
                        $twg_url .= $sep . "twg_album=" . esc_html($user_login);
                    }
                }
                if ($skin == 'true') {
                    // check if parameters already there.
                    if (strpos($twg_url, '?') === false) {
                        $twg_url .= "?twg_skin=integrated&amp;twg_noborder=true";
                    } else {
                        $twg_url .= "&amp;twg_skin=integrated&amp;twg_noborder=true";
                    }
                }
                $html = '';
                // check the css
                if ($include_lytebox_css == 'true') {
                    // get the admin url first and then the of twg
                    $css_url = dirname($twg_url);

                    if ($options['twg_admin_url'] != '') {
                        $css_url = dirname(dirname($options['twg_admin_url']));
                    }
                    $css_url .= '/lightbox/lytebox.css';
                    $html .= '<link type="text/css" rel="stylesheet" href="' . $css_url . '" />';
                }

                if ((!empty($content_id) && !empty($content_styles))
                        || !empty($hide_elements)) {
                    $html .= "<script>
                   jQuery(document).ready(function() {";
                    if (!empty($hide_elements)) {
                        $html .= "jQuery('" . esc_html($hide_elements) . "').css('display', 'none');";
                    }
                    if (!empty($content_id)) {
                        $elements = esc_html($content_id); // this field should not have a problem if they are encoded.
                        $values = esc_html($content_styles); // this field style should not have a problem if they are encoded.
                        $elementArray = explode("|", $elements);
                        $valuesArray = explode("|", $values);
                        if (count($elementArray) != count($valuesArray)) {
                            echo '<div class="errordiv">' . __('Configuration error: The attributes content_id and content_styles have to have the amount of value sets separated by |.', 'twg-wrapper') . '</div>';
                            return;
                        } else {
                            for ($x = 0; $x < count($elementArray); ++$x) {
                                $valuesArrayPairs = explode(";", trim($valuesArray[$x], " ;:"));
                                for ($y = 0; $y < count($valuesArrayPairs); ++$y) {
                                    $elements = explode(":", $valuesArrayPairs[$y]);
                                    $html .= "jQuery('" . $elementArray[$x] . "').css('" . $elements[0] . "', '" . $elements[1] . "');";
                                }
                            }
                        }
                    }
                    $html .= " });
                 </script>";
                }
                $html .= "<iframe id='twg_iframe' name='twg_iframe' src='" . $twg_url . "' width='" . esc_html($width) . "' height='" . esc_html($height) . "' scrolling='" . esc_html($scrolling) . "' ";
                if (!empty ($marginwidth)) {
                    $html .= " marginwidth='" . esc_html($marginwidth) . "' ";
                }
                if (!empty ($marginheight)) {
                    $html .= " marginheight='" . esc_html($marginheight) . "' ";
                }
                if ($frameborder != '') {
                    $html .= " frameborder='" . esc_html($frameborder) . "' ";
                }
                if (!empty ($transparency)) {
                    $html .= " allowtransparency='" . esc_html($transparency) . "' ";
                }
                if (!empty ($class)) {
                    $html .= " class='" . esc_html($class) . "' ";
                }

                $html .= "></iframe>\n ";
            }
            return $html;
        }

        function printAdminPage() {
            require_once('tinywebgallery-admin-page.php');
        }

        //End function printAdminPage()
        function load_widgets() {
            register_widget('twg_random_image_widget');
        }
    }
}
require_once('tinywebgallery-random-widget.php'); //  includes the code for the iframe widget
//  setup new instance of plugin
if (class_exists("twgWrapper")) {
    $cons_twgWrapper = new twgWrapper();
}
//Actions and Filters	
if (isset($cons_twgWrapper)) {
    //Initialize the admin panel
    if (!function_exists("twgWrapper_ap")) {
        function twgWrapper_ap() {
            global $cons_twgWrapper;
            if (!isset($cons_twgWrapper)) {
                return;
            }
            $options = $cons_twgWrapper->getAdminOptions();
            if ($options['show_menu_link'] == "true") {
                add_menu_page('TinyWebGallery', 'TinyWebGallery', 'manage_options', basename(__FILE__), array(&$cons_twgWrapper, 'printAdminPage'));
            }
            if (function_exists('add_options_page')) {
                add_options_page('TinyWebGallery', 'TinyWebGallery', 'manage_options', basename(__FILE__), array(&$cons_twgWrapper, 'printAdminPage'));
            }
        }
    }
    add_action('admin_menu', 'twgWrapper_ap', 1); //admin page
    add_action('widgets_init', array(&$cons_twgWrapper, 'load_widgets'), 1); // loads widgets
    add_action('init', array(&$cons_twgWrapper, 'loadLanguage'), 1); // add languages
    add_action('admin_head', array(&$cons_twgWrapper, 'addAdminHeaderCode'), 99); // load css
    add_shortcode('twg', array(&$cons_twgWrapper, 'do_iframe_script'), 1); // setup shortcode [twg]
    register_activation_hook(__FILE__, array(&$cons_twgWrapper, 'activate'));
}
?>
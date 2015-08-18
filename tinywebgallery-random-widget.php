<?php
/* 
TinyWebGallery Wrapper
http://www.tinywebgallery.com
Michael Dempfle

Widget include

*/
class twg_random_image_widget extends WP_Widget {
    function __construct() {
        /* Widget settings. */
        $widget_ops = array('classname' => 'twg_random_image_widget', 'description' => __('Displays a random image or the top viewed image of TinyWebGallery', 'twg-wrapper'));
        /* Widget control settings. */
        $control_ops = array('width' => 220, 'height' => 300, 'id_base' => 'twg_random_image_widget');
        /* Create the widget. */
        parent::__construct('twg_random_image_widget', __('TinyWebGallery random image', 'twg-wrapper'), $widget_ops, $control_ops);
    }

    function widget($args, $instance) {
        extract($args);
        // we get the dettings from the wrapper.
        $options = get_option("twgWrapperAdminOptions");
        /* User-selected settings. */
        $title = apply_filters('widget_title', $instance['title']);
        /* Before widget (defined by themes). */
        echo $before_widget;
        /* Title of widget (before and after defined by themes). */
        if ($title)
            echo $before_title . $title . $after_title;

        // This are some predefined inline styles. 
        $style = '
               <style>
               .twg_shadow {
              	margin-top: 3px;
                -moz-box-shadow: 3px 3px 4px #111;
              	-webkit-box-shadow: 3px 3px 4px #111;
              	box-shadow: 3px 3px 4px #111;
              	/* For IE 8 */
              	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color=\'#111111\')";
              	/* For IE 5.5 - 7 */
              	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color=\'#111111\');
               }
               .twg_single_border {
                 margin-top: 3px;
                 border: 1px solid #111111;
                 padding: 4px;
               }
                .twg_margin_top {
                 margin-top: 3px;  
               }
               </style>
               ';
        // get the image.php url 
        if ($instance['twg_image'] == 'internal') {
            $twg_image_url = dirname($options['twg_url']);
            if ($options['twg_admin_url'] != '') {
                $twg_image_url = dirname(dirname($options['twg_admin_url']));
            }
            $twg_image_url .= '/image.php';
        } else {
            $twg_image_url = $instance['twg_image_url'];
        }
        // get the link
        $link_open = "";
        $link_close = "";
        if ($instance['twg_source'] != 'no') {
            if ($instance['twg_source'] == 'internal') {
                $twg_link_url = $options['twg_url'];
            } else {
                $twg_link_url = $instance['twg_source_url'];
            }
            $link_target = '';
            if ($instance['twg_target'] != 'none') {
                $link_target = ' target="' . $instance['twg_target'] . '" ';
            }
            
            $toimage = ($instance['twg_random_display'] == 'image') ? '&amp;twg_random_display=true' : '';

            $link_open = '<a ' . $link_target . ' href="' . $twg_link_url . '?twg_random=' . $this->number . $toimage . '">';
            $link_close = '</a>';
        }
        $image_html = '<img id="twg_rand_image_' . $this->number . '" src="' . $twg_image_url . '?twg_random=' . $this->number;
        $image_html .= '&amp;twg_type=' . $instance['twg_type'];

        if ($instance['twg_album'] != '') {
            $image_html .= '&amp;twg_album=' . $instance['twg_album'];
        }
        if ($instance['twg_random_size'] != '') {
            $image_html .= '&amp;twg_random_size=' . $instance['twg_random_size'];
        }
        if ($instance['twg_random_subdir'] == 'true') {
            $image_html .= '&amp;twg_random_subdir=' . $instance['twg_random_subdir'];
        }
        if ($instance['twg_css'] != '') {
            $image_html .= '" class="' . $instance['twg_css'];
        } else if ($instance['twg_shadow'] != '') {
            $image_html .= '" class="' . $instance['twg_shadow'];
        } else if ($instance['twg_margin_top'] != '') {
            $image_html .= '" class="' . $instance['twg_margin_top'];
        }

        $image_html .= '"/>';

        // div
        $div_open = '';
        $div_close = '';
        if ($instance['twg_center'] == 'true' || $instance['twg_reserve_space'] == true) {
            $div_open = '<table cellspacing="0" cellpadding="0" ';
            if ($instance['twg_center'] == 'true') {
                $div_open .= 'style="width:100%;text-align:center;"';
            }
            $div_open .= '><tr><td';
            if ($instance['twg_reserve_space'] == 'true') {
                $padding = ($instance['twg_shadow'] == '') ? 6 : 16;
                $div_open .= ' style="height:' . ($instance['twg_random_size'] + $padding) . 'px;vertical-align:middle;"';
            }
            $div_open .= '>';
            $div_close = '</td></tr></table>';
        }

        echo $style . $div_open . $link_open . $image_html . $link_close . $div_close;

        // all variables have an unique id because this supports multiple slideshows on one page! 
        if ($instance['twg_slideshow'] == 'true') {
            // script to reload
            echo '<script type="text/javascript">' . "\n";
            echo 'var twg_img_base'. $this->number .' = jQuery("#twg_rand_image_' . $this->number . '").attr("src");' . "\n";
            echo 'var twg_counter'. $this->number .'=0;' . "\n";
            echo 'var image_fade'. $this->number .' = ' . $instance['twg_fade'] . ';' . "\n";
            echo 'var iid'. $this->number .' = "#twg_rand_image_' . $this->number . "\";\n";
            echo 'var img_id'. $this->number .' = "twg_rand_image_' . $this->number . "\";\n";
            echo 'var oldclass'. $this->number .' = jQuery(iid'. $this->number .').attr("class"); ';
            echo 'function changeImage'. $this->number .'() {' . "\n";
            echo 'if (image_fade'. $this->number .' != 1) {
                     var image1'. $this->number .' = jQuery("<img />").attr("class", oldclass'. $this->number .').attr("id",img_id'. $this->number .')
                        .attr("src", twg_img_base'. $this->number .' + "&ts=" + twg_counter'. $this->number .'++)
                        .load(function(){ 
                            jQuery(iid'. $this->number .').fadeTo(\'fast\', image_fade'. $this->number .' , function() {
                              jQuery(iid'. $this->number .').replaceWith( image1'. $this->number .' ); 
                              jQuery(iid'. $this->number .').fadeTo(\'normal\', 1, function() {            
                                 jQuery(iid'. $this->number .').removeAttr("style");                                 
                              });
                            });     
                         });
                    } else {
                      jQuery(iid'. $this->number .').attr("src",twg_img_base'. $this->number .' + "&ts=" + twg_counter'. $this->number .'++); 
                    }   ';
            echo '}' . "\n";
            echo 'window.setInterval("changeImage' . $this->number . '()",' . $instance['twg_slideshow_time'] . '000);' . "\n";
            echo '</script>' . "\n";
        }
        /*
         *  After widget (defined by themes). */
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = $new_instance['title'];
        $instance['twg_image'] = $new_instance['twg_image'];
        $instance['twg_image_url'] = $new_instance['twg_image_url'];
        $instance['twg_type'] = $new_instance['twg_type'];
        $instance['twg_album'] = $new_instance['twg_album'];
        $instance['twg_random_size'] = $new_instance['twg_random_size'];
        $instance['twg_random_subdir'] = $new_instance['twg_random_subdir'];
        $instance['twg_center'] = $new_instance['twg_center'];
        $instance['twg_css'] = $new_instance['twg_css'];
        $instance['twg_source'] = $new_instance['twg_source'];
        $instance['twg_source_url'] = $new_instance['twg_source_url'];
        $instance['twg_target'] = $new_instance['twg_target'];
        $instance['twg_random_display'] = $new_instance['twg_random_display'];
        $instance['twg_shadow'] = $new_instance['twg_shadow'];
        $instance['twg_slideshow'] = $new_instance['twg_slideshow'];
        $instance['twg_slideshow_time'] = $new_instance['twg_slideshow_time'];
        $instance['twg_fade'] = $new_instance['twg_fade'];
        $instance['twg_reserve_space'] = $new_instance['twg_reserve_space'];
        return $instance;
    }

    function form($instance) {
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'twg-wrapper'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"
                   style="width:90%"/>
        </p>
        <h3><?php _e('Image settings', 'twg-wrapper'); ?></h3>
        <p>
        <?php _e('URL to TWG image.php:', 'twg-wrapper'); ?><br/>
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_image'); ?>"
                          value="internal" <?php if ($instance['twg_image'] == "internal" || $instance['twg_image'] == "") {
                echo "checked=\"checked\"";
            } ?>  />&nbsp;<?php _e('Use URL from the settings', 'twg-wrapper'); ?></label><br/>
            <label><input id="<?php echo $this->get_field_id('twg_image'); ?>" type="radio" name="<?php echo $this->get_field_name('twg_image'); ?>"
                          value="external" <?php if ($instance['twg_image'] == "external") {
                echo "checked=\"checked\"";
            } ?>  />&nbsp;<?php _e('Use URL below', 'twg-wrapper'); ?></label><br/>
            <input type="text" id="<?php echo $this->get_field_id('twg_image_url'); ?>"
                   name="<?php echo $this->get_field_name('twg_image_url'); ?>"
                   value="<?php echo $instance['twg_image_url']; ?>" style="width:90%" onClick="jQuery('input:radio[id=<?php echo $this->get_field_id('twg_image'); ?>]:nth(0)').attr('checked',true);"/>
       
        </p>
        <p>
        <?php _e('Display mode:', 'twg-wrapper'); ?><br/>
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_type'); ?>" value="random"
            <?php if ($instance['twg_type'] == "random" || $instance['twg_type'] == "") {
                echo "checked=\"checked\"";
            } ?>  /> <?php _e('Random', 'twg-wrapper'); ?></label>&nbsp;&nbsp;
            <label><input id="twg_type_2" type="radio" name="<?php echo $this->get_field_name('twg_type'); ?>"
                          value="top"
            <?php if ($instance['twg_type'] == "top") {
                echo "checked=\"checked\"";
            } ?>  /> <?php _e('Top viewed', 'twg-wrapper'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twg_album'); ?>"><?php _e('Album:', 'twg-wrapper'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('twg_album'); ?>"
                   name="<?php echo $this->get_field_name('twg_album'); ?>"
                   value="<?php echo $instance['twg_album']; ?>" style="width:90%"/>
            <span style="font-size:11px;"
                  class="description"><?php _e('Enter the value of ?twg_album=', 'twg-wrapper'); ?></span>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twg_random_size'); ?>"><?php _e('Image size:', 'twg-wrapper'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('twg_random_size'); ?>"
                   name="<?php echo $this->get_field_name('twg_random_size'); ?>"
                   value="<?php echo (($instance['twg_random_size'] == "") ? "200" : $instance['twg_random_size']); ?>"
                   style="width:50px"/> px
        </p>
        <p>
            <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('twg_random_subdir'); ?>"
                   id="<?php echo $this->get_field_id('twg_random_subdir'); ?>" value="true"
            <?php if ($instance['twg_random_subdir'] == "true" || $instance['twg_random_subdir'] == "") {
                echo "checked=\"checked\"";
            } ?>  />
            <label for="<?php echo $this->get_field_id('twg_random_subdir'); ?>"><?php _e('Include sub directories', 'twg-wrapper'); ?></label>
            <br />
            <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('twg_center'); ?>"
                   id="<?php echo $this->get_field_id('twg_center'); ?>" value="true"
            <?php if ($instance['twg_center'] == "true" || $instance['twg_center'] == "") {
                echo "checked=\"checked\"";
            } ?>  />
            <label for="<?php echo $this->get_field_id('twg_center'); ?>"><?php _e('Center image', 'twg-wrapper'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twg_shadow'); ?>">
            <?php _e('Shadow / Border:', 'twg-wrapper'); ?>
            </label>
            <select name="<?php echo $this->get_field_name('twg_shadow'); ?>"
                    id="<?php echo $this->get_field_id('twg_target'); ?>">
                <option value="" <?php if ($instance['twg_shadow'] == "") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('None or css below', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="twg_shadow" <?php if ($instance['twg_shadow'] == "twg_shadow") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Drop shadow', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="twg_single_border" <?php if ($instance['twg_shadow'] == "twg_single_border") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Single line border', 'twg-wrapper'); ?>&nbsp;</option>
                 <option value="twg_margin_top" <?php if ($instance['twg_shadow'] == "twg_margin_top") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Margin top (3px)', 'twg-wrapper'); ?>&nbsp;</option>
            </select>
            <br />
            <label for="<?php echo $this->get_field_id('twg_css'); ?>"><?php _e('Css style class:', 'twg-wrapper'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('twg_css'); ?>"
                   name="<?php echo $this->get_field_name('twg_css'); ?>" value="<?php echo $instance['twg_css']; ?>"
                   style="width:90%"/>
        </p>
        <h3><?php _e('Link settings', 'twg-wrapper'); ?></h3>
        <p>
        <?php _e('URL to TWG:', 'twg-wrapper'); ?><br />
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_source'); ?>"
                          value="no" <?php if ($instance['twg_source'] == "no") {
                echo "checked=\"checked\"";
            } ?>  />&nbsp;<?php _e('No link', 'twg-wrapper'); ?></label><br />
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_source'); ?>"
                          value="internal" <?php if ($instance['twg_source'] == "internal" || $instance['twg_source'] == "") {
                echo "checked=\"checked\"";
            } ?>  />&nbsp;<?php _e('Use URL from the settings', 'twg-wrapper'); ?></label><br />
            <label><input id="<?php echo $this->get_field_id('twg_source'); ?>" type="radio" name="<?php echo $this->get_field_name('twg_source'); ?>"
                          value="external" <?php if ($instance['twg_source'] == "external") {
                echo "checked=\"checked\"";
            } ?>  />&nbsp;<?php _e('Use URL below', 'twg-wrapper'); ?></label><br />
            <input type="text" id="<?php echo $this->get_field_id('twg_source_url'); ?>"
                   name="<?php echo $this->get_field_name('twg_source_url'); ?>"
                   value="<?php echo $instance['twg_source_url']; ?>" style="width:90%" onClick="jQuery('input:radio[id=<?php echo $this->get_field_id('twg_source'); ?>]:nth(0)').attr('checked',true);"/>
            <br/><span style="font-size:11px;"
                       class="description"><?php _e('If you use the TWG iframe wrapper you should enter the Wordpress URL where you have included TWG. The wrapper does add the needed parameters to the TWG iframe.', 'twg-wrapper'); ?></span>
        </p>
        <p>
        <?php _e('Link destination:', 'twg-wrapper'); ?>
            <br/>
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_random_display'); ?>" value="image"
            <?php if ($instance['twg_random_display'] == "image" || $instance['twg_random_display'] == "") {
                echo "checked=\"checked\"";
            } ?>  /> <?php _e('Image', 'twg-wrapper'); ?></label>&nbsp;&nbsp;
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_random_display'); ?>" value="album"
            <?php if ($instance['twg_random_display'] == "album") {
                echo "checked=\"checked\"";
            } ?>  /> <?php _e('Album', 'twg-wrapper'); ?></label>&nbsp;&nbsp;
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twg_target'); ?>">
            <?php _e('Target:', 'twg-wrapper'); ?>
            </label>
            <select name="<?php echo $this->get_field_name('twg_target'); ?>"
                    id="<?php echo $this->get_field_id('twg_target'); ?>">
                <option value="none" <?php if ($instance['twg_target'] == "none") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('None', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="_blank" <?php if ($instance['twg_target'] == "_blank") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('New window (_blank)', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="_top" <?php if ($instance['twg_target'] == "_top") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Topmost window (_top)', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="_self" <?php if ($instance['twg_target'] == "_self") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Same window (_self)', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="_parent" <?php if ($instance['twg_target'] == "_parent") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Parent window (_parent)', 'twg-wrapper'); ?>&nbsp;</option>
            </select>
        </p>

        <h3><?php _e('Slideshow settings', 'twg-wrapper'); ?></h3>
        <p>
        <?php _e('Activate slideshow:', 'twg-wrapper'); ?>
            <br />
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_slideshow'); ?>" value="true"
            <?php if ($instance['twg_slideshow'] == "true") {
                echo "checked=\"checked\"";
            } ?>  /> <?php _e('Yes', 'twg-wrapper'); ?></label>&nbsp;&nbsp;
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_slideshow'); ?>" value="false"
            <?php if ($instance['twg_slideshow'] == "false" || $instance['twg_slideshow'] == "") {
                echo "checked=\"checked\"";
            } ?>  /> <?php _e('No', 'twg-wrapper'); ?></label>&nbsp;&nbsp;
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twg_slideshow_time'); ?>"><?php _e('Slideshow interval:', 'twg-wrapper'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('twg_slideshow_time'); ?>"
                   name="<?php echo $this->get_field_name('twg_slideshow_time'); ?>"
                   value="<?php echo (($instance['twg_slideshow_time'] == "") ? "5" : $instance['twg_slideshow_time']); ?>"
                   style="width:50px"/> s
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twg_fade'); ?>">
            <?php _e('Fade:', 'twg-wrapper'); ?>
            </label>
            <select name="<?php echo $this->get_field_name('twg_fade'); ?>"
                    id="<?php echo $this->get_field_id('twg_fade'); ?>">
                <option value="1"   <?php if ($instance['twg_fade'] == "1") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('None', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="0.8" <?php if ($instance['twg_fade'] == "0.8") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Low', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="0.6" <?php if ($instance['twg_fade'] == "0.6") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Medium', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="0.4" <?php if ($instance['twg_fade'] == "0.4") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('High', 'twg-wrapper'); ?>&nbsp;</option>
                <option value="0"   <?php if ($instance['twg_fade'] == "0") {
                    echo "selected=\"selected\"";
                } ?>  ><?php _e('Full', 'twg-wrapper'); ?>&nbsp;</option>
            </select>
        </p>
        <p>
        <?php _e('Reserve space:', 'twg-wrapper'); ?>
            <br />
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_reserve_space'); ?>" value="true"
            <?php if ($instance['twg_reserve_space'] == "true" || $instance['twg_reserve_space'] == "") {
                echo "checked=\"checked\"";
            } ?>  /> <?php _e('Yes', 'twg-wrapper'); ?></label>&nbsp;&nbsp;
            <label><input type="radio" name="<?php echo $this->get_field_name('twg_reserve_space'); ?>" value="false"
            <?php if ($instance['twg_reserve_space'] == "false") {
                echo "checked=\"checked\"";
            } ?>  /> <?php _e('No', 'twg-wrapper'); ?></label>&nbsp;&nbsp;
            <br /><span style="font-size:11px;"
                       class="description"><?php _e('Reserve the space for a vertical image. If No the layout can jump when slideshow is running and images have a different height.', 'twg-wrapper'); ?></span>
        </p>

        <h3><?php _e('Help', 'twg-wrapper'); ?></h3>
        <p><?php _e('Please read <a style="text-decoration:none;" href="http://www.tinywebgallery.com/en/faq.php#h4" target="_blank">howto 4</a> of the TWG FAQ for details.', 'twg-wrapper'); ?></p>
        <?php      
    }
}
?>
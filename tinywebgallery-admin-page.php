<?php
/* 
TinyWebGallery Wrapper
http://www.tinywebgallery.com
Michael Dempfle

Administration include

*/
?>
<script>
    function checkInputNumber(intputField) {
        intputField.value = intputField.value.split(' ').join('');
        var f = intputField.value;
        if (intputField.value == '') return;
        var match = f.match(/^(\-){0,1}(\d+)(px|%|em|pt)?$/);
        if (!match) {
            alert("<?php _e("Please check the value you have entered. Only numbers with an optional px, %, em or pt are allowed", "twg-wrapper");?>");
        }
    }
</script>
<?php
if (is_user_logged_in() && is_admin()) {

    $lang = 'en';
    $login = '?action=login&amp;dir=&amp;order=name&amp;srt=yes&amp;tview=no&amp;sview=yes';
    $devOptions = $this->getAdminOptions();
// print_r($devOptions);
    if (isset($_POST['update_iframe-loader'])) { //save option changes
        $adminSettings = array('securitykey', 'twg_url', 'width', 'height', 'scrolling',
            'marginwidth', 'marginheight', 'frameborder', 'transparency', 'skin',
            'addalbum', 'twg_admin_url', 'twg_admin_user', 'twg_admin_pw', 'show_menu_link',
            'include_lytebox_css', 'content_id', 'content_styles', 'hide_elements', 'class',
            'shortcode_attributes', 'url_forward_parameter');

        if (!wp_verify_nonce($_POST['twg-options'], 'twg-options')) die('Sorry, your nonce did not verify.');

        foreach ($adminSettings as $item) {
            $devOptions[$item] = trim($_POST[$item]);
        }
        update_option($this->adminOptionsName, $devOptions);
        ?>
        <div class="updated">
            <p><strong><?php _e("Settings Updated.", "twg-wrapper");?></strong>
            </p>
        </div>
<?php
    }
    $show_note = false;   
    $twg_admin_url = $devOptions['twg_admin_url'];
    
    $show_note_admin = $twg_admin_url == '';
    
    if ($devOptions['twg_admin_user'] == '' || $devOptions['twg_admin_pw'] == '') {
        $login = '';
        $show_note = true;
    }
    ?>
    <style type="text/css">
        table th {
            text-align: left;
        }
    </style>
<div class="wrap">
    <form method="post" target="_blank" action="<?php echo $twg_admin_url; ?><?php echo $login ?>">
        <div id="icon-options-general" class="icon_jfu">
            <br>
        </div>
        <h2>
        <?php _e('TinyWebGallery administration login', 'twg-wrapper'); ?></h2>


    <?php if (!$show_note) { ?>
        <input name="p_user" value="<?php echo $devOptions['twg_admin_user']; ?>" type="hidden">
        <input name="p_pass" value="<?php echo $devOptions['twg_admin_pw']; ?>" type="hidden">
        <input name="lang" value="<?php echo $lang ?>" type="hidden">
    <?php
    }
     if (!$show_note_admin && $show_note) {
        echo '<p>' . __('<strong>Please note:</strong> Username and password are not specified. The button below will go to the login page of the TWG administration. If you specify username and password in the settings below a direct login will be done.', 'twg-wrapper') . '</p>';
    }
    if (!$show_note_admin) {
    ?>
        <p><input class="button-primary" type="submit" name="update_iframe-loader"
               value="<?php _e('To the TinyWebGallery administration', 'twg-wrapper') ?>"/></p>
    <?php } else {
     _e('<p><strong>Please note:</strong>TinyWebGallery has an administration that is not written for Wordpress directly. Please specify the url and the login data below at \'TinyWebGallery administration settings\' for a direct login.</p>', 'twg-wrapper');  } 
     ?>
    </form>
    <form method="post" action="options-general.php?page=tinywebgallery-wrapper.php">
    <?php wp_nonce_field('twg-options', 'twg-options'); ?>

        <div id="icon-options-general" class="icon_jfu">
            <br>
        </div>
        <h2>
        <?php _e('TinyWebGallery default settings', 'twg-wrapper'); ?></h2>

        <p>
        <?php _e('This plugin will include your existing TWG installation in an iframe. Please enter the url and the size you want to include TinyWebGallery. You have a couple of additional default options which help to integrate TinyWebGallery better into your template. You can overwrite all of this settings by specifying the parameter in the shortcode. Please read the documentation after each field about the parameter you have to use.', 'twg-wrapper'); ?>
        </p>

        <p>
        <?php _e('Please use the following shortcode to include TinyWebGallery to your page: ', 'twg-wrapper'); ?>
            <strong>[twg securitykey="<?php echo $devOptions['securitykey']; ?>"]</strong>
        </p>
        <table class="form-table">
        <?php
        printTextInput($devOptions, __('Security key', 'twg-wrapper'), 'securitykey', __('This is the security key which has to be used in the shorttag. This is mandatory because otherwise anyone who can create an article can insert a gallery as well.  The default security key was randomly generated during installation. Please change the key if you like. You should use this in combination with e.g. Page security to make sure that only the users you define can modify pages.', 'twg-wrapper'));
        printTrueFalse($devOptions, __('Allow shortcode attributes', 'twg-wrapper'), 'shortcode_attributes', __('Allow to set attributes in the shortcode. All of the attributes can be overwritten in the shortcode if you set \'Yes\'. Otherwise the settings you specify here are used.', 'twg-wrapper'));
        printTextInput($devOptions, __('TinyWebGallery url', 'twg-wrapper'), 'twg_url', __('Enter the full URL to your TWG installation. You have to include the full path including the index.php. e.g .http://www.tinywebgallery.com/demo/index.php. Shortcode attribute: twg_url=""', 'twg-wrapper'));
        printNumberInput($devOptions, __('Width', 'twg-wrapper'), 'width', __('The width of the iframe. You can specify the value in PX or in %. If you don\'t specify anything px is assumed.  Shortcode attribute: width=""', 'twg-wrapper'));
        printNumberInput($devOptions, __('Height', 'twg-wrapper'), 'height', __('The height of the iframe. You can specify the value in px or in %. If you don\'t specify anything px is assumed. Shortcode attribute: height=""', 'twg-wrapper'));
        printAutoNo($devOptions, __('Scrolling', 'twg-wrapper'), 'scrolling', __('Defines if scrollbars are shown if the gallery is too big for your iframe. Please note: If you select \'Yes\' IE does always show scrollbars! So only use this if needed. On the TinyWebGallery web page is a big howto about integrating TinyWebgallery into an iframe. Shortcode attribute: scrolling="auto" or scrolling="no"', 'twg-wrapper'));
        printNumberInput($devOptions, __('Margin width', 'twg-wrapper'), 'marginwidth', __('The margin width of the iframe. You can specify the value in px. If you don\'t specify anything px is assumed.  Shortcode attribute: marginwidth=""', 'twg-wrapper'));
        printNumberInput($devOptions, __('Margin height', 'twg-wrapper'), 'marginheight', __('The margin height of the iframe. You can specify the value in px. If you don\'t specify anything px is assumed.  Shortcode attribute: marginheight=""', 'twg-wrapper'));
        printNumberInput($devOptions, __('Frame border', 'twg-wrapper'), 'frameborder', __('The frame border of the iframe. You can specify the value in px. If you don\'t specify anything px is assumed.  Shortcode attribute: frameborder=""', 'twg-wrapper'));
        printTrueFalse($devOptions, __('Transparency', 'twg-wrapper'), 'transparency', __('If you like that the iframe is transparent and your background is shown you should set this to \'Yes\'. If this value is not set then the iframe is transparent in IE but transparent in e.g. Firefox. So by default you should leave this to \'Yes\'. Shortcode attribute: transparency="true" or transparency="false" ', 'twg-wrapper'));
        printTextInput($devOptions, __('Class', 'twg-wrapper'), 'class', __('You can define a class for the iframe if you like. Shortcode attribute: class=""', 'twg-wrapper'));
        printTextInput($devOptions, __('URL forward parameters', 'twg-wrapper'), 'url_forward_parameter', __('Define the parameters that should be passed from the browser url to the iframe url. Please seperate the parameters by \',\'. In TWG this enables you to jump directly to an album or image although TWG is included in an iframe. For TWG the most interesting parameters are: twg_album, twg_show, twg_random and twg_random_display. Shortcode attribute: url_forward_parameter=""', 'twg-wrapper'));
        printTrueFalse($devOptions, __('Use skin \'integrated\'', 'twg-wrapper'), 'skin', __('TinyWebGallery (since 1.8.5) has a skin that is optimized for integration. It is very simple one without a border, no background and black text color. Setting this value to \'Yes\' will choose this skin. \'No\' will use the skin you have configured in TWG. Shortcode attribute: skin="integrated"', 'twg-wrapper'));
        printTrueFalse($devOptions, __('Add user name as album', 'twg-wrapper'), 'addalbum', __('You can add the user name as album to the url. If you use wfu to upload images each user has his images in his own folder. And with setting this to \'Yes\' you can directly link to this folder. Shortcode attribute: addalbum="true" or addalbum="false"', 'twg-wrapper'));
        printTrueFalse($devOptions, __('Include Lytebox css', 'twg-wrapper'), 'include_lytebox_css', __('TinyWebGallery has support for the Lytebox lighbox script. Lytebox is able to view images in a lighbox that is not restricted to the iframe and uses the whole page. To enable this the lytebox.css has to be included in the main page. This feature is only available for registered user of TWG. So you can set this to \'No\' if your version of TWG is not registered. Wordpress and TWG have to be installed on the same domain. The lytebox does NOT work in the preconfigured example because of the same origin policy of Javascript. See the recommended settings below.  Shortcode attribute: include_lytebox_css="true", include_lytebox_css="false"', 'twg-wrapper'));
        ?>
        </table>
        <p>
            <input class="button-primary" type="submit" name="update_iframe-loader"
                   value="<?php _e('Update Settings', 'twg-wrapper') ?>"/>
        </p>

        <h3><?php _e('Advanced options', 'twg-wrapper') ?></h3>

        <p>
        <?php _e('With the following options you can modify your template on the fly to give the iframe more space! At most templates you would have to create a page template with a special css and this is quite complicated. By using the options below your template is modified on the fly by jQuery. Please look at the screenshots to make this options more clear. The options are very useful for templates that have a top navigtation because otherwise your menu is gone! If you still want to do this you should add a back link to the page. The examples below are for Twenty Ten, iNove and the default wordpress theme.', 'twg-wrapper'); ?>
        </p>
        <table class="form-table">
        <?php
        printTextInput($devOptions, __('Content id', 'twg-wrapper'), 'content_id', __('Some templates do not use the full width for their content and even most \'One column, no sidebar Page Template\' templates only remove the sidebar but do not change the content width. Please set the id of the div starting with a hash (#) that defines the content. In the field below you then define the style you want to overwrite. For Twenty Ten and WordPress Default the id is #content, for iNove it is #main. You can also define more than one element. Please seperate them by | and provide the styles below. Please read the note below how to find this id for other templates. #content|h2 means that you want to set a new style for the div content and the heading h2 below. Shortcode attribute: content_styles=""', 'twg-wrapper'));
        printTextInput($devOptions, __('Content styles', 'twg-wrapper'), 'content_styles', __('Define the styles that have to be overwritten to enable the full width. Most of the time have to modify some of the following attributes: width, margin-left, margin-right, padding-left. Please use ; as seperator between styles. If you have defined more than one element above (Content id) please seperate the different style sets with |. The default values are: Wordpress default: \'width:450px;padding-left:45px;\'. Twenty Ten: \'margin-left:20px;margin-right:240px\'. iNove: \'width:605px\'. Please read the note below how to find this styles for other templates. If you have defined #content|h2 at the Content id you can e.g. set \'width:650px;padding-left:25px;|padding-left:15px;\'. Shortcode attribute: content_styles=""', 'twg-wrapper'));

        printTextInput($devOptions, __('Hide elements', 'twg-wrapper'), 'hide_elements', __('This setting allows to hide elements when the iframe is shown. This can be used to hide the sidebar or the heading. Usage: If you want to hide a div you have to enter a hash (#) followed by the id e.g. #sidebar. If you want to hide a heading which is a &lt;h2&gt; you have to enter h2. You can define several elements seperated by, e.g. #sidebar,h2. This gives you a lot more space to show the content of the iframe. To get the id of the sidebar go to Appearance -> Editor -> Click on \'Sidebar\' on the right side. Then look for the first \'div\' you find. The id of this div is the one you need. For some common templates the id is e.g. #menu, #sidebar, or #primary. For Twenty Ten and iNove you can remove the sidebar directly: Page attributes -> Template -> no sidebar. Wordpress default: \'#sidebar\'. I recommend to use firebug (see below) to find the elements and ids. You can use any valid jQuery selector pattern here! Shortcode attribute: hide_sidebar=""', 'twg-wrapper'));
        ?>
        </table>
        <p>
        <?php _e('<strong>How to find the id and the attributes:</strong><ol><li>Manually: Go to Appearance -> Editor and select the page template. The you have to look with div elements are defined. e.g. container, content, main. Also classes can be defined here. Then you have to select the style sheet below and search for this ids and classes and look which one does define the width of you content.</li><li>Firebug: For Firefox you can use the plugin firebug to select the content element directly in the page. On the right side the styles are always shown. Look for the styles that set the width or any bigger margins. This are the values you can then overwrite by the settings above.</li><li><strong>Small jquery help</strong><br>Above you have to use the jQuery syntax:<p><ul><li>- tags - if you want to hide/modify a tag directly (e.g. h1, h2) simply use it directly e.g. h1,h2</li><li>- id - if you want to hide/modify an element where you have the id use #id</li><li>- class - if you want to hide/modify an element where you have the class use .class</li></ul></p>For more complex selectors please read the jQuery documentation.</li></ol>', 'twg-wrapper'); ?>
        </p>

        <p>
            <input class="button-primary" type="submit" name="update_iframe-loader"
                   value="<?php _e('Update Settings', 'twg-wrapper') ?>"/>
        </p>

        <div id="icon-options-general" class="icon_jfu">
            <br>
        </div>
        <h2>
        <?php _e('TinyWebGallery administration settings', 'twg-wrapper'); ?></h2>

        <p>
        <?php _e('TinyWebGallery has an administration that is not written for Wordpress directly. Therefore you have to specify the url if you like to go to the admin panels directly. If you specify a user and a password you are logged in automatically and you can manage TWG there.', 'twg-wrapper'); ?>
        </p>
        <table class="form-table">
        <?php
                    printTextInput($devOptions, __('TinyWebGallery admin url', 'twg-wrapper'), 'twg_admin_url', 'Enter the full URL to your TWG administration. You have to include the full path including the index.php. e.g .http://www.tinywebgallery.com/demo/admin/index.php');
        printTextInput($devOptions, __('User name', 'twg-wrapper'), 'twg_admin_user', __('User name for the TWG administration. This user name is stored directly in the wordpress database. If you don\'t like this simply leave user and password empty. You have to enter them then at TWG directly.', 'twg-wrapper'));
        printTextInput($devOptions, __('Password', 'twg-wrapper'), 'twg_admin_pw', __('Password for the TWG administration. This password is stored directly in the wordpress database. If you don\'t like this simply leave user and password empty. You have to enter them then at TWG directly.', 'twg-wrapper'), 'password');
        printTrueFalse($devOptions, __('Show TinyWebGallery link in main menu', 'twg-wrapper'), 'show_menu_link', __('By default the TinyWebGallery link is ony shown in the settings menu. If you like to go to the administration of TWG faster set this setting to \'Yes\' and the TinyWebGallery link is shown as main menu item.', 'twg-wrapper'));
        ?>
        </table>
        <p>
            <input class="button-primary" type="submit" name="update_iframe-loader"
                   value="<?php _e('Update Settings', 'twg-wrapper') ?>"/>
        </p>
    </form>
    <div id="icon-options-general" class="icon_jfu">
        <br>
    </div>
    <h2>
    <?php _e('TinyWebGallery recommended settings', 'twg-wrapper'); ?></h2>

    <p><?php _e('If you use TinyWebGallery in an iframe there are some recommended configuration settings because the iframe is limited in space:           
        <ol>              
            <li>Disable comments or use the setting that comments are displayed inside the iframe where comments are entered</li>              
            <li>Use scrolling = No. If you select \'Yes\' IE does always show scrollbars! So only use this if needed. On the TinyWebGallery web page is a big howto about integrating TinyWebgallery into an iframe.</li>              
            <li>Check if the most viewed images fit to your iframe. If not you should disable this functionality or reduce the numbers shown there.</li>              
            <li>Check if the search result fits to your iframe. If not you should disable this functionality.</li>                
            <li>Use the integrated skin or disable at least the border. This gives you more space and a good integration into Wordpress. The integrated skin does not define any border, has a transparent background and a black text color. It has only a line between the TWG menu and the image content.</li>                
            <li>Uploading with WFU: You can use my Wordpress flash uploader plugin which enables you to offer direct upload to the gallery picture folder. WFU uses the same upload flash than TWG and is directly integrated in Wordpress and can be configured there as well.</li>                
            <li>Lytebox: If you like to use the lytebox script you have to have TWG and Wordpress on the <strong>same</strong> domain. Otherwise the Javascript in the iframe is not allowed to access the main page (same origin policy). In the preconfigred example of the TinyWebGallery wrapper the lytebox does NOT work but it does if you install everything on one domain.</li>          
        </ol>', 'twg-wrapper'); ?>
    </p>

    <div id="icon-options-general" class="icon_jfu">
        <br>
    </div>
    <h2>
    <?php _e('TinyWebGallery random image widget', 'twg-wrapper'); ?></h2>

    <p>
    <?php _e('With the TinyWebGallery wrapper you have also installed the TinyWebGallery random image widget. It displays a random image or a most viewed image.', 'twg-wrapper'); ?>
    </p>

    <p>
    <?php _e('For the widget the TinyWebGallery installation specified above is used.', 'twg-wrapper'); ?>
    </p>

    <div id="icon-options-general" class="icon_jfu">
        <br>
    </div>
    <h2>
    <?php _e('TinyWebGallery register/donate', 'twg-wrapper'); ?></h2>

    <p>
    <?php _e('This is the wrapper of TinyWebGallery and the random image function of TinyWebGallery. If you like TWG or one of my other projects like JFU, WFU or TFU you should consider to register because you can use all products with one single license and get all features of the gallery and a complete upload solution as well.', 'twg-wrapper'); ?>
    </p>

    <p>
    <?php _e('Please go <a href="http://www.tinywebgallery.com" target="_blank">www.tinywebgallery.com</a> for details.', 'twg-wrapper'); ?>
    </p>
    <br>           
<?php 
}
function printTrueFalse($options, $label, $id, $description) {
    echo '
      <tr valign="top">
      <th scope="row">' . $label . '</th>
      <td>
      ';
    echo '<input type="radio" id="' . $id . '" name="' . $id . '" value="true" ';
    if ($options[$id] == "true") {
        echo 'checked="checked"';
    }
    echo ' /> ' . __('Yes', 'twg-wrapper') . '&nbsp;&nbsp;<input type="radio" id="' . $id . '" name="' . $id . '" value="false" ';
    if ($options[$id] == "false") {
        echo 'checked="checked"';
    }
    echo '/> ' . __('No', 'twg-wrapper') . '<br>
    <em>' . $description . '</em></td>
    </tr>
    ';
}

function printAutoNo($options, $label, $id, $description) {
    echo '
      <tr valign="top">
      <th scope="row">' . $label . '</th>
      <td>
      ';
    echo '<input type="radio" id="' . $id . '" name="' . $id . '" value="auto" ';
    if ($options[$id] == "auto") {
        echo 'checked="checked"';
    }
    echo ' /> ' . __('Yes', 'twg-wrapper') . '&nbsp;&nbsp;<input type="radio" id="' . $id . '" name="' . $id . '" value="no" ';
    if ($options[$id] == "no") {
        echo 'checked="checked"';
    }
    echo '/> ' . __('No', 'twg-wrapper') . '<br>
    <em>' . $description . '</em></td>
    </tr>
    ';
}

function printTextInput($options, $label, $id, $description, $type = 'text') {
    echo '
      <tr valign="top">
      <th scope="row">' . $label . '</th>
      <td>
      <input name="' . $id . '" type="' . $type . '" size="70" id="' . $id . '" value="' . $options[$id] . '"  /><br>
      <em>' . $description . '</em></td>
      </tr>
      ';
}

function printNumberInput($options, $label, $id, $description, $type = 'text') {
    echo '
      <tr valign="top">
      <th scope="row">' . $label . '</th>
      <td>
      <input name="' . $id . '" type="' . $type . '" size="70" id="' . $id . '" onblur="checkInputNumber(this)" value="' . $options[$id] . '"  /><br>
      <em>' . $description . '</em></td>
      </tr>
      ';
}
?>
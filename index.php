<?php

/*
Plugin Name: Trustbadge Testimonials
Plugin URI: http://www.trustbadge.com
Description: With the Trustbadge Testimonials App you can collect and show customer testimonials for your Wordpress site. How you do it? 1. <a target="_blank" href="http://trustbadge.com/wordpress?utm_source=wordpress&utm_medium=software-app&utm_content=marketing-page&utm_campaign=wordpress-app">Sign up</a> for the free Trustbadge Testimonials solution. 2. Type in your Trusted Shops ID in the <a target="_blank" href="../wp-admin/options-general.php?page=tsTrustbadge_settings">plugin settings</a>. You can find a tutorial with all details <a href="http://support.trustedshops.com/en/apps/wordpress" target="_blank">here</a>.
Version: 1.0
Author: Trusted Shops
Author URI: http://support.trustedshops.com/en/apps/wordpress
License: GPLv2
*/

define('TS_TRUSTBADGE_ACTIVE', true);

if ((get_option('tsTrustbadge_variant') == 'custom') || (get_option('tsTrustbadge_variant') == 'custom_reviews')) {
    define('TS_TRUSTBADGE_VERSION', 'custom');
} else {
    define('TS_TRUSTBADGE_VERSION', 'default');
}

$tsTbShort = "tsTrustbadge_";

$tsTbDefaultSettings = array(
    'customId' => 'tbCustom_vmksjewfn934nuf90nvue4343udkvnvs',
    'defaultSize' => '90',
    'defaultOrientation' => 'topLeft'
);

$tsTbOptionSettings = array(
    array('value' => 'default', 'name' => 'Default Trustbadge'),
    array('value' => 'reviews', 'name' => 'Fixed badge: Trustmark with reviews'),
    array('value' => 'text', 'name' => 'Fixed badge: Trustmark with guarantee claim'),
    array('value' => 'small', 'name' => 'Fixed badge: Small trustmark'),
    array('value' => 'custom', 'name' => 'Individual: Trustmark only'),
    array('value' => 'custom_reviews', 'name' => 'Individual: Trustmark with customer reviews')
);

$tsTbOptionTrustcard = array(
    array('value' => 'topLeft', 'name' => 'Top left (default)'),
    array('value' => 'topRight', 'name' => 'Top right'),
    array('value' => 'bottomLeft', 'name' => 'Bottom left'),
    array('value' => 'bottomRight', 'name' => 'Bottom right')

);

$options[] = array('value' => $page->ID, 'name' => $page->post_title);

$tsTbOptions = array (

    array( "name" => "Trustbadge Settings",
        "type" => "section"),
    array( "type" => "open"),

	array( "name" => "Trusted Shops ID",
	"desc" => "Your Trusted Shops ID. You can find it in the My Trusted Shops backend. If you do not have a Trusted Shops ID, sign up via www.trustbadge.com for the free Trustbadge. ",
	"id" => $tsTbShort."tsId",
	"type" => "text",
	"std" => ""),

    array( "name" => "Distance from bottom in px",
        "desc" => "Position on the y-axis from the bottom right corner (max. 250)",
        "id" => $tsTbShort."yOffset",
        "type" => "text",
        "std" => ""),

    array( "name" => "Choose a variant",
        "desc" => "Select Trustbadge variant to display in your blog. <b>Eventually save settings to display all options</b>",
        "id" => $tsTbShort."variant",
        "type" => "select",
        "options" => $tsTbOptionSettings,
        "std" => ""),

    array( "type" => "close"),

);

if (TS_TRUSTBADGE_VERSION == 'custom') {
    $tsTbOptions[] = array(
        "name" => "Trustbadge custom settings",
        "type" => "section"
    );
    $tsTbOptions[] = array( "type" => "open");
    $tsTbOptions[] = array(
        "name" => "Trustcard orientation",
        "desc" => "Select in which direction the trustcard should open.",
        "id" => $tsTbShort."trustcardOrientation",
        "type" => "select",
        "options" => $tsTbOptionTrustcard);
    $tsTbOptions[] = array(
        "name" => "Size",
        "desc" => "In which size should the Trustbadge appear (default: 90)?",
        "id" => $tsTbShort."badgeSize",
        "type" => "text",
        "std" => $tsTbDefaultSettings['defaultSize']);
    $tsTbOptions[] = array( "type" => "close");
}

function tsTrustbadge_settings_add_admin() {

    global $tsTbShort, $tsTbOptions;

    if( isset($_REQUEST['page']) ){

        if ( $_REQUEST['page'] == 'tsTrustbadge_settings' ) {

            if ( 'save' == $_REQUEST['action'] ) {

                // check tsId

                if ($_REQUEST['tsTrustbadge_tsId'] && strlen($_REQUEST['tsTrustbadge_tsId']) == '33') {

                    foreach ($tsTbOptions as $value) {
                        update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                    foreach ($tsTbOptions as $value) {
                        if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                    header("Location: options-general.php?page=tsTrustbadge_settings&saved=true&variant=".$_REQUEST['tsTrustbadge_variant']);
                    die;

                } else {
                    header("Location: options-general.php?page=tsTrustbadge_settings&invalidShopId=true");
                    die;
                }

            }
        }

    }
    add_options_page('Trusted Shops Trustbadge', 'Trustbadge Testimonials', 'administrator', 'tsTrustbadge_settings', 'tsTrustbadge_settings_admin');

}

function tsTrustbadge_settings_add_init() {
    wp_enqueue_style("functions", plugin_dir_url( __FILE__ ) ."style.css", false, "1.0", "all");
}

function tsTrustbadge_settings_admin() {

    global $tsTbShort, $tsTbOptions, $tsTbDefaultSettings, $tsTbOptionTrustcard;

    $i=0;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>Settings saved</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>Settings restored</strong></p></div>';
    if ( $_REQUEST['invalidShopId'] ) echo '<div id="message" class="error fade"><p><strong>Please enter a valid Shop ID. <br>You can find all your Shop IDs on your <a target=_blank href="https://www.trustedshops.com/en/shop/login.html">My Trusted Shops dashboard</a></strong></p></div>';

    ?>
    <div class="wrap rm_wrap">
    <?php
    echo '<img src="'.plugin_dir_url( __FILE__ ).'e_black_50-50.png" border="0" style="float: left; margin-right: 10px;" >';
    ?>
    <h2>
        Trustbadge Testimonials
    </h2><br />

    <?php
    if (TS_TRUSTBADGE_VERSION == 'custom') {
        echo '<div id="message" class="infoMessage"><p><strong>
        You are going to use a custom variant of the Trusted Shops Trustbadge.<br><br>
        In order to display the Trustbadge you need to add this code in your template where you want it to appear.<br>
        <code>&lt;div id="'.$tsTbDefaultSettings['customId'].'"&gt;&lt;/div&gt;</code><br><br>
        With the Trustbadge custom variants you have more display options on the admin page as well.
        </strong></p></div>';

    }
    ?>

    <div class="rm_opts">
    <form method="post">
    <?php foreach ($tsTbOptions as $value) {
        switch ( $value['type'] ) {

            case "open":
                ?>

                <?php break;

            case "close":
                ?>
                <div class="rm_title"><span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="Save" />
</span><div class="clearfix"></div></div>
                </div>
                </div>
                <br />


                <?php break;

            case "title":
                ?>


                <?php break;

            case 'text':
                ?>

                <div class="rm_input rm_text">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'])  ); } else { echo $value['std']; } ?>" />
                    <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>

                </div>
                <?php
                break;

            case 'textarea':
                ?>

                <div class="rm_input rm_textarea">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                    <textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo $value['std']; } ?></textarea>
                    <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>

                </div>

                <?php
                break;

            case 'select':
                ?>

                <div class="rm_input rm_select">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

                    <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                        <?php foreach ($value['options'] as $option) { ?>
                            <option value="<?php echo $option['value']; ?>" <?php if ( get_option( $value['id'] ) == $option['value']) { echo 'selected="selected"'; } ?>><?php echo $option['name']; ?></option><?php } ?>
                    </select>

                    <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
                </div>
                <?php
                break;

            case "checkbox":
                ?>

                <div class="rm_input rm_checkbox">
                    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

                    <?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
                    <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />


                    <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
                </div>
                <?php break;

            case "section":
                $i++;

                ?>

                <div class="rm_section">
                <div class="rm_title"><h3><?php echo $value['name']; ?></h3><div class="clearfix"></div></div>
                <div class="rm_options">


                <?php break;

            case "description":
                $i++;

                ?>

                <div class="rm_description"><?php echo $value['desc']; ?><div class="clearfix"></div></div>

                <?php break;

        }
    }
    ?>

    <input type="hidden" name="action" value="save" />
    </form>
    </div>

    <div class="tb_sidebar">

    </div>


<?php
}

add_action('admin_init', 'tsTrustbadge_settings_add_init');
add_action('admin_menu', 'tsTrustbadge_settings_add_admin');

function getTrustedShopsTrustbadge() {

    global $tsTbDefaultSettings;

    $tsId = get_option('tsTrustbadge_tsId');
    $variant = get_option('tsTrustbadge_variant');
    $yOffset = get_option('tsTrustbadge_yOffset');
    $customSize = get_option('tsTrustbadge_badgeSize');
    $customTC = get_option('tsTrustbadge_trustcardOrientation');

    if (($variant == 'custom') || ($variant == 'custom_reviews')) {
        $variantJs = ",'customElementId': '".$tsTbDefaultSettings['customId']."'";
        if ($variant == 'custom') {
            $variantJs.= ", 'customBadgeWidth' : '".$customSize."'";
        } elseif ($variant == 'custom_reviews') {
            $variantJs.= ", 'customBadgeHeight' : '".$customSize."'";
        }
        $variantJs.= ", 'trustcardDirection' : '".$customTC."'";
    } else {
        $variantJs = '';
    }

    ?>
    <script type="text/javascript">
        (function () {
            var _tsid = '<?php echo "$tsId"; ?>';
            _tsConfig = {'yOffset': '<?php echo "$yOffset"; ?>','variant': '<?php echo "$variant"; ?>'<?php echo $variantJs; ?>};
            var _ts = document.createElement('script');_ts.type = 'text/javascript';_ts.async = true;_ts.src = '//widgets.trustedshops.com/js/' + _tsid + '.js';
            var __ts = document.getElementsByTagName('script')[0];__ts.parentNode.insertBefore(_ts, __ts);})();</script>
    <noscript><a href="https://www.trustedshops.de/shop/certificate.php?shop_id=<?php echo "$tsId"; ?>"><img title="Klicken Sie auf das Gütesiegel, um die Gültigkeit zu prüfen!" src="//widgets.trustedshops.com/images/badge.png" style="position:fixed;bottom:<?php echo "$yOffset"; ?>px;right:0;" /></a></noscript>
<?php }
add_action('wp_footer', 'getTrustedShopsTrustbadge' );

<?php
/**
 * Plugin Name: Chayka.LinkedIn
 * Plugin URI: git@github.com:chayka/Chayka.LinkedIn.wpp.git
 * Description: LinkedIn integration for Chayka Framework
 * Version: 0.0.1
 * Author: Boris Mossounov <borix@tut.by>
 * Author URI: http://anotherguru.me/
 * License: MIT
 */

require_once 'vendor/autoload.php';

if(!class_exists("Chayka\\WP\\Plugin")){
    add_action( 'admin_notices', function () {
?>
    <div class="error">
        <p>Chayka.Core plugin is required in order for Chayka.LinkedIn to work properly</p>
    </div>
<?php
	});
}else{
    require_once dirname(__FILE__).'/Plugin.php';
	add_action('init', array("Chayka\\LinkedIn\\Plugin", "init"));
}

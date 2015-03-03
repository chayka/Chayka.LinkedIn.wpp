<?php
/**
 * Created by PhpStorm.
 * User: borismossounov
 * Date: 19.02.15
 * Time: 23:43
 */

namespace Chayka\LinkedIn;


use Chayka\WP\Helpers\AngularHelper;
use Chayka\WP\Helpers\ResourceHelper;

class HtmlHelper {

	/**
	 * Render view with supplied vars
	 *
	 * @param string $path
	 * @param array $vars
	 * @param bool $output
	 *
	 * @return string
	 */
	public static function renderView($path, $vars = array(), $output = true){
		$view = Plugin::getView();
		foreach($vars as $key=>$val){
			$view->assign($key, $val);
		}
		$res = $view->render($path);
		if($output){
			echo $res;
		}
		return $res;
	}

	/**
	 * Render JS SDK init
	 *
	 * @param string $locale
	 */
	public static function renderJsInit($locale = ''){
		if(LinkedInHelper::isJsApiEnabled()) {
			self::renderView( 'linkedin/js-init.phtml', array(
				'apiKey'  => LinkedInHelper::getApiKey(),
				'locale' => $locale ? $locale : NlsHelper::getLocale(),
			) );
			AngularHelper::enqueueScriptStyle('chayka-auth');
			ResourceHelper::enqueueScript('chayka-linkedin');
		}
	}

}
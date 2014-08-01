<?php

/**
 * Helper class for elFinder widgets
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 * @link http://rob006.net/
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
class ElFinderHelper extends CComponent {

	/**
	 * Available elFinder translations
	 * @see directory: assets/js/i18n
	 * @var array
	 */
	public static $availableLanguages = array(
		'ar',
		'bg',
		'ca',
		'cs',
		'da',
		'de',
		'el',
		'en',
		'es',
		'fa',
		'fr',
		'hu',
		'it',
		'jp',
		'ko',
		'nl',
		'no',
		'pl',
		'pt_BR',
		'ru',
		'sk',
		'sl',
		'sv',
		'tr',
		'vi',
		'zh_CN',
		'zh_TW',
	);

	/**
	 * Register necessary elFinder scripts and styles
	 */
	public static function registerAssets() {
		$dir = dirname(__FILE__) . '/assets';
		$assetsDir = Yii::app()->assetManager->publish($dir);
		$cs = Yii::app()->getClientScript();

		if (Yii::app()->getRequest()->enableCsrfValidation) {
			$csrfTokenName = Yii::app()->request->csrfTokenName;
			$csrfToken = Yii::app()->request->csrfToken;
			Yii::app()->clientScript->registerMetaTag($csrfToken, 'csrf-token', null, array(), 'csrf-token');
			Yii::app()->clientScript->registerMetaTag($csrfTokenName, 'csrf-param', null, array(), 'csrf-param');
		}

		// jQuery and jQuery UI
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('jquery.ui');
		$cs->registerCssFile($cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css');

		// elFinder CSS
		if (YII_DEBUG) {
			$cs->registerCssFile($assetsDir . '/css/elfinder.full.css');
		} else {
			$cs->registerCssFile($assetsDir . '/css/elfinder.min.css');
		}

		// elFinder JS
		if (YII_DEBUG) {
			$cs->registerScriptFile($assetsDir . '/js/elfinder.full.js');
		} else {
			$cs->registerScriptFile($assetsDir . '/js/elfinder.min.js');
		}

		// elFinder translation
		$lang = Yii::app()->language;
		if (!in_array($lang, self::$availableLanguages)) {
			if (strpos($lang, '_')) {
				$lang = substr($lang, 0, strpos($lang, '_'));
				if (!in_array($lang, self::$availableLanguages))
					$lang = false;
			} else {
				$lang = false;
			}
		}
		if ($lang !== false)
			$cs->registerScriptFile($assetsDir . '/js/i18n/elfinder.' . $lang . '.js');

		// some css fixes
		Yii::app()->clientScript->registerCss('elfinder-file-bg-fixer', '.elfinder-cwd-file,.elfinder-cwd-file .elfinder-cwd-file-wrapper,.elfinder-cwd-file .elfinder-cwd-filename{background-image:none !important;}');

	}

}

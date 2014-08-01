<?php

/**
 * elFinder connector
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 * @link http://rob006.net/
 * @author Bogdan Savluk <Savluk.Bogdan@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
class ElFinderConnectorAction extends CAction {

	/**
	 * Connector configuration
	 * @see https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
	 * @var array
	 */
	public $settings = array();

	public function run() {
		require_once(dirname(__FILE__) . '/php/elFinderConnector.class.php');
		require_once(dirname(__FILE__) . '/php/elFinder.class.php');
		require_once(dirname(__FILE__) . '/php/elFinderVolumeDriver.class.php');
		require_once(dirname(__FILE__) . '/php/elFinderVolumeLocalFileSystem.class.php');
		require_once(dirname(__FILE__) . '/php/elFinderVolumeMySQL.class.php');
		require_once(dirname(__FILE__) . '/php/elFinderVolumeFTP.class.php');

		// path for icons
		$dir = dirname(__FILE__) . '/assets';
		$assetsURL = Yii::app()->assetManager->getPublishedUrl($dir);
		define('ELFINDER_IMG_PARENT_URL', $assetsURL);

		$fm = new elFinderConnector(new elFinder($this->settings));
		$fm->run();
	}

}

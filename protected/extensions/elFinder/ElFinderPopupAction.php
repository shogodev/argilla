<?php
/**
 * Class ElFinderPopupAction
 */
class ElFinderPopupAction extends CAction {

	/**
	 * @var string
	 */
	public $connectorRoute = false;

	/**
	 * Popup title
	 * @var string
	 */
	public $title = 'File uploder';

	/**
	 * Client settings.
	 * @see https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
	 * @var array
	 */
	public $settings = array();

	public function run() {
		Yii::import('ext.elFinder.ElFinderHelper');
		ElFinderHelper::registerAssets();

		if (empty($this->connectorRoute))
			throw new CException('$connectorRoute must be set!');

		$settings = array(
			'url' => $this->controller->createUrl($this->connectorRoute),
			'lang' => Yii::app()->language,
		);

		$this->controller->layout = false;
		$this->controller->render('ext.elFinder.views.elfinder_popup', array(
      'title' => $this->title,
			'settings' => CJavaScript::encode($settings))
    );
	}
}

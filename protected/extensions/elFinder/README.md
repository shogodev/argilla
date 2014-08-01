elFinder 2.x Yii extension
==========================

Based on https://bitbucket.org/z_bodya/yii-elfinder with updated [elFinder](https://github.com/Studio-42/elFinder) and some code improvements

How to use
--------------------------

0. Checkout source code to your project to ext.elFinder
1. Create controller for connector action, and configure it params

	```php
	class ElfinderController extends Controller {

		// don't forget configure access rules

		public function actions() {
			return array(
				// main action for elFinder connector
				'connector' => array(
					'class' => 'ext.elFinder.ElFinderConnectorAction',
					// elFinder connector configuration
					// https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
					'settings' => array(
						'roots' => array(
							array(
								'driver' => 'LocalFileSystem',
								'path' => Yii::getPathOfAlias('webroot') . '/files/',
								'URL' => Yii::app()->baseUrl . '/files/',
								'alias' => 'Root Alias',
								'acceptedName' => '/^[^\.].*$/', // disable creating dotfiles
								'attributes' => array(
									array(
										'pattern' => '/\/[.].*$/', // hide dotfiles
										'read' => false,
										'write' => false,
										'hidden' => true,
									),
								),
							)
						),
					)
				),
				// action for TinyMCE popup with elFinder widget
				'elfinderTinyMce' => array(
					'class' => 'ext.elFinder.TinyMceElFinderPopupAction',
					'connectorRoute' => 'connector', // main connector action id
				),
				// action for file input popup with elFinder widget
				'elfinderFileInput' => array(
					'class' => 'ext.elFinder.ServerFileInputElFinderPopupAction',
					'connectorRoute' => 'connector', // main connector action id
				),
			);
		}

	}
	```

2. ServerFileInput - use this widget to choose file on server using elFinder pop-up

	```php
	$this->widget('ext.elFinder.ServerFileInput', array(
		'model' => $model,
		'attribute' => 'field_name',
		'popupConnectorRoute' => 'elfinder/elfinderFileInput', // relative route for file input action
		// ability to customize "Browse" button
	//	'customButton' => TbHtml::button('Browse images', array(
	//		'id' => TbHtml::getIdByName(TbHtml::activeName($model, 'field_name')) . 'browse',
	//		'class' => 'btn', 'color' => TbHtml::BUTTON_COLOR_DEFAULT,
	//		'size' => TbHtml::BUTTON_SIZE_DEFAULT, 'style' => 'margin-left:10px;')),
		// title for popup window (optional)
		'popupTitle' => 'Files',
	));
	```

3. ElFinderWidget - use this widget to manage files

	```php
	$this->widget('ext.elFinder.ElFinderWidget', array(
		'connectorRoute' => 'elfinder/connector', // relative route for elFinder connector action
	));
	```

4. TinyMceElFinder - use this widget to integrate elFinder with [yii-tinymce](https://bitbucket.org/z_bodya/yii-tinymce)

	```php
	$this->widget('ext.tinymce.TinyMce', array(
		'model' => $model,
		'attribute' => 'content',
		'fileManager' => array(
			'class' => 'ext.elFinder.TinyMceElFinder',
			'popupConnectorRoute' => 'elfinder/elfinderTinyMce', // relative route for TinyMCE popup action
			// title for popup window (optional)
			'popupTitle' => "Files",
		),
	));
	```

<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class BFileUploaderController extends BController
{
  public function beforeAction($action)
  {
    Yii::app()->log->getRoutes()[1]->enabled = false;
    return parent::beforeAction($action);
  }

  public function actions()
  {
    return array(
      'connector' => array(
        'class' => 'ext.elFinder.ElFinderConnectorAction',
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        'settings' => array(
          'roots' => array(
            array(
              'driver' => 'LocalFileSystem',
              'path' => realpath(Yii::getPathOfAlias('frontend').'/..') . '/f/upload',
              'URL' => '/f/upload/',
              'alias' => '/f/upload/',
              'acceptedName' => '/^[^\.].*$/',
              'attributes' => array(
                array(
                  'pattern' => '/\/[.].*$/',
                  'read' => false,
                  'write' => false,
                  'hidden' => true,
                ),
              ),
            )
          ),
        )
      ),

      'elfinderCKEditor' => array(
        'class' => 'ext.elFinder.ElFinderPopupAction',
        'connectorRoute' => 'connector',
      ),
    );
  }
} 
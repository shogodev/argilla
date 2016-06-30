<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */
Yii::import('backend.components.*');
Yii::import('backend.components.db.*');
Yii::import('backend.components.interfaces.*');
Yii::import('backend.modules.product.models.*');
Yii::import('backend.models.behaviors.*');
Yii::import('frontend.share.behaviors.*');
Yii::import('frontend.share.helpers.*');
Yii::import('frontend.share.formatters.*');
Yii::import('frontend.share.validators.*');
Yii::import('frontend.extensions.upload.components.*');
Yii::import('frontend.share.validators.*');

Yii::import('backend.modules.product.components.FacetIndexer');
Yii::import('frontend.commands.components.*');

/**
 * Class IndexerCommand
 *
 * Комана для индексирования паарметров фасеточного поиска
 */
class IndexerCommand extends LoggingCommand
{
  const MAX_CHUNK_SIZE = 10000;

  /**
   * @var CDbCommandBuilder
   */
  private $builder;

  /**
   * @var int
   */
  private $insertedRecords = 0;

  /**
   * @var FacetIndexer $facetIndexer
   */
  private $facetIndexer;

  public function init()
  {
    parent::init();

    $this->builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $this->facetIndexer = new FacetIndexer(self::MAX_CHUNK_SIZE);
    $this->facetIndexer->attachEventHandler('onSaveRecords', array($this, 'onSaveRecords'));

    $this->logger->startTimer(get_class($this));
  }

  public function actionRefresh()
  {
    $this->logger->log('Начало индексеции фильтров');

    $this->facetIndexer->reindexAll();

    $this->showSummary();

    $this->updateProduction();

    $this->logger->log('Индексеция фильтров завершена', true, true);
  }

  public function actionDelete()
  {
    $this->facetIndexer->clearIndex();
    $this->updateProduction();
  }

  public function onSaveRecords(CEvent $event)
  {
    $count = $event->params['count'];

    $this->logger->log('Обработано '.Yii::app()->format->formatNumber($count).' записей', true, true);
    $this->insertedRecords += $count;
  }

  private function updateProduction()
  {
    $path = Yii::getPathOfAlias('frontend.config.production').'.php';
    if( file_exists($path) )
      touch($path);
  }

  private function showSummary()
  {
    if( $this->insertedRecords )
    {
      $message = 'Обработано записей: '.Yii::app()->format->formatNumber($this->insertedRecords).PHP_EOL;
      $message .= $this->logger->finishTimer(get_class($this), 'Время выполнения: ');
      $this->logger->log($message, true, true);
    }
    else
    {
      throw new CException('Обработано 0 записей', 500);
    }
  }
}
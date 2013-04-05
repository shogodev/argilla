<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class BInputTest extends CTestCase
{
  public function setUp()
  {
    parent::setUp();

    Yii::app()->setUnitEnvironment('Info', 'BInfo', 'update', array('id' => '1'));
  }

  public function testGetLabel()
  {
    /**
     * @var BInfo $model
     */
    $model = BInfo::model()->findByPk(1);
    /**
     * @var BActiveForm $form
     */
    $form = Yii::app()->controller->beginWidget('BActiveForm', array('id' => $model->getFormId()));

    $checkedWord = 'Label test';

    $row = $form->textAreaRow($model, 'name', array('label' => $checkedWord));
    $this->assertTrue($this->findText($row, $checkedWord), 'failed textAreaRow '.$checkedWord);

    $row = $form->textRow($model, 'name', array('label' => $checkedWord));
    $this->assertTrue($this->findText($row, $checkedWord), 'failed textRow '.$checkedWord);
  }

  public function testShowHintPopup()
  {
    /**
     * @var BInfo $model
     */
    $model = BInfo::model()->findByPk(1);
    /**
     * @var BActiveForm $form
     */
    $form = Yii::app()->controller->beginWidget('BActiveForm', array('id' => $model->getFormId()));

    $setWord = 'Help hint';
    $checkedWord = 'title="Help hint';

    $row = $form->textAreaRow($model, 'name', array('popupHint' => $setWord));
    $this->assertTrue($this->findText($row, $checkedWord), 'failed textAreaRow '.$checkedWord);

    $row = $form->textRow($model, 'name', array('popupHint' => $setWord));
    $this->assertTrue($this->findText($row, $checkedWord), 'failed textRow '.$checkedWord);
  }

  protected function findText($row, $checkedWord)
  {
    return mb_strpos($row, $checkedWord) === false ? false : true;
  }
}
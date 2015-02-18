<?php echo "<?php\n";?>
/**
 * @author <?php echo isset(Yii::app()->params['author']) ? Yii::app()->params['author']."\n" : "... <...@...>\n"?>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-<?php echo date('Y')?> Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения:
 * public function behaviors()
 * {
 *   return CMap::mergeArray(parent::behaviors(), array(
 *     '<?php echo strtolower(substr($class, 0, 1)).substr($class, 1)?>' => array('class' => '<?php echo $class?>')
 *   ));
 * }
*/
class <?php echo $class?> extends <?php echo $baseClass."\n"; ?>
{
  public function init()
  {
    $this->set<?php echo $form?>();
  }

  /**
   * @return FForm
   */
  public function get<?php echo $form?>()
  {
    return new FForm('<?php echo $form?>', new <?php echo $model?>());
  }

  public function set<?php echo $form?>()
  {
    $form = $this->get<?php echo $form?>();

    if( Yii::app()->request->getPost('<?php echo $model?>') )
    {
      Yii::app()->setController($this->owner);
      $form->ajaxValidation();

      if( $form->save() )
      {
        $form->sendNotificationBackend();
        $form->responseSuccess(
          $this->owner->textBlockRegister('Текст успешной отправки формы <?php echo mb_strtolower($name)?>', '<div class="m7">Данные успешно отправлены</div>', null)
        );
      }

      Yii::app()->end();
    }
  }
}
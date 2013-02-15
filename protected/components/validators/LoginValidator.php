<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.validators.LoginValidator
 */
class LoginValidator extends CValidator
{
  const MAX_LENGTH = 15;

  const MIN_LENGTH = 2;

  const MAX_WORDS_COUNT = 3;

  /**
   * @var array
   */
  protected $delimiters = array(
    ' ', '-'
  );

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateAttribute($object, $attribute)
  {
    $this->validateLength($object, $attribute);
    $this->validateLetters($object, $attribute);
    $this->validateCharRegister($object, $attribute);
    $this->validateLoginBorders($object, $attribute);
    $this->validateDelimiters($object, $attribute);
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateLength($object, $attribute)
  {
    if( $this->strlen($object->{$attribute}) > self::MAX_LENGTH )
      $this->addError($object, $attribute, 'Логин не может быть более '.self::MAX_LENGTH.' символов');

    if( $this->strlen($object->{$attribute}) < self::MIN_LENGTH )
      $this->addError($object, $attribute, 'Логин не может быть менее '.self::MIN_LENGTH.' символов');

    $this->validateWordsCount($object, $attribute);
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateLetters($object, $attribute)
  {
    if( !preg_match('/^[a-zA-Z-_]+$/', $object->{$attribute}) )
    {
      $error = 'Логин может содержать только буквы английского алфавита.
                В качестве разделительных символов можно использовать пробел или тире "-"';

      $this->addError($object, $attribute, $error);
    }
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateCharRegister($object, $attribute)
  {
    if( preg_match('/[a-z][A-Z]/', $object->{$attribute}) )
    {
      $error = 'Логин не может содержать заглавную букву после обычной';
      $this->addError($object, $attribute, $error);
    }
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateLoginBorders($object, $attribute)
  {
    $excluded = array(
      ' ', '-', '_'
    );

    foreach( $excluded as $char )
    {
      if( substr($object->{$attribute}, -1, 1) === $char || stripos($object->{$attribute}, $char) === 0 )
      {
        $error = 'Логин не может начинаться или заканчиваться пробелом, подчеркиванием или тире';

        $this->addError($object, $attribute, $error);
        break;
      }
    }
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  public function validateDelimiters($object, $attribute)
  {
    foreach( $this->delimiters as $delimiter )
    {
      if( strpos($object->{$attribute}, $delimiter.$delimiter) !== false )
      {
        $error = 'Запрещено использовать два разделительных символа подряд';

        $this->addError($object, $attribute, $error);
        break;
      }
    }
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateWordsCount($object, $attribute)
  {
    $specialDelimiter = '%';

    foreach( $this->delimiters as $delimiter )
    {
      $object->{$attribute} = str_replace($delimiter, $specialDelimiter, $object->{$attribute});
    }

    $data = explode($specialDelimiter, $object->{$attribute});

    if( count($data) > self::MAX_WORDS_COUNT )
      $this->addError($object, $attribute, 'Логин не может состоять более чем из '.self::MAX_WORDS_COUNT.' слов');
  }

  /**
   * @param string $data
   *
   * @return int
   */
  protected function strlen($data)
  {
    return mb_strlen($data, 'utf-8');
  }
}
<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>, Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.validators
 */
class LoginValidator extends CValidator
{
  const MAX_LENGTH = 15;

  const MIN_LENGTH = 2;

  const MAX_WORDS_COUNT = 3;

  protected $delimiters =  '\ \_\-';

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateAttribute($object, $attribute)
  {
    $this->validateLength($object, $attribute);
    $this->validateLetters($object, $attribute);
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
      $this->addError($object, $attribute, '{attribute} не может быть более '.self::MAX_LENGTH.' символов');

    if( $this->strlen($object->{$attribute}) < self::MIN_LENGTH )
      $this->addError($object, $attribute, '{attribute} не может быть менее '.self::MIN_LENGTH.' символов');

    $this->validateWordsCount($object, $attribute);
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateLetters($object, $attribute)
  {
    if( !preg_match('/^[a-zA-ZА-ЯЁа-пр-яё0-9'.$this->delimiters.']+$/u', $object->{$attribute}) )
    {
      $error = '{attribute} может содержать только цыфры и буквы английского или русского алфавита.
                В качестве разделительных символов можно использовать пробел, тире "-" и нижнее подчеркивание "_".';

      $this->addError($object, $attribute, $error);
    }

    if( preg_match('/[a-zA-Z]+/u', $object->{$attribute}) && preg_match('/[А-ЯЁа-пр-яё]+/u', $object->{$attribute}) )
      $this->addError($object, $attribute, '{attribute} может содержать буквы только одного алфавита: английского или русского.');
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateLoginBorders($object, $attribute)
  {
    if( preg_match('/^['.$this->delimiters.']+/', $object->{$attribute}) || preg_match('/['.$this->delimiters.']+$/', $object->{$attribute}) )
      $this->addError($object, $attribute, '{attribute} не может начинаться или заканчиваться пробелом, подчеркиванием или тире');
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  public function validateDelimiters($object, $attribute)
  {
    if( preg_match('/['.$this->delimiters.']+['.$this->delimiters.']+/', $object->{$attribute}) )
      $this->addError($object, $attribute, 'Запрещено использовать два разделительных символа подряд');
  }

  /**
   * @param CModel $object
   * @param string $attribute
   */
  protected function validateWordsCount($object, $attribute)
  {
    $words = preg_split('/['.$this->delimiters.']+/', $object->{$attribute});
    if( count($words) > self::MAX_WORDS_COUNT )
      $this->addError($object, $attribute, '{attribute} не может состоять более чем из '.self::MAX_WORDS_COUNT.' слов');
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
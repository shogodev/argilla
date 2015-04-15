<?php
Yii::import('system.gii.generators.model.ModelCode');

class BModelCode extends ModelCode
{
  public $labelException = array('id');

  public $requiredException = array('position', 'img', 'visible');

  private $defaultSort;

  public function generateLabels($table)
  {
    $labels = array();

    foreach($table->columns as $column)
    {
      if( in_array($column->name, $this->labelException) )
        continue;

      if( $this->commentsAsLabels && $column->comment )
        $labels[$column->name] = $column->comment;
      else
      {
        $label = ucwords(trim(strtolower(str_replace(array('-', '_'), ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $column->name)))));
        $label = preg_replace('/\s+/', ' ', $label);
        if( strcasecmp(substr($label, -3), ' id') === 0 )
          $label = substr($label, 0, -3);
        if( $label === 'Id' )
          $label = 'ID';
        $label = str_replace("'", "\\'", $label);
        $labels[$column->name] = $label;
      }
    }

    return $labels;
  }

  public function generateRules($table)
  {
    $rules = array();
    $required = array();
    $integers = array();
    $numerical = array();
    $length = array();
    $safe = array();
    $emails = array();

    /**
     * @var CDbColumnSchema $column
     */
    foreach($table->columns as $column)
    {
      if( $column->autoIncrement )
        continue;

      $r = !$column->allowNull && $column->defaultValue === null && $column->dbType != 'text' && $column->dbType != 'timestamp';

      if( $r && !in_array($column->name, $this->requiredException) )
        $required[] = $column->name;
      if( $column->type === 'integer' )
        $integers[] = $column->name;
      elseif( $column->type === 'double' )
        $numerical[] = $column->name;
      elseif( $column->type === 'string' && $column->size > 0 )
        $length[$column->size][] = $column->name;
      elseif( !$column->isPrimaryKey && !$r )
        $safe[] = $column->name;

      if( $column->name == 'email')
        $emails[] = $column->name;
    }

    if( $required !== array() )
      $rules[] = "array('".implode(', ', $required)."', 'required')";
    if( $integers !== array() )
      $rules[] = "array('".implode(', ', $integers)."', 'numerical', 'integerOnly' => true)";
    if( $numerical !== array() )
      $rules[] = "array('".implode(', ', $numerical)."', 'numerical')";
    if( $length !== array() )
    {
      foreach($length as $len => $cols)
        $rules[] = "array('".implode(', ', $cols)."', 'length', 'max' => $len)";
    }
    if( $emails !== array() )
      $rules[] = "array('".implode(', ', $emails)."', 'email')";
    if( $safe !== array() )
      $rules[] = "array('".implode(', ', $safe)."', 'safe')";

    return $rules;
  }

  public function getDefaultSort()
  {
    if( is_null($this->defaultSort) )
    {
      $this->defaultSort = '';

      if( $timestampAttribute = $this->getTimestampAttribute() )
        $this->defaultSort = $timestampAttribute;
    }

    return $this->defaultSort;
  }

  public function getTimestampAttribute()
  {
    $table = Yii::app()->db->getSchema()->getTable($this->tableName);

    foreach($table->columns as $column)
    {
      if( $column->dbType == 'timestamp' )
      {
        return $column->name;
      }
    }

    return null;
  }
}

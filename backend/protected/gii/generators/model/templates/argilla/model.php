<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
?>
<?php echo "<?php\n"; ?>
/**
 * @author ... <...@...>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ...
 *
 * @method static <?php echo $modelClass; ?> model(string $class = __CLASS__)
 *
<?php foreach($columns as $column): ?>
 * @property <?php echo $column->type.' $'.$column->name."\n"; ?>
<?php endforeach; ?>
<?php if(!empty($relations)): ?>
 *
<?php foreach($relations as $name => $relation): ?>
 * @property <?php
  if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches))
    {
      $relationType = $matches[1];
      $relationModel = $matches[2];

      switch($relationType){
          case 'HAS_ONE':
              echo $relationModel.' $'.$name."\n";
          break;
          case 'BELONGS_TO':
              echo $relationModel.' $'.$name."\n";
          break;
          case 'HAS_MANY':
              echo $relationModel.'[] $'.$name."\n";
          break;
          case 'MANY_MANY':
              echo $relationModel.'[] $'.$name."\n";
          break;
          default:
              echo 'mixed $'.$name."\n";
      }
  }
    ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?php echo $modelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
  public function rules()
  {
    return array(
<?php foreach($rules as $rule): ?>
      <?php echo $rule.",\n"; ?>
<?php endforeach; ?>
      array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on' => 'search'),
    );
  }

<?php if(!empty($relations)): ?>
  public function relations()
  {
    return array(
<?php foreach($relations as $name => $relation): ?>
      <?php echo "'$name' => $relation,\n"; ?>
<?php endforeach; ?>
    );
  }

<?php endif; ?>
  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

<?php
foreach($columns as $name=>$column)
{
  if( $column->type === 'string' )
  {
    echo "\t\t\$criteria->compare('$name', \$this->$name, true);\n";
  }
  else
  {
    echo "\t\t\$criteria->compare('$name', \$this->$name);\n";
  }
}
?>

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}
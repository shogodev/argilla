<?php
/**
 * Проверка БД с которой работает движок - доступы, настройки самой БД и т.д.
 *
 * @author Fedor A Borshev <fedor@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package build.tasks.checkDbTask
 */

require_once "phing/Task.php";

class CheckDbTask extends Task
{

  /**
   * @var boolean проверять доступ к триггерам
   */
  private $checkTriggers;

  /**
   * @var boolean проверять доступ к созданию и просмотру структуры вьюх
   */
  private $checkViews;

  /**
   * @var boolean проверять доступ к созданию и изменению хранимых процедур
   */
  private $checkRoutines;

  /**
   * @var boolean проверять доступ к LOCK TABLES
   */
  private $checkLockTables;

  /**
   * @var boolean проверять доступ к CREATE TMP TABLE
   */
  private $checkTmpTable;

  /**
   * @var boolean проверять включен ли InnoDB
   */
  private $checkInnoDb;

  private $pdo;
  private $connected = false;

  private $accessRights;
  
  private $user;
  private $host;

  public function setTriggers($val)
  {
    $this->checkTriggers = $val;
  }

  public function setViews($val)
  {
    $this->checkViews = $val;
  }

  public function setRoutines($val)
  {
    $this->checkRoutines = $val;
  }

  public function setLockTables($val)
  {
    $this->checkLockTables = $val;
  }

  public function setTmpTables($val)
  {
    $this->checkTmpTable = $val;
  }

  public function setInnoDb($val)
  {
    $this->checkInnoDb = $val;
  }
  
  public function main()
  {
    if(!$this->connected)
      $this->connect();

    if($this->checkTriggers)
      $this->checkPrivelege('Trigger');

    if($this->checkViews)
    {
      $this->checkPrivelege('Show_view');
      $this->checkPrivelege('Create_view');
    }

    if($this->checkRoutines)
    {
      $this->checkPrivelege('Create_routine');
      $this->checkPrivelege('Alter_routine');
    }

    if($this->checkLockTables)
    {
      $this->checkPrivelege('Lock_tables');
    }

    if($this->checkTmpTable)
    {
      $this->checkPrivelege('Create_tmp_table');
    }

    if($this->checkInnoDb)
    {
      $this->checkEngine('InnoDB');
    }
  }

  protected function checkPrivelege($priv)
  {
    if(!$this->hasPrivelege($priv))
      throw new BuildException("Required DB privelege '$priv' not granted for '" . $this->user . '@' . $this->host . "' on db '" . $this->project->getProperty('db.dbname') . "'");
  }

  protected function checkEngine($engine)
  {
    $q = $this->pdo->query('SHOW ENGINES;');
    $q->execute();
    foreach($q as $row)
    {
      if($row['Engine'] == $engine and ($row['Support'] == 'YES' or $row['Support'] =='DEFAULT'))
        return true;
    }
    throw new BuildException("Required Engine '$engine' is not supported by current DB driver");
  }

  protected function hasPrivelege($priv)
  {
    $q = $this->pdo->prepare("SELECT * FROM mysql.user WHERE Host = :host AND User = :user AND ${priv}_priv = 'Y';"); // check global privelegies at first
    $q->execute(array(
      ':user' => $this->project->getProperty('db.mysqlUser'),
      ':host' => $this->project->getProperty('db.mysqlHost'),
    ));
    if(sizeOf($q->fetchAll()))
      return true;

    $q = $this->pdo->prepare("SELECT * FROM mysql.db WHERE Host = :host AND User = :user AND Db = :db AND ${priv}_priv= 'Y';"); // and then privelegies for current db
    $q->execute(array(
      ':user' => $this->project->getProperty('db.mysqlUser'),
      ':host' => $this->project->getProperty('db.mysqlHost'),
      ':db'   => $this->project->getProperty('db.dbname'),
    ));
    if(sizeOf($q->fetchAll()))
      return true;

    return false;
  }


  private function connect()
  {
    $dsn = $dsn = $this->project->getProperty('db.driver') . ':' . 'host=' . $this->project->getProperty('db.host');

    try
    {
      $this->pdo = new PDO($dsn,
        $this->project->getProperty('db.username'),
        $this->project->getProperty('db.password')
      );
    } catch (PDOException $e) {
      throw new BuildException("CheckDbPermissionsTask: Could not connect to PDO: $dsn");
    }
    $this->connected = true;
  }

}


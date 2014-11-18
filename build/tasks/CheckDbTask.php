<?php
/**
 * Проверка БД с которой работает движок - доступы, настройки самой БД и т.д.
 * @author Fedor A Borshev <fedor@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
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

  /**
   * @var PDO
   */
  private $pdo;
  /**
   * @var bool
   */
  private $_connected = false;

  private $_user;
  private $_host;
  private $_dbName;

  /**
   * @var PrivilegeChecker
   */
  private $_privilegeChecker;

  #region setters
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

  #endregion

  public function main()
  {
    if( !$this->_connected )
    {
      $this->connect();
    }

    try
    {
      $this->_privilegeChecker = new PrivilegeChecker(PrivilegeChecker::getShowGrantsOutput($this->pdo));
    }
    catch(Exception $e)
    {
      if( strpos($e->getMessage(), 'The MySQL server is running with the --skip-grant-tables') !== false )
      {
        return;
      }
      else
      {
        throw $e;
      }
    }

    if( $this->checkTriggers )
    {
      $this->checkPrivilege(PrivilegeEnum::TRIGGER);
    }

    if( $this->checkViews )
    {
      $this->checkPrivilege(PrivilegeEnum::SHOW_VIEW);
      $this->checkPrivilege(PrivilegeEnum::CREATE_VIEW);
    }

    if( $this->checkRoutines )
    {
      $this->checkPrivilege(PrivilegeEnum::CREATE_ROUTINE);
      $this->checkPrivilege(PrivilegeEnum::ALTER_ROUTINE);
    }

    if( $this->checkLockTables )
    {
      $this->checkPrivilege(PrivilegeEnum::LOCK_TABLES);
    }

    if( $this->checkTmpTable )
    {
      $this->checkPrivilege(PrivilegeEnum::CREATE_TEMPORARY_TABLES);
    }

    if( $this->checkInnoDb )
    {
      $this->checkEngine('InnoDB');
    }
  }

  protected function checkPrivilege($privilege)
  {
    if( !$this->hasPrivilege($privilege) )
    {
      throw new BuildException(
        "Required DB privelege '{$privilege}' not granted for '{$this->_user}'@'{$this->_host}' on db '{$this->_dbName}'");
    }
  }

  protected function checkEngine($engine)
  {
    $q = $this->pdo->query('SHOW ENGINES;');
    $q->execute();
    foreach($q as $row)
    {
      if( $row['Engine'] == $engine and ($row['Support'] == 'YES' or $row['Support'] == 'DEFAULT') )
        return true;
    }
    throw new BuildException("Required Engine '$engine' is not supported by current DB driver");
  }

  protected function hasPrivilege($privilege)
  {
    return $this->_privilegeChecker->hasPrivilege($privilege, $this->_dbName, $this->_user, $this->_host);
  }

  private function connect()
  {
    $dsn = $dsn = $this->project->getProperty('db.driver').':'.'host='.$this->project->getProperty('db.host');

    try
    {
      $this->pdo = new PDO($dsn,
        $this->project->getProperty('db.username'),
        $this->project->getProperty('db.password'));
    }
    catch(PDOException $e)
    {
      throw new BuildException("CheckDbPermissionsTask: Could not connect to PDO: $dsn");
    }

    $this->_connected = true;
    $this->_user = $this->project->getProperty('db.mysqlUser');
    $this->_host = $this->project->getProperty('db.mysqlHost');
    $this->_dbName = $this->project->getProperty('db.dbname');
  }
}


/**
 * Привилегии.
 */
class PrivilegeEnum
{
  const TRIGGER = 'trigger';
  const SHOW_VIEW = 'show view';
  const CREATE_VIEW = 'create view';
  const CREATE_ROUTINE = 'create routine';
  const ALTER_ROUTINE = 'alter routine';
  const LOCK_TABLES = 'lock tables';
  const CREATE_TEMPORARY_TABLES = 'create temporary tables';
}


/**
 * Инкапсулиреут правила проверки привилегий.
 */
class PrivilegeChecker
{
  /**
   * Возвращает результат команды SHOW GRANTS;
   *
   * @param PDO $connection Соединение с базой данных.
   *
   * @throws BadMethodCallException
   * @return array Результат команды SHOW GRANTS;
   */
  public static function getShowGrantsOutput(PDO $connection)
  {
    if( !$query = $connection->query('SHOW GRANTS;') )
    {
      throw new BadMethodCallException($connection->errorInfo()[2], $connection->errorInfo()[1]);
    }

    return $query->fetchAll();
  }

  /**
   * @var string[] Массив GRANT команд.
   */
  private $_showGrantsOutput;

  /**
   * Создает новый объект для проверки привилегий в базе данных для пользователя.
   *
   * @param string[] $showGrantsRawOutput Результат команды SHOW GRANTS;
   */
  public function __construct(array $showGrantsRawOutput)
  {
    $this->_showGrantsOutput = $this->preprocess($showGrantsRawOutput);
  }

  /**
   * Проверает наличие указанной привилегии для определенного пользователя и хоста.
   *
   * @param string $privilege Название привилегии (см. PrivilegeEnum)
   * @param string $db Имя базы данных.
   * @param string $user Имя пользователя.
   * @param string $host Имя хоста.
   *
   * @return bool true в случае наличия привилегии, иначе false.
   */
  public function hasPrivilege($privilege, $db, $user, $host)
  {
    try
    {
      $grantQuery = $this->findRow($this->_showGrantsOutput, $db, $user, $host);
    }
    catch(UnexpectedValueException $e)
    {
      return false;
    }

    return $this->checkPrivilege($grantQuery, $privilege);
  }

  /**
   * Выполняет предварительную обработку для результата команды SHOW GRANTS;
   *
   * @param string $showGrantsRawOutput Результат команды SHOW GRANTS;
   *
   * @return string[] Массив GRANT команд.
   */
  private function preprocess($showGrantsRawOutput)
  {
    return array_map(function (array $rowAsArray)
    {
      return reset($rowAsArray);
    }, $showGrantsRawOutput);
  }

  /**
   * Отыскивает строку, содержащую GRANT команду, для указанного пользователя и хоста.
   *
   * @param string[] $grantCommands Все GRANT команды.
   * @param string $db Имя базы данных.
   * @param string $user Имя пользователя.
   * @param string $host Имя хоста.
   *
   * @throws UnexpectedValueException Бросается если GRANT команда для указанного пользователя и хоста не найдена.
   * @return string GRANT команда для указанного пользователя и хоста.
   */
  private function findRow(array $grantCommands, $db, $user, $host)
  {
    $candidates = array_filter($grantCommands, function ($row) use ($db, $user, $host)
    {
      /** @var $row string */
      if( stripos($row, 'GRANT USAGE ON') !== false )
      {
        return false;
      }

      return preg_match("/('{$user}'@'{$host}')|('{$user}'@'%')/", $row);
    });

    if( count($candidates) === 0 )
    {
      throw new UnexpectedValueException();
    }

    return reset($candidates);
  }

  private function parsePrivileges($string)
  {
    if( !preg_match('/GRANT\s+(?<privileges>.+)\s+ON/i', $string, $matches) )
      return array();

    $privilegeList = array_map(function ($privilege)
    {
      return strtolower(trim($privilege));
    }, explode(',', $matches['privileges']));

    return is_array($privilegeList) ? $privilegeList : array();
  }

  /**
   * Проверяет наличие привилегии в SQL GRANT команде.
   *
   * @param string $string Строка, содержащая SQL GRANT команду.
   * @param string $checkPrivilege Название привилегии на которую делается проверка.
   *
   * @return bool true в случае наличия привилегии, иначе false.
   */
  private function checkPrivilege($string, $checkPrivilege)
  {
    $privileges = $this->parsePrivileges($string);

    return in_array(strtolower($checkPrivilege), $privileges) || in_array('all privileges', $privileges) || in_array('all', $privileges);
  }
}
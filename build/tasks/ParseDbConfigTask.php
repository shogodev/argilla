<?php
/**
 * ParseDbConfigTask, Получает настройки бд.
 *
 * Настройки БД берутся из файла protected/config/db.php. Устанавливаются свойства db.driver, db.host, db.username, db.pass и db.prefix
 * @author Fedor A Borshev <fedor@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package build.tasks.ParseDbConfigTask
 *
 */
require_once "phing/Task.php";

class ParseDbConfigTask extends Task 
{
  private $file;

  private $pdo;

  private $connected = false;

  public function setFile($file)
  {
    $this->file = $file;
  }

  public function main()
  {
    if (!file_exists($this->file))
    {
      throw new BuildException('Cannot open db config file');
    }
	
    $db = require ($this->file);

    $this->project->setProperty('db.username',  $db['username']);
    $this->project->setProperty('db.password',  $db['password']);
    $this->project->setProperty('db.prefix',    $db['tablePrefix']);

    $connectionString = preg_replace('/\s*/', '', $db['connectionString']);

    $dbType = 'mysql';
    preg_match('/^([^\:]+):/', $connectionString, $q);
    $dbType = $q[1];
					
    $this->project->setProperty('db.driver', $dbType);

    $dsnVars = preg_split('/:|;/', preg_replace("/^$dbType:/",'',$connectionString)); // получаем список всех свойств драйвера из $dsn в виде key = value
					
    foreach($dsnVars as $dsnVar) // переносим все свойства в проект, устанавливаем db.<свойство>
    {
      list($key, $value) = preg_split('/=/', $dsnVar);
      $this->project->setProperty('db.' . $key, $value);
    }

    $this->connect();
    $this->getMysqlUserHost();
  }

  private function getMysqlUserHost()
  {
    $q = $this->pdo->query('SELECT CURRENT_USER();');
    $q->execute();

    $row = $q->fetch();

    list($user, $host) = explode('@', $row[0]);

    $this->project->setProperty('db.mysqlUser', $user);
    $this->project->setProperty('db.mysqlHost', $host);
  }

  private function connect()
  {
    $dsn = $this->project->getProperty('db.driver') . ':' . 'host=' . $this->project->getProperty('db.host');
    try
    {
      $this->pdo = new PDO($dsn,
        $this->project->getProperty('db.username'),
        $this->project->getProperty('db.password')
      );
    } catch (PDOException $e) {
      throw new BuildException("ParseDbConfigTask: Could not connect to PDO: $dsn");
    }
    $this->connected = true;
  }
}
?>

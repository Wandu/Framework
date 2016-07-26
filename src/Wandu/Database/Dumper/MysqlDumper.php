<?php
namespace Wandu\Database\Dumper;

use PDO;
use Symfony\Component\Process\Process;
use Wandu\Database\Contracts\DumperInterface;

class MysqlDumper implements DumperInterface
{
    /** @var string */
    protected $host;
    
    /** @var string */
    protected $username;
    
    /** @var string */
    protected $password;
    
    /** @var string */
    protected $database;
    
    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($host, $username, $password, $database)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function dump($dumpTargetFile = null, array $ignoreTables = [])
    {
        // if you see the error message below, run "mysql_upgrade -u root -p --force" and restart mysqld.
        // mysqldump: Couldn't execute 'SHOW VARIABLES LIKE 'ndbinfo\_version'': SELECT
        // command denied to user 'dump'@'localhost' for table 'session_variables' (1142)
        $cmd = "mysqldump -h{$this->host}";
        $cmd .= " -u{$this->username}";
        $cmd .= " -p{$this->password}";
        $cmd .= " {$this->database}";
        foreach ($ignoreTables as $table) {
            $cmd .= " --ignore-table={$this->database}.{$table}";
        }
        //  | sed 's$),($),\\n($g'
        $command = new Process("{$cmd} > {$dumpTargetFile}");
        $command->mustRun();
    }

    /**
     * {@inheritdoc}
     */
    public function restore($dumpTargetFile, array $ignoreTables = [])
    {
        $connection = new Pdo(
            "mysql:host={$this->host};port=3306",
            $this->username,
            $this->password
        );

        $connection->query("USE {$this->database}");
        foreach ($connection->query('SHOW TABLES')->fetchAll(PDO::FETCH_ASSOC) as $table) {
            $table = array_pop($table);
            if (in_array($table, $ignoreTables)) continue;
            $connection->query("DROP TABLE `{$table}`")->execute();
        }

        $sql = file_get_contents($dumpTargetFile);
        $connection->prepare($sql)->execute();
    }
}

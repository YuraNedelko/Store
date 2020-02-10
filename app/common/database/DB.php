<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 25.03.2019
 * Time: 18:27
 */

namespace app\common\database;

use app\common\core\App;
use PDO;
use PDOException;

class DB
{
    /**
     * @var null|PDO
     */
    private static $connection;

    /**
     * Returns existing connection or create a new one if connection doesn't exist in order to prevent
     * creation of multiple connections
     * @return null|PDO
     */
    public static function getConnection(): ?PDO
    {
        if (!self::$connection) {
            if (App::getConfig()) {
                $config = App::getConfig();
                $host = $config->host;
                $db = $config->db;
                $user = $config->user;
                $pass = $config->pass;
                $charset = $config->charset;

                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $opt = [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                try {
                    self::$connection = new PDO($dsn, $user, $pass, $opt);
                } catch (PDOException $e) {
                    return null;
                }
            } else {
                return null;
            }

        }
        return self::$connection;
    }
}
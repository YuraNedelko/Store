<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 27.03.2019
 * Time: 18:52
 */

namespace app\common\models;


use app\common\core\App;
use app\common\core\Model;
use app\common\database\DB;
use PDOException;

/**
 * Appointment model
 *
 * @property integer $id
 * @property string $name
 *
 */
class Author extends Model
{
    public $tableName = "authors";
}
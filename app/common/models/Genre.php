<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 23.03.2019
 * Time: 18:25
 */

namespace app\common\models;

use app\common\core\Model;

/**
 *
 * @property integer $id
 * @property string $name
 *
 */
class Genre extends Model
{
    public $tableName = "genres";
}
<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 23.03.2019
 * Time: 18:39
 */

namespace app\common\core;

use app\common\database\DB;
use PDOException;

class Model
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var int
     */
    protected $perPage = 10;

    /**
     * @var array
     */
    public $fields = array();

    /**
     * @var array
     */
    protected $related = array();

    /**
     * @var array
     */
    protected $rules = array();

    /**
     * @var \PDO|null
     */
    protected $connection;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var string
     */
    protected $scenario;


    public function __construct()
    {
        $this->connection = DB::getConnection();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return isset($this->fields[$key]) ? $this->fields[$key] : null;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value)
    {
        $this->fields[$key] = $value;
    }

    /**
     * Finds one record in database.
     * If rule is not specified finds first record else find record that satisfies the rules
     * @param array $rule
     * @return static|null
     */
    public static function findOne($rule = null)
    {
        $model = new static();
        $rules = "";
        if ($rule) {
            if (isset($rule[0]) && isset($rule[1]) && isset($rule[2])) {
                $rules = $rule[0] . $rule[1] . ":" . $rule[0];
                $params[":" . $rule[0]] = $rule[2];
            }
            $stmt = DB::getConnection()->prepare("SELECT * FROM $model->tableName WHERE $rules LIMIT 1");
            if ($stmt) {
                try {
                    $stmt->execute($params);
                    $result = $stmt->fetch();
                } catch (PDOException $e) {
                    return null;
                }
                if ($result) {
                    $model->fields = $result;
                    return $model;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            $stmt = DB::getConnection()->prepare("SELECT * FROM $model->tableName ORDER BY id LIMIT 1");
            if ($stmt) {
                try {
                    $stmt->execute();
                    $result = $stmt->fetch();
                } catch (PDOException $e) {
                    return null;
                }

                if ($result) {
                    $model->fields = $result;
                    return $model;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
    }

    /**
     * @param int $offset
     * @return array
     */
    public static function paginate(int $offset = 0){
        $model = new static();
        try{
            $numberOfRecords = $model->connection->query("SELECT count(*) FROM {$model->tableName}")->fetchColumn();
            $offset = $offset * $model->perPage;

            $stmt = $model->connection->prepare("SELECT * FROM $model->tableName LIMIT {$model->perPage} OFFSET $offset");
            $models = [];
            if ($stmt) {
                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    return [];
                }
                $rows = $stmt->fetchAll();
                foreach ($rows as $row) {
                    $model = new static();
                    $model->fields = $row;
                    $models[] = $model;
                }
                return [$model->tableName => $models, 'totalCount' => $numberOfRecords, 'perPage' => $model->perPage];
            } else {
                return [];
            }
        }catch (\Exception $e){
            return [];
        }
    }


    /**
     * deletes from database
     * @return bool
     */
    public function delete(): bool
    {
        $stmt = DB::getConnection()->prepare("DELETE FROM $this->tableName WHERE id = $this->id");
        if ($stmt) {
            try {
                $stmt->execute();
            } catch (PDOException $e) {
                return false;
            }
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Action performed after successful validation
     */
    protected function beforeValidate()
    {
    }

    /**
     * Action performed after successful validation
     */
    protected function afterValidate()
    {
    }

    /**
     * Get all the records from database that belong to this model
     * @return static[]|array
     */
    public static function findAll(): array
    {
        $model = new static();
        $stmt = DB::getConnection()->prepare("SELECT * FROM $model->tableName");
        $models = [];

        if ($stmt) {
            try {
                $stmt->execute();
            } catch (PDOException $e) {
                return [];
            }
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                $model = new static();
                $model->fields = $row;
                $models[] = $model;
            }
            return $models;
        } else {
            return [];
        }

    }

    /**
     * Allows you to find records that satisfy given rule. You can also specify how to order this records.
     * @param array $rules
     * @param string $orderBy
     * @return static[]
     */
    public static function where($rules, $orderBy = null): array
    {
        if ($orderBy) {
            $orderBy = "ORDER BY $orderBy ASC";
        } else {
            $orderBy = "";
        }

        $model = new static();
        $rulesArray = array();
        $params = array();

        foreach ($rules as $rule) {
            if (isset($rule[0]) && isset($rule[1]) && isset($rule[2])) {
                $rulesArray[] = $rule[0] . $rule[1] . ":" . $rule[0];
                $params[":" . $rule[0]] = $rule[2];
            }
        }

        if (count($rulesArray) > 1)
            $rules = implode(' AND ', $rulesArray);
        else
            $rules = implode('', $rulesArray);


        $stmt = DB::getConnection()->prepare("SELECT * FROM $model->tableName WHERE $rules $orderBy");

        if ($stmt) {
            try {
                $stmt->execute($params);
            } catch (PDOException $e) {
                return [];
            }
            $rows = $stmt->fetchAll();
            $models = [];
            foreach ($rows as $row) {
                $model = new static();
                $model->fields = $row;
                $models[] = $model;
            }
            return $models;
        } else {
            return [];
        }
    }

    /**
     * Validate model
     * @return bool
     */
    protected function validate(): bool
    {

        $this->beforeValidate();

        $errors = false;
        $this->errors = [];

        foreach ($this->rules as $key => $value) {
            //check if rule for this parameter exist and that that it's scenario = current scenario if "on" in
            // rules array is not specified we assume that it should be validated in all scenarios.
            // If scenario != current scenario parameter is not validated
            if (!array_key_exists('on', $value) || (array_key_exists('on', $value) && in_array($this->scenario,
                        explode(",", $value['on'])))) {
                // check if required parameter is present
                if (in_array('required', $value)) {
                    //check if parameter exist in class variables
                    if ((!array_key_exists($key, $this->fields)) && (!property_exists($this, $key) || $this->{$key} == null)) {
                        $errors = true;
                        $this->errors[$key] = "$key is required";
                        //check if parameter exist in fields array that represent values in db table
                    } elseif (array_key_exists($key, $this->fields) && $this->fields[$key] == null) {
                        $errors = true;
                        $this->errors[$key] = "$key is required";
                    }
                }
                if ($errors)
                    break;

                // check if type rule for this parameter exists
                if (array_key_exists('type', $value)) {
                    //check if parameter exist in class variables
                    if (array_key_exists($key, $this->fields)) {
                        switch ($value['type']) {
                            case "number":
                                if (!is_numeric($this->fields[$key])) {
                                    $errors = true;
                                    $this->errors[$key] = "$key should be number";
                                } else {
                                    $this->fields[$key] = $this->fields[$key] + 0;
                                }
                                break;

                            case "integer":
                                // check if parameter is integer or can be cast to integer
                                if (!is_integer($this->fields[$key])) {
                                    if (!filter_var($this->fields[$key], FILTER_VALIDATE_INT)) {
                                        $errors = true;
                                        $this->errors[$key] = "$key should be integer";
                                    } else {
                                        $this->fields[$key] = (int)$this->fields[$key];
                                    }
                                }
                                break;

                            case "boolean":
                                // check if parameter is boolean or can be cast to boolean
                                if (!is_bool($this->fields[$key])) {
                                    if (filter_var($this->fields[$key], FILTER_VALIDATE_BOOLEAN,
                                            FILTER_NULL_ON_FAILURE) === null) {
                                        $errors = true;
                                        $this->errors[$key] = "$key has wrong format";
                                    } else {
                                        // PDO prepared transforms boolean false to empty string that causes
                                        // invalid sql query, in order to prevent it we cast it to int
                                        $this->fields[$key] = (int)filter_var($this->fields[$key], FILTER_VALIDATE_BOOLEAN);
                                    }

                                }
                                break;

                            case "date":
                                if (!strtotime($this->fields[$key])) {
                                    $errors = true;
                                    $this->errors[$key] = "Wrong date format";
                                }
                                break;

                            case "string":
                                if (!is_string($this->fields[$key])) {
                                    $errors = true;
                                    $this->errors[$key] = "$key must be text";
                                }
                                break;
                        }
                    } //check if parameter exist in fields array that represent values in db table
                    elseif (property_exists($this, $key)) {
                        switch ($value['type']) {
                            case "number":
                                if (!is_numeric($this->{$key})) {
                                    $errors = true;
                                    $this->errors[$key] = "$key must be number";
                                } else {
                                    $this->{$key} = $this->{$key} + 0;
                                }
                                break;

                            case "date":
                                if (!strtotime($this->{$key})) {
                                    $errors = true;
                                    $this->errors[$key] = "Wrong date format";
                                }
                                break;

                            case "integer":
                                // check if parameter is integer or can be casted to integer
                                if (!is_integer($this->{$key})) {
                                    if (!filter_var($this->{$key}, FILTER_VALIDATE_INT)) {
                                        $errors = true;
                                        $this->errors[$key] = "$key should be integer";
                                    } else {
                                        $this->{$key} = (int)$this->{$key};
                                    }
                                }
                                break;

                            case "boolean":
                                // check if parameter is boolean or can be casted to boolean
                                if (!is_bool($this->{$key})) {
                                    if (filter_var($this->{$key}, FILTER_VALIDATE_BOOLEAN,
                                            FILTER_NULL_ON_FAILURE) === null) {
                                        $errors = true;
                                        $this->errors[$key] = "$key has wrong format";
                                    } else {
                                        // PDO prepared transforms boolean false to empty string that causes invalid
                                        // sql query, in order to prevent it we cast it to int
                                        $this->{$key} = (int)filter_var($this->{$key}, FILTER_VALIDATE_BOOLEAN);
                                    }
                                }
                                break;

                            case "string":
                                if (!is_string($this->{$key})) {
                                    $errors = true;
                                    $this->errors[$key] = "$key must be text";
                                }
                                break;
                        }
                    }

                }
                if ($errors)
                    break;

                if (array_key_exists('validator', $value)) {
                    //check if parameter exist in class variables
                    if (array_key_exists($key, $this->fields)) {
                        //call custom validation functions for parameter specified in rules as "validator"
                        if (method_exists(static::class, $value['validator'])) {
                            $error = call_user_func_array(array($this, $value['validator']), array($this->fields[$key]));
                            if ($error) {
                                $this->errors[$key] = $error;
                                $errors = true;
                            }
                        }
                        //check if parameter exist in fields array that represent values in db table
                    } elseif (property_exists($this, $key)) {
                        //call custom validation functions for parameter specified in rules as "validator"
                        if (method_exists(static::class, $value['validator'])) {
                            $error = call_user_func_array(array($this, $value['validator']), array($this->{$key}));
                            if ($error) {
                                $this->errors[$key] = $error;
                                $errors = true;
                            }
                        }
                    }

                }
                if ($errors)
                    break;
            }
        }
        if (!$errors) {
            $this->afterValidate();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Load values into model's fields array that represent values in db table.
     * It prevents downloading of params that are not specified in rules and current scenario
     *  to prevent malicious behaviour
     * @param array $params
     * @return bool
     */
    public function load(array $params)
    {
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $this->rules) && (!array_key_exists('on', $this->rules[$key]) ||
                    in_array($this->scenario, explode(",", $this->rules[$key]['on'])))) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                } else {
                    $this->fields[$key] = $value;
                }
            }
        }
        return $this->validate();
    }

    /**
     * Returns all validation errors
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Saves model to database
     * @return bool
     */
    public function save(): bool
    {
        if ($this->validate()) {
            foreach ($this->fields as $key => $value) {
                $names[] = $key;
                $values[] = ":" . $key;
            }

            $value = implode(",", $values);
            $name = implode(",", $names);

            // if model represents new table entry
            if (!isset($this->fields['id'])) {
                $stmt = $this->connection->prepare("INSERT INTO $this->tableName ($name) VALUES ($value)");
                if ($stmt) {
                    try {
                        $result = $stmt->execute($this->fields);
                        $this->fields['id'] = $this->connection->lastInsertId();
                        return $result;
                    } catch (PDOException $e) {
                        return false;
                    }
                } else {
                    return false;
                }
                // if table entry already exists we make update
            } else {
                foreach ($this->fields as $key => $value) {
                    if ($key != "id") {
                        $params[$key] = $value;
                        $sql[] = $key . "=:" . $key;
                    }
                }
                $sql = implode(",", $sql);

                $stmt = $this->connection->prepare("UPDATE $this->tableName SET $sql WHERE id= $this->id");

                if ($stmt) {
                    try {
                        return $stmt->execute($params);
                    } catch (PDOException $e) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

    }

    /**
     * @param string $tableName
     * @param string $foreignkey
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function findRelated(string $tableName, string $foreignkey)
    {
        $stmt = DB::getConnection()->prepare("SELECT * FROM $tableName WHERE id=:foreignkey LIMIT 1");
        if ($stmt) {
            $stmt->execute([':foreignkey' => $foreignkey]);
            $this->fields = $stmt->fetch();
        } else {
            throw new \Exception();
        }
    }

    /**
     * Generate model that represents db record connected to this model's db record
     * @param string $related
     * @param string $foreignKey
     * @return array
     */
    protected function belongsTo(string $related, string $foreignKey): array
    {
        if (in_array($related . $foreignKey, $this->related)) {
            $model = $this->related[$related . $foreignKey];
        } else {
            try {
                $model = new $related;
                $model->findRelated($model->tableName, $this->fields['id']);
                $this->related[$related . $foreignKey] = $model;
            } catch (\Exception $e) {
                $model = [];
            }
        }
        return $model;
    }

    /**
     * Find many to many related data
     * @param string $relatedTable
     * @param string $proxyTable
     * @return array
     */
    public function connectedViaProxyTable(string $relatedTable, string $proxyTable): array
    {
        if (in_array($relatedTable . $proxyTable, $this->related)) {
            return $this->related[$relatedTable . $proxyTable];
        } else {
            $this->related[$relatedTable . $proxyTable] = [];
            try {
                $stmt = $this->connection->prepare("SELECT {$relatedTable}.id FROM $relatedTable RIGHT JOIN 
                $proxyTable on {$relatedTable}.id = {$proxyTable}.{$relatedTable}_id WHERE {$proxyTable}.{$this->tableName}_id 
                = {$this->fields['id']} GROUP BY {$relatedTable}.id");
                $stmt->execute();
                if ($stmt) {
                    $rows = $stmt->fetchAll();
                    foreach ($rows as $row) {
                        try {
                            $modelName = 'app\common\models\\' . ucfirst(rtrim($relatedTable, 's'));
                            $model = App::resolve($modelName);
                            if ($model) {
                                $model = $model::findOne(['id', '=', $row['id']]);
                                if ($model) {
                                    $this->related[$relatedTable . $proxyTable][] = $model;
                                }
                            } else {
                                $this->related[$relatedTable . $proxyTable] = [];
                                return $this->related[$relatedTable . $proxyTable];
                            }
                        } catch (\ReflectionException $e) {
                            $this->related[$relatedTable . $proxyTable] = [];
                            return $this->related[$relatedTable . $proxyTable];
                        }
                    }
                    return $this->related[$relatedTable . $proxyTable];
                } else {
                    return $this->related[$relatedTable . $proxyTable];
                }
            } catch (PDOException $e) {
                return $this->related[$relatedTable . $proxyTable];
            }
        }
    }

    /**
     * @param string $relatedTable
     * @param string $proxyTable
     * @param array $newRelated
     * @return bool
     */
    public function sync(string $relatedTable, string $proxyTable, array $newRelated): bool
    {
        try {
            $connection = $this->connection;
            $isTransaction = $connection->inTransaction();

            if (!$isTransaction) {
                $connection->beginTransaction();
            }

            $stmt = $connection->prepare("DELETE FROM $proxyTable  
                WHERE {$proxyTable}.{$this->tableName}_id = {$this->id}");


            if ($stmt && $stmt->execute()) {
                $rowsSQL = [];
                $toBind = [];

                foreach ($newRelated as $index => $relatedID) {
                    $params = [];
                    $paramThisTable = ":" . "{$this->tableName}_id" . $index;
                    $paramConnectedTable = ":" . "{$relatedTable}_id" . $index;
                    $toBind[$paramThisTable] = $this->id;
                    $toBind[$paramConnectedTable] = $relatedID;
                    array_push($params, $paramThisTable, $paramConnectedTable);
                    $rowsSQL[] = "(" . implode(", ", $params) . ")";
                }

                if ($rowsSQL) {

                    $stmt = $connection->prepare("INSERT INTO $proxyTable  ({$this->tableName}_id, {$relatedTable}_id)
                        VALUES " . implode(", ", $rowsSQL));

                    foreach ($toBind as $param => $val) {
                        $stmt->bindValue($param, $val);
                    }

                    $stmt->execute();

                    if ($stmt) {
                        if (!$isTransaction) {
                            $connection->commit();
                        }
                        return true;
                    } else {
                        if (!$isTransaction) {
                            $connection->commit();
                        }
                        return false;
                    }
                } else {
                    if (!$isTransaction) {
                        $connection->commit();
                    }
                    return false;
                }
            } else {
                if (!$isTransaction) {
                    $connection->commit();
                }
                return false;
            }
        } catch (\PDOException $e) {
            return false;
        }
    }


    /**
     * Returns current scenario name
     * @return string
     */
    public function getScenario(): string
    {
        return $this->scenario;
    }

    /**
     * Sets current scenario
     * @param string $scenario
     */
    public function setScenario(string $scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * Returns fields array that represent values in db table
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

}
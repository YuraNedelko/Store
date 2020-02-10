<?php


namespace app\frontend\models;


use app\common\core\Model;

class OrderForm extends Model
{
    public $name;
    public $surname;
    public $amount;

    protected $rules =
        [
            'name' => ["required", "type" => "string"],
            'surname' => ["required", "type" => "string"],
            'amount' => ["required", "type" => "integer"]
        ];

}
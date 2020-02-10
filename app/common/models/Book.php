<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 25.03.2019
 * Time: 17:01
 */

namespace app\common\models;


use app\common\core\Model;
use app\common\database\DB;

/**
 *
 * @property integer $id
 * @property string $name
 * @property string $password
 */
class Book extends Model
{
    /**
     * @var string
     */
    public $tableName = "books";

    /**
     * @var array
     */
    public $authors = null;

    /**
     * @var array
     */
    public $genres = null;

    /**
     * @var int
     */
    protected $perPage = 2;


    protected $rules =
        [
            'name' => ['required', 'type' => 'string', 'on' => 'create,edit'],
            'price' => ['required', 'type' => 'number', 'on' => 'create,edit'],
            'short_description' => ['required', 'type' => 'string', 'on' => 'create,edit'],
            'authors' => ['required', 'on' => 'create,edit'],
            'genres' => ['required', 'on' => 'create,edit'],
        ];

    /**
     * @param string $offset
     * @param array $query
     * @return array
     * @throws \PDOException
     */
    public static function paginateWithParameters(array $query, $offset = 0){
        $authorQuery = '';
        $genreQuery = '';
        $whereClause = '';

        if(isset($query['author']) && $query['author']){
            $authorQuery = " RIGHT JOIN book_author AS proxyAuthor on books.id = proxyAuthor.books_id RIGHT JOIN authors on proxyAuthor.authors_id = authors.id";
        }
        if(isset($query['genre']) && $query['genre']){
            $genreQuery = " RIGHT JOIN book_genre AS proxyGenre on books.id = proxyGenre.books_id RIGHT JOIN genres on proxyGenre.genres_id = genres.id ";
        }

        if(isset($query['author']) && $query['author']){
            $whereClause = " WHERE authors.id = ${query['author']}";
            if(isset($query['genre']) && $query['genre']){
                $whereClause = $whereClause . " AND genres.id = ${query['genre']}";
            }
        } elseif (isset($query['genre']) && $query['genre']) {
            $whereClause = " WHERE genres.id = ${query['genre']}";
        }

        $connection = DB::getConnection();

        $numberOfRecords = $connection->query("SELECT count(*) from books {$authorQuery}{$genreQuery}{$whereClause}")->fetchColumn();

        $model = new static();
        $offset = $offset * $model->perPage;
        $stmt = $connection->prepare("SELECT books.id, books.name, books.price, books.short_description FROM books {$authorQuery}{$genreQuery}{$whereClause} LIMIT {$model->perPage} OFFSET {$offset }");

        $models = [];
        if ($stmt) {
            $stmt->execute();
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
    }

    public function authors()
    {
        return $this->connectedViaProxyTable('authors', 'book_author');
    }


    public function genres()
    {
        return $this->connectedViaProxyTable('genres', 'book_genre');
    }


}
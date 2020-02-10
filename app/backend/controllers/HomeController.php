<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 23.03.2019
 * Time: 15:52
 */

namespace app\backend\controllers;

use app\common\core\Controller;
use app\common\core\Request\RequestInterface;
use app\common\database\DB;
use app\common\models\Author;
use app\common\models\Book;
use app\common\models\Genre;


class HomeController extends Controller
{
    /**
     * @return string
     */
    public function index()
    {
        return $this->render('index', [], 'layout');
    }

    /**
     * @return array
     */
    private function getAuthors()
    {
        return array_column(Author::findAll(), 'fields');
    }

    /**
     * @return array
     */
    private function getGenres()
    {
        return array_column(Genre::findAll(), 'fields');
    }

    /**
     * @param Book $book
     * @return array
     */
    private function getBookInfo(Book $book)
    {
        $genres = [];
        $authors = [];
        foreach ($book->authors() as $author) {
            $authors[] = $author->id;
        }
        foreach ($book->genres() as $genre) {
            $genres[] = $genre->id;
        }
        return ['bookInfo' => $book->getFields(), 'authors' => $authors, 'genres' => $genres];
    }

    /**
     * @param RequestInterface $request
     * @param string $id
     * @return array
     * @throws \Exception
     */
    public function getEditBookParameters(RequestInterface $request, $id)
    {
        if ($request->isAjax()) {
            $book = Book::findOne(['id', '=', $id]);
            if ($book) {
                $bookInfo = $this->getBookInfo($book);
            } else {
                throw new \Exception();
            }
            $authors = $this->getAuthors();
            $genres = $this->getGenres();
            return ['success' => true, 'authors' => $authors, 'genres' => $genres, 'book' => $bookInfo];
        } else {
            throw new \Exception();
        }
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws \Exception
     */
    public function getCreateBookParameters(RequestInterface $request)
    {
        if ($request->isAjax()) {
            $authors = $this->getAuthors();
            $genres = $this->getGenres();
            return ['success' => true, 'authors' => $authors, 'genres' => $genres];
        } else {
            throw new \Exception();
        }
    }

    /**
     * @param RequestInterface $request
     * @param int $offset
     * @return array
     * @throws \Exception
     */
    public function getBooks(RequestInterface $request, $offset = 0)
    {
        if ($request->isAjax()) {
            $booksPagination = Book::paginate($offset);

            if( !isset($booksPagination['books']) || !isset($booksPagination['totalCount']) || !isset($booksPagination['perPage']) ){
                throw new \Exception();
            }

            return ['success' => true, 'books' => array_column($booksPagination['books'], 'fields'),
                'pageCount' => $booksPagination['totalCount'], 'perPage' => $booksPagination['perPage'] ];
        } else {
            throw new \Exception();
        }
    }


    /**
     * @param RequestInterface $request
     * @return array
     * @throws \Exception
     */
    public function edit(RequestInterface $request, $id)
    {
        if ($request->isPost()) {
            if ($request->isAjax()) {
                $book = Book::findOne(['id', '=', $id]);
                if ($book) {
                    $book->setScenario('edit');
                    if ($book->load($request->getPost())) {
                        $connection = DB::getConnection();
                        $connection->beginTransaction();
                        if ($book->save()) {
                            if ($book->sync('authors', 'book_author', $book->authors) &&
                                $book->sync('genres', 'book_genre', $book->genres)) {
                                $connection->commit();
                                return ['success' => true];
                            } else {
                                $connection->rollBack();
                                throw new \Exception();
                            }
                        } else {
                            $connection->rollBack();
                            return ['errors' => $book->getErrors(), 'failedModel' => $book];
                        }
                    } else {
                        return ['errors' => $book->getErrors(), 'failedModel' => $book];
                    }
                } else {
                    throw new \Exception;
                }
            } else {
                throw new \Exception;
            }
        } else {
            throw new \Exception;
        }
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws \Exception
     */
    public function create(RequestInterface $request)
    {
        if ($request->isPost()) {
            if ($request->isAjax()) {
                $book = new Book();
                if ($book) {
                    $book->setScenario('create');
                    if ($book->load($request->getPost()) && $book->save()) {
                        $connection = DB::getConnection();
                        $connection->beginTransaction();
                        if ($book->sync('authors', 'book_author', $book->authors) &&
                            $book->sync('genres', 'book_genre', $book->genres)) {
                            $connection->commit();
                            return ['success' => true];
                        } else {
                            $connection->rollBack();
                            $book->delete();
                            throw new \Exception();
                        }
                    } else {
                        return ['errors' => $book->getErrors()];
                    }
                } else {
                    throw new \Exception;
                }
            } else {
                throw new \Exception;
            }
        } else {
            throw new \Exception;
        }
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws \Exception
     */
    public function delete(RequestInterface $request, $id)
    {
        if ($request->isPost()) {
            if ($request->isAjax()) {
                $book = Book::findOne(['id', '=', $id]);
                if ($book && $book->delete()) {
                    return ['success' => true];
                } else {
                    throw new \Exception;
                }
            } else {
                throw new \Exception;
            }
        } else {
            throw new \Exception;
        }
    }

}
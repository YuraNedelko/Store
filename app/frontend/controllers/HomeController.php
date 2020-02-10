<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 23.03.2019
 * Time: 15:52
 */

namespace app\frontend\controllers;

use app\common\core\App;
use app\common\core\Controller;
use app\common\core\Request\RequestInterface;
use app\common\models\Author;
use app\common\models\Book;
use app\common\models\Genre;
use app\frontend\models\OrderForm;


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
     * @param RequestInterface $request
     * @param $id
     * @throws \Exception
     * @return array
     */
    public function makeOrder(RequestInterface $request, $id){
        if($request->isPost()){
            if($request->isAjax()){
                $book = Book::findOne((['id', '=', $id]));
                if($book){
                    $orderForm = new OrderForm();
                    if($orderForm->load($request->getPost())){
                        $mail = App::resolve('Mail');

                        $mail->setFrom(App::getConfig()->adminEmail);
                        $mail->addAddress(App::getConfig()->adminEmail);     // Add a recipient

                        // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'New order';
                        $mail->Body = $this->render('mailOrder', [
                            'name' => $orderForm->name,
                            'surname' => $orderForm->surname,
                            'amount' => $orderForm->amount,
                            'bookName' => $book->name,
                            'bookPrice' => $book->price
                        ]);

                        $mail->send();
                        return ['success' => true];
                    }else{
                        return ['errors' => $orderForm->getErrors()];
                    }
                }else{
                    throw new \Exception();
                }
            } else {
                throw new \Exception();
            }
        } else {
            throw new \Exception();
        }
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
            $authors[] = $author->name;
        }
        foreach ($book->genres() as $genre) {
            $genres[] = $genre->name;
        }
        return ['bookInfo' => $book->getFields(), 'authors' => $authors, 'genres' => $genres];
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
            $booksPagination = Book::paginateWithParameters($request->getQuery(), $offset);

            if( !isset($booksPagination['books']) || !isset($booksPagination['totalCount']) || !isset($booksPagination['perPage']) ){
                throw new \Exception();
            }

            //var_dump(array_column($booksPagination['books'], 'fields'));

            return ['success' => true, 'books' => array_column($booksPagination['books'], 'fields'),
                'pageCount' => $booksPagination['totalCount'], 'perPage' => $booksPagination['perPage'],
                'authors' => $this->getAuthors(), 'genres' => $this->getGenres()];
        } else {
            throw new \Exception();
        }
    }


    /**
     * @param RequestInterface $request
     * @return array
     * @throws \Exception
     */
    public function view(RequestInterface $request, $id)
    {
        if ($request->isAjax()) {
            $book = Book::findOne(['id', '=', $id]);
            if ($book) {
                $bookInfo = $this->getBookInfo($book);
                return ['success' => true, 'book' => $bookInfo];
            } else {
                throw new \Exception;
            }
        } else {
            throw new \Exception;
        }
    }



}
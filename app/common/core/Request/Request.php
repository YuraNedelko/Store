<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 23.03.2019
 * Time: 19:55
 */

namespace app\common\core\Request;

use app\common\core\Routing\Interfaces\RouteCollectionInterface;

class Request implements RequestInterface
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $post;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var array
     */
    private $query = [];

    /**
     * @var string
     */
    private $action;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var RouteCollectionInterface
     */
    private $routeCollection;

    public function __construct(RouteCollectionInterface $collection)
    {
        $this->routeCollection = $collection;
    }

    public function parseRequest()
    {
        $this->parseHTTPMethod();
        if ($this->isPost()) {
            $this->post = $_POST;
        }
        $this->parseQuery();
        $this->parseUri();
        $this->resolveRequest();
    }

    protected function resolveRequest()
    {
        $this->routeCollection->getResolvedRouteWithParams($this->method, $this->uri);
        $this->action = $this->routeCollection->getAction();
        $this->params = $this->routeCollection->getParams();
    }

    protected function parseHTTPMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    protected function parseUri()
    {
        $this->uri = strtok($_SERVER["REQUEST_URI"], '?');
    }

    protected function parseQuery()
    {
        parse_str($_SERVER['QUERY_STRING'], $this->query);
    }

    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Check if request is ajax
     * @return bool
     */
    public function isAjax(): bool
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if request is post
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method === "POST";
    }


    /**
     * Returns post data
     * @return array
     */
    public function getPost(): array
    {
        return $this->post;
    }


}
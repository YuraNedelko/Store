<?php


namespace app\common\core\Request;


interface RequestInterface
{
    public function parseRequest();

    /**
     * @return string
     */
    public function getAction(): ?string;

    /**
     * @return array
     */
    public function getParams(): array;

    /**
     * @return array
     */
    public function getQuery(): array;

    /**
     * Check if request is ajax
     * @return bool
     */
    public function isAjax(): bool;

    /**
     * Check if request is post
     * @return bool
     */
    public function isPost(): bool;


    /**
     * Returns post data
     * @return array
     */
    public function getPost(): array;
}
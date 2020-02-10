<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 24.03.2019
 * Time: 18:04
 */

namespace app\common\core;


class View
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Assign variables to view
     * @param $data
     */
    function assign(array $data)
    {
        $this->data = $data;
    }

    /**
     * Generate view
     * @param string $content_view
     * @param string $layout_view
     * @return string
     */
    function generate(string $content_view, string $layout_view = null): string
    {
        ob_start();

        if ($this->data)
            extract($this->data);

        if ($layout_view)
            require "{$_SERVER['DOCUMENT_ROOT']}/../views/$layout_view.php";
        else
            require "{$_SERVER['DOCUMENT_ROOT']}/../views/$content_view.php";

        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

}
<?php /** @noinspection PhpUndefinedNamespaceInspection */

/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 24.03.2019
 * Time: 18:57
 */

namespace app\common\core;

use app\common\core\View;

class Controller
{
    /**
     * Method used to render page
     * @param $content
     * @param array $data
     * @param string $layout
     * @return string
     */
    protected function render($content, $data = null, $layout = null)
    {
        $view = new View();
        $view->assign($data);
        return $view->generate($content, $layout);
    }


    /**
     * Redirect to specified url
     * @param string $url
     * @param bool $permanent
     */
    protected function Redirect(string $url, $permanent = false)
    {
        if (headers_sent() === false) {
            header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
        }

        exit();
    }

}
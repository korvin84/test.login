<?php

class Application {

    private static $instance;

    private function __construct()
    {
        require_once CFG_DIR . 'config.php';
        DEVELOP_STATUS ? error_reporting(-1) : error_reporting(0);
    }

    private function __clone()
    {
        
    }

    private static function getInstance()
    {
        if (!is_object(self::$instance))
        {
            self::$instance = new self;
        }

        return self::$instance;
    }
    
    private static function init()
    {
        self::getInstance();
    }

    /**
     * Разбирает URL
     * @return array Возвращает array(controller, action, queryString)
     */
    private function parseURL()
    {
        $urlArray = explode('/', filter_input(INPUT_GET, 'url', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        switch (count($urlArray))
        {
            case 1: return array(DEFAULT_CONTROLLER, DEFAULT_ACTION, array());
            case 2: return array($urlArray[0], DEFAULT_ACTION, array());
            case 3: return array($urlArray[0], $urlArray[1], array());
            default: return array($urlArray[0], $urlArray[1], array_slice($urlArray, 2));
        }
    }

    /**
     * Собственно загрузка
     */
    private function dispatch()
    {
        list($controller, $action, $queryString) = $this->parseURL();
        
        $controllerClass = ucwords($controller) . 'Controller';
        $controllerName = ucwords($controller);

        $dispatch = new $controllerClass($controllerName, $action);

        if (method_exists($controllerClass, $action))
        {
            //call_user_func_array(array($dispatch, $action), $queryString);
            $dispatch->$action($queryString);            
        }
        else
        {
            header('Location: ' . BASE_PATH . 'error/not_found/');
            exit;
            //echo "file:" . __FILE__ . "( line:" . __LINE__ . ")";
        }
    }

    public static function run()
    {        
        return Application::getInstance()->dispatch();
    }

}

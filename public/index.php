<?php

//корневой каталог
define('ROOT_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

//каталоги приложения
define('APP_DIR', ROOT_DIR . 'app' . DIRECTORY_SEPARATOR);
define('LIB_DIR', ROOT_DIR . 'lib' . DIRECTORY_SEPARATOR);
define('CFG_DIR', ROOT_DIR . 'cfg' . DIRECTORY_SEPARATOR);
define('PUBLIC_DIR', ROOT_DIR . 'public' . DIRECTORY_SEPARATOR);

define('CONTROLLERS_DIR', APP_DIR . 'controllers' . DIRECTORY_SEPARATOR);
define('MODELS_DIR', APP_DIR . 'models' . DIRECTORY_SEPARATOR);
define('VIEWS_DIR', APP_DIR . 'views' . DIRECTORY_SEPARATOR);

/**
 * Автозагрузка классов
 * @param string $name Имя класса в нижнем регистре
 */
function __autoload($name)
{
    //if ($name!='Application' and $name!='ErrorController' and $name!='Controller' and $name!='Template'
    //  and !empty($name)){echo $name;exit;}
    if (file_exists(LIB_DIR . strtolower($name) . '.php'))
            require_once(LIB_DIR . strtolower($name) . '.php');
    else if (file_exists(CONTROLLERS_DIR . strtolower($name) . '.php'))
            require_once(CONTROLLERS_DIR . strtolower($name) . '.php');
    else if (file_exists(MODELS_DIR . strtolower($name) . '.php'))
            require_once(MODELS_DIR . strtolower($name) . '.php');
    else
    {
        header('Location: ' . BASE_PATH . 'error/not_found/');
        exit;
        //echo "file:" . __FILE__ . "( line:" . __LINE__ . ")";
    }
}

Application::run();

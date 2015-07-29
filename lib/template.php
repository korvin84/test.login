<?php

class Template {

    protected $variables = array();
    protected $_controller;
    protected $_action;

    public function __construct($controller, $action)
    {
        $this->_controller = $controller;
        $this->_action     = $action;
    }

    /**
     * Устанавливаем переменные с занесением в массив $variables
     */
    public function set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    public function __set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->variables[$name])) return $this->variables[$name];
    }

    /**
     * Отображаем шаблон
     */
    public function render()
    {
        //переменные шаблона
        extract($this->variables);

        //если в папке views/controller/ нет header.phtml или footer.phtml, 
        //то загружаем header.phtml и footer.phtml по умолчанию        
        $header_phtml = VIEWS_DIR . strtolower($this->_controller) . DIRECTORY_SEPARATOR . 'header.phtml';
        $footer_phtml = VIEWS_DIR . strtolower($this->_controller) . DIRECTORY_SEPARATOR . 'footer.phtml';
        $action_phtml = VIEWS_DIR . strtolower($this->_controller) . DIRECTORY_SEPARATOR . $this->_action . '.phtml';

        $this->setHeader("html");
        file_exists($header_phtml) ? include $header_phtml : include DEFAULT_HEADER_PHTML;
        if (file_exists($action_phtml)) include $action_phtml;
        file_exists($footer_phtml) ? include $footer_phtml : include DEFAULT_FOOTER_PHTML;
    }

    public function setHeader($type = "text")
    {
        if ($type == "text") header("Content-Type:text/plain");
        if ($type == "html") header("Content-type:text/html; charset=utf-8");
    }

    /**
     * Просто выводит текст
     * @param string $text
     */
    public function showString($text)
    {
        echo $text;
    }

    public function __toString()
    {
        return __CLASS__;
    }

}

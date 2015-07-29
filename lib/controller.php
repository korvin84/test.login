<?php

/**
 * Базовый класс контроллера
 */
abstract class Controller {

    protected $_controller;
    protected $_action;
    protected $_template;

    public function __construct($controller, $action)
    {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_template = new Template($controller, $action);
    }

    public function set($name, $value)
    {
        $this->_template->set($name, $value);
    }

    public function __toString()
    {
        return __CLASS__;
    }

}

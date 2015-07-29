<?php

class ErrorController extends Controller {

    public function index()
    {
        $this->_template->title = "Неизвестная ошибка";        
        $this->_template->render();
    }
    
    public function not_found()
    {
        $this->_template->title = "Страница не найдена";   
        $this->_template->render();
    }
    
    public function db_error()
    {
        $this->_template->title = "Ошибка БД";   
        $this->_template->render();
    }
    
    public function session_error()
    {
        $this->_template->title = "Ошибка сессии";   
        $this->_template->render();
    }

}

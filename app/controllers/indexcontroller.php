<?php

class IndexController extends Controller {

    public function index()
    {
        $this->_template->page = 'main';
        $this->_template->title = 'Главная страница';
        $this->_template->render();
    }

    public function about()
    {
        $this->_template->page = 'about';
        $this->_template->title = 'О сайте';
        $this->_template->render();
    }

    public function contact()
    {
        $this->_template->page = 'contact';
        $this->_template->title = 'Контакты';
        $this->_template->render();
    }

}

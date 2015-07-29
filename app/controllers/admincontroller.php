<?php

class AdminController extends Controller {

    private $admin;

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);

        $this->admin = new Admin();

        //неавторизованных отправляем на страницу логина
        if (!$this->admin->status() && !preg_match("/^login.*$/i", $action))
        {
            header('Location: ' . BASE_PATH . 'admin/login/');
            exit;
        }
    }

    public function index()
    {
        $this->_template->page  = 'adminindex';
        $this->_template->title = 'Админка';
        $this->_template->render();
    }

    public function login()
    {
        $this->_template->page  = 'adminlogin';
        $this->_template->title = 'Авторизация';
        $this->_template->render();
    }

    public function loginCheck()
    {
        $this->_template->showString($this->admin->loginCheck());
    }

    public function users()
    {
        $this->_template->users = $this->admin->getUsersData();
        $this->_template->page  = 'adminusers';
        $this->_template->title = 'Список пользователей';
        $this->_template->render();
    }

    public function logout()
    {
        $this->admin->logout();
    }

}

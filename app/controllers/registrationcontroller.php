<?php

class RegistrationController extends Controller {

    public function index()
    {
        $this->_template->page  = 'registration';
        $this->_template->title = 'Регистрация';
        $this->_template->render();
    }

    /**
     * загружает файл из формы, выводит json-результат с ошибкой <br>
     * или путем до загруженного файла в случае успеха
     */
    public function upload()
    {
        $upload = new Upload();
        $this->_template->showString($upload->get_json_result());
    }

    public function addUser()
    {
        $addUser = new AddUser();
        $this->_template->showString($addUser->getResult());
    }

    public function uniqueNick()
    {
        $uniqueNick = new UniqueNick();
        $this->_template->setHeader();
        $this->_template->showString($uniqueNick->isUniqueNick());
    }

}

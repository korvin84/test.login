<?php

class Admin extends DB {

    public function __construct()
    {
        parent::__construct();
        if (!session_start())
        {
            if (!$this->admin->status() && !preg_match("/^login.*$/i", $action))
            {
                header('Location: ' . BASE_PATH . 'error/session_error/');
                exit;
            }
        }
    }

    public function status()
    {
        if ((!isset($_SESSION['status']) || $_SESSION['status'] == false))
                return false;
        return true;
    }

    public function loginCheck()
    {
        $form = filter_input(INPUT_POST, 'form', FILTER_SANITIZE_STRING);
        parse_str($form, $data);

        $q = self::$db->prepare("SELECT id FROM admins WHERE login = ? AND password = ?");
        $q->execute(array($data['login'], $data['password']));
        if ($q->rowCount() == 1)
        {
            $_SESSION['admin_login'] = $data['login'];
            $_SESSION['status']      = true;

            return true;
        }
        return false;
    }

    public function logout()
    {
        $_SESSION['status'] = false;

        unset($_SESSION['admin_login']);
        header('Location: ' . BASE_PATH . 'admin/login/');
        exit;
    }

    public function getUsersData()
    {
        $q = self::$db->prepare("SELECT * FROM users");
        $q->execute();
        return $q->fetchAll(PDO::FETCH_CLASS, "User");
    }

}

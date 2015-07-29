<?php

/**
 * Добавление пользователя в БД
 */
class AddUser extends DB {

    private $id;
    private $fname;
    private $sname;
    private $pname;
    private $dob;
    private $nick;
    private $country;
    private $region;
    private $city;
    private $files;
    private $avatar;
    private $email;
    private $tel;
    private $output;

    public function __construct()
    {
        parent::__construct();

        $this->getFormData();
        $this->checkFormData();
    }

    public function getResult()
    {
        return $this->addUser();
    }

    private function getFormData()
    {
        $form = filter_input(INPUT_POST, 'form', FILTER_SANITIZE_STRING);
        parse_str($form, $data);

        $this->fname   = $data['fname'];
        $this->sname   = $data['sname'];
        $this->pname   = $data['pname'];
        $this->dob     = $data['dob'];
        $this->nick    = $data['nick'];
        $this->country = $data['country'];
        $this->region  = $data['region'];
        $this->city    = $data['city'];
        $this->files   = @$data['files'];
        $this->avatar  = @$data['avatar'];
        $this->email   = $data['email'];
        $this->tel     = $data['tel'];
    }

    private function checkFormData()
    {
        $this->output['errors'] = false;

        if (empty($this->fname) || !$this->isName($this->fname))
                $this->output['errors']['fname'] = "Неверное имя";

        if (empty($this->sname) || !$this->isName($this->sname))
                $this->output['errors']['sname'] = "Неверная фамилия";

        if (empty($this->pname) || !$this->isName($this->pname))
                $this->output['errors']['pname'] = "Неверное отчество";

        if (empty($this->dob) || !$this->isDate($this->dob))
                $this->output['errors']['dob'] = "Неверная дата";

        if (empty($this->nick) || !$this->isNick($this->nick))
                $this->output['errors']['nick'] = "Неверный ник";

        $uniqueNick                     = new UniqueNick();
        if (!$uniqueNick->isUniqueNick($this->nick))
                $this->output['errors']['nick'] = "Этот ник занят";

        if (empty($this->country) || !$this->isGeoName($this->country))
                $this->output['errors']['country'] = "Неверная страна";

        if (empty($this->region) || !$this->isGeoName($this->region))
                $this->output['errors']['region'] = "Неверный регион";

        if (empty($this->city) || !$this->isGeoName($this->city))
                $this->output['errors']['city'] = "Неверный город";

        if (empty($this->email) || !$this->isEmail($this->email))
                $this->output['errors']['email'] = "Неверный E-mail";

        if (empty($this->tel) || !$this->isTelNumber($this->tel))
                $this->output['errors']['tel'] = "Неверный телефон";


        if (empty($this->files) || !is_array($this->files))
                $this->output['errors']['files'] = "Неверные фотографии";
        else
        {
            foreach ($this->files as $path)
            {
                if (empty($path) || !file_exists(PUBLIC_DIR . $path) || is_dir(PUBLIC_DIR . $path) || !getimagesize(PUBLIC_DIR . $path))
                {
                    $this->output['errors']['files'] = "Неверные фотографии";
                    break;
                }
            }
        }

        if (empty($this->avatar) || isset($this->output['errors']['files']) || !in_array($this->avatar, $this->files))
                $this->output['errors']['avatar'] = "Неверный аватар";
    }

    private function addUser()
    {
        if ($this->output['errors'])
                return json_encode($this->output['errors']);

        self::$db->prepare(
                        "INSERT INTO users
                 SET id = ?,
                 fname = ?, sname = ?, pname = ?,
                 dob = ?,
                 nick = ?,
                 country = ?, region = ?, city = ?,
                 files = ?, avatar = ?,
                 email = ?, tel = ?")
                ->execute(array(
                    null,
                    $this->fname,
                    $this->sname,
                    $this->pname,
                    DateTime::createFromFormat('d-m-Y', $this->dob)->format('Y-m-d'),
                    $this->nick,
                    $this->country,
                    $this->region,
                    $this->city,
                    serialize($this->files),
                    $this->avatar,
                    $this->email,
                    $this->tel));

        $this->id = self::$db->lastInsertId();
        return $this->id;
    }

    private function isEmail($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) return true;
        return false;
    }

    private function isDate($value)
    {
        try
        {
            new DateTime($value);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    private function isUrl($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) return true;
        return false;
    }

    private function isName($value)
    {
        if (preg_match("/^[a-z\x80-\xFF-]{2,50}$/i", $value)) return true;
        return false;
    }

    private function isNick($value)
    {
        //первый символ не цифра, затем еще не менее 4-х
        if (preg_match("/^[^\d][a-z0-9]{4,50}+$/i", $value)) return true;
        return false;
    }

    private function isGeoName($value)
    {
        if (preg_match("/^[a-z\x80-\xFF0-9- ]{2,50}+$/i", $value)) return true;
        return false;
    }

    /**
     * +1-234-567-89-99
     */
    private function isTelNumber($value)
    {
        if (preg_match("/^\+[\d]{1}(\-[\d]{3}){2}(\-[\d]{2}){2}$/", $value))
                return true;
        return false;
    }

}

<?php

class Upload {

    //допустимые расширения и размер
    private $allowed_filesize;
    private $allowed_extensions;
    private $target_path; //в эту папку (внутри PUBLIC_DIR) будут сохранятся файлы.     
    private $target_fname; //имя под которым файл будет сохранен в папке $target_path
    private $target_full_path; //в эту папку будут сохранятся файлы, имя включает PUBLIC_DIR и $target_fname. 
    private $fname; //имя загружаемого временного файла
    private $fsize; //размер загружаемого временного файла
    private $fext; //расширение загружаемого временного файла
    private $output;

    public function __construct($allowed_filesize = 1572864, $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png'))
    {
        $this->allowed_extensions = $allowed_extensions;
        $this->allowed_filesize   = $allowed_filesize;
        $this->target_path        = 'data' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

        $this->upload();
    }

    public function get_json_result()
    {
        return json_encode($this->output);
    }

    private function upload()
    {
        if (empty($_FILES))
        {
            $this->output = array("error" => "Нет файлов для загрузки");
            return;
        }

        //получаем информацию о загружаемом файле
        $this->fname            = $_FILES['Filedata']['tmp_name'];
        $this->fext             = pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION);
        $this->fsize            = $_FILES['Filedata']['size'];
        $this->target_fname     = md5($_FILES['Filedata']['name']) . '.' . $this->fext;
        $this->target_full_path = PUBLIC_DIR . $this->target_path . $this->target_fname;

        if (!list($sizeX, $sizeY) = getimagesize($this->fname))
        {
            $this->output = array("error" => "Ошибка размера изображения");
            return;
        }

        //проверяем расширение
        if (!in_array($this->fext, $this->allowed_extensions))
        {
            $this->output = array("error" => $this->fext . " - неразрешенный тип файла");
            return;
        }

        //проверяем размер
        if ($this->fsize > $this->allowed_filesize)
        {
            $this->output = array("error" => "Размер файла превышает допустимое значение");
            return;
        }

        //сохраняем временный файл,
        if (!move_uploaded_file($this->fname, $this->target_full_path))
        {
            $this->output = array("error" => "Не удалось сохранить файл");
            return;
        }
        
        // создаем превьюшку
        $preview = new Preview($this->target_full_path);

        $this->output = array(
            "success" => "Файл успешно загружен",
            "path"    => DIRECTORY_SEPARATOR . $this->target_path . $this->target_fname,
            "preview" => $preview->get_preview_filename());
    }

}

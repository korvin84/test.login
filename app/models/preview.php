<?php

/**
 * Создание превьюшек
 * Поддерживаются типа JPG/Jpeg, png, gif
 */
class Preview {

    private $image;
    private $type;
    private $width;
    private $height;
    private $_base_size;
    private $_filename;
    private $newfilename;
    private $_compression;

    public function __construct($filename, $base_size = 100, $compression = 75)
    {
        $this->_filename    = $filename;
        $this->_base_size   = $base_size;
        $this->_compression = $compression;

        $path_parts        = pathinfo($this->_filename);
        $this->newfilename = $path_parts['dirname'] . DIRECTORY_SEPARATOR . 'preview_' . $path_parts['basename'];

        $this->load();
        $this->resize();
        $this->save();
    }

    /**
     * 
     * @return string Возвращает путь до превью внутри папки PUBLIC_DIR
     */
    public function get_preview_filename()
    {
        return str_replace(PUBLIC_DIR, DIRECTORY_SEPARATOR, $this->newfilename);
    }

    private function load()
    {
        $image_info = getimagesize($this->_filename);
        $this->type = $image_info[2];

        switch ($this->type)
        {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($this->_filename);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($this->_filename);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($this->_filename);
                break;
        }

        $this->width  = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    private function resize()
    {
        if ($this->width >= $this->height)
        {
            $new_width  = $this->_base_size;
            $new_height = ceil($this->_base_size * $this->height / $this->width);
        }
        else
        {
            $new_height = $this->_base_size;
            $new_width  = ceil($this->_base_size * $this->width / $this->height);
        }

        $new_image   = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
        $this->image = $new_image;
    }

    private function save()
    {
        switch ($this->type)
        {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $this->newfilename, $this->_compression);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->image, $this->newfilename);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->image, $this->newfilename);
                break;
        }
    }

}

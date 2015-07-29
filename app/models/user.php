<?php

class User {

    public $id;
    public $fname;
    public $sname;
    public $pname;
    public $dob;
    public $nick;
    public $country;
    public $region;
    public $city;
    public $files;
    public $avatar;
    public $email;
    public $tel;

    public function getAvatar()
    {
        return $this->getPreview($this->avatar);
    }

    public function getImages()
    {
        $images = array();

        $files = unserialize($this->files);
        foreach ($files as $k => $path)
        {
            $images[$k]['image']   = $path;
            $images[$k]['preview'] = $this->getPreview($path);
        }
        return $images;
    }

    private function getPreview($path)
    {
        $path_parts = pathinfo($path);
        return $path_parts['dirname'] . DIRECTORY_SEPARATOR . 'preview_' . $path_parts['basename'];
    }

}

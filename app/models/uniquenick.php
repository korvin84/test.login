<?php

/**
 * Проверка ника на уникальность
 */
class UniqueNick extends DB {

    public function __construct()
    {
        parent::__construct();
    }

    public function isUniqueNick($value = false)
    {
        if (!$value)
                $value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $q = self::$db->prepare("SELECT id FROM users WHERE nick = ?");
        $q->execute(array($value));

        if ($q->rowCount() > 0) return false;
        return true;
    }

}

<?php

class UserModel
{
    private $users;

    function __construct()
    {
        $db_instance = new DBHelper();
        $this->users = $db_instance->users;
    }

    public function get_user_id($email, $displayName)
    {
        $find = array("email_hash" => get_hash($email));
        $cursor = $this->users->find($find);
        $row = $cursor->getNext();
        if (empty($row)) {
            $find["displayName"] = $displayName;
            $this->users->insert($find);
            return $find["_id"];
        } else {
            return $row["_id"];
        }

    }

    public function get_users()
    {
        return iterator_to_array($this->users->find());
    }
}
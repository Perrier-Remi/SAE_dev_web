<?php

namespace iutnc\netvod\authentification;

use iutnc\netvod\exception\InvalidPropertyNameException;

class user {

    const STANDARD_USER = 1;
    const ADMIN_USER = 100;
    public string $email;
    public string $hpassword;
    public int $id;

    /**
     * @param string $email
     * @param string $hpassword
     * @param int $id
     */
    public function __construct(string $email, string $hpassword, int $id)
    {
        $this->id = $id;
        $this->email = $email;
        $this->hpassword = $hpassword;
    }

    public function __get(string $at):mixed {
        if (property_exists($this,$at)) return $this->$at;
        throw new InvalidPropertyNameException("$at: invalid Property");
    }

    public function __set(string $at, mixed $val):void {
        if (property_exists($this,$at)) $this->$at = $val;
        throw new InvalidPropertyNameException("$at: invalid Property");
    }

}

?>
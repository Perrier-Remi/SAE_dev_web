<?php

namespace SAE_dev_web\src\classes\authentification\user;

class user
{
    public string $email;
    public string $hpassword;

    /**
     * @param string $email
     * @param string $hpassword
     */
    public function __construct(string $email, string $hpassword)
    {
        $this->email = $email;
        $this->hpassword = $hpassword;
    }

}

?>
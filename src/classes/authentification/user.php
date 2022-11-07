<?php

class user
{
    public string $email;
    public int $id;
    public string $hpassword;

    /**
     * @param string $email
     * @param int $id
     * @param string $hpassword
     */
    public function __construct(string $email, int $id, string $hpassword)
    {
        $this->email = $email;
        $this->id = $id;
        $this->hpassword = $hpassword;
    }

}

?>
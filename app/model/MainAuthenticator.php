<?php

use Nette\Security as NS;

class MainAuthenticator extends Repository implements NS\IAuthenticator
{

    function authenticate(array $credentials)
    {
        list($email, $password) = $credentials;
        $row = $this->connection->table('users')
            ->where('email', $email)->fetch();

        if (!$row) {
            throw new NS\AuthenticationException('Uživatel nenalezen!');
        }

        if ($row->password !== hash('sha256', $password)) {
            throw new NS\AuthenticationException("Nesprávné heslo!");
        }

        return new NS\Identity($row->id, $row->sec_level, ['email' => $row->email]);
    }
}
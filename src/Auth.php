<?php

declare(strict_types=1);

namespace Southaxis\RegiCare;

class Auth extends RegiCare
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']['loginKey']);
    }

    /**
     * Authenticates using session.
     *
     * @param $loginKey
     */
    public function authenticate(): bool
    {
        if (isset($_SESSION['user']['loginKey'])) {
            $response = $this->getClient()->send($this->getClient()->request(1, 'authenticate', [
                'loginKey' => $_SESSION['user']['loginKey'],
                'apiKey'   => $this->getConfig()->getToken(),
            ]));

            $result = json_decode($response->getBody()->getContents());

            return ! isset($result->result->error);
        }

        return false;
    }

    public function login($gebruikersnaam, $wachtwoord)
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'login', [
            'gebruikersnaam' => $gebruikersnaam,
            'wachtwoord'     => $wachtwoord,
            'apiKey'         => $this->getConfig()->getToken(),
        ]));

        $res = json_decode($return->getBody()->getContents());

        if (isset($res->result->error)) {
            return $res->result->error;
        }
        $_SESSION['user'] = [
            'naam'     => $res->result->naam,
            'loginKey' => $res->result->loginKey,
            'rol'      => $res->result->rol,
        ];

        return $res->result;
    }

    public function logout(): bool|string
    {
        if (self::isLoggedIn()) {
            $return = $this->getClient()->send($this->getClient()->request(1, 'logout', [
                'loginKey' => $_SESSION['user']['loginKey'],
                'apiKey'   => $this->getConfig()->getToken(),
            ]));

            $res = json_decode($return->getBody()->getContents());

            if (isset($res->result->error)) {
                return $res->result->error;
            }

            session_start();
            session_destroy();

            return true;
        }

        return 'NIET_INGELOGD';
    }

    public function profielAanmelden($gegevens = [])
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielAanmelden', [
            'gegevens' => $gegevens,
            'apiKey'   => $this->getConfig()->getToken(),
        ]));
        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielPersoonToevoegen($gegevens = [])
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielPersoonToevoegen', [
            'apiKey'   => $this->getConfig()->getToken(),
            'loginKey' => $_SESSION['user']['loginKey'],
            'gegevens' => $gegevens,
        ]));

        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielPersoonBewerken($gegevens = [], $persoonID = '')
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielPersoonBewerken', [
            'apiKey'    => $this->getConfig()->getToken(),
            'loginKey'  => $_SESSION['user']['loginKey'],
            'persoonID' => $persoonID,
            'gegevens'  => $gegevens,
        ]));

        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielPersoonGekoppeld()
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielPersoonGekoppeld', [
            'loginKey' => $_SESSION['user']['loginKey'],
            'apiKey'   => $this->getConfig()->getToken(),
        ]));

        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielPersoonGegevens($persoonID)
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielPersoonGegevens', [
            'loginKey'  => $_SESSION['user']['loginKey'],
            'apiKey'    => $this->getConfig()->getToken(),
            'persoonID' => $persoonID,
        ]));

        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielGegevens()
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielGegevens', [
            'loginKey' => $_SESSION['user']['loginKey'],
            'apiKey'   => $this->getConfig()->getToken(),
        ]));
        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielOpslaan($gegevens = [])
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielOpslaan', [
            'loginKey' => $_SESSION['user']['loginKey'],
            'apiKey'   => $this->getConfig()->getToken(),
            'gegevens' => $gegevens,
        ]));
        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielAdresControle(string $zipcode = '', string $nummer = '')
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielAdresControle', [
            'apiKey'   => $this->getConfig()->getToken(),
            'postcode' => $zipcode,
            'nummer'   => $nummer,
        ]));
        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielAdresOphalen($zipcode = '', $nummer = '')
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielAdresOphalen', [
            'apiKey'   => $this->getConfig()->getToken(),
            'postcode' => $zipcode,
            'nummer'   => $nummer,
        ]));
        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    public function profielIbanControle(string $iban = '')
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'profielIbanControle', [
            'apiKey' => $this->getConfig()->getToken(),
            'iban'   => $iban,
        ]));

        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }
}

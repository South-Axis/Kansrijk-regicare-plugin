<?php

declare(strict_types=1);

namespace Southaxis\RegiCare;

use Psr\Http\Message\ResponseInterface;

class Vacatures extends RegiCare
{
    /**
     * Haalt alle vacatures op met een eventuele filter.
     *
     * @param array $filter
     * @param mixed $werksoortID
     *
     * @return  $vacatures
     */
    public function vacatureOverzicht($werksoortID, $filter = [])
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureOverzicht', [
            'werksoortID' => $werksoortID,
            'apiKey'      => $this->getConfig()->getToken(),
            'filter'      => $filter,
            'loginKey'    => $_SESSION['user']['loginKey'] ?? '',
        ]));

        $res = json_decode($return->getBody()->getContents());

        if (isset($res->result->error)) {
            return $res->result->error;
        }

        return $res->result;
    }

    /**
     * @param array $filter
     * @param mixed $werksoortID
     *
     * @return ResponseInterface
     */
    public function vacatureOvezichtAantal($werksoortID, $filter = [])
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureOverzichtAantal', [
            'werksoortID' => $werksoortID,
            'apiKey'      => $this->getConfig()->getToken(),
            'filter'      => $filter,
            'loginKey'    => isset($_SESSION['user']['loginKey']) ? $_SESSION['user']['loginKey'] : '',
        ]));
        $res = json_decode($return->getBody()->getContents());

        return $res;
    }

    /**
     * @param mixed $werksoortID
     *
     * @return ResponseInterface
     */
    public function vacatureFilter($werksoortID)
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureFilter', [
            'werksoortID' => $werksoortID,
            'apiKey'      => $this->getConfig()->getToken(),
        ]));

        return json_decode($return->getBody()->getContents());
    }

    /**
     * @param array $filter
     * @param mixed $werksoortID
     *
     * @return string
     */
    public function vacatureInschrijvingen($werksoortID, $filter = [])
    {
        if (Auth::isLoggedIn()) {
            $return = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureIngeschreven', [
                'werksoortID' => $werksoortID,
                'apiKey'      => $this->getConfig()->getToken(),
                'filter'      => $filter,
                'loginKey'    => $_SESSION['user']['loginKey'],
            ]));

            $res = json_decode($return->getBody()->getContents());

            return $res->result->error ?? $res->result;
        }

        return 'NIET_INGELOGD';
    }

    /**
     * Hier moet loginKey een rol spelen.
     *
     * @param array $gegevens
     * @param mixed $werksoortID
     * @param mixed $vacatureID
     *
     * @return ResponseInterface
     */
    public function vacatureInschrijvingPlaatsen($werksoortID, $vacatureID, $gegevens)
    {
        if (Auth::isLoggedIn()) {
            $response = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureInschrijvingPlaatsen', [
                'werksoortID' => $werksoortID,
                'apiKey'      => $this->getConfig()->getToken(),
                'vacatureID'  => $vacatureID,
                'gegevens'    => $gegevens,
                'loginKey'    => $_SESSION['user']['loginKey'],
            ]));
            $result = json_decode($response->getBody()->getContents());
            if (isset($result->result->error)) {
                return $result->result->error;
            }

            return $result->result;
        }

        return 'NIET_INGELOGD';
    }

    /**
     * @param mixed $werksoortID
     * @param mixed $inschrijvingID
     *
     * @return ResponseInterface
     */
    public function vacatureInschrivingAnnuleren($werksoortID, $inschrijvingID)
    {
        if (Auth::isLoggedIn()) {
            $response = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureInschrijvingAnnuleren', [
                'werksoortID'    => $werksoortID,
                'apiKey'         => $this->getConfig()->getToken(),
                'inschrijvingID' => $inschrijvingID,
                'loginKey'       => $_SESSION['user']['loginKey'],
            ]));
            $result = json_decode($response->getBody()->getContents());
            if (isset($result->result->error)) {
                return $result->result->error;
            }

            return $result->result;
        }

        return 'NIET_INGELOGD';
    }

    /**
     * @param mixed $werksoortID
     *
     * @return ResponseInterface
     */
    public function vacatureInschrijvingsFilter($werksoortID)
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureInschrijvingFilter', [
            'werksoortID' => $werksoortID,
            'apiKey'      => $this->getConfig()->getToken(),
        ]));
        $result = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    /**
     * @param array $filter
     * @param mixed $werksoortID
     *
     * @return ResponseInterface
     */
    public function vacaturebankConfiguratie($werksoortID)
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankConfiguratie', [
            'werksoortID' => $werksoortID,
            'apiKey'      => $this->getConfig()->getToken(),
        ]));
        $result = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    public function geplaatsteVacatures($werksoortID, $filter = [])
    {
        if (Auth::isLoggedIn()) {
            $response = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureGeplaatst', [
                'werksoortID' => $werksoortID,
                'apiKey'      => $this->getConfig()->getToken(),
                'filter'      => $filter,
                'loginKey'    => $_SESSION['user']['loginKey'],
            ]));
            $result = json_decode($response->getBody()->getContents());
            if (isset($result->result->error)) {
                return $result->result->error;
            }

            return $result->result;
        }

        return 'NIET_INGELOGD';
    }

    /**
     * @param string $loginKey
     * @param mixed  $vacatureID
     * @param mixed  $werksoortID
     *
     * @return ResponseInterface
     */
    public function vacatureGegevens($vacatureID, $werksoortID)
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureGegevens', [
            'vacatureID'  => $vacatureID,
            'apiKey'      => $this->getConfig()->getToken(),
            'werksoortID' => $werksoortID,
            'loginKey'    => $_SESSION['user']['loginKey'],
        ]));
        $result = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    public function vacatureTemplate($werksoortID)
    {
        if (Auth::isLoggedIn()) {
            $response = $this->getClient()->send($this->getClient()->request(1, 'vacaturebankVacatureTemplate', [
                'werksoortID' => $werksoortID,
                'apiKey'      => $this->getConfig()->getToken(),
                'loginKey'    => $_SESSION['user']['loginKey'],
            ]));
            $result = json_decode($response->getBody()->getContents());
            if (isset($result->result->error)) {
                return $result->result->error;
            }

            return $result->result;
        }

        return 'NIET_INGELOGD';
    }
}

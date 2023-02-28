<?php

declare(strict_types=1);

namespace Southaxis\RegiCare;

use Graze\GuzzleHttp\JsonRpc\Exception\ClientException;
use function array_merge;
use function wp_redirect;

class Activiteiten extends RegiCare
{
    /**
     * This will get all the activities from the database.
     */
    public function getAllActivities(array $filter = []): mixed
    {
        $filter  = array_merge($filter, ['activiteittype' => [280]]);
        $request = $this->getClient()->request(
            1,
            'activiteitOverzicht',
            [
                'loginKey' => null,
                'apiKey'   => $this->getConfig()->getToken(),
                'filter'   => $filter,
            ]
        );

        $return = $this->getClient()->send($request);

        $res = json_decode($return->getBody()->getContents());

        return $res->result->error ?? $res->result;
    }

    /**
     * This wil get all the filters possible that can be put on a activity.
     *
     * @return mixed
     */
    public function getAllFilters()
    {
        $response = $this->getClient()->send($this->getClient()->request(
            1,
            'activiteitFilter',
            [
                'apiKey'   => $this->getConfig()->getToken(),
                'volledig' => false,
            ]
        ));

        $result = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    /**
     * This will return the data from one specific activity.
     *
     * @param mixed $activiteitID
     *
     * @return mixed
     */
    public function getSpecificActivity($activiteitID)
    {
        try {
            $response = $this->getClient()->send($this->getClient()->request(1, 'activiteitInformatie', [
                'activiteitID' => $activiteitID,
                'apiKey'       => $this->getConfig()->getToken(),
            ]));
        } catch (ClientException $e) {
            /** @noinspection ForgottenDebugOutputInspection */
            var_dump($e->getResponse()?->getBody()->getContents());
        }

        $result = json_decode($response->getBody()->getContents());

        return $result->result->error ?? $result->result;
    }

    /**
     * The amount of available activities.
     *
     * @param array $filter
     *
     * @return mixed
     */
    public function activiteitOverzichtAantal($filter = [])
    {
        $return = $this->getClient()->send($this->getClient()->request(1, 'activiteitOverzichtAantal', [
            'loginKey' => null,
            'apiKey'   => $this->getConfig()->getToken(),
            'filter'   => $filter,
        ]));
        $res    = json_decode($return->getBody()->getContents());
        if (isset($res->result->error)) {
            return $res->result->error;
        }

        return $res->result;
    }

    /**
     *get the information about a specific meeting.
     *
     * @param mixed $bijeenkomstID
     *
     * @return mixed
     */
    public function activitySpecificMeetingInformation($bijeenkomstID)
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'activiteitBijeenkomstInformatie', [
            'bijeenkomstID' => $bijeenkomstID,
            'apiKey'        => $this->getConfig()->getToken(),
        ]));
        $result   = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    /**
     *get the available meetings.
     *
     * @param array $filter
     *
     * @return mixed
     */
    public function activityMeetingInformation($filter = [])
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'activiteitBijeenkomstOverzicht', [
            'filter' => $filter,
            'apiKey' => $this->getConfig()->getToken(),
        ]));
        $result   = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    /**
     *get the amount of available meetings.
     *
     * @param array $filter
     */
    public function amountOfActivityMeetings($filter = []): mixed
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'activiteitBijeenkomstOverzichtAantal', [
            'filter' => $filter,
            'apiKey' => $this->getConfig()->getToken(),
        ]));
        $result   = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    /**
     * get information about a specific location.
     *
     * @param mixed $locatieID
     *
     * @return mixed
     */
    public function activitySpecifcLocation($locatieID)
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'activiteitLocatieInformatie', [
            'locatieID' => $locatieID,
            'apiKey'    => $this->getConfig()->getToken(),
        ]));
        $result   = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    /**
     *get all the available locations.
     *
     * @param array $filter
     */
    public function availableLocations($filter = []): mixed
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'activiteitLocatieOverzicht', [
            'filter' => $filter,
            'apiKey' => $this->getConfig()->getToken(),
        ]));
        $result   = json_decode($response->getBody()->getContents());
        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }

    /**
     * get the amount of available locations.
     *
     * @param array $filter
     */
    public function amountOfAvailableLocations($filter = []): mixed
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'activiteitLocatieOverzichtAantal', [
            'filter' => $filter,
            'apiKey' => $this->getConfig()->getToken(),
        ]));

        $result = json_decode($response->getBody()->getContents());

        return $result->result->error ?? $result->result;
    }

    /**
     * get all the registrations on a activity.
     */
    public function summaryRegisteredPeopleActivity(array $filter = []): string
    {
        if (Auth::isLoggedIn()) {
            $return = $this->getClient()->send($this->getClient()->request(1, 'activiteitIngeschreven', [
                'loginKey' => $_SESSION['user']['loginKey'],
                'apiKey'   => $this->getConfig()->getToken(),
                'filter'   => $filter,
            ]));

            $res = json_decode($return->getBody()->getContents());

            return $res->result->error ?? $res->result;
        }

        return 'NIET_INGELOGD';
    }

    /**
     * register someone on a activity.
     *
     * @param mixed $activiteitID
     */
    public function registeringOnActivity($activiteitID, int $persoonID, string|array $filter = []): string
    {
        if (Auth::isLoggedIn()) {
            $return = $this->getClient()->send($this->getClient()->request(1, 'activiteitInschrijvingPlaatsen', [
                'loginKey'     => $_SESSION['user']['loginKey'],
                'apiKey'       => $this->getConfig()->getToken(),
                'activiteitID' => $activiteitID,
                'gegevens'     => ['persoonID' => $persoonID],
                'filter'       => $filter,
            ]));

            return json_decode($return->getBody()->getContents());
        }

        return 'NIET_INGELOGD';
    }

    /**
     * cancel a registration on a activity.
     *
     * @param mixed $inschrijvingID
     */
    public function cancelRegistration($inschrijvingID): string
    {
        if (Auth::isLoggedIn()) {
            $return = $this->getClient()->send($this->getClient()->request(1, 'activiteitInschrijvingAnnuleren', [
                'loginKey'       => $_SESSION['user']['loginKey'],
                'apiKey'         => $this->getConfig()->getToken(),
                'inschrijvingID' => $inschrijvingID,
            ]));

            $res = json_decode($return->getBody()->getContents());

            if (isset($res->result->error)) {
                return $res->result->error;
            }

            return $res->result;
        }

        return 'NIET_INGELOGD';
    }

    /**
     * register anonymous on a activity.
     *
     * @param array $gegevens
     * @param mixed $activiteitID
     *
     * @return mixed
     */
    public function registerAnonymous($activiteitID, $gegevens = [])
    {
        $response = $this->getClient()->send($this->getClient()->request(1, 'activiteitInschrijvingAnoniem', [
            'gegevens'     => $gegevens,
            'activiteitID' => $activiteitID,
            'apiKey'       => $this->getConfig()->getToken(),
        ]));

        $result = json_decode($response->getBody()->getContents());

        if (isset($result->result->error)) {
            return $result->result->error;
        }

        return $result->result;
    }
}

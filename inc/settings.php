<?php

declare(strict_types=1);

// start the session so that the logged in user can be retrieved
use GuzzleHttp\Exception\BadResponseException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Southaxis\RegiCare\Activiteiten;
use Southaxis\RegiCare\ActivityShow;
use Southaxis\RegiCare\Auth;
use function Southaxis\Helpers\mapRegicareFilters;
use function Southaxis\Helpers\service;

defined('ABSPATH') || exit('Forbidden');

if (! session_id()) {
    session_start();
}

//remove a dynamic field from a link
function removeqsvar($url, $varname): string
{
    [$urlpart, $qspart] = array_pad(explode('?', $url), 2, '');
    parse_str($qspart, $qsvars);
    unset($qsvars[$varname]);
    $newqs = http_build_query($qsvars);

    return $urlpart . '?' . $newqs;
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function profielAdresOphalen(): void
{
    global $wp;
    $auth = service(Auth::class);

    $validatedUserData = validateAndSetPost(['post' => ['string', true], 'nummer' => ['string', true]]);

    try {
        $filter = $auth->getAllActivities($validatedUserData[0][0], $validatedUserData[1][0]);
    } catch (BadResponseException) {
        $nothing = '<p>excuses voor het ongemak de activiteiten zijn op het moment niet beschikbaar</p>';
    }
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function showFilterActivities(): void
{
    global $wp;

    $activity = service(Activiteiten::class);

    try {
        $activities = $activity->getAllActivities(mapRegicareFilters($_POST));
    } catch (BadResponseException $e) {
        /** @noinspection ForgottenDebugOutputInspection */
        \error_log($e->getMessage());
    }

    if (empty($activities)) {
        wp_send_json(0);

        exit;
    }

    outputActivityResultHTML($activities);

    exit(0);
}

/**
 * @noinspection PhpFunctionCyclomaticComplexityInspection
 */
function outputActivityResultHTML(array $activities): void
{
    global $wp;

    $filter = $activities;

    if (0 !== count($filter)) { ?>
        <?php foreach ($filter as $activity) { ?>
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="card mb-3 w-100">
                    <div class="card-body">
                        <h5 class="card-title"> <?php echo $activity->omschrijving; ?></h5>
                        <p class="card-text activityText"> <?php echo wp_trim_words($activity->omschrijvingUitgebreid, $num_words = 55, $more = null); ?></p>
                        <div class="popUpBox" style="display: none">
                            <div class="metaInfoHolder">
                                <p class="card-text"><?php echo $activity->omschrijvingUitgebreid; ?></p>
                                <div class="tableHolder">
                                    <table>
                                        <tbody>
                                        <?php if (null !== $activity->trefwoorden) { ?>
                                            <tr>
                                                <td style="padding-right: 15px">
                                                    <b>Trefwoorden</b>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $activity->trefwoorden;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Wanneer</b>
                                            </td>
                                            <td>
                                                <?php
                                                if (
                                                    null !== $activity->interval
                                                    && null !== $activity->dag
                                                    && null !== $activity->startDatum
                                                    && null !== $activity->eindDatum
                                                ) {
                                                    $interval = array_values(get_object_vars($activity->interval));

                                                    if (is_array($activity->dag)) {
                                                        $day = array_values($activity->dag);
                                                    } else {
                                                        $day = array_values(get_object_vars($activity->dag));
                                                    }

                                                    echo array_shift($interval) . ', ' . array_shift($day) . ' ' . date('d F', strtotime($activity->startDatum)) . '<b> T/M </b> ' . date('d F', strtotime($activity->eindDatum));
                                                } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Tijd</b>
                                            </td>
                                            <td>
                                                <?php
                                                if (null !== $activity->startTijd && null !== $activity->eindTijd) {
                                                    echo $activity->startTijd . ' - ' . $activity->eindTijd;
                                                } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Url</b>
                                            </td>
                                            <td>
                                                <?php
                                                if (null !== $activity->url) {
                                                    echo $activity->url;
                                                } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Locatie</b>
                                            </td>
                                            <td>
                                                <?php
                                                if (null !== $activity->locatie) {
                                                    if (is_array($activity->locatie)) {
                                                        if (! empty($activity->locatie)) {
                                                            $location_values = array_values($activity->locatie);
                                                        }
                                                    } else {
                                                        $location_values = array_values(get_object_vars($activity->locatie));
                                                    }

                                                    echo array_shift($location_values);
                                                } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Ruimte</b>
                                            </td>
                                            <td>
                                                <?php
                                                if (null !== $activity->ruimte) {
                                                    if (is_array($activity->ruimte)) {
                                                        if (! empty($activity->ruimte)) {
                                                            $space_values = array_values($activity->ruimte);
                                                        }
                                                    } else {
                                                        $space_values = array_values(get_object_vars($activity->ruimte));
                                                    }

                                                    echo array_shift($space_values);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Plaats</b>
                                            </td>
                                            <td>
                                                <?php
                                                if (null !== $activity->inschrijvingenMaximum) {
                                                    echo 'Maximum Inschrijvingen: ' . $activity->inschrijvingenMaximum . '<br>';
                                                }
                                                $num = $activity->inschrijvingenMaximum - $activity->inschrijvingen;
                                                if (is_int($num)) {
                                                    echo 'Aantal vrije plekken: ' . $num;
                                                } else {
                                                    echo 'Aantal vrije plekken: 0';
                                                } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Kosten</b>
                                            </td>
                                            <td>
                                                €
                                                <?php echo $activity->prijs ?? 0; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Doelgroep</b>
                                            </td>
                                            <td>
                                                <?php
                                                if (null !== $activity->groepering) {
                                                    $data = [];
//                                                    dd($activity->groepering);
                                                    foreach ($activity->groepering as $value) {
                                                        if (count(explode(' jaar ', $value)) > 1) {
                                                            [, $b] = explode(' jaar ', $value);
                                                            $data[] = $b;
                                                        } else {
                                                            $data[] = $value;
                                                        }
                                                    }

                                                    echo implode(', ', $data);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 15px">
                                                <b>Interesse</b>
                                            </td>
                                            <td>
                                                <?php
                                                if (null !== $activity->activiteittype) {
                                                    echo join(', ', array_values(get_object_vars($activity->activiteittype)));
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if (1 === $activity->inschrijven) { ?>
                                    <a class="btn btn-orange float-right" href="<?php echo home_url($wp); ?>/inschrijven/?activityID=<?php echo $activity->activiteitID; ?>">Inschrijven</a>
                                <?php } ?>
                            </div>
                        </div>
                        <a class="btn btn-orange vertoon">Toon meer</a>
                    </div>
                </div>
            </div>
        <?php }
    } else {
        ?>
        <div class="col sb-text-center">
            <div id="aanbodHolder">
                <div style="margin-top: 25px;" class="alert alert-info w-100">
                    Er zijn geen activiteiten gevonden met de opgegeven parameters.
                </div>
            </div>
        </div>
        <?php
    }

    exit();

//    foreach ($activities as $activity) {
//        if (! empty($activity->interval) && ! empty($activity->dag) && ! empty($activity->startDatum) && ! empty($activity->eindDatum)) {
//            $interval_values = array_values(get_object_vars($activity->interval));
//            $day_values      = array_values(get_object_vars($activity->dag));
//
//            $whenHTML = sprintf('%s, %s %s<b> T/M </b> %s', array_shift($interval_values), array_shift($day_values), date('d F', strtotime($activity->startDatum)), date('d F', strtotime($activity->eindDatum)));
//        }
//
//        if (! empty($activity->startTijd) && ! empty($activity->eindTijd)) {
//            $timeHTML = sprintf('%s - %s', $activity->startTijd, $activity->eindTijd);
//        }
//
//        if (! empty($activity->locatie)) {
//            $location_values = array_values(get_object_vars($activity->locatie));
//            $locationHTML    = array_shift($location_values);
//        }
//
//        if (! empty($activity->ruimte)) {
//            $space_values = array_values(get_object_vars($activity->ruimte));
//            $spaceHTML    = array_shift($space_values);
//        }
//
//
    ?>
    <!--        <div class="col-lg-4 d-flex align-items-stretch">-->
    <!--            <div class="card mb-3 w-100">-->
    <!--                <div class="card-body">-->
    <!--                    <h5 class="card-title">--><?php //= $activity->omschrijving;
    ?><!--</h5>-->
    <!--                    <p class="card-text activityText">--><?php //= wp_trim_words($activity->omschrijvingUitgebreid, 55, null);
    ?><!--</p>-->
    <!--                    <div class="popUpBox" style="display: none">-->
    <!--                        <div class="metaInfoHolder">-->
    <!--                            <p class="card-text">--><?php //= $activity->omschrijvingUitgebreid;
    ?><!--</p>-->
    <!--                            <div class="tableHolder">-->
    <!--                                <table>-->
    <!--                                    <tbody>-->
    <!--                                    --><?php //if ($activity->trefwoorden !== null) {
    ?>
    <!--                                        <tr>-->
    <!--                                            <td style="padding-right: 15px">-->
    <!--                                                <b>Trefwoorden</b>-->
    <!--                                            </td>-->
    <!--                                            <td>-->
    <!--                                                --><?php //= $activity->trefwoorden
    ?>
    <!--                                            </td>-->
    <!--                                        </tr>-->
    <!--                                    --><?php //}
    ?>
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Wanneer</b></td>-->
    <!--                                        <td>--><?php //= $whenHTML ?? '';
    ?><!--</td>-->
    <!--                                    </tr>-->
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Tijd</b></td>-->
    <!--                                        <td>--><?php //= $timeHTML ?? '';
    ?><!--</td>-->
    <!--                                    </tr>-->
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Url</b></td>-->
    <!--                                        <td>--><?php //= ! empty($activity->url) ? $activity->url : '';
    ?><!--</td>-->
    <!--                                    </tr>-->
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Locatie</b></td>-->
    <!--                                        <td>--><?php //= $locationHTML ?? '';
    ?><!--</td>-->
    <!--                                    </tr>-->
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Ruimte</b></td>-->
    <!--                                        <td>--><?php //= $spaceHTML ?? '';
    ?><!--</td>-->
    <!--                                    </tr>-->
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Plaats</b></td>-->
    <!--                                        <td>-->
    <!--                                            --><?php
//                                            if (! empty($activity->inschrijvingenMaximum)) {
//                                                echo sprintf('Maximum Inschrijvingen: %s<br>', $activity->inschrijvingenMaximum);
//                                            }
//
//                                            $num = $activity->inschrijvingenMaximum - $activity->inschrijvingen;
//                                            echo is_int($num) ? 'Aantal vrije plekken: ' . $num : 'Aantal vrije plekken: 0';
    ?>
    <!--                                        </td>-->
    <!--                                    </tr>-->
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Kosten</b></td>-->
    <!--                                        <td>€ --><?php //= ! empty($activity->prijs) ? $activity->prijs : 0
    ?><!--</td>-->
    <!--                                    </tr>-->
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Doelgroep</b></td>-->
    <!--                                        <td>-->
    <!--                                            --><?php
//                                            if (! empty($activity->groepering)) {
//                                                $data = [];
//
//                                                foreach ($activity->groepering as $value) {
//                                                    [, $b] = explode(' jaar ', $value);
//                                                    $data[] = $b;
//                                                }
//
//                                                echo implode(', ', $data);
//                                            }
//
    ?>
    <!--                                        </td>-->
    <!--                                    </tr>-->
    <!--                                    <tr>-->
    <!--                                        <td style="padding-right: 15px"><b>Interesse</b></td>-->
    <!--                                        <td>--><?php //= ! empty($activity->activiteittype) ? implode(', ', array_values(get_object_vars($activity->activiteittype))) : '';
    ?><!--</td>-->
    <!--                                    </tr>-->
    <!--                                    </tbody>-->
    <!--                                </table>-->
    <!--                            </div>-->
    <!--                            --><?php
//                            if (1 === $activity->inschrijven) {
//                                printf('<a class="btn btn-orange float-right" href="%s/inschrijven/?activityID=%s">Inschrijven</a>', home_url($wp), $activity->activiteitID);
//                            }
//
    ?>
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <a class="btn btn-orange vertoon">Toon meer</a>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        --><?php
//    }
}

add_action('wp_ajax_showFilterActivities', 'showFilterActivities');
add_action('wp_ajax_nopriv_showFilterActivities', 'showFilterActivities');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function validateAddress(): void
{
    $auth = service(Auth::class);

    if (isset($_REQUEST['zipcode'], $_REQUEST['houseNumber'])) {
        global $wp;

        try {
            $adress = $auth->profielAdresControle($_REQUEST['zipcode'], $_REQUEST['houseNumber']);
        } catch (BadResponseException) {
            echo json_encode(['error' => 'error']);

            exit();
        }

        if (! isset($adress->result) || 'OK' !== $adress->result) {
            echo json_encode(['error' => false]);

            exit();
        }

        try {
            $adress = $auth->profielAdresOphalen($_REQUEST['zipcode'], $_REQUEST['houseNumber']);
        } catch (BadResponseException) {
            echo json_encode(['error' => 'error']);

            exit();
        }

        if (isset($adress->straat, $adress->nummer, $adress->postcode, $adress->plaats)) {
            echo json_encode($adress);
        } else {
            echo json_encode(['error' => false]);
        }
    }

    exit();
}

add_action('wp_ajax_validateAddress', 'validateAddress');
add_action('wp_ajax_nopriv_validateAddress', 'validateAddress');

//log the user into the regicare API.
add_filter('init', 'regicare_login');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_login(): void
{
    if (isset($_POST['regicare_email'], $_POST['regicare_password'])) {
        $auth = service(Auth::class);
        global $wp;

        $link           = home_url($wp);
        $authentication = $auth->login($_POST['regicare_email'], $_POST['regicare_password']);

        if (is_string($authentication)) {
            wp_redirect("{$link}/login/?error=true");
        } else {
            if (isset($_SESSION['redirect_url'])) {
                $redirect_url = $_SESSION['redirect_url'];
                unset($_SESSION['redirect_url']);
                wp_redirect($redirect_url);
            } else {
                wp_redirect($link);
            }
        }

        exit();
    }
}

add_filter('init', 'regicare_forgot_password');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_forgot_password(): void
{
    if (isset($_POST['regicare_email'])) {
        $auth = service(Auth::class);
        global $wp;
        $link = home_url($wp);

        $client = RPCCLient::factory(get_option('regicare_domain'), [
            'timeout' => 100,
            'verify'  => false,
        ]);

        $result = $client->send($client->request(1, 'profielWachtwoordAanvraag', [
            'gebruikersnaam' => $_POST['regicare_email'],
            'apiKey'         => get_option('regicare_key'),
        ]));

        // $res = $auth->forgotPassword($_POST["regicare_email"]);
        wp_redirect("{$link}/wachtwoord-vergeten?error=false");

        exit();
    }
}

add_filter('init', 'regicare_logout');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_logout(): void
{
    $auth = service(Auth::class);
    global $wp;

    $link   = home_url($wp);
    $logout = false;

    if (isset($_REQUEST['logout'])) {
        $logout = ($_REQUEST['logout']);
    }

    if ('true' == $logout) {
        $oldLink                  = home_url($wp) . $_SERVER['REQUEST_URI'];
        $_SESSION['redirect_url'] = $oldLink;
        $authentication           = $auth->logout();
        if (is_string($authentication)) {
            wp_redirect($link);
        } else {
            unset($_SESSION['redirect_url'], $_SESSION['user']);

            wp_redirect(home_url($wp));
        }

        exit();
    }
}

add_filter('init', 'regicare_register_activity');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_register_activity(): void
{
    global $wp;
    $activity = service(Activiteiten::class);

    $link = home_url($wp->request);

    if (isset($_POST['activityID'])) {
        if (null == $_POST['loginKey']) {
            $oldLink                  = home_url($wp) . $_SERVER['REQUEST_URI'];
            $_SESSION['redirect_url'] = $oldLink;
            wp_redirect(home_url($wp) . '/login');

            exit();
        }

        if (isset($_POST['persoonID'])) {
            foreach ($_POST['persoonID'] as $persoonID) {
                $persoonID = (int)$persoonID;
                $register  = $activity->registeringOnActivity($_POST['activityID'], $persoonID, $_POST['loginKey']);
            }
            wp_redirect(home_url($wp) . '/bedankt');

            exit();
        }
        wp_redirect("{$link}/inschrijven/?activityID=" . $_POST['activityID'] . '&error=NOCHILD');

        exit();
    }
}

add_filter('init', 'regicare_register');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_register(): void
{
    if (isset($_SESSION['form'], $_POST['emailadres'], $_POST['wachtwoord']) && 'register' === $_SESSION['form']) {
        global $wp;
        $auth = service(Auth::class);

        $baseUrl           = home_url($wp->request);
        $validatedUserData = validateAndSetPost(
            [
                'regicare_roepnaam' => ['name', true],
                'voorletters'       => ['name', true],
                'tussenvoegsel'     => ['name', false],
                'achternaam'        => ['name', true],
                'geslacht'          => ['gender', true],
                'geboortedatum'     => ['date', true],
                'toevoeging'        => ['string', false],
                'postcode'          => ['post', true],
                'nummer'            => ['string', true],
                'telefoonVast'      => ['phone', false],
                'telefoonMobiel'    => ['phone', false],
                'emailadres'        => ['mail', true],
                'iban'              => ['string', false],
                'autoincasso'       => ['string', false],
                'wachtwoord'        => ['string', true],
            ]
        );
        foreach ($validatedUserData as $col => $val) {
            if (isset($val[1])) {
                $validatedUserData['code'] = 'FORMDATAMISSING';
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/registreren/');

                exit();
            }
        }

        try {
            $adress = $auth->profielAdresControle($_REQUEST['postcode'], $_REQUEST['nummer']);
        } catch (BadResponseException $e) {
            $validatedUserData['code'] = $e;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/registreren/');

            exit();
        }

        if (! isset($adress->result) || 'OK' !== $adress->result) {
            $validatedUserData['code'] = $adress;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/registreren/');

            exit();
        }

        try {
            $adress = $auth->profielAdresOphalen($_REQUEST['postcode'], $_REQUEST['nummer']);
        } catch (BadResponseException $e) {
            $validatedUserData['code'] = $e;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/registreren/');

            exit();
        }

        if (! isset($adress->straat, $adress->nummer, $adress->postcode, $adress->plaats)) {
            $validatedUserData['code'] = $adress;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/registreren/');

            exit();
        }

        if ('' !== $_REQUEST['iban']) {
            try {
                $iban = $auth->profielIbanControle($_REQUEST['iban']);
            } catch (BadResponseException $e) {
                $validatedUserData['code'] = $e;
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/registreren/');

                exit();
            }
            if (! isset($iban->result) || 'OK' === $iban->result) {
                $validatedUserData['code'] = $iban;
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/registreren/');

                exit();
            }
        }

        $params = [
            'gegevens' => [
                'roepnaam'       => $_POST['regicare_roepnaam'],
                'voorletters'    => $_POST['voorletters'],
                'tussenvoegsel'  => $_POST['tussenvoegsel'],
                'achternaam'     => $_POST['achternaam'],
                'geslacht'       => $_POST['geslacht'],
                'geboortedatum'  => date('Y-m-d', strtotime($_POST['geboortedatum'])),
                'postcode'       => $adress->postcode,
                'nummer'         => $_POST['nummer'],
                'toevoeging'     => $_POST['toevoeging'],
                'straat'         => $adress->straat,
                'plaats'         => $adress->plaats,
                'land'           => 'Nederland',
                'telefoonVast'   => $_POST['telefoonVast'],
                'telefoonMobiel' => $_POST['telefoonMobiel'],
                'emailadres'     => $_POST['emailadres'],
                'iban'           => ($iban ?? ''),
                'autoincasso'    => $_POST['autoincasso'],
                'wachtwoord'     => $_POST['wachtwoord'],
            ],
        ];

        $authentication = $auth->profielAanmelden($params['gegevens']);

        if (is_string($authentication)) {
            $validatedUserData['code'] = $authentication;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/registreren/');

            exit();
        }
        $auth->login($_POST['emailadres'], $_POST['wachtwoord']);
        wp_redirect($baseUrl . '/kind-registreren/');

        exit();
    }
}

add_action('loop_start', 'check_login', 1, 0);
add_action('template_redirect', 'check_login', 1, 0);
/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function check_login(): void
{
    global $wp;
    $auth = service(Auth::class);
    global $wp_query;

    $page = $wp_query->post->post_name;

    $pages = ['account', 'account-bijwerken', 'kind-aanpassen', 'kind-registreren', 'kinderen-account'];
    if (in_array($page, $pages)) {
        if (! $auth->authenticate()) {
            unset($_SESSION['user']);
            $oldLink                  = home_url($wp) . $_SERVER['REQUEST_URI'];
            $_SESSION['redirect_url'] = $oldLink;
            wp_redirect(home_url($wp) . '/login');

            exit();
        }
    }

    $logout = ['registreren', 'login'];
    if (in_array($page, $logout)) {
        if ($auth->authenticate()) {
            wp_redirect(home_url($wp));

            exit();
        }
    }
}

add_action('loop_start', 'check_child', 1, 0);
add_action('template_redirect', 'check_child', 1, 0);
/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function check_child(): void
{
    global $wp;
    $auth = service(Auth::class);
    global $wp_query;

    if ('kind-aanpassen' === $wp_query->post->post_name) {
        $baseUrl = home_url($wp->request);
        if (! isset($_SESSION['childID'])) {
            wp_redirect($baseUrl . '/kinderen-account/');
        } else {
            if (isset($_SESSION['childID']) && '' === $_SESSION['childID']) {
                $_SESSION['error']         = [];
                $_SESSION['error']['code'] = 'PERSOON_ID_ONGELDIG';
                wp_redirect($baseUrl . '/kinderen-account/');

                exit();
            }

            try {
                $childID = $auth->profielPersoonGegevens($_SESSION['childID']);
            } catch (BadResponseException $e) {
                $_SESSION['error']         = [];
                $_SESSION['error']['code'] = $e;
                wp_redirect($baseUrl . '/kinderen-account/');

                exit();
            }

            if ('PERSOON_ID_ONGELDIG' === $childID) {
                $_SESSION['error']         = [];
                $_SESSION['error']['code'] = 'PERSOON_ID_ONGELDIG';
                wp_redirect($baseUrl . '/kinderen-account/');

                exit();
            }
        }
    }
}

add_filter('init', 'regicare_register_child');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_register_child(): void
{
    if (isset($_SESSION['form'], $_POST['form']) && 'registerChild' === $_SESSION['form'] && 'childRegister' === $_POST['form']) {
        $auth = service(Auth::class);
        global $wp;

        $baseUrl           = home_url($wp->request);
        $validatedUserData = validateAndSetPost(
            [
                'regicare_roepnaam' => ['name', true],
                'voorletters'       => ['name', true],
                'tussenvoegsel'     => ['name', false],
                'achternaam'        => ['name', true],
                'geslacht'          => ['gender', true],
                'geboortedatum'     => ['date', true],
                'telefoonVast'      => ['phone', false],
                'telefoonMobiel'    => ['phone', false],
                'emailadres'        => ['mail', true],
            ]
        );

        if (isset($_POST['house']) && 'true' === $_POST['house']) {
            $validatedUserAddress = validateAndSetPost(
                [
                    'toevoeging' => ['string', false],
                    'postcode'   => ['post', true],
                    'nummer'     => ['string', true],
                ]
            );
            $validatedUserData    = array_merge($validatedUserData, $validatedUserAddress);
        }

        if (! isset($_POST['house']) && 'true' !== $_POST['house'] && 'false' !== $_POST['house']) {
            $validatedUserData['house'] = [$_POST['house'], false];
        } else {
            $validatedUserData['house'] = [$_POST['house']];
        }

        foreach ($validatedUserData as $col => $val) {
            if (isset($val[1])) {
                $validatedUserData['code'] = 'FORMDATAMISSING';
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/kind-registreren/');

                exit();
            }
        }

        if ('true' === $_POST['house']) {
            try {
                $adress = $auth->profielAdresControle($_POST['postcode'], $_POST['nummer']);
            } catch (BadResponseException $e) {
                $validatedUserData['code'] = $e;
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/kind-registreren/');

                exit();
            }

            if (! isset($adress->result) || 'OK' !== $adress->result) {
                $validatedUserData['code'] = $adress;
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/kind-registreren/');

                exit();
            }

            try {
                $adress = $auth->profielAdresOphalen($_POST['postcode'], $_POST['nummer']);
            } catch (BadResponseException $e) {
                $validatedUserData['code'] = $e;
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/kind-registreren/');

                exit();
            }

            if (! isset($adress->straat, $adress->nummer, $adress->postcode, $adress->plaats)) {
                $validatedUserData['code'] = $adress;
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/kind-registreren/');

                exit();
            }
        }

        if ('false' === $_POST['house']) {
            if (null == $_SESSION['user']['loginKey']) {
                $oldLink                  = home_url($wp) . $_SERVER['REQUEST_URI'];
                $_SESSION['redirect_url'] = $oldLink;
                wp_redirect(home_url($wp) . '/login');

                exit();
            }
            $parentUser = $auth->profielGegevens();
        }

        $params = [
            'gegevens' => [
                'roepnaam'       => $_POST['regicare_roepnaam'],
                'voorletters'    => $_POST['voorletters'],
                'tussenvoegsel'  => $_POST['tussenvoegsel'],
                'achternaam'     => $_POST['achternaam'],
                'geslacht'       => $_POST['geslacht'],
                'geboortedatum'  => date('Y-m-d', strtotime($_POST['geboortedatum'])),
                'telefoonVast'   => $_POST['telefoonVast'],
                'telefoonMobiel' => $_POST['telefoonMobiel'],
                'emailadres'     => $_POST['emailadres'],
                'postcode'       => ('true' === $_POST['house'] ? $adress->postcode : $parentUser->postcode),
                'nummer'         => ('true' === $_POST['house'] ? $adress->nummer : $parentUser->nummer),
                'toevoeging'     => ('true' === $_POST['house'] ? $_POST['toevoeging'] : $parentUser->toevoeging),
                'straat'         => ('true' === $_POST['house'] ? $adress->straat : $parentUser->straat),
                'plaats'         => ('true' === $_POST['house'] ? $adress->plaats : $parentUser->plaats),
                'land'           => ('true' === $_POST['house'] ? 'Nederland' : $parentUser->land),
            ],
        ];

        $authentication = $auth->profielPersoonToevoegen($params['gegevens']);

        if (is_string($authentication)) {
            $validatedUserData['code'] = $authentication;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/kind-registreren/');
        } else {
            if (null == $_SESSION['user']['loginKey']) {
                $oldLink                  = home_url($wp) . $_SERVER['REQUEST_URI'];
                $_SESSION['redirect_url'] = $oldLink;
                wp_redirect(home_url($wp) . '/login');

                exit();
            }
            wp_redirect($baseUrl . '/nieuw-kind/');

            exit();
        }

        exit();
    }
}

add_filter('init', 'regicare_update_profile');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_update_profile(): void
{
    if (isset($_POST['form'], $_SESSION['form']) && 'accountUpdate' === $_POST['form'] && 'updateAccount' === $_SESSION['form']) {
        $auth = service(Auth::class);
        global $wp;

        $baseUrl = home_url($wp->request);

        $validatedUserData = validateAndSetPost(
            [
                'regicare_roepnaam' => ['name', true],
                'voorletters'       => ['name', true],
                'tussenvoegsel'     => ['name', false],
                'achternaam'        => ['name', true],
                'geslacht'          => ['gender', true],
                'geboortedatum'     => ['date', true],
                'toevoeging'        => ['string', false],
                'postcode'          => ['post', true],
                'nummer'            => ['string', true],
                'telefoonVast'      => ['phone', false],
                'telefoonMobiel'    => ['phone', false],
                'emailadres'        => ['mail', true],
                'iban'              => ['string', true],
                'autoincasso'       => ['string', false],
            ]
        );

        foreach ($validatedUserData as $col => $val) {
            if (isset($val[1])) {
                $validatedUserData['code'] = 'FORMDATAMISSING';
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/account-bijwerken/');

                exit();
            }
        }

        try {
            $adress = $auth->profielAdresControle($_REQUEST['postcode'], $_REQUEST['nummer']);
        } catch (BadResponseException $e) {
            $validatedUserData['code'] = $e;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/account-bijwerken/');

            exit();
        }

        if (! isset($adress->result) || 'OK' !== $adress->result) {
            $validatedUserData['code'] = $adress;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/account-bijwerken/');

            exit();
        }

        try {
            $adress = $auth->profielAdresOphalen($_REQUEST['postcode'], $_REQUEST['nummer']);
        } catch (BadResponseException $e) {
            $validatedUserData['code'] = $e;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/account-bijwerken/');

            exit();
        }

        if (! isset($adress->straat, $adress->nummer, $adress->postcode, $adress->plaats)) {
            $validatedUserData['code'] = $adress;
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/account-bijwerken/');

            exit();
        }

        if ('' !== $_REQUEST['iban']) {
            try {
                $iban = $auth->profielIbanControle($_REQUEST['iban']);
            } catch (BadResponseException $e) {
                $validatedUserData['code'] = $e;
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/account-bijwerken/');

                exit();
            }

            if (! isset($iban->result) || 'OK' === $iban->result) {
                $validatedUserData['code'] = $iban;
                $_SESSION['error']         = $validatedUserData;
                wp_redirect($baseUrl . '/account-bijwerken/');

                exit();
            }
        }

        $params = [
            'gegevens' => [
                'roepnaam'       => $_POST['regicare_roepnaam'],
                'voorletters'    => $_POST['voorletters'],
                'tussenvoegsel'  => $_POST['tussenvoegsel'],
                'achternaam'     => $_POST['achternaam'],
                'geslacht'       => $_POST['geslacht'],
                'geboortedatum'  => date('Y-m-d', strtotime($_POST['geboortedatum'])),
                'postcode'       => $adress->postcode,
                'nummer'         => $_POST['nummer'],
                'toevoeging'     => $_POST['toevoeging'],
                'straat'         => $adress->straat,
                'plaats'         => $adress->plaats,
                'land'           => 'Nederland',
                'telefoonVast'   => $_POST['telefoonVast'],
                'telefoonMobiel' => $_POST['telefoonMobiel'],
                'emailadres'     => $_POST['emailadres'],
                'iban'           => ($iban ?? ''),
                'autoincasso'    => $_POST['autoincasso'],
            ],
        ];

        $result = $auth->profielOpslaan($params['gegevens']);

        if ('OK' == $result->result) {
            wp_redirect(home_url($wp) . '/account/');

            exit();
        }

        $validatedUserData['code'] = $result;
        $_SESSION['error']         = $validatedUserData;
        wp_redirect($baseUrl . '/account-bijwerken/');

        exit();
    }
}

add_filter('init', 'regicare_redirect_update_child');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_redirect_update_child(): void
{
    if (isset($_SESSION['form'], $_POST['childID']) && 'accountChildRedirect' === $_SESSION['form']) {
        global $wp;
        $auth = service(Auth::class);

        $baseUrl = home_url($wp->request);

        if ('' === $_POST['childID']) {
            $_SESSION['error']         = [];
            $_SESSION['error']['code'] = 'PERSOON_ID_ONGELDIG';
            wp_redirect($baseUrl . '/kinderen-account/');

            exit();
        }

        try {
            $childID = $auth->profielPersoonGegevens($_POST['childID']);
        } catch (BadResponseException $e) {
            $_SESSION['error']         = [];
            $_SESSION['error']['code'] = $e;
            wp_redirect($baseUrl . '/kinderen-account/');

            exit();
        }

        if ('PERSOON_ID_ONGELDIG' === $childID) {
            $_SESSION['error']         = [];
            $_SESSION['error']['code'] = 'PERSOON_ID_ONGELDIG';
            wp_redirect($baseUrl . '/kinderen-account/');

            exit();
        }

        $_SESSION['childID'] = $_POST['childID'];

        wp_redirect($baseUrl . '/kind-aanpassen/');

        exit();
    }
}

add_filter('init', 'regicare_update_child_profile');

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function regicare_update_child_profile(): void
{
    if (! isset($_POST['form'], $_SESSION['form']) || 'accountChildUpdate' !== $_POST['form'] || 'updateChildAccount' !== $_SESSION['form']) {
        return;
    }

    global $wp;
    $auth    = service(Auth::class);
    $baseUrl = home_url($wp->request);

    $validatedUserData = validateAndSetPost([
        'regicare_roepnaam' => ['name', true],
        'voorletters'       => ['name', true],
        'tussenvoegsel'     => ['name', false],
        'achternaam'        => ['name', true],
        'geslacht'          => ['gender', true],
        'geboortedatum'     => ['date', true],
        'toevoeging'        => ['string', false],
        'postcode'          => ['post', true],
        'nummer'            => ['string', true],
        'telefoonVast'      => ['phone', false],
        'telefoonMobiel'    => ['phone', false],
        'emailadres'        => ['mail', true],
    ]);

    foreach ($validatedUserData as $col => $val) {
        if (isset($val[1])) {
            $validatedUserData['code'] = 'FORMDATAMISSING';
            $_SESSION['error']         = $validatedUserData;
            wp_redirect($baseUrl . '/kind-aanpassen/');

            exit();
        }
    }

    if (isset($_POST['childID']) && '' === $_POST['childID']) {
        $validatedUserData['code'] = 'PERSOON_ID_ONGELDIG';
        $_SESSION['error']         = $validatedUserData;
        wp_redirect($baseUrl . '/kinderen-account/');

        exit();
    }

    try {
        $childID = $auth->profielPersoonGegevens($_POST['childID']);
    } catch (BadResponseException $e) {
        $validatedUserData['code'] = $e;
        $_SESSION['error']         = $validatedUserData;
        wp_redirect($baseUrl . '/kind-aanpassen/');

        exit();
    }

    if ('PERSOON_ID_ONGELDIG' === $childID) {
        $validatedUserData['code'] = 'PERSOON_ID_ONGELDIG';
        $_SESSION['error']         = $validatedUserData;
        wp_redirect($baseUrl . '/kinderen-account/');

        exit();
    }

    try {
        $adress = $auth->profielAdresControle($_REQUEST['postcode'], $_REQUEST['nummer']);
    } catch (BadResponseException $e) {
        $validatedUserData['code'] = $e;
        $_SESSION['error']         = $validatedUserData;
        wp_redirect($baseUrl . '/kind-aanpassen/');

        exit();
    }

    if (! isset($adress->result) || 'OK' !== $adress->result) {
        $validatedUserData['code'] = $adress;
        $_SESSION['error']         = $validatedUserData;
        wp_redirect($baseUrl . '/kind-aanpassen/');

        exit();
    }

    try {
        $adress = $auth->profielAdresOphalen($_REQUEST['postcode'], $_REQUEST['nummer']);
    } catch (BadResponseException $e) {
        $validatedUserData['code'] = $e;
        $_SESSION['error']         = $validatedUserData;
        wp_redirect($baseUrl . '/kind-aanpassen/');

        exit();
    }

    if (! isset($adress->straat, $adress->nummer, $adress->postcode, $adress->plaats)) {
        $validatedUserData['code'] = $adress;
        $_SESSION['error']         = $validatedUserData;
        wp_redirect($baseUrl . '/kind-aanpassen/');

        exit();
    }

    /**
     * @noinspection IssetArgumentExistenceInspection
     */
    $params = [
        'gegevens' => [
            'roepnaam'       => $_POST['regicare_roepnaam'],
            'voorletters'    => $_POST['voorletters'],
            'tussenvoegsel'  => $_POST['tussenvoegsel'],
            'achternaam'     => $_POST['achternaam'],
            'geslacht'       => $_POST['geslacht'],
            'geboortedatum'  => date('Y-m-d', strtotime($_POST['geboortedatum'])),
            'postcode'       => $adress->postcode,
            'nummer'         => $_POST['nummer'],
            'toevoeging'     => $_POST['toevoeging'],
            'straat'         => $adress->straat,
            'plaats'         => $adress->plaats,
            'land'           => 'Nederland',
            'telefoonVast'   => $_POST['telefoonVast'],
            'telefoonMobiel' => $_POST['telefoonMobiel'],
            'emailadres'     => $_POST['emailadres'],
            'iban'           => $iban ?? '',
            'autoincasso'    => $_POST['autoincasso'],
        ],
    ];

    $result = $auth->profielPersoonBewerken($params['gegevens'], $_POST['childID']);

    if ('OK' === $result->result) {
        wp_redirect(home_url($wp) . '/kinderen-account/');

        exit();
    }

    $validatedUserData['code'] = $result;
    $_SESSION['error']         = $validatedUserData;
    wp_redirect($baseUrl . '/kind-aanpassen/');

    exit();
}

add_action('admin_menu', 'regicare_plugin_settings');

/**
 * Voeg de pagina toe en voeg de hook toe voor het registreren van settings.
 */
function regicare_plugin_settings(): void
{
    add_menu_page('RegiCare Settings', 'RegiCare', 'manage_options', 'regicare-settings-mail', 'regicare_plugin_options');
    add_action('admin_init', 'register_regicare_settings');
}

/**
 * Registreer de settings.
 */
function register_regicare_settings(): void
{
    register_setting('regicare_settings', 'regicare_key');
    register_setting('regicare_settings', 'regicare_domain');
}

/**
 * Laat de settings form zien.
 */
function regicare_plugin_options(): void
{
    if (! current_user_can('manage_options')) {
        /** @noinspection ForgottenDebugOutputInspection */
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
        <img src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/southaxis.svg'; ?>" alt="SouthAxis" style="width: 13%; height: auto;">
        <h1>RegiCare plugin settings.</h1>
        <h2>Hier kan je alle RegiCare instellingen aanpassen.</h2>
        <!--suppress HtmlUnknownTarget -->
        <form id="regicare_settings" action="options.php" method="post">
            <?php settings_fields('regicare_settings'); ?>
            <?php do_settings_sections('regicare_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API key</th>
                    <td>
                        <input type="text" id="regicare_key" name="regicare_key" value="<?php echo esc_attr(get_option('regicare_key')); ?>"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">API Endpoint</th>
                    <td>
                        <input type="text" id="regicare_domain" name="regicare_domain" value="<?php echo esc_attr(get_option('regicare_domain')); ?>"/>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_filter('wp_nav_menu_items', 'do_shortcode');

<?php
if (!defined('ABSPATH')) {
    exit;
}

require 'vendor/autoload.php';

if (!class_exists('ActivityShow')) {
    class ActivityShow
    {
        private function get_first_regi_data($data)
        {
            $data = (gettype($data) != 'array') ? get_object_vars($data) : $data;
            foreach ($data as $key => $value) {
                return ['key' => $key, 'value' => $value];
            }
        }

        public function uniqueArray($array)
        {
            $one_dimension = array_map('serialize', $array);
            $unique_one_dimension = array_unique($one_dimension);
            $unique_multi_dimension[1] = array_map('unserialize', $unique_one_dimension);
            $uniqueArray[] = $unique_multi_dimension;
            return $uniqueArray;
        }

        public function registerActivity()
        {
            global $wp;
            global $activity;
            global $auth;

            if (isset($_SESSION['user']['loginKey'] )) {
                $oldLink = home_url($wp) . $_SERVER['REQUEST_URI'];
                $_SESSION['redirect_url'] = $oldLink;
                $user = $auth->profielGegevens();
                $children = $auth->profielPersoonGekoppeld();
                $childHTML = '<p>Kies hieronder wie u wilt inschijven voor de activiteit.</p><div class="user-picker">';
                $user->tussenvoegsel = $user->tussenvoegsel === null ? " " : " {$user->tussenvoegsel} ";
                $childHTML .= '<div class="row gutter"><input type="checkbox"  class="form-control form-control-checkbox " name="persoonID[]" id="' . @$user->persoonID . '" value="' . @$user->persoonID . '">

                        <label for="' . @$user->persoonID . '">' . $user->roepnaam . $user->tussenvoegsel . $user->achternaam . '</label></div>';
                if (!empty($children)) {
                    foreach ($children as $child) {
                        $child->tussenvoegsel = $child->tussenvoegsel === null ? " " : " {$child->tussenvoegsel} ";
                        $childHTML .= '<div class="row gutter"><input type="checkbox"  class="form-control form-control-checkbox " name="persoonID[]" id="' . $child->persoonID . '" value="' . $child->persoonID . '">

                        <label for="' . $child->persoonID . '">' . $child->roepnaam . $child->tussenvoegsel . $child->achternaam . '</label></div>';
                    }
                } else {
                    $childHTML .= '<p>Er zijn nog geen kinderen aan uw account gekoppeld.</p><form></form><a class="sb-color-orange" href="' . home_url($wp) . '/kind-registreren/">Voeg een kind toe aan uw account</a>';
                }
                $childHTML .= "</div>";
                if (isset($_REQUEST['error'])) {
                    $childHTML .= '<p class="sb-text-center sb-login-warning">U moet iemand kiezen om in te schrijven!</p>';
                }
                $childHTML .= '<a class="sb-color-orange" href="' . home_url($wp) . '/kind-registreren/">Voeg een kind toe aan uw account</a><br>';
            }

            $activityID = "";

            if (isset($_REQUEST['activityID'])) {
                $activityID = $error = ($_REQUEST['activityID']);
            }

            $act = $activity->getSpecificActivity($activityID);

            if (gettype($act) != "string") {
                ob_start();
                ?>
                <div class="row">
                    <div class="col col-md-4 col-reg-activity">
                        <div class="leftMetaHolder">
                            <h4 class="sb-color-orange"><?php echo $act->omschrijving ?></h4>
                            <p>
                                <b>Wanneer</b><br>
                                <?php
                                echo $this->get_first_regi_data($act->interval)['value'] . ", " . $this->get_first_regi_data($act->dag)['value'] . " " . date('d F', strtotime($act->startDatum)) . "<b> T/M </b> " . date('d F', strtotime($act->eindDatum));
                                ?>
                            </p>
                            <p>
                                <b>Locatie</b><br>
                                <?php echo $this->get_first_regi_data($act->locatie)['value']; ?>
                            </p>
                            <p>
                                <b>Kosten</b><br>
                                €
                                <?php
                                if ($act->prijs != null) {
                                    echo $act->prijs;
                                } else {
                                    echo 0;
                                } ?>
                            </p>
                            <p>
                                <b>Doelgroep</b><br>
                                <?php
                                if ($act->groepering != null) {
                                    $data = [];
                                    foreach ($act->groepering as $key => $value) {
                                        list($a, $b) = explode(' jaar ', $value);
                                        $data[] = $b;
                                    }
                                    echo join(", ", $data);
                                } ?>
                            </p>
                            <p>
                                <b>Interesse</b><br>
                                <?php
                                if ($act->activiteittype != null) {
                                   echo join(", ", array_values(get_object_vars($act->activiteittype)));
                                }
                                ?>
                            </p>
                            <p>
                                <b>Vrije plekken</b><br>
                                <?php
                                $num = $act->inschrijvingenMaximum - $act->inschrijvingen;
                                $place = $act->inschrijvingenMaximum - $act->inschrijvingen;
                                if ($act->inschrijvingen != null) {
                                    echo $num;
                                } else {
                                    echo $place;
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col col-md-4 col-reg-activity">
                        <div class="rightMetaHolder">
                            <h3 class="sb-color-orange sb-text-center">Aanmeldformulier</h3>
                            <p>U staat op het punt om u aan te melden voor de activiteit <?php echo $act->omschrijving ?>. De kosten hiervoor zullen €
                            <?php
                                if ($act->prijs != null) {
                                    echo $act->prijs;
                                } else {
                                    echo 0;
                                }
                            ?>
                            bedragen. Kansrijk Edam-Volendam gaat zorgvuldig om met uw persoonlijke gegevens en zal deze niet delen met derden.</p>
                            <br>
                            <br>
                            <p>Door op inschrijven te klikken gaat u akkoord met de <a class="sb-color-orange" href="<?php echo home_url($wp) ?>/algemene-voorwaarden/">algemene voorwaarden en inschrijfregels.</a></p>
                            <form method="post" enctype="multipart/form-data" id="registerActivityForm">
                                <?php if (@$_SESSION['user']['loginKey'] != null) {
                                    echo $childHTML;
                                }
                                ?>
                                <input type="text" name="loginKey" class="form-control" id="loginKey" value="<?php echo $_SESSION['user']['loginKey'] ?? '' ?>" aria-describedby="loginKey" style="display: none;">
                                <input type="text" name="activityID" class="form-control" id="activityID" value="<?php echo $activityID ?>" aria-describedby="activityID" style="display: none;">
                                <button type="submit" class="btn btn-orange sb-align-bottom">
                                    <?php if ($act->inschrijven == 1 && $act->inschrijvingenMaximum - $act->inschrijvingen != 0) {; ?>
                                        Inschrijven
                                    <?php } else {
                                    ?>
                                        Meld u aan voor de wachtlijst
                                    <?php
                                    } ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php
                return ob_get_clean();
            } else {
                echo "<div>Er was een fout bij het ophalen van de data</div>";
                if (!isset($_REQUEST['activityID'])) {
                    echo "<div>Geen geldige activiteit</div>";
                }
            }
        }

        public function showAllActivities()
        {
            global $activity;
            $activityTypes = [];
            $activityGroup = [];
            $activityAudience = [];
            $everything = null;

            try {
                $everything = $activity->getAllActivities();
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                $nothing = "<p>excuses voor het ongemak de activiteiten zijn op het moment niet beschikbaar</p>";
            }

            global $wp;
            $link = home_url($wp->request);
            ob_start();

            ?>
            <div class='container'>
                <table class="sb-table">
                    <tr>
                        <td colspan="3"><label for='tag'>Zoek Activiteit</label>
                            <input type="text" class="form-control" style="background-color: #fff !important; border: 1px solid #ced4da; border-radius: 0.25rem;" id="tag" name="tag">
                        </td>
                    </tr>
                    <tr>
                        <td><label for='groepering'>Leeftijd</label>
                            <select class='form-control' id='groepering' name='groepering'>
                                <option></option>
                                <option value='79'>4-12 jaar kinderen</option>
                                <option value='81'>12-18 jaar jongeren</option>
                            </select>
                        </td>
                        <td><label for='vrijkenmerk06'>Interesse</label>
                            <select class='form-control' id='vrijkenmerk06' name='vrijkenmerk06'>
                                <option></option>
                                <option value='281'>Cognitief</option>
                                <option value='282'>Fysiek</option>
                                <option value='283'>Creatief</option>
                                <option value='284'>Technisch</option>
                                <option value='285'>Sociaal Emotioneel</option>
                                <option value='289'>LEF</option>
                            </select>
                        </td>
                        <td>
                            <div class="sb-col-md-4">
                                <label for='dag'>Dag</label>
                                <select class='form-control' id='dag' name='dag'>
                                    <option></option>
                                    <option value='2'>Maandag</option>
                                    <option value='3'>Dinsdag</option>
                                    <option value='4'>Woensdag</option>
                                    <option value='5'>Donderdag</option>
                                    <option value='6'>Vrijdag</option>
                                    <option value='7'>Zaterdag</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"><label for='locatie'>Locatie</label>
                            <select class='form-control' id='locatie' name='locatie'>
                                <option></option>
                                <option value='1'>De Singel (Edam)</option>
                                <option value='2'>Pop-Cultuurhuis PX</option>
                                <option value='3'>De Ark</option>
                                <option value='4'>Maria Goretti Gebouw</option>
                                <option value='5'>Popoefenruimte PX</option>
                                <option value='6'>Sporthal Opperddam</option>
                                <option value='7'>Sporthal Bolwerck</option>
                                <option value='8'>Sporthal Seinpaal</option>
                                <option value='9'>Sporthal De Kreil</option>
                                <option value='10'>Dorpshuis Beets</option>
                                <option value='11'>Sportzaal De Schoolstraat</option>
                                <option value='12'>MFA Oosthuizen</option>
                                <option value='13'>Zwembad de Waterdam</option>
                                <option value='14'>Gymstuif Julianaweg</option>
                                <option value='15'>Plein PX</option>
                                <option value='16'>Plein Singel</option>
                                <option value='17'>Parkeerterrein Parallelweg</option>
                                <option value='18'>Boelenspark</option>
                                <option value='19'>Veldje beets</option>
                                <option value='20'>Indianendorp</option>
                                <option value='21'>MFA Oosthuizen</option>
                                <option value='22'>Natuurpark Broeckgouw</option>
                                <option value='23'>Speelveldje middelie</option>
                                <option value='24'>Noordervesting voetbalveldje</option>
                                <option value='25'>Gouden Slot speelveldje</option>
                                <option value='26'>Cruijf court</option>
                                <option value='27'>Oude Zuidwester speelveldje</option>
                                <option value='28'>Strandje Marinapark</option>
                                <option value='29'>Slobbeland</option>
                                <option value='30'>Achterzijde oude Seinpaal</option>
                                <option value='31'>RKAV veld 8</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <button class='sb-filter-button btn-orange sb-filter-button-text' id="submit_filters">Filteren</button>
            </div>

            <?php if ($everything != null) {
            ?>
                <div class='SB-activities-overview mt-3'>
                    <div class='container mt-5 displayActivities' id='all'>
                        <div class='row' id="filterResult" style="width: 100%">
                            <?php foreach ($everything as $activity) {
                            ?>
                                <div class="col-lg-4 d-flex align-items-stretch">
                                    <div class="card mb-3 w-100">
                                        <div class="card-body">
                                            <h5 class="card-title"> <?php echo $activity->omschrijving ?></h5>
                                            <p class="card-text activityText"> <?php echo wp_trim_words($activity->omschrijvingUitgebreid, $num_words = 55, $more = null) ?></p>
                                            <div class="popUpBox" style="display: none">
                                                <div class="metaInfoHolder">
                                                    <p class="card-text"><?php echo $activity->omschrijvingUitgebreid ?></p>
                                                    <div class="tableHolder">
                                                        <table>
                                                            <tbody>
                                                                <?php if ($activity->trefwoorden != null) { ?>
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
                                                                        if ($activity->interval != null && $activity->dag != null && $activity->startDatum != null && $activity->eindDatum != null) {
                                                                            echo array_shift(array_values(get_object_vars($activity->interval))) . ", " . array_shift(array_values(get_object_vars($activity->dag))) . " " . date('d F', strtotime($activity->startDatum)) . "<b> T/M </b> " . date('d F', strtotime($activity->eindDatum));
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-right: 15px">
                                                                        <b>Tijd</b>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if ($activity->startTijd != null && $activity->eindTijd != null) {
                                                                            echo $activity->startTijd . " - " . $activity->eindTijd;
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-right: 15px">
                                                                        <b>Url</b>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if ($activity->url != null) {
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
                                                                        if ($activity->locatie != null) {
                                                                            echo array_shift(array_values(get_object_vars($activity->locatie)));
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-right: 15px">
                                                                        <b>Ruimte</b>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if ($activity->ruimte != null) {
                                                                            echo array_shift(array_values(get_object_vars($activity->ruimte)));
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-right: 15px">
                                                                        <b>Plaats</b>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if ($activity->inschrijvingenMaximum != null) {
                                                                            echo "Maximum Inschrijvingen: " . $activity->inschrijvingenMaximum . "<br>";
                                                                        }
                                                                        $num = $activity->inschrijvingenMaximum - $activity->inschrijvingen;
                                                                        if (is_int($num)) {
                                                                            echo "Aantal vrije plekken: " . $num;
                                                                        } else {
                                                                            echo "Aantal vrije plekken: 0";
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-right: 15px">
                                                                        <b>Kosten</b>
                                                                    </td>
                                                                    <td>
                                                                        €
                                                                        <?php
                                                                        if ($activity->prijs != null) {
                                                                            echo $activity->prijs;
                                                                        } else {
                                                                            echo 0;
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-right: 15px">
                                                                        <b>Doelgroep</b>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if ($activity->groepering != null) {
                                                                            $data = [];
                                                                            foreach ($activity->groepering as $key => $value) {
                                                                                list($a, $b) = explode(' jaar ', $value);
                                                                                $data[] = $b;
                                                                            }
                                                                            echo join(", ", $data);
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding-right: 15px">
                                                                        <b>Interesse</b>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if ($activity->activiteittype != null) {
                                                                           echo join(", ", array_values(get_object_vars($activity->activiteittype)));
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <?php if ($activity->inschrijven === 1) { ?>
                                                        <a class="btn btn-orange float-right" href="<?php echo home_url($wp) ?>/inschrijven/?activityID=<?php echo $activity->activiteitID ?>">Inschrijven</a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <a class="btn btn-orange vertoon">Toon meer</a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php
            } else {
                echo $nothing;
            }
            return ob_get_clean();
        }

        public function showAllActivitiesEmpty()
        {
            ob_start();

            ?>
            <div class='container'>
                <table class="sb-table">
                    <tr>
                        <td colspan="3"><label for='groepering'>Zoek Activiteit</label>
                            <input type="text" class="form-control" style="background-color: #fff !important; border: 1px solid #ced4da; border-radius: 0.25rem;" id="tag" name="tag">
                        </td>
                    </tr>
                    <tr>
                        <td><label for='groepering'>Leeftijd</label>
                            <select class='form-control' id='groepering' name='groepering'>
                                <option></option>
                                <option value='79'>4-12 jaar kinderen</option>
                                <option value='81'>12-18 jaar jongeren</option>
                            </select>
                        </td>
                        <td><label for='vrijkenmerk06'>Interesse</label>
                            <select class='form-control' id='vrijkenmerk06' name='vrijkenmerk06'>
                                <option></option>
                                <option value='281'>Cognitief</option>
                                <option value='282'>Fysiek</option>
                                <option value='283'>Creatief</option>
                                <option value='284'>Technisch</option>
                                <option value='285'>Sociaal Emotioneel</option>
                                <option value='289'>LEF</option>
                            </select>
                        </td>
                        <td>
                            <div class="sb-col-md-4">
                                <label for='dag'>Dag</label>
                                <select class='form-control' id='dag' name='dag'>
                                    <option></option>
                                    <option value='2'>Maandag</option>
                                    <option value='3'>Dinsdag</option>
                                    <option value='4'>Woensdag</option>
                                    <option value='5'>Donderdag</option>
                                    <option value='6'>Vrijdag</option>
                                    <option value='7'>Zaterdag</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"><label for='locatie'>Locatie</label>
                            <select class='form-control' id='locatie' name='locatie'>
                                <option></option>
                                <option value='1'>De Singel (Edam)</option>
                                <option value='2'>Pop-Cultuurhuis PX</option>
                                <option value='3'>De Ark</option>
                                <option value='4'>Maria Goretti Gebouw</option>
                                <option value='5'>Popoefenruimte PX</option>
                                <option value='6'>Sporthal Opperddam</option>
                                <option value='7'>Sporthal Bolwerck</option>
                                <option value='8'>Sporthal Seinpaal</option>
                                <option value='9'>Sporthal De Kreil</option>
                                <option value='10'>Dorpshuis Beets</option>
                                <option value='11'>Sportzaal De Schoolstraat</option>
                                <option value='12'>MFA Oosthuizen</option>
                                <option value='13'>Zwembad de Waterdam</option>
                                <option value='14'>Gymstuif Julianaweg</option>
                                <option value='15'>Plein PX</option>
                                <option value='16'>Plein Singel</option>
                                <option value='17'>Parkeerterrein Parallelweg</option>
                                <option value='18'>Boelenspark</option>
                                <option value='19'>Veldje beets</option>
                                <option value='20'>Indianendorp</option>
                                <option value='21'>MFA Oosthuizen</option>
                                <option value='22'>Natuurpark Broeckgouw</option>
                                <option value='23'>Speelveldje middelie</option>
                                <option value='24'>Noordervesting voetbalveldje</option>
                                <option value='25'>Gouden Slot speelveldje</option>
                                <option value='26'>Cruijf court</option>
                                <option value='27'>Oude Zuidwester speelveldje</option>
                                <option value='28'>Strandje Marinapark</option>
                                <option value='29'>Slobbeland</option>
                                <option value='30'>Achterzijde oude Seinpaal</option>
                                <option value='31'>RKAV veld 8</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <button class='sb-filter-button btn-orange sb-filter-button-text' id="submit_filters">Toon activiteiten</button>
            </div>
            <div class="container loader-container">
                <div class="loader"></div>
            </div>
            <div class='SB-activities-overview mt-3'>
                <div class='container mt-5 displayActivities' id='all'>
                    <div class='row' id="filterResult" style="width: 100%">
                    </div>
                </div>
            </div>
        <?php
            return ob_get_clean();
        }
    }
}

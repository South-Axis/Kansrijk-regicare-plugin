<?php

declare(strict_types=1);

defined('ABSPATH') || exit('Forbidden');

?>
<div class='container'>
    <table class="sb-table">
        <tr>
            <td colspan="3">
                <label for="tag">Waar bent u naar op zoek? Typ hier uw zoekopdracht</label>
                <input type="text" class="form-control" style="background-color: #fff !important; border: 1px solid #ced4da; border-radius: 0.25rem;" id="tag" name="tag">
            </td>
        </tr>
        <tr>
            <td>
                <label for="groepering">Leeftijd</label>
                <select class='form-control' id='groepering' name='groepering'>
                    <option selected value="">Selecteer een leeftijd...</option>
                    <option value='79'>4-12 jaar kinderen</option>
                    <option value='81'>12-18 jaar jongeren</option>
                </select>
            </td>
            <td>
                <label for='dag'>Dag</label>
                <select class='form-control' id='dag' name='dag'>
                    <option selected value="">Selecteer een dag...</option>
                    <option value='2'>Maandag</option>
                    <option value='3'>Dinsdag</option>
                    <option value='4'>Woensdag</option>
                    <option value='5'>Donderdag</option>
                    <option value='6'>Vrijdag</option>
                    <option value='7'>Zaterdag</option>
                </select>
            </td>
            <td>
                <label for='locatie'>Locatie</label>
                <select class='form-control' id='locatie' name='locatie'>
                    <option selected value="">Selecteer een locatie...</option>
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

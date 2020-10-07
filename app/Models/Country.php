<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class Country extends Model
{
    protected $table = "country";

    public static function getCountryInfo($c_id) {
        try {
            $countryInfo = self::from('country_info')->where('Code2', $c_id)->get();

            $classTerm = 'class="country-info--term';
            $classDef = 'class="country-info--def';

            $dl = [];
            $dl[] = '<dl "class=country-info">';
            $dl[] = '<dt ' . $classTerm . '">Country Name:</dt>';
            $dl[] = '<dd id="c-name"' . $classDef . '">' . $countryInfo[0]->Country_Name .'</dd>';
            $dl[] = '<dt ' . $classTerm . '">Government Form:</dt>';
            $dl[] = '<dd id="govt-form"' . $classDef . '">' . $countryInfo[0]->GovernmentForm .'</dd>';
            $dl[] = '<dt ' . $classTerm . '">Popolation:</dt>';
            $dl[] = '<dd id="c-pop"' . $classDef . '">' . ($countryInfo[0]->Population / 1000000) .'M</dd>';
            $dl[] = '<dt ' . $classTerm . '">GNP:</dt>';
            $dl[] = '<dd id="c-gnp"' . $classDef . '">' . $countryInfo[0]->GNP .'</dd>';
            $dl[] = '<dt ' . $classTerm . '">Capital City:</dt>';
            $dl[] = '<dd id="c-cap"' . $classDef . '">' . $countryInfo[0]->Capital .'</dd>';
            $dl[] = '</dl>';

            return implode("", $dl);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
}

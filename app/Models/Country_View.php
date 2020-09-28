<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use Log;

class Country_View extends Model
{
    protected $table = "country_info";

    public function getDataAndCountryListForGIOjs() {
        $country = new Country;
        $country->setOECDData("JP");
        $countrylist = $country->getOECDCountryList();
        $GIOdata = $country->getDataForGIOjs($country, "JP");

        return view('world', ['data' => $GIOdata, 'country_list' => $countrylist]);
    }

    public function getCountryInfo($c_id) {
        $country = new Country;
        $country->setOECDData($c_id);
        $oecdCountries = $country->getOECDCountryList();
        $countryInfo = self::from('country_info')->whereIn('Code2', $oecdCountries);
        
        return $countryInfo;
    }
}

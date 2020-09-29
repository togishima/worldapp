<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use Log;

class CountryView extends Model
{
    protected $table = "country_info";
    protected $country;

    public function getDataForView() {
        $country = Country::firstOrNew();
        $country->setOECDData("JP");
    
        return $country->getDataForGIOjs($country, "JP");;
    }

    public function getCountryInfo($c_id) {
        $country = Country::firstOrNew();
        $country->setOECDData($c_id);
        $countryInfo = self::from('country_info')->where('Code2', $c_id)->get();
        
        return $countryInfo;
    }

    public function getJsonData(Request $request, $c_id)
    {
        $country = new Country;
        $country->setOECDData($c_id);
        $GIOdata = $country->getDataForGIOjs($country, $c_id)->get();
        
        return response()->json($GIOdata);
    }
}

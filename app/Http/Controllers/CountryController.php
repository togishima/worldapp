<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Log;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $country = new Country;
        $country->setOECDData("JP");
        $countrylist = $country->getOECDCountryList();

        $initCountry = "JP";
        $GIOdata = $country->getDataForGIOjs($country, $initCountry);
        $countryInfo = $country->countryInfo($initCountry);

        return view('world', ['data' => $GIOdata, 'countryList' => $countrylist, 'countryInfo'=>$countryInfo]);
    }

    public function getJsonData(Request $request, $c_id)
    {
        $country = Country::firstOrNew();
        $country->setOECDData($c_id);
        $GIOdata = $country->getDataForGIOjs($country, $c_id);
        
        return response()->json($GIOdata);
    }
    
    public function getCountryInfo(Request $request, $c_id) {
        $country = Country::firstOrNew();
        $country->setOECDData($c_id);
        $countryInfo = $country->from('country_info')->where('Code2', $c_id)->get();
        
        return json_encode($countryInfo);
    }
}

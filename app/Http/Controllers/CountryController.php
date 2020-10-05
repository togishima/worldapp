<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\CountryView;
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
        $view = new CountryView;
        $country = Country::firstOrNew();
        $initCountry = "JP";

        $GIOdata = $view->getGIOData($initCountry);
        $countrylist = $view->getCountryList();
        $countryInfo = $country->getCountryInfo($initCountry);

        return view('world', ['data' => $GIOdata, 'countryList' => $countrylist, 'countryInfo'=>$countryInfo]);
    }

    public function getJsonData(Request $request, $c_id)
    {
        $country = Country::firstOrNew();
        $country->setOECDData($c_id);
        $GIOdata = $view->getGIOData($c_id);
        
        return response()->json($GIOdata);
    }
    
    public function getCountryInfo(Request $request, $c_id) {
        $country = Country::firstOrNew();
        $countryInfo = $country->from('country_info')->where('Code2', $c_id)->get();
        
        return json_encode($countryInfo);
    }
}

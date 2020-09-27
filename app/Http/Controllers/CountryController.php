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
        $GIOdata = $country->getDataForGIOjs($country, "JP");

        return view('world', ['data' => $GIOdata, 'country_list' => $countrylist]);
    }

    public function getJsonData(Request $request, $c_id)
    {
        $country = new Country;
        $country->setOECDData($c_id);
        $GIOdata = $country->getDataForGIOjs($country, $c_id);
        
        return response()->json($GIOdata);
    }
}

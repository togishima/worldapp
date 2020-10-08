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
        $initCountry = "JP";
        $view = new CountryView;
        $GIOdata = $view->getGIOData($initCountry);
        $countrylist = $view->getCountryList();
        $countryInfoDOM = Country::getCountryInfoDOM($initCountry);

        return view('world', [
            'data' => $GIOdata,
            'countryList' => $countrylist,
            'countryInfoDOM' => $countryInfoDOM
        ]);
    }

    public function getJsonData(Request $request, $c_id)
    {
        $view = new CountryView;
        $GIOdata = $view->getGIOData($c_id);

        return response()->json($GIOdata);
    }

    public function getCountryInfo(Request $request, $c_id)
    {
        $countryInfo = Country::getCountryInfo($c_id);
        return json_encode($countryInfo);
    }
}

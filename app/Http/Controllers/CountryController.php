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
        $initCountry = "JP";
        $GIOdata = $view->getGIOData($initCountry);
        $countrylist = $view->getCountryList();
        $countryInfo = Country::getCountryInfo($initCountry);

        return view('world', [
            'data'=> $GIOdata, 
            'countryList' => $countrylist, 
            'countryInfo'=>$countryInfo
            ]);
    }

    public function getJsonData(Request $request, $c_id)
    {
        $view = CountryView::fistOrNew();
        $GIOdata = $view->getGIOData($c_id);
        
        return response()->json($GIOdata);
    }
    
    public function getCountryInfo(Request $request, $c_id) {
        $countryInfo = Country::getCountryInfo($c_id);
        return json_encode($countryInfo);
    }
}

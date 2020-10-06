<?php

namespace App;

use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Log;

class CountryView
{
    protected $countryList = [];
    protected $countryCodes;

    function __construct() {
        $this->countryCodes = DB::select('select Code, Code2 from country_code');
    }

    function getGIOData($CO) {
        try {
            //$CO を$COUに変換
            $COU = self::translateCountryCode($CO);
            //MySQLからデータを抽出
            $data = Country::getMIGData($COU, 2017);
            //データをGIO.js用に加工
            $GIOdata = [];
            foreach($data as $obsv) {
                
                $e = self::translateCountryCode($obsv->Nationality);
                $this->countryList[] = $e;
                $i = self::translateCountryCode($obsv->Destination);
                $v = $obsv->Value;

                if(isset($e) && isset($i) && $v) {
                    $GIOdata[] = [
                        'e'=> $e, 
                        'i'=> $i, 
                        'v' => $v
                    ];
                }
            }
            Log::debug($GIOdata);
            return $GIOdata;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function translateCountryCode($code) {
        try {
            if (strlen($code) == 2) {
                 $index = array_search($code, array_column($this->countryCodes, "Code2"));

                 if($index !== false) {
                     return $this->countryCodes[$index]->Code;
                 }

            } else {
                $index = array_search($code, array_column($this->countryCodes, "Code"));

                 if($index !== false) {
                     return $this->countryCodes[$index]->Code2;
                 }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    function getCountryList() {
        try {
            $country = Country::firstOrNew();
            $c_list = $country->select('Name')->WhereIn('Code2', $this->countryList);

            $array = [];
            foreach($c_list as $country) {
                $array[] = $country->Name;
            }

            return  $array;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    

    /*
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
    */
}

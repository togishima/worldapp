<?php

namespace App;

use App\Models\Country;
use Log;

class CountryView
{
    protected $countryList = [];

    function getGIOData($CO) {
        try {
            $country = Country::firstOrNew();
            //$CO を$COUに変換
            $COU = self::translateCountryCode($CO);
            //MySQLからデータを抽出
            $data = $country->getMIGData($COU, 2017);
            //データをGIO.js用に加工
            $GIOdata = [];
            foreach($data as $obsv) {
                $e = self::translateCountryCode($obsv->Nationality);
                $i = $CO;
                $v = $obsv->Value;

                if(isset($e)) {
                    $this->countryList[] = $e;
                }

                if(isset($e) && isset($i) && $v) {
                    $GIOdata[] = [
                        'e'=> $e, 
                        'i'=>$i, 
                        'v' =>($v*100)
                    ];
                }
            }
            return $GIOdata;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function translateCountryCode($code) {
        try {
            $country = Country::firstOrNew();
            if (strlen($code) == 2) {
                $COU = $country->select('Code')->from('country_code')->where('Code2', $code)->get();
                return $COU[0]->Code;
            } else {
                $CO = $country->select('Code2')->from('country_code')->where('Code', $code)->get();
                if(isset($CO[0])) {
                    return $CO[0]->Code2;
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

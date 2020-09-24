<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class Country extends Model
{
    protected $table = "country";
    protected $countryInfo;
    protected $data;
    protected $GIOdata;    

    function setOECDData() {     
        $oecd = new OECD;

        //OECDのリストからコードを含む配列を作成
        $oecdCountries = $oecd->getOECDCountries();
        $this->countryInfo = self::from('country_code')->get();

        //日本から各国への移民データを取得
        $this->data = $oecd->getOutBoundData("Japan", 2012);        
    }

    function findCountryCode($c_code) {
        foreach($this->countryInfo as $countryInfo) {
            if($countryInfo->Code == $c_code) {
                return $countryInfo->Code2;
            }
        }
    }

    public function getData() {
        return $this->data;
    }

    public function getDataForGIOjs($country) {
        $gdata = [];
        $gdata['dataSetKeys'] = [];
        $count= 1;
        //Log::debug($country->data);
        foreach($country->data as $year => $array) {
            array_push($gdata['dataSetKeys'], "key".$count);
            
            if ($count == 1) {
                $gdata['initDataSet'] = "key".$count;
            }

            $gdata["key".$count] =[];
            foreach($array as $c_code => $mig_value) {
                $tmp = [];
                $i = $country->findCountryCode($c_code);
                $tmp = [
                    "e" => "JP",
                    "i" => $i,
                    "v" => ($mig_value * 1000)
                ];
                array_push($gdata["key".$count], $tmp);
            }
            $count++;          
        }
        return $gdata;
    }


}

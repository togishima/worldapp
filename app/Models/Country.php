<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class Country extends Model
{
    protected $table = "country";
    protected $countryInfo;
    protected $dataIn;
    protected $dataOut;
    protected $GIOdata;    

    function setOECDData() {     
        $oecd = new OECD;

        //OECDのリストからコードを含む配列を作成
        $oecdCountries = $oecd->getOECDCountries();
        $this->countryInfo = self::from('country_code')->get();

        //日本から各国への移民データを取得
        $this->dataOut = $oecd->getOutBoundData("Japan", 2012);
        $this->dataIn = $oecd->getInBoundData("Japan", 2012);
    }

    function findCountryCode($c_code) {
        foreach($this->countryInfo as $countryInfo) {
            if($countryInfo->Code == $c_code) {
                return $countryInfo->Code2;
            }
        }
    }

    public function getDataForGIOjs($country) {
        $gdata = [];
        $gdata['dataSetKeys'] = [];
        $gdata['initDataSet'] = "key1";

        //Outboundデータの処理
        $count= 1;
        foreach($country->dataOut as $year => $array) {
            array_push($gdata['dataSetKeys'], "key".$count);
            //キー（年度）毎に配列を作成
            $gdata["key".$count] = [];

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
        $count = 1;

        //inboundデータの処理
        foreach($country->dataIn as $year => $array) {
            foreach($array as $c_code => $mig_value) {
                $tmp = [];
                $i = $country->findCountryCode($c_code);
                if($i == null) {
                    continue;
                }
                $tmp = [
                    "e" => $i,
                    "i" => "JP",
                    "v" => ($mig_value * 1000)
                ];
                array_push($gdata["key".$count], $tmp);
            }
            $count++;
        }
        return $gdata;
    }
}

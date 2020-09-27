<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class Country extends Model
{
    protected $table = "country";
    protected $countryInfo;
    protected $oecdCountryList;
    protected $dataIn;
    protected $dataOut;
    protected $GIOdata;

    function setOECDData($c_id) {     
        $oecd = new OECD;
        $oecdTmpList = $oecd->getOECDCountries();
        $failed_list = ["AR", "BG", "CR", "GR","RU", "SA", "SK","ZA"];
        $this->oecdCountryList = self::from('country_code')->whereIn('Code', $oecdTmpList)->whereNotIn('Code2', $failed_list)->get();
        
        //日本から各国への移民データを取得
        $c_name = self::findCountryName($c_id);
        $this->dataOut = $oecd->getOutBoundData($c_name, 2015);
        $this->dataIn = $oecd->getInBoundData($c_name, 2015);
    }

    function getOECDCountryList() {
        

        return $this->oecdCountryList;
    }

    function findCountryCode2($c_code2) {
        $c_list = self::from('country_code')->get();
        foreach($c_list as $countryInfo) {
            if($countryInfo->Code == $c_code2) {
                return $countryInfo->Code2;
            }
        }
    }
    function findCountryName($c_code2) {
        $c_list = self::from('country_code')->where('Code2', $c_code2)->get();
        foreach($c_list as $countryInfo) {
            if($countryInfo->Code2 == $c_code2) {
                return $countryInfo->Name;
            }
        }
    }

    public function getDataForGIOjs($country, $countryCode) {
        $gdata = [];
        $gdata['dataSetKeys'] = [];
        $gdata['initDataSet'] = "key1";

        //Outboundデータの処理
        $count= 1;
        if(isset($country->dataOut)) {
            foreach($country->dataOut as $year => $array) {
                if(array_key_exists('key'. $count, $gdata['dataSetKeys']) == false) {
                    array_push($gdata['dataSetKeys'], "key".$count);
                }
                //キー（年度）毎に配列を作成
                $gdata["key".$count] = [];

                foreach($array as $c_code => $mig_value) {
                    $tmp = [];
                    $i = $country->findCountryCode2($c_code);
                    $tmp = [
                        "e" => $countryCode,
                        "i" => $i,
                        "v" => ($mig_value * 1000)
                    ];
                    array_push($gdata["key".$count], $tmp);
                }
                $count++;          
            }
        }
        $count = 1;
        //inboundデータの処理
        if(isset($country->dataIn)) {
            foreach($country->dataIn as $year => $array) {
                if(array_key_exists('key'.$count, $gdata['dataSetKeys']) == false) {
                    array_push($gdata['dataSetKeys'], "key".$count);
                }
                foreach($array as $c_code => $mig_value) {
                    $tmp = [];
                    $i = $country->findCountryCode2($c_code);
                    if($i == null) {
                        continue;
                    }
                    $tmp = [
                        "e" => $i,
                        "i" => $countryCode,
                        "v" => ($mig_value * 1000)
                    ];
                    array_push($gdata["key".$count], $tmp);
                }
                $count++;
            }
        }
        return $gdata;
    }
}

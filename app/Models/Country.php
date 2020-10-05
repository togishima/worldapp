<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class Country extends Model
{
    protected $table = "country";

    function getMIGData($COU, $year) {
        try {
            $data = self::select('Nationality', 'Destination', 'Value')
                ->from('oecd_data')
                ->where('Destination', $COU)
                ->where('Year', $year)
                ->where('Value', ">", 0)
                ->get();

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getCountryInfo($c_id) {
        $countryInfo = self::from('country_info')->where('Code2', $c_id)->get();
        Log::debug($countryInfo);

        $classTerm = 'class="country-info--term';
        $classDef = 'class="country-info--def';

        $dl = [];
        $dl[] = '<dl "class=country-info">';
        $dl[] = '<dt ' . $classTerm . '">Country Name:</dt>';
        $dl[] = '<dd id="c-name"' . $classDef . '">' . $countryInfo[0]->Country_Name .'</dd>';
        $dl[] = '<dt ' . $classTerm . '">Government Form:</dt>';
        $dl[] = '<dd id="govt-form"' . $classDef . '">' . $countryInfo[0]->GovernmentForm .'</dd>';
        $dl[] = '<dt ' . $classTerm . '">Popolation:</dt>';
        $dl[] = '<dd id="c-pop"' . $classDef . '">' . ($countryInfo[0]->Population / 1000000) .'M</dd>';
        $dl[] = '<dt ' . $classTerm . '">GNP:</dt>';
        $dl[] = '<dd id="c-gnp"' . $classDef . '">' . $countryInfo[0]->GNP .'</dd>';
        $dl[] = '<dt ' . $classTerm . '">Capital City:</dt>';
        $dl[] = '<dd id="c-cap"' . $classDef . '">' . $countryInfo[0]->Capital .'</dd>';
        $dl[] = '</dl>';

        return implode("", $dl);
    }

    /*
    protected $countryInfo;
    protected $oecdCountryList;
    protected $dataIn;
    protected $dataOut;
    protected $GIOdata;

    function setOECDData($c_id) {     
        $oecd = new OECD;
        $oecdTmpList = $oecd->getOECDCountries();
        $failed_list = ["AR", "BG", "CR", "GR","RU", "SA", "SK","ZA"];
        $this->oecdCountryList = self::from('country_code')
        ->whereIn('Code', $oecdTmpList)
        ->whereNotIn('Code2', $failed_list)
        ->get();
        
        //日本から各国への移民データを取得
        $c_name = self::findCountryName($c_id);
        $this->dataOut = $oecd->getOutBoundData($c_name, 2013);
        $this->dataIn = $oecd->getInBoundData($c_name, 2013);
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
        $gdata['initDataSet'] = 2013;

        //Outboundデータの処理
        $year= 2013;
        if(isset($country->dataOut)) {
            foreach($country->dataOut as $year => $array) {
                if(array_key_exists($year, $gdata['dataSetKeys']) == false) {
                    array_push($gdata['dataSetKeys'], $year);
                }
                //キー（年度）毎に配列を作成
                $gdata[$year] = [];

                foreach($array as $c_code => $mig_value) {
                    $tmp = [];
                    $i = $country->findCountryCode2($c_code);
                    $tmp = [
                        "e" => $countryCode,
                        "i" => $i,
                        "v" => ($mig_value * 1000)
                    ];
                    array_push($gdata[$year], $tmp);
                }
                $year++;          
            }
        }
        $year = 2013;
        //inboundデータの処理
        if(isset($country->dataIn)) {
            foreach($country->dataIn as $year => $array) {
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
                    array_push($gdata[$year], $tmp);
                }
                $year++;
            }
        }
        return $gdata;
    }

    
    */
}

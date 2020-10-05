<?php

namespace App;

use Throwable;
use Log;

class OECD {
    protected $countries = [];
    protected $nationalities = [];
    protected $data = [];

    function __construct() {
        try {
            //OECDの移民データベースの構造を取得
            $xml = file_get_contents('https://stats.oecd.org/restsdmx/sdmx.ashx/GetDataStructure/MIG');
            $xml = preg_replace('/(<\/?)\w+:([^>]*>)/', '$1$2', $xml); //get rid of namespaces
            $xmlObject = simplexml_load_string($xml);
            $codeList = $xmlObject->CodeLists->CodeList; //使用可能なコードを取得
        
            //OECD加盟国の一覧を取得
            $countryCodes = json_decode(json_encode($codeList[3]), true);
            $countries = [];
            foreach($countryCodes['Code'] as $c) {
                $countryName = trim($c['Description'][0]);
                $countryCode = trim($c['@attributes']['value']);
                if ($countryCode == "NMEC") {
                    continue;
                } else {
                    $countries[] = ["Name" => $countryName, "code" => $countryCode];
                }
            }
            $this->countries = $countries;

            //クエリ可能な国籍一覧を取得
            $countryCodes = json_decode(json_encode($codeList[0]), true);
            $nationalities = [];
            foreach($countryCodes['Code'] as $c) {
                $countryName = trim($c['Description'][0]);
                $countryCode = trim($c['@attributes']['value']);
                if ($countryCode == "NMEC") {
                    continue;
                } else {
                    $nationalities[] = ["Name" => $countryName, "code" => $countryCode];
                }
            }
            $this->nationalities = $nationalities;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function getCountryList() {
        return $this->countries;
    }

    //APIクエリ用パラメーターの作成
    function getQueryParam($COU , $year) {
        try {
            $natList = $this->nationalities;
            $targetIndex = array_search($COU, array_column($natList, "code"));
            $targetCOU = $natList[$targetIndex]['code'];
            
            $query = [];
            $query[] = 'https://stats.oecd.org/SDMX-JSON/data/MIG/';
            
            foreach($natList as $c ) {
                if($c['code'] == $targetCOU || strlen($c['code']) >= 4 || $c['code'] == "TOT") {
                    continue;
                } else {
                    $query[] = $c['code'];
                }

                if($c !== end($natList)) {
                    $query[] = "+";
                }
            }
            $query[] = ".";
            $query[] = 'B11.TOT.';
            $query[] = $targetCOU;

            //指定の年から2017までのデータ
            $query[] = '/OECD?startTime=' . $year . '&endTime=2017';

            //配列を文字列にして返す
            return implode("", $query);

        } catch (\Throwable $e) {
            throw $e;
        }    
    }

    //指定した国の流入情報を取得
    function getInBoundData($COU, $year) {
        //datasetsを取り出す
        $url = self::getQUeryParam($COU, $year);
        try {
            $context = stream_context_create(array(
                'http' => array('ignore_errors' => true)
            ));

            $data = json_decode(file_get_contents($url, false, $context));

            $pos = strpos($http_response_header[0], '200');
            if ($pos === false) {
                return false;
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        $countryData = $data->structure->dimensions->series[0]->values;
        $dataSets = $data->dataSets;

        //各国のコードを配列に格納
        $countryCodes = [];
        foreach($countryData as $data) {
            $countryCodes[] = $data->id;
        }

        //dataSetsからobservationsの値を取り出す
        $tmpData = [];
        foreach($dataSets as $dataset) {
            foreach($dataset->series as $series) {
                $count = $year;
                $tmp = [];
                foreach ($series->observations as $obsv) {
                    $tmp[$count] = $obsv[0];
                    $count ++;
                }
                $tmpData[] = $tmp;
            }
        }

        //countryCodesをキーにtmpDataを統合
        $obsvData = array_combine($countryCodes, $tmpData);     
        
        return $obsvData;
    }
}
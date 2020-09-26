<?php

namespace App\Models;

use Carbon\Carbon;
use Log;

class OECD {
    protected $carbon;
    protected $countries;
    protected $countryCodes = [];
    protected $data = [];

    function __construct() {
        //OECDの移民データベースの構造を取得
        $xml = file_get_contents('https://stats.oecd.org/restsdmx/sdmx.ashx/GetDataStructure/MIG');
        $xml = preg_replace('/(<\/?)\w+:([^>]*>)/', '$1$2', $xml); //get rid of namespaces
        $xmlObject = simplexml_load_string($xml);
        $xmlArray = json_decode( json_encode( $xmlObject ), TRUE );
        $codeList = $xmlArray['CodeLists']['CodeList'];

        //OECD加盟国の一覧を取得
        $this->countries = [];
        $countryCodes = $codeList[3]['Code'];
        
        foreach($countryCodes as $c) {
            $countryName = trim($c['Description'][0]);
            $countryCode = trim($c['@attributes']['value']);
            if ($countryCode == "NMEC") {
                continue;
            } else {
                $countries[$countryName] = $countryCode;
            }
        }
        $this->countries = $countries;
    }

    public function getOECDCountries() {
        return $this->countries;
    }

    //APIクエリ用パラメーターの作成
    function getQueryParam($country,$datatype) {
        $countryList = $this->countries;
        $query = [];
        $query[] = 'https://stats.oecd.org/SDMX-JSON/data/MIG/';
        //全てを選択した場合
        if ($datatype == "inbound") {
            foreach($countryList as $c) {
                if($c == "NMEC") {
                    continue;
                }
                $query[] = $c;
                if($c !== end($countryList)) {
                    $query[] = "+";
                }
            }
            $query[] = ".";
        //クエリの対象国を絞った場合の処理
        } elseif (array_key_exists($country, $countryList)) {
            $query[] = $countryList[$country] . '.';
        } 

        //B11 = 各国から対象国への移住数、B12=対象の国から他国への移住数、TOT＝合計
        if($datatype == "inbound") {
            $query[] = 'B11.TOT.';
        } else {
            $query[] = 'B12.TOT.';
        }

        //調べたい国から各国への移民数を取得する
        if($datatype == "inbound") {
            $query[] = $countryList[$country];
        } else {
            foreach($countryList as $key => $value) {
                $query[] = $value;
                if($value !== end($countryList)) {
                    $query[] = "+";
                }
            }
        }
        //2012年以降のデータを取得
        $query[] = '/OECD?startTime=2012';

        return implode("", $query);
    }

    /**
     * GIO.jsデータ成形用関数群
     */

    //データセットからデータを抽出
    function extractData($datasets) {
        $tmp = [];
        foreach($datasets as $dataset) {
            foreach($dataset['series'] as $series) {
                $tmp[] = $series['observations'];
            }
        }
        return $tmp;
    }

    //国データから国コードを抽出
    function extractCountryCodes($countryData) {
        $countryCodes = [];
        foreach($countryData as $data) {
            $countryCodes[] = $data['id'];
        }
        return $countryCodes;
    }
    //データセットを配列に成形
    function formatData($tmp, $year, $countryCodes) {
        $numOfDataSets = 2018-$year;      
        $obsv = [];
        for($i = 0; $i < $numOfDataSets; $i++) {
            $arr = [];
            $currentYear = $year+$i;
            foreach($tmp as $tmpArr) {
                if (isset($tmpArr[0][0])) {
                    $arr[] = $tmpArr[0][0];
                } else {
                    $arr[] = null;
                }
            }
            //obsvの値のキーにセットして出力
            $obsv[$currentYear] = array_combine($countryCodes, $arr);
        }
        return $obsv;
    }
    
    function getOutBoundData($country, $year) {
        //datasetsを取り出す
        $url = self::getQUeryParam($country, 'outbound');
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        //Log::debug($data);

        //dataSetsからobservationsの値を取り出す
        $tmp = self::extractData($data['dataSets']);

        //国コードを抽出
        $countryData = $data['structure']['dimensions']['series'][3]['values'];
        $countryCodes = self::extractCountryCodes($countryData);
        
        //データを成形
        $observationData = self::formatData($tmp, $year, $countryCodes);

        return $observationData;
    }

    function getInBoundData($country, $year) {
        //datasetsを取り出す
        $url = self::getQUeryParam($country, 'inbound');
        $json = file_get_contents($url);
        $data = json_decode($json, true);

        //dataSetsからobservationsの値を取り出す
        $tmp = self::extractData($data['dataSets']);

        //国名を抽出
        $countryData = $data['structure']['dimensions']['series'][0]['values'];
        $countryCodes = self::extractCountryCodes($countryData);
        
        //データを成形
        $observationData = self::formatData($tmp, $year, $countryCodes);     
        
        return $observationData;
    }
}
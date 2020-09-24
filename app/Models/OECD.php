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
            $countries[$countryName] = $countryCode;
        }
        $this->countries = $countries;
    }

    public function getOECDCountries() {
        return $this->countries;
    }

    //APIクエリ用パラメーターの作成
    function getQueryParam($country, $year) {
        $countryList = $this->countries;
        $query = [];
        $query[] = 'https://stats.oecd.org/SDMX-JSON/data/MIG/';
        //クエリの対象国（調べたい国）があるかチェック
        if(array_key_exists($country, $countryList)) {
            $query[] = $countryList[$country] . '.';
        }
        //B12=対象の国から他国への移住数、TOT＝合計
        $query[] = 'B12.TOT.';
        //調べたい国から各国への移民数を取得する
        foreach($countryList as $key => $value) {
            $query[] = $value;
            if($value !== end($countryList)) {
                $query[] = "+";
            }
        }
        //入力された年をクエリに組み込み
        $query[] = '/OECD?startTime=' . $year . '&endTime=' . ($year+1);

        return implode("", $query);
    }

    //jsonデータをGIO.js用に成形
    function getOutBoundData($country, $year) {
        //datasetsを取り出す
        $url = self::getQUeryParam($country, $year);
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        Log::debug($data);

        //dataSetsからobservationsの値を取り出す
        $tmp = [];
        foreach($data['dataSets'] as $dataset) {
            foreach($dataset['series'] as $series) {
                $tmp[] = $series['observations'];
            }
        }

        //国名を抽出
        $countryData = $data['structure']['dimensions']['series'][3]['values'];
        $countryCodes = [];
        foreach($countryData as $data) {
            //Log::debug($data);
            $countryCodes[] = $data['id'];
        }
        
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
}
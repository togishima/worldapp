<?php

namespace App\Model;

use Carbon\Carbon;
use Log;

class OECD {
    protected $carbon;
    protected $codeList;
    protected $oecd_countries;

    //OECDの移民データベースの構造を取得（jsonデータに変換）
    function __construct() {
        $xml = file_get_contents('https://stats.oecd.org/restsdmx/sdmx.ashx/GetDataStructure/MIG');
        $xml = preg_replace('/(<\/?)\w+:([^>]*>)/', '$1$2', $xml); //get rid of namespaces
        $xmlObject = simplexml_load_string($xml);
        $xmlArray = json_decode( json_encode( $xmlObject ), TRUE );
        $this->codeList = $xmlArray['CodeLists']['CodeList'];

        //OECD加盟国の一覧を取得
        $this->oecd_countries = [];
        $countryCodes = $this->codeList[3]['Code'];
        foreach($countryCodes as $c) {
            $countryName = trim($c['Description'][0]);
            $countryCode = trim($c['@attributes']['value']);
            $countries[$countryName] = $countryCode;
        }
        $this->oecd_countries = $countries;
    }

    //APIクエリ用パラメーターの作成
    public function getQueryParam($country, $year) {
        $countryList = $this->oecd_countries;
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
}
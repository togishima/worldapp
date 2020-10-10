<?php

namespace App;

use App\Models\OECDData;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Log;

class CountryView
{
    protected $countryList = [];
    protected $countryCodes;

    function __construct()
    {
        //国コード変換用のリストを作成
        $this->countryCodes = DB::select('select Code, Code2 from country_code');
    }

    /**
     * @param 国コード（2文字）
     * @return $GIOdata
     */
    function getGIOData($CO)
    {
        try {
            //$CO を$COUに変換
            $COU = self::translateCountryCode($CO);

            //MySQLからデータを抽出
            $data = OECDData::getMIGData($COU);
            if (empty($data)) {
                return false;
            }
            //格納用の連想配列を作成
            $GIOdata = [];
            $GIOdata['dataSetKeys'] = [];
            $GIOdata['initDataset'] = [];
            foreach ($data as $record) {
                if (empty($GIOdata['initDataSet'])) {
                    $GIOdata['initDataSet'] = $record->Year;
                }
                if (array_search($record->Year, $GIOdata['dataSetKeys']) !== false) {
                    continue;
                }
                $GIOdata['dataSetKeys'][] = $record->Year;
                $GIOdata[(string)$record->Year] = [];
            }

            //データをGIO.js用に加工して返す

            foreach ($data as $obsv) {
                //各プロパティを変数に格納
                $e = self::translateCountryCode($obsv->Nationality);
                $i = self::translateCountryCode($obsv->Destination);
                $v = $obsv->Value;

                if (isset($e)) {
                    $this->countryList[] = $e;
                }

                if (isset($e) && isset($i) && $v) {
                    array_push($GIOdata[$obsv->Year], [
                        'e' => $e,
                        'i' => $i,
                        'v' => ($v * 1000)
                    ]);
                }
            }

            return $GIOdata;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * @param 国コード（2文字 or 3文字）
     * @return 国コード2文字⇒3文字、3文字⇒2文字
     */
    function translateCountryCode($code)
    {
        try {
            if (strlen($code) == 2) {
                $index = array_search($code, array_column($this->countryCodes, "Code2"));
                if ($index !== false) {
                    return $this->countryCodes[$index]->Code;
                }
            } else {
                $index = array_search($code, array_column($this->countryCodes, "Code"));
                if ($index !== false) {
                    return $this->countryCodes[$index]->Code2;
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * @return 最後にデータを取得した際に作成したリスト
     */
    function getCountryList()
    {
        try {
            $List = Country::select('Name', 'Code2')->whereIn('Code2', array_unique($this->countryList))->get();
            return $List;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

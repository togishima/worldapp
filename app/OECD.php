<?php

namespace App;

use App\Models\OECDData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Throwable;
use Log;

class OECD {
    protected $countries = [];
    protected $nationalities = [];
    protected $tanslationData = [];

    /**　コンストラクタ
     * インスタンス変数に
     * 1. OECDの構造を取得し、クエリに使える国名をリスト形式で取得し格納
     * 2. データベースから国コード変換用のデータを取得格納
     */
    function __construct() {
        try {
            //XMLオブジェクトを生成
            $xml = file_get_contents('https://stats.oecd.org/restsdmx/sdmx.ashx/GetDataStructure/MIG');
            $xml = preg_replace('/(<\/?)\w+:([^>]*>)/', '$1$2', $xml); //get rid of namespaces
            $xmlObj = simplexml_load_string($xml);

            //コード一覧を取得
            $codeList = $xmlObj->CodeLists->CodeList; //使用可能なコードを取得

            function extractCode($codeList) {
                $omitList = ["UUU", "YYY", "CAX", "CGX", "CEX", "EEA", "E15", "TOT"];
                $list = [];
                foreach ($codeList as $c_code) {
                    $Code = $c_code->attributes()->value;

                    if (strlen($Code) !== 3 || array_search($Code, $omitList) !== false) {
                        continue;
                    }

                    $list[] = $c_code->attributes()->value;
                }
                return $list;
            }

            //OECD加盟国の一覧を取得
            $this->countries = extractCode($codeList[3]);

            //クエリ可能な国籍一覧を取得
            $this->nationalities = extractCode($codeList[0]);

            //コード変換用の配列をセット
            $this->translationData = DB::select('select Code, Code2 from country_code');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * @param $code //国別コード 2桁 or 3桁
     * @return $COU or $CO //2桁のコードは3桁に、3桁のコードは2桁にして返す
     */
    function translateCountryCode($code) {
        try {
            //引数で渡ってきたコードの長さによって処理を変える
            switch (strlen($code)) {
                case 2:
                    $columnName = "Code";
                    $index = array_search($code, array_column($this->translationData, "Code2"));
                    break;
                case 3:
                    $columnName = "Code2";
                    $index = array_search($code, array_column($this->translationData, "Code"));
                    $code = $this->translationData[$index]->Code2;
                    break;
                default:
                    return false;
            }

            //GIO.jsで使える国コードのリストを使ってValidateしてからreturn
            $GIOCountryList = ['AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AZ', 'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BR', 'BS', 'BT', 'BW', 'BY', 'BZ', 'CA', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CU', 'CV', 'CY', 'CZ', 'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ', 'EC', 'EE', 'EG', 'EH', 'ER', 'ES', 'ET', 'FI', 'FJ', 'FK', 'FM', 'FO', 'FR', 'GA', 'GB', 'GD', 'GE', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GT', 'GU', 'GW', 'GY', 'HK', 'HN', 'HR', 'HT', 'HU', 'ID', 'IE', 'IL', 'IM', 'IN', 'IQ', 'IR', 'IS', 'IT', 'JE', 'JM', 'JO', 'JP', 'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ', 'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'ME', 'MG', 'MH', 'MK', 'ML', 'MM', 'MN', 'MP', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ', 'NA', 'NC', 'NE', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ', 'OM', 'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PS', 'PT', 'PW', 'PY', 'QA', 'RE', 'RO', 'RS', 'RU', 'RW', 'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'ST', 'SV', 'SY', 'SZ', 'TC', 'TD', 'TG', 'TH', 'TJ', 'TL', 'TM', 'TN', 'TO', 'TR', 'TT', 'TV', 'TW', 'TZ', 'UA', 'UG', 'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU', 'WF', 'WS', 'YE', 'YT', 'ZA', 'ZM', 'ZW'];
            if ($index !== false && array_search($code, $GIOCountryList) !== false) {
                return $this->translationData[$index]->$columnName;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /**
     * @param $COU = 国コード str(3), $year = データ検索の開始年 int 4
     * @return $quert = string （APIクエリ用の文字列） 
     */
    function buildQueryString($COU, $year) {
        try {
            $natList = $this->nationalities;
            $targetIndex = array_search($COU, $natList);
            $targetCOU = $natList[$targetIndex];

            $query = [];
            $query[] = 'https://stats.oecd.org/SDMX-JSON/data/MIG/';

            foreach ($natList as $c) {
                if ($c == $targetCOU) {
                    continue;
                }

                $query[] = $c;

                if ($c !== end($natList)) {
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

    //指定した国の流入情報をAPIから取得
    function fetchInboundData($COU, $year) {
        //datasetsから必要な値等をを取り出す処理
        try {
            //指定された国コードの2013～2017年のデータクエリの作成
            $url = self::buildQueryString($COU, $year);
            $res = Http::withOptions(['http_errors' => false])->get($url);

            //httpエラーの場合はfalseを返す
            if (($res->failed())) {
                return false;
            }

            //$res(json)をオブジェクトに変換
            $data = json_decode($res->body());

            //国コードのリストと各年のデータを切り出し
            $countryData = $data->structure->dimensions->series[0]->values;
            $dataSets = $data->dataSets;

            //各国のコードを配列に格納
            $countryCodes = [];
            foreach ($countryData as $data) {
                $countryCodes[] = $data->id;
            }

            //dataSetsから各年のobservationsの値を配列(key = year, values = 値)に切り出し
            $tmpData = [];
            foreach ($dataSets as $dataset) {
                foreach ($dataset->series as $series) {
                    $count = $year;
                    $tmp = [];
                    foreach ($series->observations as $obsv) {
                        $tmp[$count] = $obsv[0];
                        $count++;
                    }
                    $tmpData[] = $tmp;
                }
            }

            //countryCodesをキーにtmpDataを統合して返す
            return array_combine($countryCodes, $tmpData);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function fetchAPIData($COU) {
        try {
            //データは2013年～2017年の物を使用
            for ($i = 0; $i < 5; $i++) {
                $year = 2013 + $i;
                $data = self::fetchInboundData($COU, $year);

                //データが取得できなければ次のループへ
                if (empty($data)) {
                    continue;
                }

                //取得したデータをOECDDataオブジェクトにマッピングしてMySQLに保存
                foreach ($data as $c_code => $obsv) {

                    //Destination or Nationalityに入るべきデータがnullの場合は処理しない
                    if (empty($c_code) || empty($obsv[$year])) {
                        continue;
                    }

                    //マッピング前にISOコードへ変換
                    $des = self::translateCountryCode($COU);
                    $nat = self::translateCountryCode($c_code);

                    //コードを変換できなかった場合は処理しない
                    if (empty($des) || empty($nat)) {
                        continue;
                    }

                    //マッピング用のモデルを呼び出す（データベースにない場合は新規でインスタンスを生成）
                    $dataModel = OECDData::firstOrNew([
                        "Destination" => $des,
                        "Nationality" => $nat,
                        "Year" => $year
                    ]);
                    $dataModel->Value = $obsv[$year];

                    //マッピングしたプロパティを保存
                    $dataModel->save();
                }
                echo "MySQL：" . $COU . "の" . $year . "年データを更新しました" . "\n";
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
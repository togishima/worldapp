<?php

namespace App;

use App\Models\OECDData;
use App\Models\Country;
use Log;

class CountryView
{
    protected $countryList = [];

    /**
     * @param 国コード（2文字）
     * @return $GIOdata
     */
    function getGIOData($CO)
    {
        try {
            //MySQLからデータを抽出
            $data = OECDData::getMIGData($CO);
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
                $e = $obsv->Nationality;
                $i = $obsv->Destination;
                $v = $obsv->Value;

                if (isset($e)) {
                    $this->countryList[] = $e;
                }

                if (isset($e) && isset($i) && $v) {
                    array_push($GIOdata[$obsv->Year], [
                        'e' => $e,
                        'i' => $i,
                        'v' => $v
                    ]);
                }
            }

            return $GIOdata;
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
            $GIOCountryList = ['AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AZ', 'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BR', 'BS', 'BT', 'BW', 'BY', 'BZ', 'CA', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CU', 'CV', 'CY', 'CZ', 'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ', 'EC', 'EE', 'EG', 'EH', 'ER', 'ES', 'ET', 'FI', 'FJ', 'FK', 'FM', 'FO', 'FR', 'GA', 'GB', 'GD', 'GE', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GT', 'GU', 'GW', 'GY', 'HK', 'HN', 'HR', 'HT', 'HU', 'ID', 'IE', 'IL', 'IM', 'IN', 'IQ', 'IR', 'IS', 'IT', 'JE', 'JM', 'JO', 'JP', 'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ', 'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'ME', 'MG', 'MH', 'MK', 'ML', 'MM', 'MN', 'MP', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ', 'NA', 'NC', 'NE', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ', 'OM', 'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PS', 'PT', 'PW', 'PY', 'QA', 'RE', 'RO', 'RS', 'RU', 'RW', 'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'ST', 'SV', 'SY', 'SZ', 'TC', 'TD', 'TG', 'TH', 'TJ', 'TL', 'TM', 'TN', 'TO', 'TR', 'TT', 'TV', 'TW', 'TZ', 'UA', 'UG', 'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU', 'WF', 'WS', 'YE', 'YT', 'ZA', 'ZM', 'ZW'];
            $List = Country::select('Name', 'Code2')
                ->whereIn('Code2', $GIOCountryList)
                ->whereIn('Code2', array_unique($this->countryList))
                ->get();
            return $List;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

<?php
/**
* Salesクラス
* 成果通知周りのmodelクラス
* @package Summaries
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: Summaries.php,v 1.0 2013/5/15Exp 
*/
Class Sales extends CommonBase{
    /*
    コンストラクタ
    */
    public function __construct() {
        try {
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /*
    * 月別売上
    * 
    * @access private
    * @return array 
    */
    public function countMonthlySales(){
        try{
            $sql = "SELECT
                        substr(updated,1,7) month,
			timesale,
			sum(payment) sale
		    FROM
			data_summaries
		    WHERE
			status=2
		    AND
			user_id>0
		    AND
			deleted=0
		    GROUP BY
			month, timesale;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
//		$num = 0;
		while($row = mysql_fetch_assoc($result)){
			$data[$row['month']][$row['timesale']] = $row['sale'];
		}
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }

    /*
    * 日別売上
    * 
    * @access private
    * @return array 
    */
    public function countDailySales($month){
        try{
	    $day1 = $month."-01";
	    $day2 = date("Y-m-d", strtotime($day1." +1 month"));

            $sql = "SELECT
                        substr(updated,1,10) day,
			timesale,
			sum(payment) sale
		    FROM
			data_summaries
		    WHERE
			status=2
		    AND
			user_id>0
		    AND
			deleted=0
		    AND
			updated BETWEEN '".$day1."' AND '".$day2."'
		    GROUP BY
			day, timesale;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
//		$num = 0;
		while($row = mysql_fetch_assoc($result)){
			$data[$row['day']][$row['timesale']] = $row['sale'];
		}
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }



    /*
    * 案件別売上
    * 
    * @access private
    * @return array 
    */
    public function countPPSales($term, $timesale){
        try{
	    if(strlen($term)>=10){
		$day1 = substr($term, 0, 10);
		$day2 = date("Y-m-d", strtotime($day1." +1 day"));
	    } else {
		$day1 = substr($term, 0, 7)."-01";
		$day2 = date("Y-m-d", strtotime($day1." +1 month"));
	    }
	    
            $sql = "SELECT
			ds.advertisement_id,
			ds.advertisement_key,
			ds.campany_type,
			ds.payment,
			count(*) co,
			lb.app_point/10 point
		    FROM
			data_summaries ds,
			log_bankbooks lb
		    WHERE
			ds.summary_id=lb.summary_id
		    AND
			ds.status=2
		    AND
			ds.user_id>0
		    AND
			ds.deleted=0
		    AND
			ds.updated BETWEEN '".$day1."' AND '".$day2."'
		    AND
			ds.timesale=".$timesale."
		    GROUP BY
			ds.advertisement_id;";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
		$data = array();
		$num=0;
		while($row = mysql_fetch_assoc($result)){
			if($row['campany_type']==9){ //同一単価を同一案件として集計
				$data['sv'.$row['payment']]['name'] = 'アンケート';
				$data['sv'.$row['payment']]['payment'] = $row['payment'];
				$data['sv'.$row['payment']]['point'] = $row['point'];
				$data['sv'.$row['payment']]['co'] += $row['co'];
				$data['sv'.$row['payment']]['sale'] += $row['payment'] * $row['co'];
				$data['sv'.$row['payment']]['profit'] = $row['payment'] - $row['point'];
			} else {
				$data[$num] = $row;
				$data[$num]['name'] = $this->getPromotionName($row);
				$data[$num]['sale'] = $row['payment'] * $row['co'];
				$data[$num]['profit'] = $row['payment'] - $row['point'];
				$num++;
			}
/*
			if($row['campany_type']==1){
				$data[$num]['asp'] = "M";//affiTown
			} else if($row['campany_type']==2){
				$data[$num]['asp'] = "T";//SmaAD
			} else if($row['campany_type']==3){
				$data[$num]['asp'] = "A";//AppDriver
			}
*/
		}
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }
    private function getPromotionName($arr){
        try {
	    if($arr['campany_type']==1){//AffiTown
		$sql = "SELECT
				name
			FROM
				master_affitown_advertisements
			WHERE
				timesale = 0 AND
				affitown_advertisement_id = '".substr($arr['advertisement_id'],2)."' LIMIT 1;";
	    } else {//その他
		$sql = "SELECT
				name
			FROM
				master_advertisements
			WHERE
				campany_type = '".$arr['campany_type']."'
			AND
				advertisement_key = '".$arr['advertisement_key']."'
			LIMIT 1;";
	    }

            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }
            $row = mysql_fetch_assoc($result);
	    return $row['name'];

        } catch(Exception $e){
            return false;
        }
    }

    /*
    * 昨日の売上
    * 
    * @access private
    * @return array 
    */
    public function setDailySales(){
        try{

	    $date = date("Y-m-d",strtotime("-1 day"));
	    $sales = $this->dailySumPayment($date);

            $sql = "INSERT INTO
			daily_sales
		    SET
			accepted_date = '".$date."',
			sales = ".$sales.";";

            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("insert失敗".$sql);
            }

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    private function dailySumPayment($date){
        try{
            $sql = "SELECT
			sum(payment) sale
		    FROM
			data_summaries
		    WHERE
			status=2
		    AND
			user_id>0
		    AND
			deleted=0
		    AND
			updated BETWEEN '".$date."' AND '".date("Y-m-d", strtotime($date." +1 day"))."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
		$row = mysql_fetch_assoc($result);
                return $row['sale'];
            }
        } catch(Exception $e){
            return false;
        }
    }


    /*
    * 月別掃出し（円換算）
    *
    * @access private
    * @return array
    */

    public function countMonthlyAllPoints(){
        try{
            $sql = "SELECT
                        substr(updated,1,7) month,
                        point_type,
                        sum(app_point/10) point
                    FROM
                        log_bankbooks
                    WHERE
                        status=1
                    AND
                        deleted=0
                    GROUP BY
                        month, point_type;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
                while($row = mysql_fetch_assoc($result)){
                        $data[$row['month']][$row['point_type']] = $row['point'];
                }
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }




    public function countMonthlyPoints(){
        try{
            $sql = "SELECT
                        substr(updated,1,7) month,
			sum(app_point/10) point
		    FROM
			log_bankbooks
		    WHERE
			status=1 AND point_type>0
		    AND
			user_id>0
		    AND
			deleted=0
		    GROUP BY
			month;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
		while($row = mysql_fetch_assoc($result)){
			$data[$row['month']] = $row['point'];
		}
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }


    /*
    * 日別掃出し
    *
    * @access private
    * @return array
    */
    public function countDailyAllPoints($month){
        try{
            $day1 = $month."-01";
            $day2 = date("Y-m-d", strtotime($day1." +1 month"));

            $sql = "SELECT
                        substr(updated,1,10) day,
                        point_type,
                        sum(app_point/10) point
                    FROM
                        log_bankbooks
                    WHERE
                        status=1
                    AND
                        deleted=0
                    AND
                        updated BETWEEN '".$day1."' AND '".$day2."'
                    GROUP BY
                        day, point_type;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
                while($row = mysql_fetch_assoc($result)){
                        $data[$row['day']][$row['point_type']] = $row['point'];
                }
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }



    public function countDailyPoints($month){
        try{
	    $day1 = $month."-01";
	    $day2 = date("Y-m-d", strtotime($day1." +1 month"));

            $sql = "SELECT
                        substr(updated,1,10) day,
			sum(app_point/10) point
		    FROM
			log_bankbooks
		    WHERE
			status=1
		    AND
			updated BETWEEN '".$day1."' AND '".$day2."'
		    AND
			user_id>0
		    AND
			deleted=0
		    AND
			point_type>0
		    GROUP BY
			day;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
		while($row = mysql_fetch_assoc($result)){
			$data[$row['day']] = $row['point'];
		}
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }


    public function countPPPoints($term, $timesale){
        try{
	    if(strlen($term)>=10){
		$day1 = substr($term, 0, 10);
		$day2 = date("Y-m-d", strtotime($day1." +1 day"));
	    } else {
		$day1 = substr($term, 0, 7)."-01";
		$day2 = date("Y-m-d", strtotime($day1." +1 month"));

	    }

            $sql = "SELECT
                        substr(updated,1,10) day,
			sum(app_point/10) point
		    FROM
			log_bankbooks
		    WHERE
			status=1
		    AND
			user_id>0
		    AND
			updated BETWEEN '".$day1."' AND '".$day2."'
		    AND
			point_type>0
		    GROUP BY
			day;";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
		while($row = mysql_fetch_assoc($result)){
			$data[$row['day']] = $row['point'];
		}
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }




    /*
    * 成果テーブルのデータを取得(全データ)
    *
    * @access public
    * @return array
    */
    public function searchAllSummaries($array){
        try{
            $sql = "SELECT
                        *
                    FROM
                        data_summaries
                    WHERE
                        user_id = '".$array["user_id"]."' AND
                        advertisement_key = '".$array["advertisement_key"]."' AND
                        outcome_id = '".$array["outcome_id"]."' AND
                        campany_type = '".$array["campany_type"]."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return "not_date";
            } else{
                $row = mysql_fetch_assoc($result);
                return $row;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 案件IDで成果テーブルのCV数を取得(全データ)
    *
    * @access public
    * @return array
    */
    public function searchConversionSummaries($advertisement_id, $term_type){
        try{
            // term_type 0:累計 1:当日 2:前日
            $term = "";
            if($term_type==1){
                $term = " updated >= date(now()) AND ";
            } else if($term_type==2){
                $term = " updated BETWEEN '".date("Y-m-d", strtotime("-1 day"))."' AND '".date("Y-m-d")."' AND ";
            }

            $sql = "SELECT
                        *
                    FROM
                        data_summaries
                    WHERE
                        advertisement_id = '".$advertisement_id."' AND
			user_id>0 AND
                        ".$term."
                        status=2 AND deleted=0;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            return mysql_num_rows($result);
            
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 案件IDで成果テーブルのCV数を取得(全データ)
    *
    * @access public
    * @return array
    */
    public function searchConversionSummariesDetail($advertisement_id){
        try{
            $sql = "SELECT
                        *
                    FROM
                        data_summaries
                    WHERE
			user_id>0 AND
                        advertisement_id = '".$advertisement_id."' order by updated desc;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            while($table = mysql_fetch_assoc($result)){
                $serialize_data = unserialize($table['serialize_data']);
                $row[$num]['campaign_name'] = $serialize_data['campaign_name'];
                $row[$num] = $table;
                $num++; 
            }
            return $row;
            
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

// 5000DIG => 1000CNP  =\500
    public function countMonthlyExchange(){
        try{
            $sql = "SELECT
                        substr(created,1,7) month,
                        count(*) co
                    FROM
                        data_gift_codes
                    WHERE
                        status>0
                    AND
                        deleted=0
                    GROUP BY
                        month;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
                while($row = mysql_fetch_assoc($result)){
                        $data[$row['month']] = $row['co'];
                }
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }

    public function countDailyExchange($month){
        try{
            $day1 = $month."-01";
            $day2 = date("Y-m-d", strtotime($day1." +1 month"));

            $sql = "SELECT
                        substr(created,1,10) day,
                        count(*) co
                    FROM
                        data_gift_codes
                    WHERE
                        status>0
                    AND
                        deleted=0
                    AND
                        created BETWEEN '".$day1."' AND '".$day2."'
                    GROUP BY
                        day;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else {
                while($row = mysql_fetch_assoc($result)){
                        $data[$row['day']] = $row['co'];
                }
                return $data;
            }
        } catch(Exception $e){
            return false;
        }
    }

    /*
    * CSV出力
    *
    * @access private
    * @return array
    */

    public function exportFile($month){
        try{
	    $filename = 'pointdig_summarydata_'.$month.'.csv';
            $handle = fopen($filename, 'w');

            if(is_writable($filename)){
                if(!$handle){
                    echo "Cannot open file ($filename)";
                    exit;
//              }else{
//                  echo "use file ($filename)";
                }
            }else{
                echo "Cannot write file ($filename)";
                exit;
            }

	    $data = array();

            $day1 = $month."-01";
            $day2 = date("Y-m-d", strtotime($day1." +1 month"));
            $sql1 = "SELECT
				advertisement_id,
				payment,
				serialize_data,
				count(*) co
			FROM
				data_summaries
			WHERE
				created BETWEEN '".$day1."' AND '".$day2."'
			AND
				status in (2, 3)
			GROUP BY advertisement_id
			ORDER BY advertisement_id ASC;";

            if(!$result1 = mysql_query($sql1,$this->getDatabaseLink())){
                throw new Exception($sql1);
            }
            $result_cnt1 = mysql_num_rows($result1);
            if($result_cnt1==0){
            } else {
                while($row1 = mysql_fetch_assoc($result1)){
			$serialize_data = unserialize($row1['serialize_data']);
			$data[$row1['advertisement_id']]['co1'] = $row1['co'];
			$data[$row1['advertisement_id']]['name'] = $serialize_data['campaign_name'];
			$data[$row1['advertisement_id']]['payment'] = $row1['payment'];
		}
	    }

            $sql2 = "SELECT
				advertisement_id,
				payment,
				serialize_data,
				count(*) co
			FROM
				data_summaries
			WHERE
				updated BETWEEN '".$day1."' AND '".$day2."'
			AND
				status=2
			GROUP BY advertisement_id
			ORDER BY advertisement_id ASC;";

            if(!$result2 = mysql_query($sql2,$this->getDatabaseLink())){
                throw new Exception($sql2);
            }
            $result_cnt2 = mysql_num_rows($result2);
            if($result_cnt2==0){
            } else {
                while($row2 = mysql_fetch_assoc($result2)){
			$serialize_data = unserialize($row2['serialize_data']);
			$data[$row2['advertisement_id']]['co2'] = $row2['co'];
			$data[$row2['advertisement_id']]['name'] = $serialize_data['campaign_name'];
			$data[$row2['advertisement_id']]['payment'] = $row2['payment'];
		}
	    }

                $str  = "\"案件ID\",".
                        "\"案件名\",".
                        "\"発生件数\",".
                        "\"発生報酬\",".
                        "\"確定件数\",".
                        "\"確定報酬\",".
                        "\"確定率\",";
                $str = mb_convert_encoding($str, "SJIS", "UTF-8");
                fwrite($handle, $str."\r\n");
		foreach($data as $key => $value){
		    $str2 = "";
                    $str2 .= "\"".$key."\",";
                    $str2 .= "\"".$value['name']."\",";
                    $str2 .= "\"".$value['co1']."\",";
                    $str2 .= "\"".number_format($value['co1'] * $value['payment'])."\",";
                    $str2 .= "\"".$value['co2']."\",";
                    $str2 .= "\"".number_format($value['co2'] * $value['payment'])."\",";
                    $str2 .= "\"".number_format($value['co2'] / $value['co1'] * 100, 1)."\",";
                    $str2 = mb_convert_encoding($str2, "SJIS", "UTF-8");
                    fwrite($handle, $str2."\r\n");
		}

                echo "出力完了しました ファイル名：".$filename;
	    fclose($handle);
	    return $filename;
        } catch(Exception $e){
            echo get_class()." ".$e->getMessage();
            return false;
        }
    }

    public function exportFile2($month){
        try{
	    $filename = 'pointdig_summarydata2_'.$month.'.csv';
            $handle = fopen($filename, 'w');

            if(is_writable($filename)){
                if(!$handle){
                    echo "Cannot open file ($filename)";
                    exit;
//              }else{
//                  echo "use file ($filename)";
                }
            }else{
                echo "Cannot write file ($filename)";
                exit;
            }

	    $data = array();

            $day1 = $month."-01";
            $day2 = date("Y-m-d", strtotime($day1." +1 month"));
            $sql = "SELECT
				user_id,
				advertisement_id,
				payment,
				updated,
				serialize_data
			FROM
				data_summaries
			WHERE
				updated BETWEEN '".$day1."' AND '".$day2."'
			AND
				status=2
			ORDER BY updated ASC;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
            } else {
		$num = 0;
                while($row = mysql_fetch_assoc($result)){
			$serialize_data = unserialize($row['serialize_data']);
			$data[$num] = $row;
			$data[$num]['name'] = $serialize_data['campaign_name'];
			$num++;
		}
	    }

                $str  = "\"成果発生日時\",".
			"\"ユーザID\",".
			"\"案件ID\",".
                        "\"案件名\",".
                        "\"報酬\",";
                $str = mb_convert_encoding($str, "SJIS", "UTF-8");
                fwrite($handle, $str."\r\n");
		foreach($data as $key => $value){
		    $str2 = "";
                    $str2 .= "\"".$value['updated']."\",";
                    $str2 .= "\"".$value['user_id']."\",";
                    $str2 .= "\"".$value['advertisement_id']."\",";
                    $str2 .= "\"".$value['name']."\",";
                    $str2 .= "\"".number_format($value['payment'])."\",";
                    $str2 = mb_convert_encoding($str2, "SJIS", "UTF-8");
                    fwrite($handle, $str2."\r\n");
		}

                echo "出力完了しました ファイル名：".$filename;
	    fclose($handle);
	    return $filename;
        } catch(Exception $e){
            echo get_class()." ".$e->getMessage();
            return false;
        }
    }


}

<?php
/**
* BankBookクラス
* BankBookからのデータ取得
* @package BankBook
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: BankBook.php,v 1.0 2013/8/11Exp $
*/
class BankBook extends CommonBase {
    /**
    *  コンストラクタ
    */
    public function __construct(){
        try {
/*
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
 */
            parent::__construct();
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
    }
    /**
    * トータルポイントの表示
    *
    * @accsess public
    * @param string login_id    ログインID 
    */
    public function getTotalPoint($login_id) {
        try {
            if(!$login_key = $this -> getLoginKey($login_id)){
                throw new Exception("ユーザーkey取得失敗");
            } else{
                $data['point'] = $this -> getTotalPoints($login_id,$login_key);
		if(is_numeric($data['point'])){
			$data['point'] = number_format($data['point']);
		}
                $this -> displayJsonArray($data);
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }

     private function dateConvert($d){
        try {
            if(strlen($d)==19){// yyyy-mm-dd hh:mm:ss
                $year   = substr($d,  0, 4);
                $month  = substr($d,  5, 2);
                if($month<10){
                    $month = substr($month, 1, 1);
                }
                $day    = substr($d,  8, 2);
                if($day<10){
                    $day = substr($day, 1, 1);
                }
                $hour   = substr($d, 11, 2);
                $minute = substr($d, 14, 2);
                $second = substr($d, 17, 2);
                return $year."年".$month."月".$day."日 ".$hour.":".$minute.":".$second;
            } else {
                return "";
            }
         } catch(Exception $e) {
            return "";
         }
     }

    /**
    * 通帳に表示するためのデータの取得とJSON出力
    *
    * @accsess public
    * @param string login_id    ログインID
    */
    public function getBankBook($login_id){
        try {
            if(!$user_id = $this -> getUserID($login_id)){
                throw new Exception("ユーザーID取得失敗");
            }
            $sql = "SELECT
                            *
                    FROM
                            log_bankbooks
                    WHERE
                            deleted = 0 AND
                            user_id =".$user_id." AND ".
//                           " (app_point>0 or sub_point>0 or status=2) ".
                           " status>0 ".
                  "  ORDER BY updated DESC ;";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $num = 0;
            $item_lists = array();
            $serialize_data = array();
            //ユーザーキーの取得
            if(!$login_key = $this -> getLoginKey($login_id)){
                throw new Exception("ユーザーkey取得失敗");
            }

            //トータルポイントの取得
            //$point = $this->getTotalPoints($login_id,$login_key);
            //$item_lists['total_point'] = $point;

            //CreateLog::putDebugLog("point ".$point);

           //配列に入れる
            while($row = mysql_fetch_assoc($result)){
                $item_lists[$num]['date'] = $this->dateConvert($row['updated']);
                $item_lists[$num]['app_point'] = $row['app_point'];
		if($row['sub_point']>0){
                	$item_lists[$num]['app_point'] = "-".$row['sub_point'];
		}
                switch($row['status']){
                    case 0:
                        $item_lists[$num]['status'] = "非承認";//表示されない
                        break;
                    case 1:
                        $item_lists[$num]['status'] = "DIG付与済み";
                        break;
                    case 2:
                        $item_lists[$num]['status'] = "DIG申請中";
                        break;
                }
                $serialize_data = unserialize($row['serialize_data']);
                $item_lists[$num]['campaign_name'] = $serialize_data['campaign_name'];//通帳に表示される項目名
                $num++;
            }

            //JSONで出力
            //$this->displayJsonArray($item_lists);
            //exit();
            return $item_lists;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
            exit();
        }
    }
}

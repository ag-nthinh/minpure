<?php
/**
* Advertisementsクラス
* 案件のインサート、アップデート、検出
*
* @package Advertisements
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: Advertisements.php,v 1.0 2013/8/11Exp $
*/
Class Advertisements extends CommonBase { 
    /*
    コンストラクタ
        */
    public function __construct(){
        try {
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
    }
    /*
    *  広告IDと一致する広告データを取得
    *
    *  @accesss public
    *  @return boolean
    */
    public function searchAdvertisementId($advertisement_id){
        try {
            $sql = "
                        SELECT
                                serialize_data,
                                price,
                                point,
                                advertisement_id,
                                advertisement_key,
                                advertisement_type,
                                name,
                                img_url,
                                campany_type,
                                landing_url,
                                package_name,
                                market,
                                UNIX_TIMESTAMP('".date("Y-m-d H:i:s")."')-UNIX_TIMESTAMP(newly_arrived) as  time_subtraction
                        FROM
                                master_advertisements
                        WHERE
                                advertisement_id ='".$advertisement_id."' AND
                                deleted = 0 LIMIT 1;";    
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return $result;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
     /*
    *  広告キーとASPが一致する広告データを取得
    *
    *  @accesss public
    *  @return boolean
    */
    public function getAdvertisementData($advertisement_key,$campany_type){
        try{
            $now_time=date("Y-m-d H:i:s");
            $sql = 'SELECT
                            *
                    FROM
                            master_advertisements
                    WHERE
                            advertisement_key = "'.$advertisement_key.'" AND
                            campany_type = '.$campany_type.' order by advertisement_id desc LIMIT 1';
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $row = mysql_fetch_assoc($result);
            return $row;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /* アプリ案件で案件名とASPの種類が一致したらUPDATEする ※使っていない
    *
    * @accsess public
    * @return 案件データ/または false
    */
    public function checkAdvertisementName($name,$campany_type){
        try {
            $sql = "
                SELECT
                    *
                FROM
                    master_advertisements
                WHERE
                    campany_type = ".$campany_type." AND
                    name = '".$name."' LIMIT 1;";
            if(!$result = mysql_query($sql)){
                throw new Exception("けんさくしっぱい/".$sql."/".mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }else if($result_count == 1){
                $row = mysql_fetch_assoc($result);
                return $row;
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /* master_advertisementsテーブルの案件チェック
    *
    * @accsess public
    * @return　案件データ　/または　false
    */
    public function selectAdvertisements($advertisement_key,$outcome_id,$campany_type){
        try{
            $sql = "
                SELECT
                    *
                FROM
                    master_advertisements
                WHERE
                    campany_type = ".$campany_type." AND
                    advertisement_key = '".$advertisement_key."' AND
                    outcome_id = '".$outcome_id."' LIMIT 1;";
            if(!$result = mysql_query($sql)){
                throw new Exception("けんさくしっぱい/".$sql."/".mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }else if($result_count == 1){
                $row = mysql_fetch_assoc($result);
                return $row;
            }

        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 案件のインサート
    * @accsess public
    * @return boolean
    */
    public function insertAdvertisements($arr){
        try {
            global $cnt;
            global $mail_flg;

            $sql = "INSERT INTO
                master_advertisements
            SET
                advertisement_key = '".$arr['advertisement_key']."',
                advertisement_type = ".$arr['advertisement_type'].",
                market = '".$arr['market']."',
                duplicate_flg = 0,
                campany_type = ".$arr['campany_type'].",
                name = '".mysql_real_escape_string($arr['name'])."',
                margin = '".$arr['margin']."',
                point = '".$arr['point']."',
                price = '".$arr['price']."',
                outcome_id = '".$arr['outcome_id']."',
                img_url = '".$arr['img_url']."',
                landing_url = '".$arr['landing_url']."',
                start_time = '".$arr['start_time']."',
                end_time = '".$arr['end_time']."',
                newly_arrived = '".$arr['newly_arrived']."',
                max_count = 0,
                priority = ".$arr['priority'].",
                package_name = '".$arr['package_name']."',
                status = ".$arr['status'].",
                created = '".date("Y-m-d H:i:s")."',
                updated = '".date("Y-m-d H:i:s")."',
                serialize_data = '".$arr['serialize_data']."';";

//            	if($arr['campany_type'] == 2){CreateLog::putdebugLog($sql);}

                if(!mysql_query($sql,$this->getDatabaseLink())){
                    $cnt--;
                    throw new Exception("insert失敗".$sql);
                }
                if($arr['status'] == 1 && $arr['market']==1 && $arr['requisite']==15){
                    $cnt = $cnt + 1;
                }
                $mail_flg = true;

                return true;
        } catch(Exception $e){
            //CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * master_advertisementsテーブルにアップデート
    *
    * @accsess public
    */
    public function updateAdvertisements($arr){
        try{
            if($arr['newly_arrived_flg']==1){
                $newly_arrived="newly_arrived='".date("Y-m-d H:i:s")."',";
            } else{
                $newly_arrived="";
            }
            if($arr['img_url']==""){
                $img_url="";
            } else{
                $img_url="img_url ='".$arr['img_url']."',";
            }
            $sql = "UPDATE
                master_advertisements
            SET
                advertisement_key = '".$arr['advertisement_key']."',
                outcome_id = '".$arr['outcome_id']."',
                name = '".mysql_real_escape_string($arr['name'])."',
                margin = '".$arr['margin']."',
                point = '".$arr['point']."',
                price = '".$arr['price']."',
                ".$img_url."
                landing_url = '".$arr['landing_url']."',
                status = '".$arr['status']."',
                start_time = '".$arr['start_time']."',
                end_time = '".$arr['end_time']."',
                updated = '".date("Y-m-d H:i:s")."',
                ".$newly_arrived."
                deleted = 0
            WHERE
                advertisement_id = ".$arr['advertisement_id'].";";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                CreateLog::putErrorLog(get_class()." "."update失敗".$arr['name'].$sql);
            }

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
}

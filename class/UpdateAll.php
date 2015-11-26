<?php
/**
  * UpdateAllクラス
  * 管理画面のアップデートに関する機能をまとめたクラス
  * @package UpdateAll
  * @author 氏名 平野　雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/1月/14日Exp $
 */
Class UpdateAll extends CommonBase{
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
            die("false");
        }
    }
    /*
    *  ギフトコードを配布する関数
    *
    *  @accesss public
    *  @return 
    */
    public function updateDistributions(){
        try{
            $now_time=date("Y-m-d H:i:s");
            $user_id = mysql_real_escape_string($_POST['user_id']);
            $download_url = mysql_real_escape_string($_POST['download_url']);
            $distribution_id = mysql_real_escape_string($_POST['distribution_id']);
            $deleted = mysql_real_escape_string($_POST['deleted']);
            $status = mysql_real_escape_string($_POST['status']);
            if($deleted == ""){
                $deleted = 0;
            }
            $sql = "UPDATE 
                            data_distributions 
                    SET 
                            download_url = '".$download_url."',
                            status = ".$status.",
                            updated = '".$now_time."' ,
                            deleted = ".$deleted."
                    WHERE 
                            user_id = ".$user_id."
                    AND
                            distribution_id = ".$distribution_id.";";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
        } catch(Exception $e){
             CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }

    /*
    *  案件再掲載
    *
    *  @accesss public
    *  @return 
    */
    public function ableAdvertisements(){
        try{
            $now_time = date("Y-m-d H:i:s");
            $sql = "
                    UPDATE
                            master_advertisements
                    SET 
                            status = 1
                    WHERE
                            auto_on = 1;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            return true; 
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }


    /*
    *  案件取り下げ
    *
    *  @accesss public
    *  @return 
    */
    public function disableAdvertisement($advertisement_id, $type){
        try{
            $set = "";
            if($type==0){//日上限に達したので掲載を下げるが翌日に上がる
                $set = "";
            } else if($type==1){//全体上限に達したので再掲載しないようにする
                $set = " auto_on = 0, ";
            }

            $sql = "
                    UPDATE
                            master_advertisements
                    SET
                            ".$set."
                            status = 0
                    WHERE
                            advertisement_id = '".$advertisement_id."';";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            return true; 
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    /*
    *  広告テーブルのアップデート
    *
    *  @accesss public
    *  @return 
    */
    public function updateAdvertisements(){
        try{
            $now_time = date("Y-m-d H:i:s");
            $data=array();
            $name = mysql_real_escape_string($_POST['name']);
            $advertisement_id = mysql_real_escape_string($_POST['advertisement_id']);
            $advertisement_key = mysql_real_escape_string($_POST['advertisement_key']);
            $advertisement_type = mysql_real_escape_string($_POST['advertisement_type']);
            $campany_type = mysql_real_escape_string($_POST['campany_type']);
            $auto_on = mysql_real_escape_string($_POST['auto_on']);
            $status = mysql_real_escape_string($_POST['status']);
            $market = mysql_real_escape_string($_POST['market']);
            $max_count = mysql_real_escape_string($_POST['max_count']);
            $daily_max_count = mysql_real_escape_string($_POST['daily_max_count']);
            $deleted = mysql_real_escape_string($_POST['deleted']);
            $newly_arrived = mysql_real_escape_string($_POST['newly_arrived']);
            $priority = mysql_real_escape_string($_POST['priority']);
            $newly_arrived_flg = $_POST['newly_arrived_flg']; 
            $package_name = mysql_real_escape_string($_POST['package_name']);
            $price =mysql_real_escape_string($_POST['price']);
            $point =mysql_real_escape_string($_POST['point']);
            $margin =mysql_real_escape_string($_POST['margin']);
            $img_url = mysql_real_escape_string($_POST['img_url']);
            $landing_url = mysql_real_escape_string($_POST['landing_url']);
            $outcome_id = mysql_real_escape_string($_POST['outcome_id']);
            $note = mysql_real_escape_string($_POST['note']);
            $start_time = mysql_real_escape_string($_POST['start_time']);
            $end_time = mysql_real_escape_string($_POST['end_time']);
            $updated = mysql_real_escape_string($_POST['updated']);
            if($newly_arrived_flg ==1){
                $newly_arrived ="newly_arrived = '".$now_time."', ";
            } else{
                $newly_arrived ='newly_arrived = "0000-00-00 00:00:00", ';
            }
            $data['adjustment_data'] = $_POST['adjustment_data'];
            $data['detail'] = $_POST['detail'];
            $data['remark'] = $_POST['remark'];
            $data['name'] = $_POST['outcome_name'];
            $serialize_data = mysql_real_escape_string(serialize($data));
            $sql = "
                    UPDATE
                            master_advertisements
                    SET 
                            ".$newly_arrived." 
                            name = '".$name."',
                            advertisement_key = '".$advertisement_key."',
                            outcome_id = '".$outcome_id."',
                            advertisement_type = '".$advertisement_type."',
                            campany_type = '".$campany_type."',
                            auto_on = '".$auto_on."',
                            status = '".$status."',
                            price = '".$price."',
                            point = '".$point."',
                            margin = '".$margin."',
                            max_count ='".$max_count."',
                            daily_max_count ='".$daily_max_count."',
                            market ='".$market."',
                            priority = '".$priority."',
                            img_url ='".$img_url."',
                            landing_url ='".$landing_url."',
                            note ='".$note."',
                            serialize_data = '".$serialize_data."',
                            package_name = '".$package_name."',
                            start_time = '".$start_time."',
                            end_time = '".$end_time."',
                            updated = '".$updated."',
                            deleted = '".$deleted."'
                    WHERE
                            advertisement_id = '".$advertisement_id."'
                    
            ;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
             
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
}

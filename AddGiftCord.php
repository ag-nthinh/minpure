<?php
/**
* AddGiftCordクラス
* ギフトコードを自動配布
* @package AddGiftCordd
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: AddGiftCord.php,v 1.0 2013/2/6Exp $
*/
Class AddGiftCord extends CommonBase {
    /*
    * コンストラクタ
    */
    public function __construct(){
       try {
            //データベース接続
            if(!$this->connectDataBase()) {
               throw new Exception("DB接続失敗で異常終了しました。");            
            }
       } catch(Exception $e) {
           CreateLog::putErrorLog(get_class()." ".$e->getMessage());
           mysql_query("rollback;",$this->getDatabaseLink());
       }
    }
    /**
    * csvファイルのインサート
    *
    * @accsess public
    * @return boolean
    */
    public  function insertCsvFile(){
        try{
            $filename = $_FILES['sample'];
            $handle = fopen($filename['tmp_name'], "r" );
            while ($array = fgetcsv($handle)) {
        //        echo "\n".$array[0];
                if(!$this->insertgiftcord($array[0])){
                    throw new Exception("ギフトコードのインサート失敗");
                }
            }
            fclose( $handle );
            return true;            
        } catch(Exception $e){
            return false;
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /**
    * csvファイルのsql文
    *
    * @accsess public
    * @return boolean
    */
    public function insertgiftcord($download_url){
        try{
            $sql ="INSERT INTO
                            data_giftcord
                    SET
                            present_id =1,
                            status=0,
                            created=now(),
                            download_url='".$download_url."';";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e){
            return false;
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    
    /**
    * 配布テーブルからユーザーIDを取得してプッシュ通知を送る
    *
    * @accsess public
    * @return boolean
    */
    public function getUserIdDistributions($array){
        try{
          //user_id取得
            $sql ="SELECT
                            user_id
                   FROM
                            data_distributions
                   WHERE
                            download_url = '".$array['download_url']."' AND
                            deleted = 0;
                  ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            //プッシュ通知を送る
            $obj = new SendPush();
            $json_array["present_id"] = $array['present_id'];
            $json_array["activity"] = "codelist";
            $json_array["text"] =GIFTCORD_PUSH;
            $json_data  = json_encode($json_array, JSON_UNESCAPED_UNICODE);
            if(!$obj -> sendPush($row['user_id'],$json_data)){
                throw new Exception("push通知失敗");
            }  
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 配布テーブル（一件分）にギフトコードを配布＆ステータスを配布ずみにする
    *
    * @accsess public
    * @param array $array
    * @return boolean
    */
    public function onStatusDistributions($array){
        try{
            //現在の日付から14日後の日付取得
            $time_covered = date("Y-m-d H:i:s",strtotime("+2 week"));
            $now_time = date("Y-m-d H:i:s");
            $sql ="UPDATE
                            data_distributions
                   SET
                            download_url = '".$array['download_url']."',
                            status = 1,
                            time_covered = '".$time_covered."',
                            updated = '".$now_time."'
                   WHERE
                            present_id = '".$array['present_id']."' AND 
                            status = 0  AND
                            deleted = 0 AND
                            download_url IS NULL 
                            order by distribution_id asc limit 1 
                   ";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            //アップデート数をカウント
            if(mysql_affected_rows($this->getDatabaseLink()) ==0 ){
                return true;//アップデート数0の場合、ギフトコードを配布するユーザーがいないためtrueで戻る
            }
            //ギフトコードを配布したため配布したギフトコードのステータスを使用済みにする
            if( !$this -> onStatusGiftCord($array)){
                throw new Exception("onStatusGiftCord失敗");
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ギフトコードテーブルのステータスを配布ずみにする
    *
    * @accsess public
    * @return boolean
    */
    public function onStatusGiftCord($array){
        try{
            $now_time = date("Y-m-d H:i:s");
            $sql ="UPDATE 
                            data_giftcord
                   SET
                            status = 1,
                            updated = '".$now_time."'
                   WHERE
                            giftcord_id = '".$array['giftcord_id']."' AND
                            deleted = 0 AND
                            present_id = '".$array['present_id']."';
                  ";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ギフトコードテーブルから未配布ギフトコードを全て取得
    *
    * @accsess public
    * @return boolean
    */
    public function getGiftCord(){
        try{
            //未配布ギフトコードを全て取得
            $sql ="SELECT
                            giftcord_id,
                            present_id,
                            download_url
                   FROM
                            data_giftcord
                   WHERE
                            status = 0  AND
                            deleted = 0 AND
                            download_url is not null;
                  ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $num = 0;
            while($row = mysql_fetch_assoc($result)){
                $giftcord_array[$num]['giftcord_id'] = $row['giftcord_id'];//ギフトコードID
                $giftcord_array[$num]['present_id'] = $row['present_id'];//プレゼントID
                $giftcord_array[$num]['download_url'] = $row['download_url'];//ギフトコード
                //トランザクション開始
                if(!mysql_query("begin;",$this->getDatabaseLink())){
                    throw new Exception("トランザクション失敗");
                }
                //ギフトコードを配布
                if(!$this->onStatusDistributions($giftcord_array[$num])){
                    throw new Exception("ギフトコード配布失敗");
                }
                //トランザクション完了
                if(!mysql_query("commit;",$this->getDatabaseLink())){
                    throw new Exception("トランザクション失敗");
                }
                if(!$this -> getUserIdDistributions($giftcord_array[$num])){
                    throw new Exception("push通知失敗");
                } 
                $num++;
            }
            
            return true;
        } catch(Exception $e){  
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
            return false;
        }
    }
    
}    

<?php
/**
  * NTTCSクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 猿渡 俊輔
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class CNGiftCode extends CommonBase{
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

    /**
     * 案件を取得
     *
     * @accsess public
     */
     public function getCode($login_id){
        try{
            //ユーザーKEYの取得
            if(!$login_key = $this->getLoginKey($login_id)){
                throw new Exception("ユーザーKEYの取得に失敗。");
            }
 
            $obj = new BankBook();
            if(!$total_point = $obj -> getTotalPoints($login_id, $login_key)){
               throw new Exception("ポイントの取得に失敗しました。");
            }
	    if($total_point<5000){
               throw new Exception("ポイント不足");
	    }
            if(!$obj -> searchCode2()){
               throw new Exception("5秒以内にアクセスがありました。");
            }
	
 
            $url = "https://www.clubnets.co.jp/giftcd/dig?schflg=1&prcymd=".date("YmdHis");
/*
            // ID表示リクエストの接続情報を構成
            $options = array('http' => array(
                'method' => 'POST',
                'content' => http_build_query($send_data)
            ));
*/
            // ID表示リクエストを送信しレスポンスを格納
//            if(!$contents = file_get_contents($url, false, stream_context_create($options))){
            if(!$contents = file_get_contents($url, false)){
                throw new Exception("コードがありません");
            }
            CreateLog::putDebugLog("GET: ".$contents);

	    $arr = array();
	    $arr = json_decode($contents);

	    $gift_data = array();
	    $gift_data['point_type'] = 1;
	    $gift_data['summary_id'] = 0;
	    $gift_data['point'] = 0;
	    $gift_data['sub_point'] = 5000;
	    $gift_data['bank_status'] = 1;

            //減算処理
            if(!$this->subUserPoints($login_id,$login_key,$gift_data)){
               throw new Exception("ポイントの減算に失敗しました。");
            }
 
            $save_data = array();
            $save_data['login_id'] = $login_id;
            $save_data['code'] = mysql_real_escape_string($arr->sch);
            $save_data['exchange_end'] = mysql_real_escape_string($arr->ykg);
            if(!$this->insertCode($save_data)){
                throw new Exception("DBにインサート出来ません。");
            }
            return $save_data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
   /*
    * ギフトテーブルにインサート
    *
    * @access private
    * @return array
    */
    private function insertCode($array){
        try{
            $now_time=date("Y-m-d H:i:s");
            $sql = "INSERT INTO data_gift_codes SET
                        code = '".$array['code']."',
                        login_id = '".$array['login_id']."',
                        exchange_end = '".$array['exchange_end']."',
                        status = 1,
                        created = '".$now_time."',
                        updated = '".$now_time."';";
	    
            CreateLog::putDebugLog($sql);
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("DBにインサート出来ませんでた。".$sql);
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    /*
    * 重複申請拒否
    * 
    * @access private
    * @return array 
    */
    public function searchCode2(){
        try{
            $sql = "select * from data_gift_codes where created>=(now() - interval 5 second) AND created < now() LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return true;
            } else{
                return false;
            }
        } catch(Exception $e){
            return false;
        }
    }


    /*
    * ギフトテーブルのデータを取得
    * 
    * @access private
    * @return array 
    */
    public function searchCode($code){
        try{
            $sql = "SELECT
                        *
                    FROM
                        data_gift_codes
                    WHERE
                        code = '".$code."' LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else{
                $row = mysql_fetch_assoc($result);
                return $row;
            }
        } catch(Exception $e){
            return false;
        }
    }

     /*
    * ギフトテーブルをアップデート
    *
    * @access private
    * @return boolean
    */
    public function updateCode($code, $status){
        try{
            $now_time = date("Y-m-d H:i:s");
            $sql = "UPDATE  
                            data_gift_codes
                    SET 
                            status=".$status.",
			    updated='".$now_time."'
                    WHERE
                            code='".mysql_real_escape_string($code)."';";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("DBにUPDATE出来ませんでした。".$sql);
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    /**
     * 案件を取得
     *
     * @accsess public
     */
     public function useGift(){
        try{
	    if(!isset($_GET['scs'])){
                throw new Exception("コードがありません");
	    }
	    $code = $_GET['scs'];
            CreateLog::putDebugLog("code : ".$code);

	    if(!$code_data = $this->searchCode($code)){
                throw new Exception("存在しないコードです ".$code);
            }

	    print_r($code_data);
	    //ステータス 0:申請済み 1:取得済み 2:利用済み
     	    if($code_data['status'] == 0){
		echo 0;
	    } else if($code_data['status'] == 1){
		if(!$this->updateCode($code, 2)){
			throw new Exception("コードがありません");
            	}
		echo 1;
	    } else if($code_data['status'] == 2){
		echo 2;
	    }
            return true;
        } catch(Exception $e){
	    echo 0;
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
 
    /*
    * ギフトテーブルのデータを取得
    * 
    * @access private
    * @return array 
    */
    public function searchGiftCodes($login_id){
        try{
/*
	    if(!isset($_GET['scs'])){
                throw new Exception("コードがありません");
	    }
*/
	
            $sql = "SELECT
                        *
                    FROM
                        data_gift_codes
                    WHERE
                        login_id = '".$login_id."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $code_array = array();
            while($row = mysql_fetch_assoc($result)){
                $code_array[] = $row;
            }
            return $code_array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
}


<?php
/**
  * NTTCSクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 猿渡 俊輔
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class NTTCS extends CommonBase{
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
     public function sendGift($login_id, $gift_id){
        try{
            $url = htmlspecialchars("https://ejoica.jp/idweb/ap/iddisplay.do");//本番用

            $save_data = array();
            $save_data['request_key'] = CreateRand::getRandomString(40);
            $save_data['login_id'] = $login_id;
            $save_data['gift_id'] = htmlspecialchars($gift_id);

            if(!$gift_data = $this->searchGift($gift_id)){
                throw new Exception("ギフトデータなし");
            }
            CreateLog::putDebugLog("gift amount".$gift_data['amount']." cost".$gift_data['cost']);
            $save_data['amount'] = $gift_data['amount'];
            $save_data['cost'] = $gift_data['cost'];
            if(!$this->insertRequests($save_data)){
                throw new Exception("DBにインサート出来ません。");
            }

            $send_data = array(); 
            $send_data['OEM'] = $gift_data['access_code'];
            $send_data['CCD'] = "NTTCARDSOL";//企業コード（動作確認用）
            $send_data['SNO'] = $save_data['request_key'];//表示依頼通番(ユニーク送信ID)
            $send_data['VAL'] = $gift_data['amount'];//券面額
            $send_data['TMS'] = date("YmdHis");//タイムスタンプ

            $sign_key = "DtqjFjPzxJmisgvvqjzQ0wBgk1tf0DjFRMKEgkg8";//署名生成秘密鍵（動作確認用）
            $sign = $send_data['CCD'].$send_data['SNO'].$send_data['OEM'].$send_data['VAL'].$send_data['TMS'];//署名対象文字列
            $send_data['SIG'] = base64_encode(hash_hmac('sha256', $sign, $sign_key, true));

            CreateLog::putDebugLog("sign ".$sign);
            CreateLog::putDebugLog("sig ".$send_data['SIG']);

            // ID表示リクエストの接続情報を構成
            $options = array('http' => array(
                'method' => 'POST',
                'content' => http_build_query($send_data)
            ));
            // ID表示リクエストを送信しレスポンスを格納
            $contents = file_get_contents($url, false, stream_context_create($options));
            CreateLog::putDebugLog($contents);
            if ($contents == false){
                // 異常処理
            }else{
                $pieces = explode(",", $contents);
                $save_data['serialize_data'] = serialize($pieces);
                $ret = $pieces[0]; // リターンコード

                if($ret=='0000') {//正常終了（新規）
                    $save_data['code'] = $pieces[1]; // ギフトID
                    $kno = $pieces[2]; // 管理番号
                    $ken = $pieces[3]; // 券面額 = amount
                    $save_data['exchange_start'] = substr($pieces[4], 0, 4)."-".substr($pieces[4], 4, 2)."-".substr($pieces[4], 6, 2)." 00:00:00";
                    $save_data['exchange_end'] = substr($pieces[5], 0, 4)."-".substr($pieces[5], 4, 2)."-".substr($pieces[5], 6, 2)." 00:00:00";
                    $uip = $pieces[6]; // URL付ギフトID
                    $uik = $pieces[7]; // URL付ギフトID(携帯)
                    $zan = $pieces[8]; // 利用残高
                    if(!$this->updateRequests1($save_data)){
                        throw new Exception("DBアップデート失敗");
                    }
                    //ポイント減らす
                } else if($ret=='0001') {//正常終了（再表示処理）
                } else if($ret=='0200') {//引数エラー
                } else if($ret=='0300') {//再表示エラー
                } else if($ret=='0400') {//在庫エラー
                } else if($ret=='9999') {//システムエラー
                }
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
    public function insertRequests($array){
        try{
            $now_time=date("Y-m-d H:i:s");
            $sql = "INSERT INTO data_gift_requests SET
                        request_key = '".$array['request_key']."',
                        login_id = '".$array['login_id']."',
                        gift_id = ".$array['gift_id'].",
                        amount = ".$array['amount'].",
                        cost = ".$array['cost'].",
                        status = 0,
                        created = '".$now_time."',
                        updated = '".$now_time."';";
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
    * ギフトテーブルのデータを取得
    * 
    * @access private
    * @return array 
    */
    public function searchRequests($array){
        try{
            $sql = "SELECT
                        *
                    FROM
                        data_gift_requests
                    WHERE
                        request_id = '".$array["request_id"]."' LIMIT 1;";
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
    public function updateRequests1($array){
        try{
            $now_time=date("Y-m-d H:i:s");
            $sql = "UPDATE  
                            data_gift_requests
                    SET 
                            code='".mysql_real_escape_string($array['code'])."',
                            exchange_start='".mysql_real_escape_string($array['exchange_start'])."',
                            exchange_end='".mysql_real_escape_string($array['exchange_end'])."',
                            serialize_data='".mysql_real_escape_string($array['serialize_data'])."',
                            status=1,
                            updated='".$now_time."'
                    WHERE       
                            request_key='".$array['request_key']."';";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("DBにUPDATE出来ませんでした。".$sql);
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
     /*
    * ギフトテーブルをアップデート
    *
    * @access private
    * @return boolean
    */
    public function updateRequests2($request_id){
        try{
            $now_time=date("Y-m-d H:i:s");
            $sql = "UPDATE  
                            data_gift_requests
                    SET 
                            status=2,
                            updated='".$now_time."'
                    WHERE       
                            request_key='".$array['request_key']."';";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("DBにUPDATE出来ませんでした。".$sql);
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }


   /*
    * ギフトテーブルのデータを取得
    * 
    * @access private
    * @return array 
    */
    public function searchGift($gift_id){
        try{
            $sql = "SELECT
                        *
                    FROM
                        master_gifts
                    WHERE
                        gift_id = ".$gift_id." LIMIT 1;";
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
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

   /*
    * ギフトテーブルのデータを取得
    * 
    * @access private
    * @return array 
    */
    public function searchGiftAmounts($access_code){
        try{
            $sql = "SELECT
                        *
                    FROM
                        master_gifts
                    WHERE
                        access_code = '".$access_code."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $array = array();
            while($row = mysql_fetch_assoc($result)){
                $array[] = $row;
            }
            return $array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
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
            $sql = "SELECT
                        *
                    FROM
                        data_gift_requests
                    WHERE
                        login_id = '".$login_id."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $code_array = array();
            $num = 0;
            while($row = mysql_fetch_assoc($result)){
                $code_array[$num] = $row;
                $serialize_data = unserialize($row['serialize_data']);
                $code_array[$num]['manage'] = $serialize_data['2'];
                $code_array[$num]['url'] = $serialize_data['7'];
                $num++;
            }
            return $code_array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
}


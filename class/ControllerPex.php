<?php
/**
* ControllerPexクラス
* PexAPI周りをまとめたクラス
* @package Pex
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: History,v 1.0 2013/7/29Exp $
*/
Class ControllerPex extends ModelPex{
    var $response_header=null;
    /**
    * コンストラクタ
    */
    public function __construct() {
        try{
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /*
    * Pexギフトコードの取得
    *
    * @access public
    * @return string pex_cord
    * @return boolean false
    */
    public function Action($array){
        try{
            CreateLog::putDebugLog("発券APIにアクセス");
            //echo "発券APIにアクセス";
            //発券APIにアクセス
            if(!$result=$this->requestAPI($array)){
                throw new Exception("発券APIにアクセス失敗");
            }
            CreateLog::putDebugLog("通信エラーか確認[1]");
            //echo "\n通信エラーか確認[1]";
            CreateLog::putDebugLog("\nresponse_header>>".$this->response_header);
           // echo "\nresponse_header>>".$this->response_header;
            //通信エラーか確認
            if(preg_match("/^200/",$this->response_header)){//通信エラー
                //照会APIにアクセス
                CreateLog::putDebugLog("通信エラー");
                CreateLog::putDebugLog("照会APIにアクセス");
             //   echo "\n通信エラー";
               // echo "\n照会APIにアクセス";
                if(!$check_result=$this->inquiryAPI($array)){
                    throw new Exception("照会APIにアクセス失敗");
                }   
                CreateLog::putDebugLog("2度目の通信エラーか確認");
             //   echo "\n2度目の通信エラーか確認";
                if(preg_match("/^200/",$this->response_header)){//2度目の通信エラー
                    throw new Exception("2度目の通信エラー");
                /*******************************************************/
                /*                   アラートを出す                    */
                /*******************************************************/
                CreateLog::putDebugLog("2度目の通信エラー");
               // echo "\n2度目の通信エラー";
                } else{
                    if($check_result!="NG"){//照会APIでNGだった場合
                 //       echo "\n照会APIでNGだった場合";
                         CreateLog::putDebugLog("照会APIでNGだった場合");
                        if($check_result->detail_code != "01"){//メンテナンス中以外
                   //         echo "\nメンテナンス中以外のエラー";
                            $data_array['response_code']=0;
                            $data_array['partner_code']=$array['partner_code'];
                            $data_array['detail_code']=$check_result->detail_code;
                            $data_array['message']=$check_result->message;
                            $data_array['trade_id']=$check_result->trade_id;
                            $data_array['user_id']=$array['user_id'];
                            $data_array['serialize_data']=serialize($check_result);
                            $data_array['gift_code']="";
                            $data_array['created']=date("Y-m-d H:i:s");
                            $data_array['updated']=date("Y-m-d H:i:s");
                              
                        } else if($check_result->detail_code == "01"){//メンテナンス中
           //                 echo "\nメンテナンス中";
                            CreateLog::putDebugLog("メンテナンス中");
                            $data_array['response_code']=0;
                            $data_array['partner_code']=$array['partner_code'];
                            $data_array['detail_code']=$check_result->detail_code;
                            $data_array['message']=$check_result->message;
                            $data_array['trade_id']=$array['trade_id'];
                            $data_array['user_id']=$array['user_id'];
                            $data_array['serialize_data']=serialize($check_result);
                            $data_array['gift_code']="";
                            $data_array['created']=date("Y-m-d H:i:s");
                            $data_array['updated']=date("Y-m-d H:i:s");
                        }  
                        CreateLog::putDebugLog("レスポンステーブルにインサート");
             //           echo "\nレスポンステーブルにインサート";
                        //レスポンステーブルにインサート
                        if(!$this->insertLogResponsePublishes($data_array)){
                            throw new Exception("レスポンステーブルにインサート失敗");
                        }
               //         echo "\ndetail_code>>".$check_result->detail_code."\n";
                        
                    } else if($check_result->response_code == 'YES'){//照会APIでコード取得できた
                        CreateLog::putDebugLog("照会APIでコード取得できた");
                 //       echo "\n照会APIでコード取得できた";
                        //レスポンステーブルにインサートするデータを格納
                        $data_array['response_code']=1;
                        $data_array['partner_code']=$array['partner_code'];
                        $data_array['detail_code']=$check_result->detail_code;
                        $data_array['message']=$check_result->message;
                        $data_array['trade_id']=$check_result->trade_id;
                        $data_array['user_id']=$array['user_id'];
                        $data_array['gift_code']=$check_result->gift_data->code;
                        $data_array['serialize_data']=serialize($check_result);
                        $data_array['created']=date("Y-m-d H:i:s");
                        $data_array['updated']=date("Y-m-d H:i:s");
             //           echo "\nレスポンステーブルにインサート";
                        CreateLog::putDebugLog("レスポンステーブルにインサート");
                        CreateLog::putCodeLog($array['user_id']."/".$check_result->gift_data->code);
                        //レスポンステーブルにインサート
                        if(!$this->insertLogResponsePublishes($data_array)){
                            throw new Exception("レスポンステーブルにインサート失敗");
                        }
                    }
                }
            } else if($result->response_code == 'OK'){//発券APIから正常にコードを取得できた
                CreateLog::putDebugLog("発券APIから正常にコードを取得できた");
           //     echo "\n発券APIから正常にコードを取得できた";
                //レスポンステーブルにインサートするデータを格納
                $data_array['response_code']=1;
                $data_array['partner_code']=$array['partner_code'];
                $data_array['detail_code']=$result->detail_code;
                $data_array['message']=$result->message;
                $data_array['trade_id']=$result->trade_id;
                $data_array['user_id']=$array['user_id'];
                $data_array['gift_code']=$result->gift_data->code;
                //$data_array['limit']=$result->gift_data->expire_date;//有効期限
                //$data_array['manage_code']=$result->gift_data->manage_code;//管理番号
                $data_array['serialize_data']=serialize($result);
                $data_array['created']=date("Y-m-d H:i:s");
                $data_array['updated']=date("Y-m-d H:i:s");
              //  echo "\nレスポンステーブルにインサート";
                CreateLog::putDebugLog("レスポンステーブルにインサート");
                CreateLog::putCodeLog($array['user_id']."/".$result->gift_data->code);
                //レスポンステーブルにインサート
                if(!$this->insertLogResponsePublishes($data_array)){
                    throw new Exception("レスポンステーブルにインサート失敗");
                }
             //   echo "\ndetail_code>>".$result->detail_code."\n";
                //ギフトコードを返却
            } else if($result->response_code == 'NG' ){//NGだった
                CreateLog::putDebugLog("NGだった");
               // echo "\nNGでした";
               // echo "\n照会APIにアクセス";
                CreateLog::putDebugLog("照会APIにアクセス");
                //照会APIにアクセス
                if(!$check_result=$this->inquiryAPI($array)){
                    throw new Exception("照会APIにアクセス失敗");
                }
               // echo "\nresponse_header>>".$this->response_header;
                if(preg_match("/^200/",$this->response_header)){//ネットワークエラー等
                    /*******************************************************/
                    /*                   アラートを出す                    */
                    /*******************************************************/
                 //   echo "\n通信エラーのためアラートをだす";
                    CreateLog::putDebugLog("通信エラーのためアラートをだす");
                    throw new Exception("通信エラーのためアラートをだす");
                } else if($check_result->response_code == "NG"){//ネットワークエラーではない
                   // echo"\n照会APIからコード取得失敗";
                    CreateLog::putDebugLog("NGだった");
                    $data_array['response_code']=0;
                    $data_array['partner_code']=$array['partner_code'];
                    $data_array['detail_code']=$result->detail_code;
                    $data_array['message']=$result->message;
                    $data_array['trade_id']=$result->trade_id;
                    $data_array['user_id']=$array['user_id'];
                    $data_array['serialize_data']=serialize($result);
                    $data_array['gift_code']="";
                    $data_array['created']=date("Y-m-d H:i:s");
                    $data_array['updated']=date("Y-m-d H:i:s");
                    CreateLog::putDebugLog("レスポンステーブルにインサート");
                    //レスポンステーブルにインサート
                    if(!$this->insertLogResponsePublishes($data_array)){
                        throw new Exception("レスポンステーブルにインサート失敗");
                    } 
                //    echo "\ndetail_code>>".$result->detail_code;
                } else{
                  //  echo "\n照会APIでコード取得できた";
                    CreateLog::putDebugLog("照会APIでコード取得できた");
                    //レスポンステーブルにインサートするデータを格納
                    $data_array['response_code']=1;
                    $data_array['partner_code']=$array['partner_code'];
                    $data_array['detail_code']=$check_result->detail_code;
                    $data_array['message']=$check_result->message;
                    $data_array['trade_id']=$check_result->trade_id;
                    $data_array['user_id']=$array['user_id'];
                    $data_array['gift_code']=$check_result->gift_data->code;
                    $data_array['serialize_data']=serialize($check_result);
                    $data_array['created']=date("Y-m-d H:i:s");
                    $data_array['updated']=date("Y-m-d H:i:s");
                 //   echo "\nレスポンステーブルにインサート";
                    CreateLog::putDebugLog("レスポンステーブルにインサート");
                    CreateLog::putCodeLog($array['user_id']."/".$check_result->gift_data->code);
                    //レスポンステーブルにインサート
                    if(!$this->insertLogResponsePublishes($data_array)){
                        throw new Exception("レスポンステーブルにインサート失敗");
                    }
                }
            }
            return $data_array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 照会APIにアクセス
    *
    * @access public
    * @return string
    * @return boolean false
    */
    private function inquiryAPI($array){
        try{
            //照会APIにアクセスするための準備
            $data = array(
            'partner_code'=>$array['partner_code'],
            'response_type'=>$array['response_type'],
            'timestamp'=>time(),
            'trade_id'=>$array['trade_id']);
            //照会APIにアクセス
            $check_result = $this->sendPexData($data,PEX_CHECK_API_URL);   
            return $check_result;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 発券APIにアクセス
    *
    * @access public
    * @return string
    * @return boolean false
    */
    private function requestAPI($array){
        try{
            //発券APIにアクセスするための準備
            $data = array(
            'gift_identify_code'=>$array['gift_identify_code'],//ギフト券種類指定子
            'partner_code'=>$array['partner_code'],//パートナーコード
            'response_type'=>$array['response_type'],//レスポンス形式
            'timestamp'=>time(),//リクエスト時間
            'trade_id'=>$array['trade_id'],//取引ID
            'user_identify_code'=>$array['user_identify_code']);//ユーザー特定子
            //リクエストテーブルにインサート
            if(!$this->insertLogRequestPublishes($array)){
                throw new Exception("リクエストテーブルにインサート失敗");
            }
            //発券APIにアクセス
            $result = $this->sendPexData($data,PEX_API_URL);
            return $result;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * データ生成
    *
    * @access public
    * @return string 
    * @return boolean false
    */
    private function sendPexData($_data,$_url){
        $_data = http_build_query($_data, "", "&");
        $_hash = hash_hmac('sha1', urlencode($_data), PEX_SECRET_KEY,  FALSE );
        $_data .= "&signature=" . urlencode($_hash);

        return json_decode($this->sendPostData($_data,$_url));
       
    }
    /*
    * POST送信
    *
    * @access public
    * @return boolean 
    */
    private function sendPostData($_data,$_url){
        $_header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: ".strlen($_data)
        );
        $_options=array('http' => array(
                'method'=>'POST',
                'header' => implode("\r\n", $_header),
                'content'=>$_data
        ));
        $res = @file_get_contents($_url, false, stream_context_create($_options));
        $this->response_header = $http_response_header[0];      
        return $res;
    }
}

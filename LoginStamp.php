<?php
/**
* LoginStampクラス
* ログインスタンプ周り
* @package LoginStamp
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: LoginStamp.php,v 1.0 2013/06/04Exp 
*/
Class LoginStamp extends CommonBase{
    /*
    コンストラクタ
    */
    public function __construct() {
        try {
            parent::__construct();
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /*
    * ログインスタンプ
    *
    * @access public
    * @param int $login_id 
    * @return boolean
    */
    public function actionLoginStamp($login_id){
        try{
	    $login_max = 10;

            $now_time=date("Y-m-d H:i:s");
            //ログインスタンプテーブルからデータ取得
            // CreateLog::putDebugLog("ログインスタンプテーブルからデータ取得");
            if(!$array=$this->searchLoginStampData($login_id)){
                throw new Exception("ログインスタンプテーブルからデータ取得失敗");
            }
            CreateLog::putDebugLog("ログインスタンプテーブルからデータ取得");
            if($array=="not_login_stamp"){
                  CreateLog::putDebugLog("データが空なのでインサートする");
                if(!$login_count=$this->insertLoginStampData($login_id)){
                    throw new Exception("ログインスタンプテーブルにインサート失敗");
                }
                 CreateLog::putDebugLog("インサート成功 END");
                return 1;
            }    
            //最終ログイン日と現在の日時を比較
            CreateLog::putDebugLog("最終ログイン日と現在の日時を比較");
            $date1=strtotime($array['last_time']);
            $date2=strtotime(date("Y-m-d",strtotime('-1 day')));
           // $date2=strtotime("2013-7-31");
            CreateLog::putDebugLog("本日-1: ".$date2);
            $daydiff1=$date2-$date1;
            CreateLog::putDebugLog("最終ログイン日:".$array['last_time']); 
            CreateLog::putDebugLog("差分＝".$daydiff1);
            if($daydiff1 ==0){
                //連続ログインユーザー
              CreateLog::putDebugLog("連続ログインユーザーなのでアップデート");
                //9で成果
                if($array['login_count']==$login_max-1){
                    if(!$this->insertLoginStampPoint($login_id,5)){
                        throw new Exception("loginstanpのポイント付与");
                    }
                }
                //10の場合0にする

                if($array['login_count']==$login_max){ 
                   CreateLog::putDebugLog("10の場合0にする");
                    $array['login_count']=0;
                }
                if(!$login_count=$this->updateLoginStampData($login_id,$array['login_count']+1,$array['total_login_count']+1)){
                    throw new Exception("ログインスタンプテーブルにアップデート失敗");
                }
            } else if($daydiff1 >= 86400){
                //連続ログインユーザーでない
		CreateLog::putDebugLog("連続ログインユーザーでないのでログイン回数を1にする");
                if(!$login_count=$this->updateLoginStampData($login_id,1,1)){
                    throw new Exception("ログインスタンプテーブルにアップデート失敗");
                }
            } else {
                CreateLog::putDebugLog("本日はカウント終了した");
                return 0;
            }
            return $login_count;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * ログインスタンプテーブルからデータ取得
    *
    * @access private
    * @param int $login_id
    * @return array $row stamp_id,last_time,created,updated,login_count
    */
    private function searchLoginStampData($login_id){
        try{
            if(!$user_id=$this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql="SELECT
                        stamp_id,
                        date_format(last_time,'%Y-%m-%d') as last_time,
                        created,
                        updated,
                        login_count,
                        total_login_count
                  FROM
                        log_login_stamps
                  WHERE
                        user_id = ".$user_id." AND
                        deleted = 0  LIMIT 1;";
        //    CreateLog::putDebugLog($sql);
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            if($row==""){
                $row="not_login_stamp";
            } 
      //      CreateLog::putDebugLog("row>>".$row);
            return $row;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * ログインスタンプテーブルにインサート
    *
    * @access private
    * @param int $login_id 
    * @return boolean
    */
    private function insertLoginStampData($login_id){
        try{
            $now_time=date("Y-m-d H:i:s");
            $last_time=date("Y-m-d");
            //$last_time="2013-08-01";
            if(!$user_id=$this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql = "INSERT INTO
                            log_login_stamps
                    SET
                            user_id =".$user_id.",
                            login_count = 1,
                            total_login_count=1,
                            last_time ='".$last_time."',
                            created = '".$now_time."',
                            updated= '".$now_time."';";
           // CreateLog::putDebugLog($sql);
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return 1;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * ログインスタンプテーブルにアップデート
    *
    * @access private
    * @param int $login_id
    * @return boolean
    */
    private function updateLoginStampData($login_id,$login_count,$total_login_count){
        try{
            $now_time=date("Y-m-d H:i:s");
            $last_time=date("Y-m-d");
            //$last_time="2013-08-01";
            if(!$user_id=$this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql="UPDATE    
                        log_login_stamps
                  SET
                        login_count=".$login_count.",
                        total_login_count=".$total_login_count.",
                        last_time='".$last_time."',
                        updated='".$now_time."'
                  WHERE     
                        user_id=".$user_id.";";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return $login_count;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    *  ログインスタンプでのポイントをDBにインサート
    *
    *  @accesss public
    *  @return boolean
    */
    public function insertLoginStampPoint($login_id,$point){
        try {
            //ログインキー取得
            if(!$login_key = $this -> getLoginKey($login_id)){
                throw new Exception("method:insertLoginStampPoint(ログインキー取得失敗)");
            }
            $array = array();
            //履歴テーブルにインサートするデータを配列に格納
            $array['point_type']         = 5;//ログインスタンプは５
            $array['identifier']         = $login_id;
            $array['summary_id']         = 0;
            $array['achieve_id']         = 0;
            $array['accepted_time']      = date("Y-m-d H:i:s", time());
            $array['advertisement_key']  = 0;
            $array['campaign_name']      = "10日連続ログイン";
            $array['advertisement_id']   = 0;
            $array['advertisement_name'] = "LoginStampの実行";
            $array['sub_point']          = 0;//LoginStampなので減算ポイントはなし
            $array['point']              = $point;
            $array['bank_status']        = 1;
            //ポイント付与
            if(!$this -> addUserPoints($login_id,$login_key,$array) ){
                throw new Exception("method:insertLoginStampPoint(ポイント付与に失敗しました。)");
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
}

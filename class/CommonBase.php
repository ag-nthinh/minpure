<?php
/**
* CommonBaseクラス
* 共通クラス
* @package CommonBase
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: CommonBase.php,v 1.0 2013/2/9Exp $
*/
Class CommonBase {

    protected $link;        //Mysql link Id
    /**
    * コンストラクタ
    */
    public function __construct() {
        global $_COLLECT_CODE;      //一連の処理をまとめるグローバル関数
        $_COLLECT_CODE = CreateRand::getRandomString();
        try {
            //メンテナンスチェック
            if(EMERGENCY != 1 && $_POST['login_id']!=STRONG_LOGINID){
                $data['error'] = "EMERGENCY";
                $this->displayJsonArray($data);
                throw new Exception("EMERGENCY");
            }

            //データベース接続
            if(!$this->connectDataBase()) {
                $data['error'] = "false";
                $this->displayJsonArray($data);
                throw new Exception("false");
            }
	    if(isset($_POST['register_pass']) && $_POST['register_pass']!=""){
	    } else {
	            //ログインチェック
	            if(!$this->isLogin()) {
	                $data['error'] = "LOGIN FAILED";
	                $this->displayJsonArray($data);
	                throw new Exception("LOGIN FAILED");
	            }
/*
        	    //バージョンチェック
	            if(!$this -> checkVersion()) {   
        	        $data['error'] = "THIS VERSION IS NOT SUPPORTED";
                	$this->displayJsonArray($data);
	                throw new Exception("THIS VERSION IS NOT SUPPORTED");
        	    }
*/
	    }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die(); 
        }
    }
    /**
    * DBサーバ接続
    *
    * @accsess protected
    * @return boolean
    */
    protected function connectDataBase() {
        try {
            global $_LOG_CNT;
            $_LOG_CNT=0;
            if(!$link = mysql_connect(DB_HOST, DB_USER, DB_PASS)) {
                throw new Exception(mysql_error());
            }
            if(!mysql_select_db(DB_NAME, $link)) {
                throw new Exception(mysql_error());
            }
            if(!mysql_query("SET NAMES 'utf8'",$link)) {
                throw new Exception(mysql_error());
            }
            $this->setDatabaseLink($link);

            return true;

        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    /**
    * MysqlリンクIDを設定
    *
    * @accsess protected
    * @param resorce $link MysqlリンクID
    */
    protected function setDatabaseLink($link) {
        $this->link = $link;
    }

    /**
    * MysqlリンクIDを取得
    *
    * @accsess public
    * @return resorce $link MysqlリンクID
    */
    public function getDatabaseLink() {
        return $this->link;
    }
    /**
    * ログインチェック & ログイン
    *
    * @accsess public
    * @return boolean
    */
    public function isLogin(){
        try {
            if($this->validateLoginData()) {
                return true;
            } else {
                throw new Exception("ログインに失敗しました");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ログインデータチェック
    *
    * @accsess private
    * @return boolean
    */
    private function validateLoginData() {
        try {
            if(isset($_POST['login_id'],$_POST['login_pass'])){
                $login_id = $_POST['login_id'];
                $login_pass = $_POST['login_pass'];
            } else if(isset($_GET['login_id'],$_GET['login_pass'])){
                $login_id = $_GET['login_id'];
                $login_pass = $_GET['login_pass'];
            } else {
                throw new Exception("データが不足しています");
            }

            if(!$login_id = CommonValidate::validateLoginId($login_id)) {
                throw new Exception("IDチェック失敗 ".$login_id);
            }

            $login_key = $this->getLoginKey($login_id);
            if(!CommonValidate::validateLoginPass($login_id,$login_key,$login_pass)){
                throw new Exception("PASSチェック失敗 id=".$login_id."/ps=".$login_pass);
            }

            if(!$this->checkUserData($login_id,$login_key)){
                throw new Exception("ユーザーチェック失敗 ".$login_id);
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * login_id & login_keyのチェック
    *
    * @accsess protected
    * @param string login_id    ログインID
    * @param string login_key   ログインKey
    * @return boolean
    */
    protected function checkUserData($login_id,$login_key) {
        try {
            $sql = "SELECT 
                            * 
                    FROM 
                            data_users 
                    WHERE 
                            login_id = '".$login_id."' AND 
                            login_key = '".$login_key."' AND
                            status = 1 ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            if(mysql_num_rows($result) != 1) {
                throw new Exception("ユーザー登録が不正です");
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    
    /**
    * バージョンチェック
    *
    * @accsess protected
    * @return boolean
    */
    private function checkVersion(){
        try{
            if(isset($_POST['version'])){
                $version = $_POST['version'];
            } else if(isset($_GET['version'])){
                $version = $_GET['version'];
            } else {
                throw new Exception("データが不足しています");
            }


            if(VERSION != $version){
                throw new Exception("バージョンが違います");
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }


    public function begin(){
        try {
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクションBEGIN失敗");
            }
            return true;
            
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    public function commit(){
        try {
            //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクションCOMMIT失敗");
            }
            return true;
            
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
     public function rollback(){
        try {
            //トランザクション完了
            if(!mysql_query("rollback;",$this->getDatabaseLink())){
                throw new Exception("トランザクションROLLBACK失敗");
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    /**
    * トータルポイントの取得
    *
    * @accsess protected
    * @param string login_id    ログインID
    * @param string login_key   ログインKey
    * @return int point トータルポイント
    * @return boorean false 
    */
    public function getTotalPoints($login_id,$login_key) {
        try {
            if(!$this->checkUserData($login_id,$login_key)){
                throw new Exception("ユーザーIDが存在しません");
            }
            $point = 0;
            $points = $this->getTotalPointList($login_id,$login_key);
            foreach($points as $value){
                $point += $value;
            }
            return $point;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * トータルポイントの配列取得
    *
    * @accsess public
    * @param string login_id    ログインID
    * @param string login_key   ログインKey
    * @return array $points トータルポイントの配列
    * @return boolean false 
    */
    public function getTotalPointList($login_id,$login_key){
        try {
            $sql = "SELECT 
                            * 
                    FROM 
                            data_users 
                    WHERE 
                            login_id = '".$login_id."' AND 
                            login_key = '".$login_key."' LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            $point_list = $row['point_list'];
            $points = unserialize($point_list);
            return $points;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ユーザーポイントの更新
    *
    * @accsess protected
    * @param string login_id    ログインID
    * @param string login_key   ログインKey
    * @param array points       6ヶ月ポイントリスト
    * @return boolean          
    */
    protected function updateUserPoints($login_id,$login_key,$points){
        $new_points = serialize($points);
        $sql = "UPDATE 
                        data_users 
                SET
                        point_list ='".$new_points."'
                WHERE
                        login_id ='".$login_id."' AND
                        login_key = '".$login_key."';";

        if(mysql_query($sql,$this->getDatabaseLink())){
            return true;
        } else {
            return false;
        }
    }
     /**
    * 履歴テーブルにインサート
    *
    * @accsess protected
    * @param string login_id
    * @param array array        履歴テーブルにインサートするデータ
    * @return boolean        
    */
    protected function insertBankBook($login_id,$array){
        try {
            //ユーザーIDの取得
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗1");
            }
            $now_time = date("Y-m-d H:i:s");
            //とりあえず全データシリアライズ化
            $serialize_data = serialize($array);

	    if(!isset($array['timesale'])){
		$array['timesale'] = 0;
	    }

            $se1 = str_replace("\r\n", "    ", $serialize_data);
            $se2 = str_replace("\n", "  ", $se1);
            $se3 = str_replace("\\", "  ", $se2);

            $sql = "INSERT INTO 
                                    log_bankbooks 
                     SET
                                    point_type ='".$array['point_type']."',
                                    timesale ='".$array['timesale']."',
                                    user_id =".$user_id.",
                                    summary_id ='".$array['summary_id']."',
                                    app_point ='".$array['point']."',
                                    sub_point ='".$array['sub_point']."',
                                    status ='".$array['bank_status']."',
                                    serialize_data ='".$se3."',
                                    created = '".$now_time."'
                                    ;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            } else{
                return true;
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

     /**
    * 履歴テーブルにインサート
    *
    * @accsess protected
    * @param string login_id
    * @param array array        履歴テーブルにインサートするデータ
    * @return boolean        
    */
    protected function updateBankBook($login_id,$array){
        try {
            if(!$array['summary_id'] || $array['summary_id']=='0'){
                throw new Exception("成果履歴の存在しない通帳データ更新");
            }
            //ユーザーIDの取得
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗2");
            }
            $now_time = date("Y-m-d H:i:s");
            //とりあえず全データシリアライズ化
            $serialize_data = serialize($array);

            $set = "";
            if($array['point']>0){
                $set .= " app_point='".$array['point']."', ";
            }
            if($array['sub_point']>0){
                $set .= " sub_point='".$array['sub_point']."', ";
            }
 
            $sql = "UPDATE
                        log_bankbooks 
                    SET
                        ".$set."
                        status ='".$array['bank_status']."',
                        serialize_data ='".$serialize_data."',
                        updated = '".$now_time."'
                    WHERE
                        user_id = ".$user_id." AND
                        summary_id = ".$array['summary_id'].";";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            } else{
                return true;
            }
        } catch(Exception $e) {
            //CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 通帳データの取得
    *
    * @accsess public
    * @return $row 
    */
    protected function getBankBook($login_id,$array){
        try{
            if(!$array['summary_id'] || $array['summary_id']=='0'){
                throw new Exception("成果履歴の存在しない通帳データ参照");
            }

             //ユーザーIDの取得
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗3");
            }
 
            $sql="SELECT
                        *
                  FROM      
                        log_bankbooks
                  WHERE
                        user_id = '".$user_id."' AND
                        summary_id = '".$array['summary_id']."' LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            return $row;

        } catch(Exception $e) {
            //CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    /**
    * 加算メソッド
    *
    * @accsess protected
    * @param string login_id
    * @param string login_key
    * @param array array
    * @return boolean
    **/
    protected function addUserPoints($login_id,$login_key,$array){
        try {
            //加算ポイント
            //$add = $array['point'];
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }

            if($this -> getBankBook($login_id,$array)){//通帳記載あるなし
                //ポイント履歴の保存
                if(!$this -> updateBankBook($login_id,$array)){
                    throw new Exception("ポイント履歴の保存に失敗しました");
                }
            } else {
                //ポイント履歴の保存
                if(!$this -> insertBankBook($login_id,$array)){
                    throw new Exception("ポイント履歴の保存に失敗しました");
                }
            }

            if($array['bank_status']==1){
                //ユーザーのポイントレコード取得
                $points = $this->getTotalPointList($login_id,$login_key);

                $add = 0;
                if($bank_data = $this->getBankBook($login_id,$array)){
                    $add = $bank_data['app_point'];
                } else {
                    $add = $array['point'];
                }
 
                //ポイント加算
                $points[date('Y-m',strtotime('now'))] += $add;
                //ユーザーテーブルのトータルポイントを更新
                if(!$this->updateUserPoints($login_id,$login_key,$points)){
                    throw new Exception("アップデート失敗");
                }
            }

            //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            } else{
                return true;
            }
            
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
            return false;
        }
    }
    /**
    * 減算メソッド
    *
    * @accsess protected
    * @param string login_id
    * @param string login_key
    * @param array present_data
    * @return boolean
    **/
    protected function subUserPoints($login_id,$login_key,$present_data) {
        try {
            //減算ポイント
            $sub = $present_data['sub_point'];
            if($this->getTotalPoints($login_id,$login_key) < $sub){
                throw new Exception("トータルポイントが足りません");
            }
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
             //ポイント履歴の保存
            if(!$this -> insertBankBook($login_id,$present_data)){
                throw new Exception("ポイント履歴の保存に失敗しました");
            }
            //ユーザーのポイントレコード取得
            $points = $this->getTotalPointList($login_id,$login_key);
            $tmp = date('Y-m',strtotime('-11 month'));
            //減算スタート
            while(true){
                if($points[$tmp] < $sub){
                    $sub = $sub - $points[$tmp];
                    $points[$tmp] = 0;
                    $tmp = date('Y-m',strtotime('+1 month',strtotime($tmp)));
    
                }else{
                    $points[$tmp] = $points[$tmp] - $sub;
                    break;
               }
            }
            //ポイントを更新
            if(!$this->updateUserPoints($login_id,$login_key,$points)){
                throw new Exception("アップデート失敗");
            }

            //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            } else{
                return true;
            }

        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
            return false;
        }

    }
    /**
    * SNS投稿フラグの変更(SNSを投稿済みにする)
    *
    * @access public
    * @param string $login_id ログインID
    * @return boolean
    */
    public function changeSNSFlag($login_id){
        try {
            $now_time=date("Y-m-d H:i:s");
            //ユーザーID取得
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql = "UPDATE
                        data_snsflgs
                    SET
                        sns_flg = 0,
                        updated = '".$now_time."'
                    WHERE
                        user_id = '".$user_id."' and
                        status = 1;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql." ".mysql_error());
            } 
            
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
    /**
    * SNS投稿フラグチェック（本日投稿済みかどうか調べる）
    *
    * @access protected
    * @param string $login_id ログインID
    * @return boolean sns_flg 0:投稿不可 1:投稿可
    */
    protected function checkSNSFlag($login_id){
        try {
            //ユーザーID取得
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗4");
            }
            $sql = "SELECT
                        sns_flg
                    FROM
                        data_snsflgs
                    WHERE
                        user_id = '".$user_id."' and
                        status = 1 and
                        deleted = 0 LIMIT 1;";           
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            
            return $row['sns_flg'];
        } catch (Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
    /**
    * 短縮URLのアップデート
    *
    * @accsess public
    * @return boolean
    */
    public function updateShorteningUrl($user_id,$shortening_url){
        try {
            $sql = "UPDATE
                            data_snsflgs
                    SET
                            shortening_url='".$shortening_url."',
                            updated='".date("Y-m-d H:i:s")."'
                    WHERE
                            user_id = ".$user_id."
                     ; ";
            if(!mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    *  Snsテーブルのデータを取得
    *
    *  @accesss public
    *  @return boolean
    */
    public function getSnsData(){
        try {
            $sql="SELECT
                        *
                  FROM
                        data_snsflgs
                  WHERE
                        deleted = 0";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error().$sql);
            }
            $array=array();
            $num=0;
            while($row=mysql_fetch_assoc($result)){
                $array[$num]["user_id"]=$row['user_id'];
                $array[$num]["sns_id"]=$row['sns_id'];
                $array[$num]["sns_flg"]=$row['sns_flg'];
                $array[$num]["review_flg"]=$row['review_flg'];
                $array[$num]["status"]=$row['status'];
                $array[$num]["splash_flg"]=$row['splash_flg'];
                $array[$num]["tutorial_flg"]=$row['tutorial_flg'];
                $array[$num]["shortening_url"]=$row['shortening_url'];
                $num++;
            }
            return $array;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 履歴テーブルから、本日デイリーガチャが何回引いたかチェック
    *
    * @access protected
    * @param string $login_id ログインID
    * @return int count 本日デイリーガチャを引いた回数
    * @return boorean false
    */
    protected function checkBankbooksGacha($login_id){
        try {
            //ユーザーID取得
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗5");
            }
            //日付取得
            $time = date("Y-m-d");
            $sql = "SELECT
                            app_point
                    FROM
                            log_bankbooks
                    WHERE
                            user_id       = '".$user_id."' and
                            point_type    = 2 and
                            date(created) = '".$time."' and
                            deleted = 0 LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error().$sql);
            }
            //検索結果の行数表示
            $result_count = mysql_num_rows($result);
            return $result_count;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
    /**
    * 配布テーブルのstausを2（タップ済）に変更する
    * 
    * @access public
    * @param string $login_id ログインID
    * @param int $distribution_id 配布ID
    * @output json distribution_id 配布IDのJSON出力
    */
    public function chageDistributionStatus($login_id,$distribution_id){
        try{
            //ユーザーID取得
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗6");
            }
            $sql ="UPDATE
                            data_distributions
                   SET
                            status = 2
                   WHERE
                            user_id = '".$user_id."' AND
                            distribution_id = '".$distribution_id."'AND
                            deleted = 0;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            //配布IDのJSON出力
            $data['distribution_id'] = $distribution_id;
            $this->displayJsonArray($data);
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
    /**
    * 配布テーブルから、ギフトコードを取得
    *
    * @access public
    * @param string $login_id ログインID
    * @return boolean false 
    * @return string download_url ギフトコード
    */
    public function getDownloadUrl($login_id){
        try {
            //ユーザーID 
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗7");
            }
            $sql = "SELECT
                            distribution_id,
                            created,
                            download_url,
                            status,
                            time_covered,
                            present_id 
                    FROM
                            data_distributions
                    WHERE
                            user_id    = '".$user_id."' and
                            (status = 0 or status  = 1 or status = 2) and
                            deleted    = 0 ORDER BY updated DESC LIMIT 50  
                            ;";
           
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error()." ".$sql);
            }
            $num = 0;
            $array= array();
            while($row = mysql_fetch_assoc($result)){ 
                $array[$num]['download_url'] = $row['download_url'];
                $array[$num]['present_id'] = $row['present_id'];
                $array[$num]['distribution_id'] = $row['distribution_id'];
                $array[$num]['created'] = date("Y年n月j日",strtotime($row['created']));
                $array[$num]['status'] = $row['status']; 
                $array[$num]['time_covered'] =date("Y年n月j日",strtotime($row['time_covered']));
                $num++;
            }   
            //JSON出力
            $this -> displayJsonArray($array);
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }    
    }
    /**
    * master_presentsテーブルから、データの取得
    *
    * @access public
    * @param string $login_id ログインID
    * @param int $present_id プレゼントID
    * @return boolean
    */
    public function getPresentData($login_id, $present_id){
        try {
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗8");
            }
            $sql = "SELECT 
                            * 
                    FROM 
                            master_presents 
                    WHERE 
                            present_id = '".$present_id."' and
                            deleted = 0 LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            //履歴テーブルに入れるデータを配列に格納
            $array['point_type']         = 1;//ギフト交換は1
            $array['identifier']         = $user_id;
            $array['achieve_id']         = 0;
            $array['accepted_time']      = date("Y-m-d H:i:s", time());//現在の時間
            $array['advertisement_key']  = 0;
            $array['campaign_name']      = $row['name'];//プレゼント名
            $array['advertisement_id']   = 0;
            $array['advertisement_name'] = "ギフト交換";
            $array['sub_point']          = $row['point'];//減算ポイント
            $array['point']              = 0;
            $array['gift_identify_code'] = $row["gift_identify_code"] ;
            $array['bank_status']        = 1;//通帳表示
            return $array;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * data_summariesテーブル内に検索かけて成果済みの案件を検索
    *
    * @access public
    * @return array
    */
    public function searchOutcomeData($login_id,$advertisement_id,$outcome_id,$campany_type){
        try {
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗9");
            }
            $sql = "SELECT
                            *
                    FROM
                            data_summaries
                    WHERE
                            advertisement_id = '".$advertisement_id."' AND
                            user_id = '".$user_id."' AND
                            outcome_id = '".$outcome_id."' AND
                            campany_type = '".$campany_type."'
                            LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            $result_count = mysql_num_rows($result);
            if($result_count == 1){
                switch($row['status']){
                    case 0:
                        return 2;//承認待ち
                    case 2:
                        return 1;//成果
                    case 3:
                        return 3;//未起動
                    case 9:
                        return 9;//不可
                }
            } else{
                return 0;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * JSON表示
    *
    * @accsess protected
    * @resorce json_array jsonに入れる配列
    */
    public function displayJsonArray($json_array) {
        try {
            if(!is_array($json_array)){
                throw new Exception("配列以外が渡されました");
            }
            $json_data  = json_encode($json_array, JSON_FORCE_OBJECT);

            header('Content-type: application/json');
            echo $json_data;
            exit();
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * UserIDの取得
    *
    * @access public
    * @return int user_id ユーザーID　失敗時はfalse
    */
    protected function getUserID($login_id) {
        try {
            $sql = "SELECT
                            user_id
                    FROM
                            data_users
                    WHERE
                            login_id = '".$login_id."' AND     
                            deleted = 0 LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            if(mysql_num_rows($result)!=0){
                $row = mysql_fetch_assoc($result);
                return $row['user_id'];
            }else{
                throw new Exception("ユーザー検索エラー1 login_id=".$login_id);
            }
        } catch (Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * KEYの取得
    *
    * @accsess protected
    * @resorce string login_id ログインID
    * @return string login_key ログインKEY 失敗時はfalse
    */
    protected function getLoginKey($login_id) {
        try {
            $login_id = mysql_real_escape_string($login_id);
            $sql = "SELECT 
                            * 
                    FROM 
                            data_users 
                    WHERE 
                            login_id = '".$login_id."'AND
                            deleted = 0 LIMIT 1;";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            if(mysql_num_rows($result)>0){
                $row = mysql_fetch_assoc($result);
                return $row['login_key'];
            }else{
                throw new Exception("ユーザー検索エラー2 login_id=".$login_id);
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 重複IDの取得
    *
    * @accsess protected
    * @resorce string login_id ログインID
    * @return string duplicate_id 失敗時はfalse
    */
    protected function getDuplicateId($login_id) {
        try {
            $sql = "SELECT 
                            * 
                    FROM 
                            data_users 
                    WHERE 
                            login_id = '".$login_id."'AND
                            deleted = 0 LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            if(mysql_num_rows($result)!=0){
                $row = mysql_fetch_assoc($result);
                CreateLog::putDebugLog("重複ユーザ：".$row['duplicate_id']);
                return $row['duplicate_id'];
            }else{
                throw new Exception("ユーザー検索エラー3");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
    /**
    * login_idの取得
    *
    * @accsess protected
    * @resorce string user_id ユーザーID
    * @return string login_id ログインID 失敗時はfalse
    */
    public function getLoginId($user_id) {
        try {
            $sql = "SELECT
                            *
                    FROM
                            data_users
                    WHERE
                            user_id = ".$user_id." LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            if(mysql_num_rows($result)!=0){
                $row = mysql_fetch_assoc($result);
                return $row['login_id'];
            }else{
                throw new Exception("ユーザー検索エラー4");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 招待IDの取得
    *
    * @accsess public
    * @return $invitation_id 
    */
    public function searchInvitationId($login_id){
        try{
            $sql="SELECT
                        invitation_id
                  FROM      
                        data_users      
                  WHERE     
                        login_id='".$login_id."' LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            return $row['invitation_id'];
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ￥マークで，区切りの出力形式に変換する
    *
    *
    */
    public static function putMoney($data){
        $data="￥".number_format($data);
        return $data;
    }
    /*
    * メール関数
    *
    */
    protected function sendMail($host,$subject,$message,$from){
        //メッセージ内容をメールで送信する
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        mb_send_mail($host ,SUBJECT_MODE.$subject,$message, $from);
    }
}//End of class

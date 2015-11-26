<?php
/**
* PostFlgsクラス
* レビュー周り
* @package PostFlgs
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: PostFlgs,v 1.0 2013/2/9Exp $
*/
class PostFlgs extends CommonBase {

    public function __construct(){
        try {
            parent::__construct();
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());

        }
    }
    /**
    * googleplayに飛ぶ
    *
    * @accsess public
    */
    public function migrateGooglePlay(){
        header('Location: https://play.google.com/store/apps/details?id=jp.kodukaigetton');    
    }
    /**
    * レビューしているかチェックして、ポイント付与からGooglePlayに飛ぶ処理
    *
    * @accsess public
    * @return boolean
    */
    public function reviewFlg($login_id){
        try{
            //レビューしているかチェックする
            if(!$this -> checkReviewFlg($login_id)){
                //googleplayに飛ぶ
                $this -> migrateGooglePlay();
                die();
            }
            $array = array();
            if(!$login_key = $this -> getLoginKey($login_id)){
                throw new Exception("ログインキー取得失敗");
            }
            //履歴テーブルにインサートするデータを配列に格納
            $array['point_type']         = 3;//レビュー投稿は3
            $array['identifier']         = $login_id;
            $array['achieve_id']         = 99999;
            $array['accepted_time']      = date("Y-m-d H:i:s", time());
            $array['advertisement_key']  = 99999;
            $array['campaign_name']      = "レビュー投稿";
            $array['advertisement_id']   = 0;
            $array['advertisement_name'] = "レビュー投稿の実行";
            $array['sub_point']          = 0;//レビュー投稿なので減算ポイントはなし
            $array['point']              = REVIEW_POINT;//レビュー投稿の付与ポイント
            $array['bank_status']        = 1;
            //ポイント付与
            if(!$this -> addUserPoints($login_id,$login_key,$array) ){
                throw new Exception("ポイント付与に失敗しました。");
            }
            //レビューフラグをレビュー済みにする
            if(!$this -> onReview($login_id)){
                throw new Exception("(レビュー済に失敗しました)");
            }
            //googleplayに飛ぶ
            //$this -> migrateGooglePlay();

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /**
    * レビュー済みにする
    *
    * @accsess public
    * @return boolean
    */
    public function onReview($login_id){
        try{
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql = "UPDATE
                        data_snsflgs
                    SET
                        review_flg = 1,
                        updated = now()
                    WHERE
                        user_id = '".$user_id."' and
                        status = 1;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error()." ".$sql);
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * レビューしているかチェック
    *
    * @accsess public
    * @param string $login_id ログインID
    * @return boolean
    */
    public function checkReviewFlg($login_id){
        try{
            if(!$row = $this -> searchDataSnsFlgs($login_id)){
                throw new Exception("data_snsflgsのデータを取得に失敗");
            }
            if($row['review_flg'] == 1){
                return false;
            } else{
                return true;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ユーザーIDをもとにdata_snsflgsのデータを取得
    *
    * @accsess public
    * @param string $login_id ログインID
    * @return array $row
    * @return boolean false
    */
    public function searchDataSnsFlgs($login_id){
        try{
            if(!$user_id = $this -> getUserID($login_id)){
                throw new Exception("ユーザーID取得失敗");
            }
            $sql ="SELECT
                            *
                   FROM
                            data_snsflgs
                   WHERE
                            user_id = '".$user_id."' LIMIT 1;
            ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            return $row;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}

<?php
/**
* Splashクラス
* レビューできるか検索
* @package Splash
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: Splash,v 1.0 2013/6/18Exp $
*/
class Splash extends CommonBase {

    public function __construct(){
        try {
            parent::__construct();
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());

        }
    }
    /**
    * スプラッシュフラグの取得
    *
    * @accsess public
    * @return $splash_flg
    */
    public function searchSplashFlgs($login_id){
        try{
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql = "SELECT
                            splash_flg
                    FROM
                            data_snsflgs
                    WHERE
                            user_id=".$user_id.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            if($row['splash_flg']==0){
   //             CreateLog::putDebugLog("not_flgです");
                return "not_flg";
            }
 //             CreateLog::putDebugLog("もうスプラッシュを表示済です");
            return $row['splash_flg'];

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * スプラッシュフラグのアップデート
    *
    * @accsess public
    * @return $splash_flg
    */
    public function updateSplashFlgs($login_id){
        try{
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql = "UPDATE
                            data_snsflgs
                    SET
                            splash_flg=1
                    WHERE
                            user_id=".$user_id." AND
                            deleted=0 ; ";
            if(!mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * チュートリアルフラグの取得
    *
    * @accsess public
    * @return $tutorial_flg
    */
    public function searchTutorialFlgs($login_id){
        try{
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql = "SELECT
                            tutorial_flg
                    FROM
                            data_snsflgs
                    WHERE
                            user_id=".$user_id.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            if($row['tutorial_flg']==0){
  //              CreateLog::putDebugLog("not_tutorial_flgです");
                return "not_tutorial_flg";
            }
    //          CreateLog::putDebugLog("もうチュートリアルを表示済です");
            return $row['tutorial_flg'];

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * チュートリアルフラグのアップデート
    *
    * @accsess public
    * @return $tutorial_flg
    */
    public function updateTutorialFlgs($login_id){
        try{
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザー検索失敗");
            }
            $sql = "UPDATE
                            data_snsflgs
                    SET
                            tutorial_flg=1
                    WHERE
                            user_id=".$user_id." AND
                            deleted=0 ; ";
            if(!mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * スプラッシュフラグを0にする
    *
    * @accsess public
    * @return boolean
    */
    public function onSplashFlgs(){
        try{
            $sql = "UPDATE
                            data_snsflgs
                    SET
                            splash_flg=0
                     ; ";
            if(!mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}

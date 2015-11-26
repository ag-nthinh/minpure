<?php
/**
* AddPointクラス
* ポイント付与クラス
* @package AddPoint
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: AddPoint.php,v 1.0 2013/05/06Exp $
*/
Class AddPoint extends CommonBase {
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
       }
    }
    /**
    * 加算メソッド
    *
    * @accsess publicb
    * @param string login_id
    * @param string login_key
    * @param array array
    * @return boolean
    **/
    public function addPoints($login_id,$login_key,$array){
        try {
            //加算ポイント
            $add = $array['point'];
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
            //ポイント履歴の保存
            if(!$this -> insertBankBook($login_id,$array)){
                throw new Exception("ポイント履歴の保存に失敗しました");
            }
            //ユーザーのポイントレコード取得
            $points = $this->getTotalPointList($login_id,$login_key);
            //ポイント加算
            $points[date('Y-m',strtotime('now'))] = $points[date('Y-m',strtotime('now'))] + $add;
            //ユーザーテーブルのトータルポイントを更新
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
    * 減算メソッド
    *
    * @accsess public
    * @param string login_id
    * @param string login_key
    * @return boolean
    **/
    public function subPoints($login_id,$login_key,$array){
        try {
            if($array['point_type']==""){
                $sub_point =$array['sub_point'];
                //ユーザーのポイントレコード取得
                $points = $this->getTotalPointList($login_id,$login_key);
                $tmp = date('Y-m',strtotime('-5 month'));
                //減算スタート
                while(true){
                    if($points[$tmp] < $sub_point){
                        $sub_point = $sub_point - $points[$tmp];
                        $points[$tmp] = 0;
                        $tmp = date('Y-m',strtotime('+1 month',strtotime($tmp)));

                    }else{
                        $points[$tmp] = $points[$tmp] - $sub_point;
                        break;
                    }
                }
                //ポイントを更新
                if(!$this->updateUserPoints($login_id,$login_key,$points)){
                    throw new Exception("アップデート失敗");
                }
            } else{
                
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}

<?php
/**
* ValidatedPointクラス
* 共通クラス
* @package ValidatedPoint
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: ValidatedPoint.php,v 1.0 2013/1/4Exp $
*/
Class ValidatedPoint extends CommonBase {
    /**
    * コンストラクタ
    */
    public function __construct() {
        try {
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
            if(!$this->deleteInoperativePoint()) {
                throw new Exception("12ヶ月前のポイント削除に失敗しました。");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /**
    * 有効期限切れのポイントを削除
    *
    * @accsess private
    * @return boolean
    */
    private function deleteInoperativePoint() {
        try {
            $sql = "
            SELECT 
                    user_id,
                    login_id,
                    login_key,
                    point_list
            FROM
                    data_users
            WHERE 
                    deleted = 0
            ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error()." ".$sql);
            }
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
            $search_obj=new SearchAll();
//            $all_point=$search_obj->getAllPoint();//死んだユーザーの合計ポイント
            $lose_point=0;
//            $total_point=0;
            //全ユーザーの有効期限切れのポイントを削除して新しい月の追加
            while($row = mysql_fetch_assoc($result)){
                $point_array = unserialize($row['point_list']);
                //失効ポイントを保存する
                $lose_point = $lose_point + $point_array[date('Y-m',strtotime('-1 year'))];//失効ポイント
//                $total_point = $total_point + $this->getTotalPoints($row['login_id'],$row['login_key']);//トータルポイント
                $point_array = array(
//                            date('Y-m',strtotime('-11 month')) => $point_array[date('Y-m',strtotime('-11 month'))] ,
//                            date('Y-m',strtotime('-10 month')) => $point_array[date('Y-m',strtotime('-10 month'))] ,
//                            date('Y-m',strtotime('-9 month'))  => $point_array[date('Y-m',strtotime('-9 month'))] ,
//                            date('Y-m',strtotime('-8 month'))  => $point_array[date('Y-m',strtotime('-8 month'))] ,
//                            date('Y-m',strtotime('-7 month'))  => $point_array[date('Y-m',strtotime('-7 month'))] ,
//                            date('Y-m',strtotime('-6 month'))  => $point_array[date('Y-m',strtotime('-6 month'))] ,
                            date('Y-m',strtotime('-5 month'))  => $point_array[date('Y-m',strtotime('-5 month'))] ,
                            date('Y-m',strtotime('-4 month'))  => $point_array[date('Y-m',strtotime('-4 month'))] ,
                            date('Y-m',strtotime('-3 month'))  => $point_array[date('Y-m',strtotime('-3 month'))] ,
                            date('Y-m',strtotime('-2 month'))  => $point_array[date('Y-m',strtotime('-2 month'))] ,
                            date('Y-m',strtotime('-1 month'))  => $point_array[date('Y-m',strtotime('-1 month'))] ,
                            date('Y-m',strtotime('now')) => 0);

                $row['point_list'] = serialize($point_array);
                //point_listをアップデート
                $sql = "
                    UPDATE 
                            data_users
                    SET
                            point_list = '".$row["point_list"]."'
                    WHERE
                            user_id = ".$row['user_id'].";";
                if(!mysql_query($sql,$this->getDatabaseLink())){
                    throw new Exception("data_usersのpoint_listをUPDATEできませんでした。 ".$sql.mysql_error());
                }
            }

            CreateLog::putDebugLog("lose_points:".$lose_point);


/*
            //全失効ポイント
            $lose_total_point=$lose_point+ $all_point['total_point'];
            if(!$lose_point){ $lose_point=0; } 
            if(!$total_point){ $total_point=0; }
            
            //最新合計ポイント
            $sum_point1=$total_point-$lose_total_point;
            //1カ月前の合計ポイント
            $sum_point2=$total_point-$all_point['total_point'];      
            $host=DEVELOP_MAIL_ADDRESS;
            $subject="【ギフトGET】失効ポイント＆合計ポイント";
            $message="";
            $message.="\n失効ポイント合計([1]+[2])：".$lose_total_point;
            $message.="\n [1]月またぎ失効ポイント：".$lose_point;
            $message.="\n [2]失効アカウントの合計ポイント（累計）：".$all_point['total_point'];
            $message.="\n\n".date("n月",strtotime('-1 month'))."までの合計ポイント：".$sum_point2;
            $message.="\n↓";
            $message.="\n".date("n月")."合計ポイント：".$sum_point1;
            $from = GIFTGET_MAIL_ADDRESS_FROM;
            $this->sendMail($host,$subject,$message,$from);
*/

            $sql = "INSERT INTO
                        lose_points
                    SET
                        closing_date = '".date('Y-m-d', strtotime('-1 day'))."',
                        point = ".$lose_point."
                        ;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("lose_point update error ".$sql.mysql_error());
            }

            //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
            return false;
        }
    }
    
}

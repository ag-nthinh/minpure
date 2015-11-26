<?php
/**
  * AffiTownクラス
  * 管理画面のデータ取得関係をまとめたクラス
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class Timesales extends AffiTown{
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
     public function setTimesalePromotion($arr){
        try{
	    if(!is_numeric($arr['timesale_point']) || $arr['timesale_point'] < $arr['point']){
                throw new Exception("ポイント数の設定が正しくありません");
	    }
	    if($arr['start_time'] == ""){
		$arr['start_time'] = date("Y-m-d H:i:s");
	    }
	    if($arr['end_time'] == ""){
		$arr['end_time'] = date("Y-m-d H:i:s", strtotime("+1 year"));
	    }

            $sql = "SELECT
                        *
                    FROM
                        master_affitown_advertisements
                    WHERE
                        affitown_advertisement_id=".$arr['affitown_advertisement_id']." LIMIT 1;";
            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }

            $promotion_data = mysql_fetch_assoc($result);

            $promotion_data['timesale'] = 1;
            $promotion_data['timesale_point'] = $arr['timesale_point'];
            if($promotion_data['start_time'] < $arr['start_time']){
                $promotion_data['start_time'] = $arr['start_time'];
            }
            if($promotion_data['end_time'] > $arr['end_time']){
                $promotion_data['end_time'] = $arr['end_time'];
            }

            if(!$x = $this->setNewPromotion($promotion_data)){
                throw new Exception("データが正しくありません");
	    }

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }

    public function endTimesalePromotion($aaid){
        try {
            $sql = "UPDATE
                        master_affitown_advertisements
                    SET
                        end_time = '".date("Y-m-d H:i:s")."'
                    WHERE
                        affitown_advertisement_id = ".mysql_real_escape_string($aaid).";";

            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("insert失敗".$sql);
            }

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
    public function getTimesalePromotion($aaid){
        try {
            $sql = "SELECT
                        *
                    FROM
                        master_affitown_advertisements
                    WHERE
                        affitown_advertisement_id = '".$aaid."' LIMIT 1;";

            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }
            $data = array();
            $num = 0;
            $row = mysql_fetch_assoc($result);
            $data = $row;
            $data['img'] = str_replace("<img", "<img width='90'", $row['img']);
            if($row['amount']){
                $data['amount'] = '￥'.number_format($row['amount']);
            } else {
                $data['amount'] = '無料';
            }
            $data['point'] = floor($row['unit_price']*DEFAULT_RATE);
            if($row['action_point']=='01'){
                $data['action'] = "無料会員登録";
            } else if($row['action_point']=='02'){
                $data['action'] = "有料会員登録";
            } else if($row['action_point']=='03'){
                $data['action'] = "キャンペーン・懸賞応募";
            } else if($row['action_point']=='04'){
                $data['action'] = "アンケート回答";
            } else if($row['action_point']=='05'){
                $data['action'] = "資料請求";
            } else if($row['action_point']=='06'){
                $data['action'] = "サンプル申し込み";
            } else if($row['action_point']=='07'){
                $data['action'] = "商品購入";
            } else if($row['action_point']=='08'){
                $data['action'] = "クレジットカード申し込み";
            } else if($row['action_point']=='09'){
                $data['action'] = "初回商品購入";
            } else if($row['action_point']=='10'){
                $data['action'] = "ショップ新規購入";
            } else if($row['action_point']=='11'){
                $data['action'] = "面談完了";
            } else if($row['action_point']=='12'){
                $data['action'] = "面談申込";
            } else if($row['action_point']=='13'){
                $data['action'] = "来店予約";
            } else if($row['action_point']=='14'){
                $data['action'] = "来店";
            } else if($row['action_point']=='15'){
                $data['action'] = ""; 
            }

            return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    public function getPromotionConversion($adid, $accepted_time){
        try {
            $sql = "SELECT
			affitown_advertisement_id,
			adid,
			timesale_point point
		    FROM
			master_affitown_advertisements
		    WHERE
			timesale=1 AND
			start_time<'".$accepted_time."' AND
			end_time>'".$accepted_time."' AND
			adid = '".$adid."'
			ORDER BY start_time DESC LIMIT 1;";

            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }
            $num = 0;
            $row = mysql_fetch_assoc($result);

            return $row;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    public function getTimesalePromotions(){
        try {
            $name_length = 30;//表示案件名の最大長

            $sql = 'SELECT
			*
                    FROM
			master_affitown_advertisements 
                    WHERE
			timesale=1 AND 
			deleted=0 AND 
			status=1 AND 
			start_time<"'.date("Y-m-d H:i:s").'" AND
			end_time>"'.date("Y-m-d H:i:s").'"
			ORDER BY start_time DESC LIMIT 3;';

            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }
            $data = array();
            $num = 0;
            while($row = mysql_fetch_assoc($result)){
                $data[$num] = $row;
                if(mb_strlen($row['name'])>$name_length){
                    $data[$num]['name'] = mb_substr($row['name'], 0, $name_length)."...";
                }
                $data[$num]['img'] = str_replace("<img", "<img width='90'", $row['img']);
                if($row['amount']){
                    $data[$num]['amount'] = '￥'.number_format($row['amount']);
                } else {
                    $data[$num]['amount'] = '無料';
                }
		$data[$num]['point'] = floor($row['unit_price']*DEFAULT_RATE);
                if($row['action_point']=='01'){
                    $data[$num]['action'] = "無料会員登録";
                } else if($row['action_point']=='02'){
                    $data[$num]['action'] = "有料会員登録";
                } else if($row['action_point']=='03'){
                    $data[$num]['action'] = "キャンペーン・懸賞応募";
                } else if($row['action_point']=='04'){
                    $data[$num]['action'] = "アンケート回答";
                } else if($row['action_point']=='05'){
                    $data[$num]['action'] = "資料請求";
                } else if($row['action_point']=='06'){
                    $data[$num]['action'] = "サンプル申し込み";
                } else if($row['action_point']=='07'){
                    $data[$num]['action'] = "商品購入";
                } else if($row['action_point']=='08'){
                    $data[$num]['action'] = "クレジットカード申し込み";
                }
//                $data[$num]['time'] = str_replace("-", "/", $data[$num]['end_time']);
                $data[$num]['time'] = date("Y/m/d H:i:00", strtotime($data[$num]['end_time']));
                $num++;
            }

             return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    public function getTimesalePromotionCount(){
        try {
            $sql = 'SELECT
                        affitown_advertisement_id
                    FROM
                        master_affitown_advertisements 
                    WHERE
                        timesale=1 AND 
			deleted=0 AND 
			status=1 AND 
			end_time>"'.date("Y-m-d H:i:s").'";';

            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);

             return $result_count;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}

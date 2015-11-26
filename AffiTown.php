<?php
/**
  * AffiTownクラス
  * 管理画面のデータ取得関係をまとめたクラス
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class AffiTown extends CommonBase{
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

     private function dateConvert($d,$nolimit){
        try {
	    $start_limit = '2010-01-01 00:00:00';
	    $end_limit = '2038-01-01 00:00:00';

            if(strlen($d)==12){
		$limit = date("Y-m-d H:i:00", strtotime($d));
		if($limit < $start_limit){
			return $start_limit;
		} else if($limit > $end_limit){
			return $end_limit; 
		} else {
			return $limit;
		}
            }
         } catch(Exception $e) {
         }
	 if($nolimit == 1){
		return $end_limit;
	 } else {
		return $start_limit;
	 }
     }
     /**
     * 案件を取得
     *
     * @accsess public
     */
     public function setPromotions(){
        try{
            $now_time=date("Y-m-d H:i:s");
            $context = null;
            $url = 'http://ad.atown.jp/adserver/api/adsearch/1?token=7c227fe4-0c08-4b2e-8cf9-f823a819bc96&id=121&c=161';//CN
            $json = file_get_contents($url,false,$context);
            $arr = json_decode($json, true);
            $data = array();
            if($arr['response']['code']!='0'){
               return $data;
            }

            foreach($arr['advertise'] as $key => $value){
                $data[$key]['adid'] = $value['adId']; // 広告キー
                $data[$key]['name'] = $value['publishName'];
                $data[$key]['amount'] = $value['amount'];
                $data[$key]['tax'] = $value['tax'];// 1:税抜き / 2:税込み
                $data[$key]['start_time'] = $this->dateConvert($value['adStartDate'], 0);// yyyyMMddHHmm
                $data[$key]['end_time'] = $this->dateConvert($value['adEndDate'], 1);// yyyyMMddHHmm
                $data[$key]['approval_span'] = $value['approvalSpan'];// 1:全承認 2:7日 3:15日 4:30日 5:60日 6:90日
                $data[$key]['rest'] = $value['rest'];//残り件数
                $data[$key]['action_point'] = $value['actionPoint'];// 01:無料会員登録 02:有料会員登録 03:キャンペーン・懸賞応募 04:アンケート回答 05:資料請求 06:サンプル申し込み 07:商品購入 08:クレジットカード申し込み
                $data[$key]['unique_check'] = $value['uniqueCheck'];//1:重複確認あり 2:なし
                $data[$key]['price_type'] = $value['priceType'];//残り件数
                $data[$key]['unit_price'] = $value['unitPrice'];//報酬単価
                $data[$key]['unit_discount'] = $value['unitDiscount'];//報酬単価
                $data[$key]['description'] = $value['introduction'];//詳細説明文
                $data[$key]['timesale'] = 0;//タイムセールではない
                $data[$key]['timesale_point'] = floor($value['unitPrice'] * DEFAULT_RATE);
                $data[$key]['status'] = 1;

                //縦横比が一番1:2に近い画像＝縦x2/横が1に一番近い画像を選出
                $banner_num = 0;
                $max_banner_rate = 0;
                foreach($value['manuscript'] as $key2 => $value2){
                    $str = $value2['msSize'];
                    if(!empty($str)){
                        if(preg_match("/×/",$str)){
                            $arr = explode('×',$str);
                        }else{
                            $str = strtoupper($str);
                            $arr = explode('X',$str);
                        }
                        if(is_numeric($arr[0]) && is_numeric($arr[1])){
                            $banner_rate = $arr[1]*2/$arr[0];//縦x2/横を計算
                            if($banner_rate>1){//縦長
                                $banner_rate = 1 / $banner_rate;//横/縦x2
                            }
                            if($banner_rate>$max_banner_rate){
                                $banner_num = $key2;
                                $max_banner_rate = $banner_rate;
                            }
                        }
                    }
                }
                $data[$key]['img'] = $value['manuscript'][$banner_num]['msFile'];//画像
                $data[$key]['url'] = $value['manuscript'][$banner_num]['msUrl'];//遷移先
 
                if(!$this->getPromotion($data[$key]['adid'])){//同一adidの案件を検索する
                    $this->setNewPromotion($data[$key]);
//                } else {
//                    $this->upPromotion($data[$key]);
                } 
            }
//            $this->dropPromotions();

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }

    // APIで取得できなかった案件を取り下げる
    private function dropPromotions(){
        try {
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
 
            $sql = "UPDATE
                        master_affitown_advertisements
                    SET
                        status = 0,
                        deleted = 1,
                        updated = '".date("Y-m-d H:i:s")."'
                    WHERE
                        timesale = 0 AND
                        updated < now() - interval 5 minute AND
                        created < now() - interval 5 minute AND
                        status = 1;";

             //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            } else{
                return true;
            }      

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
            return false;
        }
    }

    public function upPromotion($arr){
        try {
            $sql = "UPDATE
                        master_affitown_advertisements
                    SET
                        adid = '".mysql_real_escape_string($arr['adid'])."',
                        name = '".mysql_real_escape_string($arr['name'])."',
                        amount = '".mysql_real_escape_string($arr['amount'])."',
                        tax = '".mysql_real_escape_string($arr['tax'])."',
                        timesale_point = '".mysql_real_escape_string($arr['timesale_point'])."',
                        start_time = '".mysql_real_escape_string($arr['start_time'])."',
                        end_time = '".mysql_real_escape_string($arr['end_time'])."',
                        approval_span = '".mysql_real_escape_string($arr['approval_span'])."',
                        rest = '".mysql_real_escape_string($arr['rest'])."',
                        action_point = '".mysql_real_escape_string($arr['action_point'])."',
                        unique_check = '".mysql_real_escape_string($arr['unique_check'])."',
                        price_type = '".mysql_real_escape_string($arr['price_type'])."',
                        unit_price = '".mysql_real_escape_string($arr['unit_price'])."',
                        unit_discount = '".mysql_real_escape_string($arr['unit_discount'])."',
                        description = '".mysql_real_escape_string($arr['description'])."',
                        note = '".mysql_real_escape_string($arr['note'])."',
                        img = '".mysql_real_escape_string($arr['img'])."',
                        url = '".mysql_real_escape_string($arr['url'])."',
                        status = '".mysql_real_escape_string($arr['status'])."',
                        updated = '".date("Y-m-d H:i:s")."'
                    WHERE
                        affitown_advertisement_id = '".mysql_real_escape_string($arr['affitown_advertisement_id'])."';";

            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("insert失敗".$sql);
            }

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
    public function setNewPromotion($arr){
        try {
	    CreateLog::putDebugLog("setNP ");
            $sql = "INSERT INTO master_affitown_advertisements SET
                        adid = '".mysql_real_escape_string($arr['adid'])."',
                        timesale = '".mysql_real_escape_string($arr['timesale'])."',
                        timesale_point = '".mysql_real_escape_string($arr['timesale_point'])."',
                        name = '".mysql_real_escape_string($arr['name'])."',
                        amount = '".mysql_real_escape_string($arr['amount'])."',
                        tax = '".mysql_real_escape_string($arr['tax'])."',
                        action_point = '".mysql_real_escape_string($arr['action_point'])."',
                        start_time = '".mysql_real_escape_string($arr['start_time'])."',
                        end_time = '".mysql_real_escape_string($arr['end_time'])."',
                        approval_span = '".mysql_real_escape_string($arr['approval_span'])."',
                        rest = '".mysql_real_escape_string($arr['rest'])."',
                        unique_check = '".mysql_real_escape_string($arr['unique_check'])."',
                        price_type = '".mysql_real_escape_string($arr['price_type'])."',
                        unit_price = '".mysql_real_escape_string($arr['unit_price'])."',
                        unit_discount = '".mysql_real_escape_string($arr['unit_discount'])."',
                        description = '".mysql_real_escape_string($arr['description'])."',
                        note = '".mysql_real_escape_string($arr['note'])."',
                        img = '".mysql_real_escape_string($arr['img'])."',
                        url = '".mysql_real_escape_string($arr['url'])."',
                        status = '".mysql_real_escape_string($arr['status'])."',
                        created = '".date("Y-m-d H:i:s")."',
                        updated = '".date("Y-m-d H:i:s")."';";

	    CreateLog::putDebugLog("setNP ".$sql);

            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("insert失敗".$sql);
            }

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }


    public function getPromotionById($affitown_advertisement_id){
        try {
            $sql = "SELECT
                        *
                    FROM
                        master_affitown_advertisements
                    WHERE
                        affitown_advertisement_id = ".$affitown_advertisement_id." LIMIT 1;";

            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }
            $row = mysql_fetch_assoc($result);

            return $row;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }



    public function getPromotion($adid){
        try {
            $sql = "SELECT
                        *
                    FROM
                        master_affitown_advertisements
                    WHERE
			timesale = 0 AND
                        adid = '".$adid."' LIMIT 1;";

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
            //$data['point'] = floor($row['unit_price']*DEFAULT_RATE);
            $data['point'] = $row['timesale_point'];

		$action_string = array(
			"01"=>"無料会員登録",
			"02"=>"有料会員登録",
			"03"=>"キャンペーン・懸賞応募",
			"04"=>"アンケート回答",
			"05"=>"資料請求",
			"06"=>"サンプル申し込み",
			"07"=>"商品購入",
			"08"=>"クレジットカード申し込み",
			"09"=>"初回商品購入",
			"10"=>"ショップ新規購入",
			"11"=>"面談完了",
			"12"=>"面談申込",
			"13"=>"来店予約",
			"14"=>"来店",
			"15"=>"",
			"16"=>"アプリインストール",
			"17"=>"その他",
			"18"=>"",
			"19"=>"口座開設"
			);

	    $data['action'] = $action_string[$row['action_point']];

            return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    public function getPromotions($arr){
        try {
	    $now_time = date("Y-m-d H:i:s");

            $name_length = 30;//表示案件名の最大長
            $where = "";
            $limit = "";
            if($arr['type']==1){//無料案件一覧
                $where .= " AND amount=0 ";
            } else if($arr['type']==2){//実質無料案件
                //$where .= " AND amount>0 AND amount<unit_price*".DEFAULT_RATE." ";
                $where .= " AND amount>0 AND amount<timesale_point ";
            } else if($arr['type']==3){//売れ筋ランキング
                $where .= " AND amount>0 ";
            }
            if(isset($arr['name']) && $arr['name']!=''){
                $where .= 'AND name LIKE BINARY "%'.$arr['name'].'%" ';
            }

            if($arr['timesale']==1 || $arr['timesale']==0){
                $where .= 'AND timesale = '.$arr['timesale'].' ';
            }

            if($arr['status']==1){
                $where .= ' AND deleted=0 AND status=1 AND start_time<"'.$now_time.'" AND end_time>"'.$now_time.'" ';
            }

            if($arr['num']){
                $limit .= " LIMIT ".$arr['num'];
            }

                $off_advertisements = "";
	    if(isset($arr['login_id']) && $arr['login_id']!=""){
	        if(!$user_id = $this->getUserID($arr['login_id'])){
        	    throw new Exception("ユーザー検索失敗1");
            	}
                $off_advertisements_array = $this->getConversionItems($user_id);
                if($off_advertisements_array){
                    foreach($off_advertisements_array as $value){
			if($value['campany_type']==1){
                        	$off_advertisements .= " AND NOT adid='".$value['advertisement_key']."' ";
			}
                    }
                }
	    }

            $sql = "SELECT
                        *
                    FROM
                        master_affitown_advertisements 
                    WHERE
                        price_type='1' ".$where.
			$off_advertisements.
		   " ORDER BY start_time DESC ".$limit.";";

//            CreateLog::putDebugLog("sql = ".$sql);

            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }
            $data = array();
            $num = 0;

		$action_string = array(
			"01"=>"無料会員登録",
			"02"=>"有料会員登録",
			"03"=>"キャンペーン・懸賞応募",
			"04"=>"アンケート回答",
			"05"=>"資料請求",
			"06"=>"サンプル申し込み",
			"07"=>"商品購入",
			"08"=>"クレジットカード申し込み",
			"09"=>"初回商品購入",
			"10"=>"ショップ新規購入",
			"11"=>"面談完了",
			"12"=>"面談申込",
			"13"=>"来店予約",
			"14"=>"来店",
			"15"=>"",
			"16"=>"アプリインストール",
			"17"=>"その他",
			"18"=>"",
			"19"=>"口座開設"
			);

            while($row = mysql_fetch_assoc($result)){
                $data[$num] = $row;
                if(mb_strlen($row['name'])>$name_length){
                    $data[$num]['name'] = mb_substr($row['name'], 0, $name_length)."...";
                }
		if($row['img']==""){
			$row['img'] = "<img src='http://133.242.210.48/cnpointdig/img/no_image.png'>";
		}
                $data[$num]['img'] = str_replace("<img", "<img width='90'", $row['img']);
                if($row['amount']){
                    $data[$num]['amount'] = '￥'.number_format($row['amount']);
                } else {
                    $data[$num]['amount'] = '無料';
                }
		//$data[$num]['point'] = floor($row['unit_price']*DEFAULT_RATE);
		$data[$num]['point'] = $row['timesale_point'];
		$data[$num]['action'] = $action_string[$row['action_point']];
                $data[$num]['time'] =   date("Y/m/d H:i:00", strtotime($data[$num]['end_time']));

		if($row['start_time']<$now_time && $row['end_time']>$now_time && $row['status']==1 && $row['deleted']==0){
			$data[$num]['view'] = 1;
		} else {
			$data[$num]['view'] = 0;
		}

                $num++;
            }

             return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    public function getAGPromotions($type){//端末用
        try {
	    $login_id = $_POST['login_id'];

            $item_lists = array();
            $item_lists['point'] = 0;

                //表示させる案件の件数を計算
                if(!isset($_POST['add'])){
                    $add=0;
                } else{
                    $add=$_POST['add'];
                }
                $begin =LINE_NUMBER*$add;
                $end = LINE_NUMBER;
                $limit = "limit ".$begin.",".$end;


            $off_advertisements = "";
	    if(isset($_POST['login_id']) && $_POST['login_id']!=""){
	        if(!$user_id = $this->getUserID($_POST['login_id'])){
        	    throw new Exception("ユーザー検索失敗1");
            	}
                $off_advertisements_array = $this->getConversionItems($user_id);
                if($off_advertisements_array){
                    foreach($off_advertisements_array as $value){
			if($value['campany_type']!=1){
                        	$off_advertisements .= " AND NOT name='".mysql_real_escape_string($value['name'])."' ";
			}
                    }
                }
	    }

                //カテゴリーの選定
                $advertisement_type=$this->selectCategory($type);
                $sql = "
                        SELECT
                                serialize_data,
                                margin,
                                point,
                                price,
                                advertisement_id,
                                advertisement_key,
                                advertisement_type,
                                outcome_id,
                                name,
                                img_url,
                                campany_type,
                                landing_url,
                                package_name,
                                priority,
                                market,
                                UNIX_TIMESTAMP('".date("Y-m-d H:i:s")."')-UNIX_TIMESTAMP(newly_arrived) as  time_subtraction
                        FROM
                                master_advertisements
                        WHERE
                                status = 1 AND
                                deleted = 0 AND
                                start_time <= '".date("Y-m-d H:i:s")."' AND
                                end_time >= '".date("Y-m-d H:i:s")."'
                                ".$off_advertisements."
                                ".$advertisement_type."
                        ORDER BY priority DESC, newly_arrived DESC ".$limit.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }

            $result_count = mysql_num_rows($result);
            if($result_count == 0){
            //JSON掃出し
            $this->displayJsonArray($item_lists);
                exit("");//案件が0の場合空で返す
            }

            $data = array();
            $num = 0;
            $obj = new AddSummaries();
            while($row = mysql_fetch_assoc($result)){
/*
                //成果済みの案件か検索
                if(!$outcome_data=$this -> searchOutcomeData($login_id,$row['advertisement_id'],$row['outcome_id'],$row['campany_type'])){
                    $item_lists[$num]['outcome_data'] = 0;//成果済みでない
                } else if($outcome_data==1) {
                    $item_lists[$num]['outcome_data'] = 1;//成果済
                } else if($outcome_data==2 && $row['campany_type'] == 8 && $row['campany_type'] == 9){
                    $item_lists[$num]['outcome_data'] = 2;//承認待ち
                } else if($outcome_data==3) {
                    $item_lists[$num]['outcome_data'] = 3;//未起動
                } else if($outcome_data==9) {
                    $item_lists[$num]['outcome_data'] = 9;//応募不可
                } else {
                    $item_lists[$num]['outcome_data'] = 0;
                }
*/
                //案件情報セット
                $data = unserialize($row['serialize_data']);
                $item_lists[$num]['market'] = $data['market'];
                $item_lists[$num]['url'] = $row['package_name'];
                $item_lists[$num]['advertisement_id'] = $row['advertisement_id'];
                $item_lists[$num]['advertisement_key'] = $row['advertisement_key'];
                $item_lists[$num]['advertisement_type'] = $row['advertisement_type'];
                $item_lists[$num]['name'] = stripslashes($row['name']);
                $item_lists[$num]['point'] = number_format($obj -> castPoint($row));
                $item_lists[$num]['img_url'] = $row['img_url'];
                $item_lists[$num]['campany_type'] = $row['campany_type'];

                //ランディングURLと案件詳細画面の種類を選定
                $tmp=$this->setLandingUrlAndItemDetailType($row, $login_id);
                $item_lists[$num]['landing_url'] =$tmp['landing_url'];

                //案件に付けるラベルの種類を決める
                if($row['priority'] > PRIORITY_LABEL){
                    $item_lists[$num]['label'] = 1;//おすすめ
                } else if($row['time_subtraction'] < NEWLY_ARRIVED_TIME){
                    $item_lists[$num]['label'] = 2;//新着
                } else {
                    $item_lists[$num]['label'] = 0;//なにもなし
                }

                    $item_lists[$num]['advertisement_name'] = mb_substr(stripslashes($data['name']),0,12);
                    $item_lists[$num]['item_detail'] =$tmp['item_detail'];

                    //案件のラベルの情報をセット
                    switch($row['market']){
                        case 3://DOCOMO
                            $item_lists[$num]['status_text'] = DOCOMO_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = RED_COLOR_CORD;
                            break;
                        case 4://スマパス
                            $item_lists[$num]['status_text'] = AU_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = ORANGE_COLOR_CORD;
                            break;
                        case 6://カード発行案件
                            $item_lists[$num]['status_text'] = CARD_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = CARD_COLOR_CORD;
                            break;
                        default:
                            $item_lists[$num]['status_text'] = "";
                            $item_lists[$num]['status_color'] = "";
                    }

                    if(!$item_lists[$num]['point']){
                        $item_lists[$num]['status_text'] = NONINCENTIVE_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = GRAY_COLOR_CORD;
                    }
                $num++;
            }

            if(!$login_key = $this -> getLoginKey($login_id = $_POST['login_id'])){
                throw new Exception("ユーザーkey取得失敗");
            } else{
                $item_lists['point'] = $this -> getTotalPoints($login_id,$login_key);
		if(is_numeric($item_lists['point'])){
			$item_lists['point'] = number_format($item_lists['point']);
		}
            }

            //JSON掃出し
            $this->displayJsonArray($item_lists);


        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    public function getAGTopPromotions(){//端末用
        try {
            $login_id = $_POST['login_id'];

            $item_lists = array();

            $off_advertisements = "";
            if(isset($_POST['login_id']) && $_POST['login_id']!=""){
                if(!$user_id = $this->getUserID($_POST['login_id'])){
                    throw new Exception("ユーザー検索失敗1");
                }
                $off_advertisements_array = $this->getConversionItems($user_id);
                if($off_advertisements_array){
                    foreach($off_advertisements_array as $value){
                       $off_advertisements .= " AND NOT name='".mysql_real_escape_string($value['name'])."' ";
                    }
                }
            }

                $sql = "SELECT
                                serialize_data,
                                margin,
                                point,
                                price,
                                advertisement_id,
                                advertisement_key,
                                advertisement_type,
                                outcome_id,
                                name,
                                img_url,
                                campany_type,
                                landing_url,
                                package_name,
                                priority,
                                market,
                                UNIX_TIMESTAMP('".date("Y-m-d H:i:s")."')-UNIX_TIMESTAMP(newly_arrived) as  time_subtraction
                        FROM
                                master_advertisements
                        WHERE
                                status = 1 AND
                                deleted = 0 AND
                                start_time <= '".date("Y-m-d H:i:s")."' AND
                                end_time >= '".date("Y-m-d H:i:s")."'
                                ".$off_advertisements."
                        ORDER BY priority DESC, newly_arrived DESC LIMIT 3;";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }

            $result_count = mysql_num_rows($result);
            if($result_count == 0){
            //JSON掃出し
            $this->displayJsonArray($item_lists);
                exit("");//案件が0の場合空で返す
            }

            $data = array();
            $num = 0;
            $obj = new AddSummaries();
            while($row = mysql_fetch_assoc($result)){
                //案件情報セット
                $data = unserialize($row['serialize_data']);
                $item_lists[$num]['market'] = $data['market'];
                $item_lists[$num]['url'] = $row['package_name'];
                $item_lists[$num]['advertisement_id'] = $row['advertisement_id'];
                $item_lists[$num]['advertisement_key'] = $row['advertisement_key'];
                $item_lists[$num]['advertisement_type'] = $row['advertisement_type'];
                $item_lists[$num]['name'] = stripslashes($row['name']);
                $item_lists[$num]['point'] = number_format($obj -> castPoint($row));
                $item_lists[$num]['img_url'] = $row['img_url'];
                $item_lists[$num]['campany_type'] = $row['campany_type'];

                //ランディングURLと案件詳細画面の種類を選定
                $tmp=$this->setLandingUrlAndItemDetailType($row, $login_id);
                $item_lists[$num]['landing_url'] =$tmp['landing_url'];

                $item_lists[$num]['advertisement_name'] = mb_substr(stripslashes($data['name']),0,12);
                $item_lists[$num]['item_detail'] =$tmp['item_detail'];

                $num++;
            }

            return $item_lists;

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    public function getAllAGPromotions($arr){//管理画面用
        try {
            $item_lists = array();

                //表示させる案件の件数を計算
                if(!isset($arr['add'])){
                    $add=0;
                } else{
                    $add=$arr['add'];
                }
                $begin = 100 * $add;
                $end = 100;
                $limit = "limit ".$begin.",".$end;

                //カテゴリーの選定
                $sql = "
                        SELECT
				*
                        FROM
                                master_advertisements
                        WHERE
                               advertisement_type in (2,4) AND price=0 AND market=1
                        ORDER BY start_time DESC ".$limit.";";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }

            $result_count = mysql_num_rows($result);

            $data = array();
            $num = 0;
            $obj = new AddSummaries();
            while($row = mysql_fetch_assoc($result)){
                $data = unserialize($row['serialize_data']);
                $item_lists[$num]['market'] = $data['market'];
                $item_lists[$num]['url'] = $row['package_name'];
                $item_lists[$num]['advertisement_id'] = $row['advertisement_id'];
                $item_lists[$num]['advertisement_key'] = $row['advertisement_key'];
                $item_lists[$num]['advertisement_type'] = $row['advertisement_type'];
                $item_lists[$num]['name'] = stripslashes($row['name']);
                $item_lists[$num]['point'] = $obj -> castPoint($row);
                $item_lists[$num]['img_url'] = $row['img_url'];
                $item_lists[$num]['campany_type'] = $row['campany_type'];

                //ランディングURLと案件詳細画面の種類を選定
                $item_lists[$num]['landing_url'] =$tmp['landing_url'];

                //案件に付けるラベルの種類を決める
                if($row['priority'] > PRIORITY_LABEL){
                    $item_lists[$num]['label'] = 1;//おすすめ
                } else if($row['time_subtraction'] < NEWLY_ARRIVED_TIME){
                    $item_lists[$num]['label'] = 2;//新着
                } else {
                    $item_lists[$num]['label'] = 0;//なにもなし
                }

                    $item_lists[$num]['advertisement_name'] = mb_substr(stripslashes($data['name']),0,12);
                    $item_lists[$num]['item_detail'] =$tmp['item_detail'];

                    //案件のラベルの情報をセット
                    switch($row['market']){
                        case 3://DOCOMO
                            $item_lists[$num]['status_text'] = DOCOMO_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = RED_COLOR_CORD;
                            break;
                        case 4://スマパス
                            $item_lists[$num]['status_text'] = AU_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = ORANGE_COLOR_CORD;
                            break;
                        case 6://カード発行案件
                            $item_lists[$num]['status_text'] = CARD_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = CARD_COLOR_CORD;
                            break;
                        default:
                            $item_lists[$num]['status_text'] = "";
                            $item_lists[$num]['status_color'] = "";
                    }

                    if(!$item_lists[$num]['point']){
                        $item_lists[$num]['status_text'] = NONINCENTIVE_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = GRAY_COLOR_CORD;
                    }
                $num++;
            }

		return $item_lists;

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }


    public function getAGPromotionsUM(){
        try {
            $type=1; 

            $item_lists = array();

                // 成果済み案件
//                $off_advertisements_array = $this->getConversionItems($user_id);
                $off_advertisements = "";
                //CreateLog::putDebugLog(print_r($off_advertisements_array, true));

/*
                if($off_advertisements_array){
                    foreach($off_advertisements_array as $value){
                        $off_advertisements .= "AND NOT (campany_type=".$value['campany_type']." AND advertisement_key='".$value['advertisement_key']."' ) ";
                    }
                }
*/
                //カテゴリーの選定
                $advertisement_type=$this->selectCategory($type);
                $sql = "
                        SELECT
                                serialize_data,
				margin,
                                point,
                                price,
                                advertisement_id,
                                advertisement_key,
                                advertisement_type,
                                outcome_id,
                                name,
                                img_url,
                                campany_type,
                                landing_url,
                                package_name,
                                priority,
                                market,
                                UNIX_TIMESTAMP('".date("Y-m-d H:i:s")."')-UNIX_TIMESTAMP(newly_arrived) as  time_subtraction
                        FROM
                                master_advertisements
                        WHERE
                                status = 1 AND
                                deleted = 0 AND
                                start_time <= '".date("Y-m-d H:i:s")."' AND
                                end_time >= '".date("Y-m-d H:i:s")."'
                                ".$off_advertisements."
                                ".$advertisement_type."
                        ORDER BY priority DESC, newly_arrived DESC;";
                        

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }

            $result_count = mysql_num_rows($result);
            if($result_count == 0){
            //JSON掃出し
            $this->displayJsonArray($item_lists);
                exit("");//案件が0の場合空で返す
            }

            $data = array();
            $num = 0;
            $obj = new AddSummaries();
            while($row = mysql_fetch_assoc($result)){
/*
                //成果済みの案件か検索
                if(!$outcome_data=$this -> searchOutcomeData($login_id,$row['advertisement_id'],$row['outcome_id'],$row['campany_type'])){
                    $item_lists[$num]['outcome_data'] = 0;//成果済みでない
                } else if($outcome_data==1) {
                    $item_lists[$num]['outcome_data'] = 1;//成果済
                } else if($outcome_data==2 && $row['campany_type'] == 8 && $row['campany_type'] == 9){
                    $item_lists[$num]['outcome_data'] = 2;//承認待ち
                } else if($outcome_data==3) {
                    $item_lists[$num]['outcome_data'] = 3;//未起動
                } else if($outcome_data==9) {
                    $item_lists[$num]['outcome_data'] = 9;//応募不可
                } else {
                    $item_lists[$num]['outcome_data'] = 0;
                }
*/
                //案件情報セット
                $data = unserialize($row['serialize_data']);
                $item_lists[$num]['market'] = $data['market'];
                $item_lists[$num]['url'] = $row['package_name'];
                $item_lists[$num]['advertisement_id'] = $row['advertisement_id'];
                $item_lists[$num]['advertisement_key'] = $row['advertisement_key'];
                $item_lists[$num]['advertisement_type'] = $row['advertisement_type'];
                $item_lists[$num]['name'] = stripslashes($row['name']);
                $item_lists[$num]['point'] = $obj -> castPoint($row);
                $item_lists[$num]['img_url'] = $row['img_url'];
                $item_lists[$num]['campany_type'] = $row['campany_type'];

                //ランディングURLと案件詳細画面の種類を選定
                $tmp=$this->setLandingUrlAndItemDetailType($row, $_GET['login_id']);// 12345:login_id
                $item_lists[$num]['landing_url'] =$tmp['landing_url'];

                //案件に付けるラベルの種類を決める
                if($row['priority'] > PRIORITY_LABEL){
                    $item_lists[$num]['label'] = 1;//おすすめ
                } else if($row['time_subtraction'] < NEWLY_ARRIVED_TIME){
                    $item_lists[$num]['label'] = 2;//新着
                } else {
                    $item_lists[$num]['label'] = 0;//なにもなし
                }
                //案件のラベルの情報をセット
                switch($row['market']){
                    case 3://DOCOMO
                        $item_lists[$num]['status_text'] = DOCOMO_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = RED_COLOR_CORD;
                        break;
                    case 4://スマパス
                        $item_lists[$num]['status_text'] = AU_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = ORANGE_COLOR_CORD;
                        break;
                    case 6://カード発行案件
                        $item_lists[$num]['status_text'] = CARD_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = CARD_COLOR_CORD;
                        break;
                    default:
                        $item_lists[$num]['status_text'] = "";
                        $item_lists[$num]['status_color'] = "";
                }

                $item_lists[$num]['advertisement_name'] = stripslashes($data['name']);
                if(mb_strlen($item_lists[$num]['advertisement_name'])>13){
                    $item_lists[$num]['advertisement_name'] = mb_substr($item_lists[$num]['advertisement_name'], 0, 13)."...";
                }
 
                    $item_lists[$num]['item_detail'] =$tmp['item_detail'];

                    if(!$item_lists[$num]['point']){
                        $item_lists[$num]['status_text'] = NONINCENTIVE_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = GRAY_COLOR_CORD;
                    }
                $num++;
            }
            return $item_lists;

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }



    /**
    * 案件のカテゴリ選定
    *
    * @accsess private
    * @param type
    *
    * @return string
    */
    private function selectCategory($type){
        /*
        *  type=1 : 全案件
        *  type=2 : au スマパス
        *  type=3 : スゴ得
        *  type=4 : 会員登録
        *  type=5 : 高単価
        *  type=6 : カード発行案件
        */
        switch($type){
            case 1:
                $advertisement_type = "AND advertisement_type in (2,4) AND price = 0 AND market = 1";
                break;
            case 2:
                $advertisement_type = "AND market = 4 ";
                break;
            case 3:
                $advertisement_type = "AND market = 3";
                break; 
            case 4:
                $advertisement_type = "AND advertisement_type in (1,3)";
                break;
            case 5:
                $advertisement_type = "AND point > 200";
                break;
            case 6:
                $advertisement_type = "AND market = 6 ";
                break;
            default:
                $advertisement_type = "";
        }
        return $advertisement_type;     
    }
    /**
    * ランディングURL生成
    *
    * @accsess private
    * @param array row  login_id
    *
    * @return landing_url
    */    
    private function setLandingUrlAndItemDetailType($row,$login_id){
        $code = Utils::str_encrypt($login_id);
        switch($row['campany_type']){
                     case 8://HangOut
                            $item_lists['item_detail'] = 0;//ネイティブ
                            //$item_lists['landing_url'] = str_replace("&uq=", "&pt=", $row['landing_url']).$code;
                            $item_lists['landing_url'] = $row['landing_url'].$code."&pt=".$code;
                            break;
                     case 7://ADコネクト
                            $item_lists['item_detail'] = 0;//ネイティブ
                            $item_lists['landing_url'] = $row['landing_url'];
                            if(strstr($row['landing_url'], "?")){
                                $item_lists['landing_url'] .= "&";
                            } else {
                                $item_lists['landing_url'] .= "?";
                            }
                            $item_lists['landing_url'] .= "suid=".$code;
                            break;
                    case 6://CAReward
                            $crypt = sha1($code.API_KEY);
                            $item_lists['item_detail'] =1;//WEB
                            $item_lists['landing_url'] = $row['landing_url'].$code."&crypt=".$crypt;
                            break;
                    case 5://ZucksAffiliate
                            $item_lists['item_detail'] =0;//ネイティブ
                            $verify =hash("sha256",$code."2b6821d4f119434606a8913ee99e4e7e");
                            //$item_lists['landing_url'] = $row['landing_url']."&uid=".$login_id."&did=".$_POST['zucks_uuid']."&v=".$verify;
                            $item_lists['landing_url'] = $row['landing_url']."&uid=".$code."&v=".$verify;
                            break;
                    case 4://Glossom
                            $item_lists['item_detail'] =1;//WEB
                            $hash=hash("sha256",$row['advertisement_key'].";".$code.";1949;1fcd219cbd31d733e66202c1572b07b2");
                            $item_lists['landing_url'] = $row['landing_url']."&campaign_id=".$row['advertisement_key']."&identifier=".$code."&digest=".$hash;
                            break;
                    case 3://AppDriver
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] = $row['landing_url'].$code;
                            break;
                    case 2://SmaAD
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] = $row['landing_url']."&u=".$code;
/*
                            $hash=hash("sha256",$row['advertisement_key'].";".$code.";745;38547e343f63729fa0e9bc3890973a48");
                            $campaign_id = "&campaign_id=".$row['advertisement_key'];
                            $digest="&digest=".$hash;
                            $item_lists['landing_url'] = $row['landing_url'].$campaign_id.$identifer.$digest;
*/
                            break;
                    case 1://affitown(未使用)
                            $item_lists['item_detail'] =1;//WEB
                            $item_lists['landing_url'] = $row['landing_url'];
                            if(strstr($row['landing_url'], "?")){
                                $item_lists['landing_url'] .= "&u1=".$code;
                            } else {
                                $item_lists['landing_url'] .= "?u1=".$code;
                            }
                            break;
                    default://一応用意
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] ="";
                            break;
        }    
        return $item_lists;
    }

    public function getAGPromotion($advertisement_id){
        try {
            $obj = new AddSummaries();

            $sql = "SELECT
                        *
                    FROM 
                        master_advertisements 
                    WHERE
                        advertisement_id = ".$advertisement_id." LIMIT 1;";

            if(!$result = mysql_query($sql)){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                return false;
            }
            $row = mysql_fetch_assoc($result);
            //$row['point'] = floor($row['point']*DEFAULT_RATE);
            $data = unserialize($row['serialize_data']);
            $row['remark'] = $data['remark'];
            $row['detail'] = $data['detail'];
            $row['point'] = $obj -> castPoint($row);
            //$row['point'] = $row['point']*10;
            $tmp=$this->setLandingUrlAndItemDetailType($row, $_POST['login_id']);// 12345:login_id
            $row['landing_url'] =$tmp['landing_url'];

            return $row;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    private function getConversionItems($user_id){
        try{
            if(!$user_id){
                return "";
            }
            $sql = "SELECT
                        ma.name
                    FROM
                        data_summaries ds,
                        master_advertisements ma
                    WHERE
                        ds.user_id=".$user_id." AND
                        ds.campany_type>1 AND
                        ma.advertisement_id=ds.advertisement_id AND
                        ds.status in (2,9);";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                    throw new Exception(mysql_error());
            }
            while($row = mysql_fetch_assoc($result)){
                $data[] = $row;
            }

            return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return "";
        }
    }

}

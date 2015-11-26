<?php
/**
* RegisterAdvertisementsクラス
* 案件を広告テーブルに入れる
*
* @package RegisterAdvertisements
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: RegisterAdvertisements.php,v 1.0 2013/2/9Exp $
*/
Class RegisterAdvertisements extends CommonBase {
    //メール内容に添える案件名を格納する配列
     public $advertisementName;
     public $returnAdvertisements;
     public $return_advertisements;
     /*
     * コンストラクタ
     */
     public function __construct(){
        try {
             global $resume_cnt;
             global $cnt;
             global $mail_flg;//trueならメールを送る
             $this->advertisementName = "";
             $this->return_advertisements = "";
             $cnt = 0;
             $resume_cnt=0;
             $mail_flg = false;
             $this->returnAdvertisements="";
           if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
     }
     /**
     * 画像urlからgif画像を保存する
     *
     * @accsess public
     * @return boolean
     */
     public function saveImage($url,$path){
        try{
            //初期化
            $ch = curl_init($url);
            //出力先ファイル名
            $fp = fopen(IMAGE_PASS.$path.".gif", "w");
            //オプション設定
            curl_setopt($ch, CURLOPT_FILE, $fp);//ファイル出力
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);//リファラー設定
            curl_setopt($ch, CURLOPT_HEADER, false);//ヘッダー出力抑制
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//リダイレクト時その先を取得
            curl_setopt($ch, CURLOPT_USERAGENT, true);//ユーザーエージェント付与
            //実行
            $response = curl_exec( $ch );
            if( !$response ){
                throw new Exception(curl_error( $ch ));
            }
            //終了
            curl_close($ch);
            } catch(Exception $e) {
                CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            }
     }
     /**
     * 各リワードベンダーの案件をDBに入みプッシュ通知とメールをそうしんする
     *
     * @accsess public
     */
     public function getOfferWall(){
         try{
            global $resume_cnt;
            global $cnt;
            global $mail_flg;
            if(!$this->getAppDriverApps()){
                CreateLog::putErrorLog("Redister Error AppDriver Apps");
            } 
            if(!$this->getSmaADApps()){
                CreateLog::putErrorLog("Redister Error SmaAD Apps");
            }
            if(!$this->getZucksApps()){
                CreateLog::putErrorLog("Redister Error Zucks Apps");
            }
            if(!$this->getMacroLineApps()){
                CreateLog::putErrorLog("Redister Error MacroLine Apps");
            }
            if(!$this->getADConnectApps()){
                CreateLog::putErrorLog("Redister Error ADConnect Apps");
            }

         } catch(Exception $e){
             CreateLog::putErrorLog(get_class()." ".$e->getMessage());
         }
     }
     /**
     * GMOTECHの案件をmaster_Advertisementsテーブルに入れる
     * 
     * @accsess public
     */
     public function getSmaADApps(){
        try{
            global $resume_cnt;
            global $cnt;
            global $mail_flg;

            $now_time = date("Y-m-d H:i:s");
	    $zone_id = "53561440"; // dig
//	    $zone_id = ""; // portal
            $url ='http://ad.smaad.jp/api/iteminfo/v2/getListDetail.php?zone_id='.$site_id;
            $xml = simplexml_load_file($url);
            $arr = json_decode(json_encode($xml), true); 

            foreach($arr['Items']['Item'] as $key =>$value){
                $tmp['advertisement_key'] = $value['AdId'];
                $tmp['campany_type'] = 2;
                $tmp['campany_name'] = "GMOTECH";
                $tmp['package_name'] = "";
                $tmp['name'] = $value['Title'];
                $tmp['margin'] = $value['Net'];
                $tmp['point'] = $value['Net'];
                $tmp['price'] = $value['Price'];
                $tmp['outcome_id'] = 0;//今の所成果地点は一つのみ
                if(empty($value['Icon'])){
                    $tmp['img_url'] ="";
                } else{
                    $tmp['img_url'] = $value['Icon'];
                }
                $tmp['landing_url'] = $value['Link'];
                if($value['Type'] == "dl"){
                    $tmp['os_id'] = 1;
                    $tmp['advertisement_type_string'] = "アプリ";
                    $tmp['newly_arrived'] = "0000-00-00 00:00:00" ;//即時反映はしない
                    $tmp['advertisement_type'] = 2;
                    $tmp['status'] = 1;
                    $tmp['market'] = 1;//とりあえず全部googleplay
                    $tmp['priority'] =23;
                } else {
                    $tmp['os_id'] = 0;
                    $tmp['advertisement_type_string'] = "WEB";
                    $tmp['advertisement_type'] = 1;
                    $tmp['market']=1;
                    $tmp['status'] = 1;
                    $tmp['priority'] =15;
                }
                $tmp['start_time'] = $now_time;
                $tmp['end_time'] = date("Y-m-d H:i:s",strtotime("+1 month"));
                //シリアライズデータの代入
                $serialize_data['detail'] = $value['Description'];
                $serialize_data['name'] = $value['Condition'];
                $serialize_data['adjustment_data'] = 0;
                $serialize_data['url'] = "";
                $serialize_data['remark'] = $value['Condition']; 
                //end
                
                $tmp['serialize_data'] = mysql_real_escape_string(serialize($serialize_data));
                $tmp['created'] = $now_time;
                $tmp['updated'] = $now_time;
		if($tmp['advertisement_key']!="56891831"){//焼酎削除
                	$this->actionAdvertisements($tmp);
		}
            }
            //終了案件の削除
            if(!$this -> deleteAdvertisement(2)){
                throw new Exception("deleteAdvertisement失敗");
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class. " ".$e->getMessage);
            return false;
        }

     }
     /**
     * AppDriverの案件をmaster_advertisementsテーブルに入れる
     *
     * @accsess public
     * @return boolean
     */
     public function getAppDriverApps(){ 
        try{
            global $resume_cnt;
            global $cnt;
            global $mail_flg;

	    $site_id = 19420; // dig
//	    $site_id = 26773; // portal
	    $media_id = 3804; // dig
//	    $media_id = 4248; // portal
	    $site_key = "f3b5dc376c4771f217aeef8545c8d5e0"; // dig
//	    $site_key = "fe981a3212b52145ba3b24aa6cbf8d1c"; // portal

            $url = 'http://ads.appdriver.jp/4.1.'.$site_id.'ads?media_id='.$media_id.'&digest='.hash("sha256",$media_id.":".$site_key);
            //$url = 'http://ads.appdriver.jp/4.1.19420ads?media_id=3804&digest=c9d2f22b19472bc094bf3a465ff6e3c804b733aad9ff0532fedd13a752689a91';//digest= hash("sha256",$media_id.":".$site_key);

            $xml = simplexml_load_file($url);
            $arr = json_decode(json_encode($xml), true);
            $create_rand = new CreateRand();
            foreach($arr['campaign'] as $value){
                $flg = false;
                foreach($value['advertisement'] as $key2 => $value2){
                    if(is_numeric($key2)){
                        $tmp = array();
                        foreach($value2 as $key3 => $value3){
                            $tmp['outcome_id'] = $value2['id'];
                            $tmp['margin'] = $value2['payment'];
                            $tmp['point'] = $value2['point'];
                            $tmp['requisite'] =$value2['requisite'];
                            $data['name'] = $value2['name'];
                        }
                        $tmp['advertisement_key'] = $value['id'];
                        $tmp['name'] = $value['name'];
                        $tmp['price'] = $value['price'];
                        $tmp['landing_url'] = $value['location'];
                        $tmp['start_time'] = $value['start_time'];
                        $tmp['end_time'] = $value['end_time'];
                        $tmp['campany_type'] = 3;
                        $tmp['campany_name'] = "AppDriver";
                        if(is_array($value['market'])){
                            $tmp['market'] = 5;
                        } else{
                            $tmp['market'] = $value['market'];
                        }
                        //マーケットがグーグルプレイのみパ>ッケージ名を抜き取る。
                        if(!empty($value['url']) && $tmp['market'] == 1 && $value['url'] != ""){
                            $parse = parse_url($parse['url']);
                            if(!empty($parse['query']) && isset($parse['query'])){
                                parse_str($parse['query']);
                                if(!empty($id) && isset($id) ){
                                    //パッケージは空にしとく
                                  //  $tmp['package_name'] = "";
                                    $tmp['package_name'] = $id;
                                } else{
                                    $tmp['package_name'] = "";
                                }
                            } else{
                                $tmp['package_name'] = "";
                            }
                        } else{
                            $tmp['package_name'] = "";
                        }
                        //シリアライズデータの格納
                        $data['detail'] = $value['detail'];
                        $data['remark'] = $value['remark'];
                        $data['adjustment_data'] = 0;
                        //案件の種類を振り分けている
                        if($value['platform']==1){
                            $tmp['advertisement_type'] = 3;
                            $tmp['advertisement_type_string']="WEB";
                            $tmp['status']=0;
                            $tmp['priority'] =15;
                            $tmp['newly_arrived']="0000-00-00 00:00:00";
                            //gif画像を保存
                            $this -> saveImage($value['icon'],$tmp['advertisement_key']);
                            //$tmp['img_url'] = 'http://133.242.210.48/cnpointdig/img/app/'.$tmp['advertisement_key'].'.gif';
                            $tmp['img_url'] = MAIN_URL.'img/app/'.$tmp['advertisement_key'].'.gif';
                        } else if($value['platform']==3){
                            $tmp['advertisement_type'] = 4;
                            $tmp['advertisement_type_string']="アプリ";
                            $tmp['status']=1;
                            if($tmp['market']==1 && $tmp['requisite']== 15){
                                $tmp['priority'] =23;
				$tmp['os_id'] = 1;
                            } else{
                                $tmp['priority'] =15;
				$tmp['os_id'] = 0;
                            }
			    if(strstr($data['detail'], 'スマートパス')){
				$tmp['status']=0;
			    }
                            $tmp['newly_arrived']=date("Y-m-d H:i:s");
                            $tmp['img_url'] = $value['icon'];
                        }
                        $tmp['serialize_data'] = mysql_real_escape_string(serialize($data));
                        $this->actionAdvertisements($tmp);
                    } else{
                        $tmp['outcome_id'] = $value['advertisement']['id'];
                        $tmp['margin'] = $value['advertisement']['payment'];
                        $tmp['point'] = $value['advertisement']['point'];
                        $data['name'] = $value['advertisement']['name'];
                        $tmp['requisite'] =$value['advertisement']['requisite']; 
                        $flg = true;
                    }
                }
                if($flg){
                        $tmp['advertisement_key'] = $value['id'];
                        $tmp['name'] = $value['name'];
                        $tmp['price'] = $value['price'];
                        $tmp['landing_url'] = $value['location'];
                        $tmp['start_time'] = $value['start_time'];
                        $tmp['end_time'] = $value['end_time'];
                        $tmp['campany_type'] = 3;
                        $tmp['campany_name'] = "AppDriver";
                        $data['url'] = "";
                        if(is_array($value['market'])){
                            $tmp['market'] = 5;
                        } else{
                            $tmp['market'] = $value['market'];
                        }
			$id="";
                        //マーケットがグーグルプレイのみパ>ッケージ名を抜き取る。
                        if(!empty($value['url']) && $tmp['market'] == 1 && $value['url'] != ""){
                            $parse = parse_url($value['url']);
                            if(!empty($parse['query']) && isset($parse['query'])){
                                parse_str($parse['query']);
                                if(!empty($id) && isset($id) ){
                                    //パッケージは空にしとく
                                  //  $tmp['package_name'] = "";
                                    $tmp['package_name'] = $id;
                                } else{
                                    $tmp['package_name'] = "";
                                }
                            } else{
                                $tmp['package_name'] = "";
                            }
                        } else{
                            $tmp['package_name'] = "";
                        }
                        //シリアライズデータの格納
                        $data['detail'] = $value['detail'];
                        $data['remark'] = $value['remark'];
                        $data['adjustment_data'] = 0;
                        //案件の種類を振り分けている
                        if($value['platform']==1){
                            $tmp['advertisement_type'] = 1;
                            $tmp['advertisement_type_string']="WEB";
                            $tmp['status']=0;
                            $tmp['priority'] =15;
                            $tmp['newly_arrived']="0000-00-00 00:00:00";
                            //gif画像を保存
                            $this -> saveImage($value['icon'],$tmp['advertisement_key']);
                            //$tmp['img_url'] = 'http://133.242.210.48/cnpointdig/img/app/'.$tmp['advertisement_key'].'.gif';
                            $tmp['img_url'] = MAIN_URL.'img/app/'.$tmp['advertisement_key'].'.gif';
                        } else if($value['platform']==3){
                            $tmp['advertisement_type'] = 2;
                            $tmp['advertisement_type_string']="アプリ";
                            $tmp['status']=1;
                            if($tmp['market'] ==1 && $tmp['requisite']== 15){
                                $tmp['priority'] =23;
                            }else{
                                $tmp['priority'] =15;
                            }
			    if(strstr($data['detail'], 'スマートパス')){
				$tmp['status']=0;
			    }
                            $tmp['newly_arrived']=date("Y-m-d H:i:s");
                            $tmp['img_url'] = $value['icon'];
                        }
                        $tmp['serialize_data'] = mysql_real_escape_string(serialize($data));
                        $this->actionAdvertisements($tmp);
                }
            }
             //ここでテーブル内の不要データを消去する
            if(!$this -> deleteAdvertisement(3)){
                throw new Exception("deleteAdvertisement失敗");
            }
           return true; 
             
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }   
    }


     /**
     * Zucksの案件をmaster_Advertisementsテーブルに入れる
     *
     * @accsess public
     */
     public function getZucksApps(){
        try{
            global $resume_cnt;
            global $cnt;
            global $mail_flg;
            ini_set('user_agent', 'Mozilla/5.0 (Linux; U; Android 4.0.1; ja-jp; Galaxy Nexus Build/ITL41D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
            $url = "http://get.mobu.jp/api/ads/3.1/?pcode=dig&it=icv&device=android&country=JP" ; //dig
            $json = file_get_contents($url);
            $arr = json_decode($json, true);
            foreach($arr['adSearch']['ads']['ad'] as $key => $value ){
                $tmp['advertisement_key'] = $value['acode'];
                $tmp['name'] = $value['title'];
                if($value['type'] == "app"){
                    $tmp['advertisement_type'] = 2;
                    $tmp['advertisement_type_string'] = "アプリ";
                    $tmp['status'] = 1;
                    $tmp['newly_arrived'] ="0000-00-00 00:00:00";
                    if($value['other_type'] == "auSmartPass"){
                        $tmp['market'] = 4;
                    	$tmp['status'] = 0;
                    } else {
                        $tmp['market'] = 1;
                    }
                } else{
                    $tmp['advertisement_type'] = 1;
                    $tmp['advertisement_type_string'] = "WEB";
                    $tmp['market'] = 5;
                    $tmp['status'] = 0;
                    $tmp['newly_arrived'] ="0000-00-00 00:00:00";
                }
                $tmp['campany_type'] = 5;
                $tmp['campany_name'] = "ZucksAffiliate";
                $tmp['priority'] = 23;
                $tmp['package_name'] = $value['app_identifier'];
                $tmp['img_url'] = $value['img'];
                $tmp['landing_url'] = $value['redirecter'];
                $tmp['price'] = $value['price'];//案件の価格
                $tmp['point'] = $value['point'];
                $tmp['margin'] = $value['unit_price'];//ポイント数が報酬
                $tmp['outcome_id'] = 0;//とりあえず広告IDをいれておく
                $tmp['start_time'] = date("Y-m-d H:i:s");
                if($value['limit_date'] == "0000-00-00 00:00:00"){
                    $tmp['end_time'] = date("Y-m-d H:i:s",strtotime("+1 month"));
                } else{
                    $tmp['end_time'] = $value['limit_date'];
                }
                //シリアライズ
                $data['adjustment_data'] = 0;
                $data['name'] = $value['result_condition'];//成果発生条件名
                $data['detail'] = $value['description'];//案件の詳細
                $data['remark'] = $value['result_condition'];//成果発生条件詳
                $data['url'] = "";
                $tmp['serialize_data'] = mysql_real_escape_string(serialize($data));//シリアライズ化
                $this->actionAdvertisements($tmp);
            }
            //終了案件の削除
            if(!$this -> deleteAdvertisement(5)){
                throw new Exception("deleteAdvertisement失敗");
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }

   /**
    * GREEの案件をmaster_Advertisementsテーブルに入れる
    *
    * @accsess public
    */
    public function getGlossomApps(){
        try{
                $site_id = "11433";
                $media_id = "1949";
                $site_key = "1fcd219cbd31d733e66202c1572b07b2";
                $now_time = date("Y-m-d H:i:s");
                $request_time = strtotime($now_time);
                $digest = hash("sha256", $site_id.':'.$request_time.':'.$site_key);
                $url = 'https://reward.gree.net/api.rest/2/p/get_campaigns.1?site_id='.$site_id.'&media_id='.$media_id.'&request_time='.$request_time.'&digest='.$digest.'&platform_id=3&lang=ja&cnt=JP';
                $json = file_get_contents($url, false, null);
//                $arr = json_decode(substr($json,3), true);
              $arr = json_decode($json, true);

                if($arr['success']==0){
                        return false;
                }
                $result_arr = $arr['result'];
                foreach($result_arr as $key => $value){
                        $tmp['advertisement_key'] = $value['campaign_id'];
                        $tmp['advertisement_type'] = 2;
                        $tmp['advertisement_type_string']="アプリ";
                        $tmp['result_key'] = $value['thanks']['0']['thanks_category'];
                        $tmp['campany_type'] = 4;
                        $tmp['campany_name'] = "Glossom";
                        $tmp['outcome_id'] = 0;
                        $tmp['margin'] = $value['thanks']['0']['media_revenue'];//成果報酬金額
                        $tmp['point'] = floor($tmp['margin']/2);
                        $tmp['name'] = $value['site_name'];
                        $tmp['img_url'] = $value['icon_url'];
                        $tmp['landing_url'] = "http://reward.gree.net/3.0.".$site_id."r?media_id=".$media_id;
                        $tmp['package_name'] = $value['market_app_id'];
                        $tmp['draft'] = $value['draft'];
                        $tmp['price'] = $value['site_price'];
                        $tmp['start_time'] = $now_time;
                        $tmp['end_time'] = date("Y-m-d H:i:s", strtotime($value['end_time']));
                        $tmp['priority'] = 23;
                        $tmp['status'] = 1;
                        $tmp['newly_arrived'] = $now_time;

                        $ca_d = false;
                        $ca_a = false;
                        $ca_s = false;
                        foreach($value['carrier'] as $key => $car_value){
                                switch($car_value){
                                        case 3://android docomo
                                                $ca_d = true;
                                                break;
                                        case 4://android au
                                                $ca_a = true;
                                                break;
                                        case 5://android softbank
                                                $ca_s = true;
                                                break;
                                }
                        }
                        if($ca_d && !$ca_a && !$ca_s){
                                $tmp['market'] = 3;
                        	$tmp['status'] = 0;
                        } else if(!$ca_d && $ca_a && !$ca_s){
                                $tmp['market'] = 4;
                        	$tmp['status'] = 0;
                        } else {
                                $tmp['market'] = 1;
                        }

                        $data['detail'] = $value['site_description']; //案件内容説明
                        $data['adjustment_data'] = 0;
                        $data['remark'] = $value['default_thanks_name'];
                        $data['name'] = $value['thanks']['0']['thanks_name'];
                        $tmp['serialize_data'] = mysql_real_escape_string(serialize($data));//シリアライズ化

                        $this->actionAdvertisements($tmp);
            }

            //終了案件の削除
            if(!$this -> deleteAdvertisement(4)){
                throw new Exception("deleteAdvertisement失敗");
            }
            return true;

        } catch(Exception $e){
            Lib_CreateLog::putErrorLog(get_class(). " ".$e->getMessage());
            return false;
        }
    }

     /**
     * ADコネクトの案件をmaster_Advertisementsテーブルに入れる
     *
     * @accsess public
     */
     public function getADConnectApps(){
        try{
            $now_time=date("Y-m-d H:i:s");
            $context = null;
            $site_id = "456"; // dig
            //$site_id = "457"; // portal

            $url = 'http://r.ad-connect.jp/ad/api/articlelist?_site='.$site_id;
            $xml = simplexml_load_file($url);
            $arr1 = json_decode(json_encode($xml), true);

            $arr = $arr1['articles']['article'];
            foreach($arr as $key => $value ){
                $tmp['advertisement_key'] = $value['article_id'];
                $tmp['campany_type'] = 7;
                $tmp['campany_name'] = "DimageShare";
                $tmp['package_name'] = "";
                $tmp['name'] = $value['article_name'];
                $tmp['margin'] = $value['price'];
                $tmp['point'] = $value['price'];
                $tmp['img_url'] = $value['icon_image_url'];
                $tmp['landing_url'] = "http://r.ad-connect.jp/ad/p/r?_site=".$site_id."&_article=".$value['article_id']."&_image=".$value['image_id'];
                $tmp['outcome_id'] = 0;
                $tmp['start_time'] = $value['start_date'];
                if(is_array($value['end_date'])){
                    $tmp['end_time'] = "2030-01-01 00:00:00";
                } else {
                    $tmp['end_time'] = $value['end_date'];
                }
                if($value['reward_article_type']==1){
                    $tmp['advertisement_type_string'] = "アプリ";
                    $tmp['advertisement_type'] = 2;
                    $tmp['market'] = 1;
                    $tmp['priority'] = 23;
                    $tmp['status'] = 1;
                } else {
                    $tmp['advertisement_type_string'] = "WEB";
                    $tmp['advertisement_type'] = 1;
                    $tmp['market'] = 5;
                    $tmp['priority'] = 15;
                    $tmp['status'] = 0;
                }
                $tmp['newly_arrived'] = "0000-00-00 00:00:00" ;//即時反映はしない
                $tmp['price'] = $value['product_price'];

                $serialize_data['name'] = $value['cv_condition'];
                $serialize_data['remark'] = $value['cv_condition'];
                 //シリアライズデータの代入
                $serialize_data['detail'] = strip_tags($value['note']);
                $serialize_data['adjustment_data'] = 0;
                //end
                $tmp['serialize_data'] = mysql_real_escape_string(serialize($serialize_data));
                $tmp['created'] = $now_time;
                $tmp['updated'] = $now_time;
                $this->actionAdvertisements($tmp);
            }
            //終了案件の削除
            if(!$this -> deleteAdvertisement(7)){
                throw new Exception("deleteAdvertisement失敗");
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }


     /**
     * CAMobileの案件をmaster_Advertisementsテーブルに入れる
     *
     * @accsess public
     */
     public function getMacroLineApps(){
        try{
            $now_time=date("Y-m-d H:i:s");
            $context = null;
	    $site_id = "BxdAjsoa"; // dig
	    $api_key = "DC1eZD9o1uUWQOwssTM9rpn28FqSBbFX"; // dig
            $url = 'http://api.macro-line.com/ads?site='.$site_id.'&key='.$api_key.'&os=2&paid=0&per_page=50'; // dig
//            $url = 'http://api.macro-line.com/ads?site='.$site_id.'&key='.$api_key.'&paid=0&per_page=50'; // portal
            $json = file_get_contents($url,false,$context);
            $arr = json_decode($json, true);

            foreach($arr['ads'] as $key => $value ){
                    $tmp['advertisement_key'] = $value['id'];
                    $tmp['campany_type'] = 8;
                    $tmp['campany_name'] = "HangOut";
                    $tmp['package_name'] = "";
                    $tmp['name'] = $value['name'];
                    $tmp['margin'] = $value['price'];
                    $tmp['point'] = $value['price'];
                    $tmp['img_url'] = $value['icon_url'];
                    $tmp['landing_url'] = $value['url'];
                    $tmp['outcome_id'] = 0;
                    $tmp['start_time'] = $value['started_on'];
                    if($value['will_stop_on']=="" || $value['will_stop_on']=="null"){
                        $tmp['end_time'] = "2030-01-01 00:00:00";
                    } else {
                        $tmp['end_time'] = $value['will_stop_on'];
                    }
                    $tmp['newly_arrived'] = "0000-00-00 00:00:00" ;//即時反映はしない
                    $tmp['status'] = 0;
                    $tmp['price'] = 0;

                    $is_au = false;
                    $is_dc = false;
                    $is_tu = false;
                    foreach($value['classes'] as $key2 => $cls_value){
                        if($cls_value=="TkfTvjjy"){
                            //auスマパス
                            $is_au = true;
                        } else if($cls_value=="rMWbmhXz"){
                            //dマーケット
                            $is_dc = true;
                        } else if($cls_value=="SbcflLJd"){
                            //スゴ得
                            $is_dc = true;
                        } else if($cls_value=="uyhXXXdH"){
                            //チュートリアル
                            $is_tu = true;
                        } else if($cls_value=="DiNHrQTC"){
                            //CPI
                        } else if($cls_value=="SyrJ3yXc"){
                            //CPA
                        }
                    }
                    if($value['is_app']==1){
                        $tmp['market'] = 1;
                        $tmp['advertisement_type'] = 2;
                        $tmp['advertisement_type_string'] = "アプリ";
                        $tmp['priority'] = 23;
                        $serialize_data['name'] = "初回起動";
                        $tmp['status'] = 1;
                    } else {
                        $tmp['market'] = 5;
                        $tmp['advertisement_type'] = 1;
                        $tmp['advertisement_type_string'] = "WEB";
                        $tmp['priority'] = 15;
                        $serialize_data['name'] = "無料会員登録";
                    }
                    if($is_au){
                        $tmp['market'] = 4;
                        $tmp['priority'] = 15;
                        $serialize_data['name'] = "初回アクセス";
                    } else if($is_dc){
                        $tmp['market'] = 3;
                        $tmp['priority'] = 15;
                        $serialize_data['name'] = "スゴ得ログイン";
                    }
                    if($is_tu){
                        $tmp['priority'] = 15;
                        $serialize_data['name'] = "チュートリアル完了";
                        $tmp['status'] = 1;
                    }

                    $serialize_data['remark'] =$value['exp_result'];
                     //シリアライズデータの代入
                    $serialize_data['detail'] = $value['exp'];
                    $serialize_data['adjustment_data'] = 0;
                    //end
                    $tmp['serialize_data'] = mysql_real_escape_string(serialize($serialize_data));
                    $tmp['created'] = $now_time;
                    $tmp['updated'] = $now_time;
                    $this->actionAdvertisements($tmp);
            }
            //終了案件の削除
            if(!$this -> deleteAdvertisement(8)){
                throw new Exception("deleteAdvertisement失敗");
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }


 
    /**
    * master_advertisementsテーブルの不要データを消去する
    * 
    * @return boolean
    */
    public function deleteAdvertisement($campany_type){
        try {
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
            $sql ="UPDATE 
                            master_advertisements 
                   SET
                            deleted = 1
                   WHERE 
                            campany_type = ".$campany_type." AND 
                            updated < now() - interval 5 minute AND   
                            created < now() - interval 5 minute ;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error()." ".$sql);
            }
             //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            } else{
                return true;
            }      
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
            return false;
        }
    }

/*
 * 既存の同一名案件との単価比較
 */
    private function sameAdvertisements($data){
        try{
            //同一名案件の中で報酬単価が最も高い物を抽出
            $sql = "SELECT
                        margin
                    FROM
                        master_advertisements
                    WHERE
                        status=1
                    AND
                        deleted=0
                    AND
                        name='".mysql_real_escape_string($data['name'])."'
                    ORDER BY margin DESC LIMIT 1;";

            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error()." ".$sql);
            }

            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                //既存案件がないときは新規登録
                return false;
            } else {
                $row = mysql_fetch_assoc($result);
                if($row['campany_type']==$data['campany_type'] && $row['advertisement_key']==$data['advertisement_key']){
                        //同一案件の場合は単価に関わらず更新
                        return false;
                } else if($row['margin']<$data['margin']){
                        //既存案件の方が低いときは
                        return false;
                } else {
                        //既存案件の方が高いとき
                        return true;
                }
            }

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

/*
 * 一旦既存の同一名案件を全て非掲載にする
 */
    private function sameAdvertisements2($data){
        try{
            //同一名案件を全て非掲載に
            $sql = "UPDATE
                        master_advertisements
                    SET
                        status=0
                    WHERE
                        status=1
                    AND
                        deleted=0
                    AND
                        name='".mysql_real_escape_string($data['name'])."';";

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
    * 案件のインサートかアップデートを行う
    *
    * @return boolean
    */
    private function actionAdvertisements($tmp){
        try {

            //追記分
            if($this -> sameAdvertisements($tmp)){
                //CreateLog::putDebugLog($tmp['campany_type']." ".$tmp['name']." 不要");
                // この案件は単価が低いので登録または更新不要
                throw new Exception("");
            }
             if(!$this -> sameAdvertisements2($tmp)){
                //CreateLog::putDebugLog($tmp['campany_type']." ".$tmp['name']." 新規");
                //既存案件なし
            } else {
                //CreateLog::putDebugLog($tmp['campany_type']." ".$tmp['name']." 更新");
            }

            global $resume_cnt;
            $advertisements = new Advertisements();
           //案件がテーブル内に存在しているかチェック
            $check_data = $advertisements -> selectAdvertisements($tmp['advertisement_key'],$tmp['outcome_id'],$tmp['campany_type']);
            if($check_data == false){
                echo "\n[".$tmp['campany_name']."]insert>>".$tmp['name'];
                if(!$advertisements -> insertAdvertisements($tmp)){
                     throw new Exception('insert失敗');
                } else{
                $this->advertisementName.= "\n【".$tmp['campany_name']."】\n案件名：".$tmp['name']."\n案件タイプ：".$tmp['advertisement_type_string']."\n単価：".$tmp['point']."円";
                }
            } else {
                //案件のアップデート処理
                echo "\n[".$tmp['campany_name']."]UPDATE>>".$tmp['name'];
                //deletedフラグが立ってる案件はresume_cntに＋１する
                if($check_data['deleted']==1){
                    $resume_cnt++;
                    $this->return_advertisements .="\n".$tmp['name'];
                    if($tmp['status']==1){
                        $tmp['newly_arrived_flg'] = 1;
                    } else{
                        $tmp['newly_arrived_flg'] = 0;
                    }
                } else{
                    $tmp['newly_arrived_flg'] = 0;
                }
                $tmp['advertisement_id'] = $check_data['advertisement_id'];
                $tmp['serialize_data'] = $check_data['serialize_data'];
                $advertisements -> updateAdvertisements($tmp);
            }
            return true;
        } catch(Exception $e){
            return false;
        }
    }

}//End of class   

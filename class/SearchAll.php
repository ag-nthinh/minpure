<?php
/**
  * SearchAllクラス
  * 管理画面のデータ取得関係をまとめたクラス
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class SearchAll extends CommonBase{
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
    * 失効アカウントの全ポイント取得
    *
    * @accsess public
    * @return array
    */
    public function getAllPoint(){
        try{
            $sql = "
            SELECT
                    user_id,
                    login_id,
                    login_key,
                    point_list
            FROM
                    data_users
            WHERE
                    deleted = 1
            ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error()." ".$sql);
            }
            //全ユーザーの有効期限切れのポイントを削除して新しい月の追加
            while($row = mysql_fetch_assoc($result)){
                //$point_array = unserialize($row['point_list']);
                $total_point=$this->getTotalPoints($row['login_id'],$row['login_key']);
                if(!isset($total_point)){
                    $total_point=0;
                }
                //$array['total_point'] = $array['total_point'] + $this->getTotalPoints($row['login_id'],$row['login_key']);//トータルポイント
                $array['total_point'] = $array['total_point'] + $total_point;
            }
            return $array;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
    }
    /**
    * 履歴データ取得
    *
    * @accsess public
    * @return array
    */
    public function commissionGiftCode($present_id){
        try{
            $sql="SELECT
                    sum(p.point)*0.05 as commission
                  FROM 
                    master_presents as p ,
                    data_distributions as d 
                  WHERE
                    p.present_id = d.present_id  and
                    d.created >= '2013-08-28 12:00:00' and
                    d.status>=1 and d.present_id= ".$present_id.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error()." ".$sql);
            }
            $row=mysql_fetch_assoc($result); 
            return $row['commission'];
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
    }

    /**
    * 履歴データ取得
    *
    * @accsess public
    * @return array
    */
    public function sumGiftCordCost($term,$present_id){
        try{
            if($present_id==1){
                $select = " count(*)*300 as cost ";
            } else if($present_id==2){
                $select = " count(*)*500 as cost ";
            } else if($present_id==3){
                $select = " count(*)*500 as cost ";
            } else if($present_id==4){
                $select = " count(*)*300 as cost ";
            } else if($present_id==5){
                $select = " count(*)*500 as cost ";
            }
            $sql = "SELECT 
                        ".$select." 
                    FROM            
                        data_distributions
                    WHERE       
                        ".$term." AND
                        present_id = ".$present_id." ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            return $row['cost'];
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    /**
    * 会社データ取得
    *
    * @accsess public
    * @return array
    */
    public function searchCampanies(){
        try{
            $sql = "SELECT 
                        *
                    FROM            
                        master_campanies;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }

            $array = array();
            while($row = mysql_fetch_assoc($result)){
               $array[$row['campany_id']] = $row; 
            }
            return $array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
    /**
    * ユーザデータ取得
    *
    * @accsess public
    * @return array
    */
    public function searchUser($user_id){
        try{
            $sql = "SELECT 
                        *
                    FROM            
                        data_users
                    WHERE       
                        user_id = ".$user_id." limit 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            $row['total_point']=$this->getTotalPoints($row['login_id'],$row['login_key']);
            return $row;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
    /**
    * ユーザデータ取得
    *
    * @accsess public
    * @return array
    */
    public function searchUsers(){
        try{
            $where="";
            if($_POST['user_id']!=""){
                $where.=" user_id like '%".$_POST['user_id']."%' AND ";
            }
            if($_POST['login_id']!=""){
                $where.=" login_id like '%".$_POST['login_id']."%' AND ";
            }
            if($_POST['udid']!=""){
                $where.=" udid like '%".$_POST['udid']."%' AND ";
            }
            if($_POST['wifi']!=""){
                $where.=" wifi like '%".$_POST['wifi']."%' AND ";
            }
            if($_POST['mac_address']!=""){
                $where.=" mac_address like '%".$_POST['mac_address']."%' AND ";
            }
            if($_POST['portal']!=""){
                $where.=" portal='".$_POST['portal']."' AND ";
            }
            if($_POST['invitation_id']!=""){
                $where.=" invitation_id like '%".$_POST['invitation_id']."%' AND ";
            }
            $offset="";
            if($_POST['page']!=""){
                $offset = ' offset '.($_POST['page']*100);
            }

            $where .=" deleted in(0,1) ";
            $sql="SELECT
                        *
                  FROM
                        data_users
                  WHERE
                        ".$where."
                        order by user_id desc limit 100".$offset.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $num=0;
            $array=array();
            while($row=mysql_fetch_assoc($result)){
                $array[$num]['total_point']=$this->getTotalPoints($row['login_id'],$row['login_key']);
                $array[$num]['user_id']=$row['user_id'];
                $array[$num]['login_id']=$row['login_id'];
                $array[$num]['login_key']=$row['login_key'];
                $array[$num]['udid']=$row['udid'];
                $array[$num]['wifi']=$row['wifi'];
                $array[$num]['mac_address']=$row['mac_address'];
                $array[$num]['ip_address']=$row['ip_address'];
                $array[$num]['duplicate_id']=$row['duplicate_id'];
                $array[$num]['point_list']=$row['point_list'];
                $array[$num]['push_token']=$row['push_token'];
                $array[$num]['push_flg']=$row['push_flg'];
                $array[$num]['portal']=$row['portal'];
                $array[$num]['status']=$row['status'];
                $array[$num]['serialize_data']=$row['serialize_data'];
                $array[$num]['created']=$row['created'];
                $array[$num]['updated']=$row['updated'];
                $array[$num]['deleted']=$row['deleted'];
                $array[$num]['invitation_id']=$row['invitation_id'];
                $array[$num]['branche_cord']=$row['branche_cord'];
                $num++;
            }
            return $array;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return $array;
        }
    }
    /**
    * 履歴データ取得
    *
    * @accsess public
    * @return array 
    */
    public function searchBankbooks($user_id){
        try{
            $sql="";
            $point_type = $_POST['point_type'];
            if($point_type!=""){
                $where .= "AND point_type =".$point_type." ";
            }
            $sql="SELECT        
                        *
                  FROM      
                        log_bankbooks
                  WHERE     
                        user_id=".$user_id."
                        ".$where.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $num=0;
            $array=array();
            while($row=mysql_fetch_assoc($result)){
                $serialize_data = unserialize($row['serialize_data']);
                $array[$num]['campaign_name']=$serialize_data['campaign_name'];
                $array[$num]['bankbook_id']=$row['bankbook_id'];
                $array[$num]['point_type']=$row['point_type'];
                $array[$num]['summary_id']=$row['summary_id'];
                $array[$num]['distribution_id']=$row['distribution_id'];
                $array[$num]['app_point']=$row['app_point'];
                $array[$num]['sub_point']=$row['sub_point'];
                $array[$num]['status']=$row['status'];
                $array[$num]['created']=$row['created'];
                $array[$num]['updated']=$row['updated'];
                $num++;
            }
            return $array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 合計獲得ポイントと減算ポイントの取得
    *
    * @accsess public
    * @return sum_point,sub_point
    */
    public function adjustmentPoint($user_id){
        try{
            $sql="";
            $point_type = $_POST['point_type'];
            if($point_type!=""){
                $where .= "AND point_type in(".$point_type.",1) ";
            }
            $sql="SELECT 
                        sum(app_point),
                        sum(sub_point)
                  FROM
                        log_bankbooks
                  WHERE     
                        user_id=".$user_id." 
                        ".$where.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            $array['sum_point']=$row['sum(app_point)'];
            $array['sub_point']=$row['sum(sub_point)']; 
            return $array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 案件データ取得
    *
    * @accsess public
    * @output JSON 
    */
    public function searchAdvertisements(){
        try{
            //POSTデータを代入
            $advertisement_type  = $_POST['advertisement_type'];
            $name                = $_POST['name'];
            $sort_num            = $_POST['sort'];       
            $picup               = $_POST['picup']; 
            $campany_type        = $_POST['campany_type'];
            $market              = $_POST['market'];
            $campany_type        = $_POST['campany_type'];
            if($_POST['deleted']!=''){
                $deleted             = " deleted=".$_POST['deleted']." ";
            } else{
                $deleted = " deleted=0 ";
            }
            if($advertisement_type!=''){
                switch($advertisement_type){
                    case 1:
                        $where .= 'AND advertisement_type ="'.$advertisement_type.'" ';
                        break;
                    case 2:
                        $where .= 'AND advertisement_type ="'.$advertisement_type.'" ';
                        break;
                    case 3:
                        $where .= 'AND advertisement_type ="'.$advertisement_type.'" ';
                        break;
                    case 4:
                        $where .= 'AND advertisement_type ="'.$advertisement_type.'" ';
                        break;
                    case 5:
                        $where .= 'AND advertisement_type in (1,3)';
                        break;
                    case 6:
                        $where .= 'AND advertisement_type in (2,4) ';
                        break;
                }
                $where .= 'AND advertisement_type ="'.$advertisement_type.'" ';
            }
            if($name!=''){
                $Like .= 'AND name LIKE BINARY "%'.$name.'%" ';
            }
            if($picup == 1){
                $where .= 'AND status = 1 ';
            }else if($picup ==2){
                $where .= 'AND status = 0 ';
            } 
            if($sort_num == 3){
                $sort = 'ORDER BY priority DESC, newly_arrived DESC ';
            } else if($sort_num == 2){
                $sort = 'ORDER BY margin ASC, newly_arrived DESC ';  
            } else if($sort_num == 1){
                $sort = 'ORDER BY margin DESC, newly_arrived DESC ';
            } else if($sort_num ==""){
                $sort .= 'ORDER BY created DESC, newly_arrived DESC ';
            }
            if($market != ''){
                $where .= "AND market ='".$market."' ";
            } 
            if($campany_type!=''){
                $where .= "AND campany_type ='".$campany_type."' ";    
            }           
            
             $offset="";
            if($_POST['page']!=""){
                $offset = ' offset '.($_POST['page']*50);
            }

           
            $sql = "SELECT
                            *
                    FROM
                            master_advertisements
                    WHERE
                    ".$deleted."
                    ".$where."
                    ".$Like."
                    ".$sort."
                    limit 50".$offset.";";

            $serialize_data = array();
            $recordset = mysql_query($sql);
            $num = 0;
            $obj = new AddSummaries();
            while($table = mysql_fetch_assoc($recordset)){
                        //シリアライズデータ
                        $serialize_data = unserialize($table['serialize_data']);
                        $row[$num]['market'] = $serialize_data['market'];
                        $row[$num]['remark'] = $serialize_data['remark'];
                        $row[$num]['detail'] = $serialize_data['detail'];
                        $row[$num]['price'] = $serialize_data['price'];
                        $row[$num]['outcome_name'] = $serialize_data['name'];
                        $row[$num]['url'] = $serialize_data['url'];
                        $row[$num]['adjustment_data'] = $serialize_data['adjustment_data'];
                        ////////////////////endシリアライズ/////////////////////////////

                        $row[$num]['advertisement_type'] = $table['advertisement_type'];
                        $row[$num]['add_point'] = $obj -> castPoint($table);//ユーザーに付与するポイント
                        $row[$num]['advertisement_id'] = $table['advertisement_id'];
                        $row[$num]['advertisement_key'] = $table['advertisement_key'];
                        $row[$num]['name'] = $table['name'];
                        $row[$num]['img_url'] = $table['img_url'];
                        $row[$num]['landing_url'] = $table['landing_url'];
                        $row[$num]['start_time'] = $table['start_time'];
                        $row[$num]['end_time'] = $table['end_time'];
                        $row[$num]['max_count'] = $table['max_count'];
                        $row[$num]['daily_max_count'] = $table['daily_max_count'];
                        $row[$num]['duplicate_flg'] =  $table['duplicate_flg'];
                        $row[$num]['point'] =  $table['point'];
                        $row[$num]['margin'] =  $table['margin'];
                        $row[$num]['auto_on'] =  $table['auto_on'];
                        $row[$num]['status'] =  $table['status'];
                        $row[$num]['deleted'] =  $table['deleted'];
                        $row[$num]['campany_type'] =  $table['campany_type'];
                        $row[$num]['price'] = $table['price'];
                        $row[$num]['outcome_id'] = $table['outcome_id'];
                        $row[$num]['package_name'] = $table['package_name'];
                        $row[$num]['priority'] = $table['priority'];
                        $row[$num]['newly_arrived'] = $table['newly_arrived'];
                        $row[$num]['market'] =  $table['market'];
                        $row[$num]['note'] =  $table['note'];
                $num++;
            }
            return $row;
        } catch(Exception $e){
           CreateLog::putErrorLog(get_class()." ".$e->getMessage()); 
        }
    }
    /*
    *  data_distributionsテーブルのデータを取ってくる関数
    *
    *  @accesss public
    *  @return boolean
    */
    public function searchDistributions(){
        try{
            $sql = 'SELECT 
                            * 
                    FROM 
                            data_distributions 
                    WHERE 
                            deleted = 0 
                    ';
//            $present_id =      $_POST['present_id'];
            $user_id =         $_POST['user_id'];
            $download_url =    $_POST['download_url'];
            $updated =         $_POST['updated'];
            $status =          $_POST['status'];
            $sort   =          $_POST['sort'];
            $page   =          $_POST['page'];
//            if ($present_id!='') {
//                $sql .= 'AND present_id ="'.$present_id.'" ';
//            }
            if($user_id!=''){
                $sql .= 'AND user_id ="'.$user_id.'" ';
            }
            if($download_url!=''){
                $sql .= 'AND download_url ="'.$download_url.'" ';
            }
            if($updated!=''){
                $sql .= 'AND updated >="'.$updated.'" ';
            }
            if($status!=''){
                $sql .= 'AND status ="'.$status.'" ';
            }
            if($sort!=''){
                $sql .= 'ORDER BY user_id DESC ';
            } else{
                $sql .= 'ORDER BY distribution_id DESC ';
            }
                $sql .= 'LIMIT 100 ';
                $sql .= 'OFFSET '.($page*100).';';
            
            $num=0;
            $recordset = mysql_query($sql);
            $obj = new AddSummaries();
            while($table = mysql_fetch_assoc($recordset)){
                $serialize_data = unserialize($table['serialize_data']);
                $row[$num]['campaign_name'] = $serialize_data['campaign_name'];
                $row[$num]['distribution_id'] = $table['distribution_id'];
                $row[$num]['present_id'] = $table['present_id'];
                $row[$num]['status'] = $table['status'];
                $row[$num]['user_id'] = $table['user_id'];
                $row[$num]['download_url'] = $table['download_url'];
                $row[$num]['created'] = $table['created'];
                $row[$num]['updated'] = $table['updated'];
                $num++; 
            }
            return $row;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /*
    *  advertiement_idでmaster_advertisementsテーブルのデータを取ってくる関数
    *
    *  @accesss public
    *  @return boolean
    */
    public function searchAdvertisementofKey($advertisement_id){
        try{
            $sql = "SELECT 
                          * 
                    FROM 
                          master_advertisements 
                    WHERE 
                          advertisement_id =".$advertisement_id." limit 1;";
           
            if(!$recordset = mysql_query($sql)){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $obj = new AddSummaries();
            $serialize_data=array();
            $table = mysql_fetch_assoc($recordset);
            foreach($table as $key => $value){
                //シリアライズデータをインサート
                $serialize_data = unserialize($table['serialize_data']);
                $data['detail'] = $serialize_data['detail'];
                $data['remark'] = $serialize_data['remark'];
                $data['outcome_name'] = $serialize_data['name'];
                $data['add_point'] = $obj -> castPoint($table);
                $data['adjustment_data'] = $serialize_data['adjustment_data'];
                //end インサートシリアライズ

                $data[$key] = $value;
            }
            //CreateLog::putErrorLog($data['url']);
            return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
/*
    *  data_distributionsテーブルのデータを取ってくる関数
    *
    *  @accesss public
    *  @return boolean
    */
    public function searchDistributionsID($distribution_id){
        try{
            $sql = "SELECT
                            *
                    FROM
                            data_distributions
                    WHERE
                            deleted = 0  AND
                            distribution_id = ".$distribution_id."
                    LIMIT   1;";
            if(!$recordset = mysql_query($sql)){
                throw new Exception($sql);
            }
            $table = mysql_fetch_assoc($recordset);
            $serialize_data = unserialize($table['serialize_data']);
            $row['user_id'] = $table['user_id'];
            $row['distribution_id'] = $distribution_id;
            $row['campaign_name'] = $serialize_data['campaign_name'];
            $row['present_id'] = $table['present_id'];
            $row['status'] = $table['status'];
            $row['download_url'] = $table['download_url'];
            $row['updated'] = $table['updated'];
            return $row;
        } catch(Exception $e){
             CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
}

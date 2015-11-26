<?php
/**
  * Bannerクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class Banner{
     public static function setHeader(){
        try{
?>
<script type="text/javascript">
var nend_params = {"media":20808,"site":105228,"spot":266016,"type":1,"oriented":3};
</script>
<script type="text/javascript" src="http://js1.nend.net/js/nendAdLoader.js"></script>
<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
     public static function set1(){
        try{
?>
<script type="text/javascript">
var nend_params = {"media":20808,"site":105228,"spot":266967,"type":1,"oriented":3};
</script>
<script type="text/javascript" src="http://js1.nend.net/js/nendAdLoader.js"></script>
<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
     public static function set2(){
        try{
?>
<script type="text/javascript">
var nend_params = {"media":20808,"site":105228,"spot":269278,"type":1,"oriented":3};
</script>
<script type="text/javascript" src="http://js1.nend.net/js/nendAdLoader.js"></script>
<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
     public static function set3(){
        try{
?>
<script type="text/javascript">
var nend_params = {"media":20808,"site":105228,"spot":266969,"type":1,"oriented":3};
</script>
<script type="text/javascript" src="http://js1.nend.net/js/nendAdLoader.js"></script>
<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }

}


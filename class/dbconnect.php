<?php
  require_once(dirname(__FILE__).'/../conf/ini.php');
  if(!$link = mysql_connect(DB_HOST, DB_USER, DB_PASS)) {
    throw new Exception(mysql_error());
  }
  if(!mysql_select_db(DB_NAME, $link)) {
    throw new Exception(mysql_error());
  }
  if(!mysql_query("SET NAMES 'utf8'",$link)) {
    throw new Exception(mysql_error());
  }
?>
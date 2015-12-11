<?php
  require_once(dirname(__FILE__).'/../class/dbconnect.php');
  $id =$_REQUEST['id'];
  $query = "DELETE FROM company_details WHERE id=$id"; 
  $result = mysql_query($query) or die ( mysql_error());
  header("Location: company.php"); 
?>
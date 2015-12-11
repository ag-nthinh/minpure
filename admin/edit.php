<?php
  require_once(dirname(__FILE__).'/../class/dbconnect.php');
  
  $status = "";
  $id =$_REQUEST['id'];
  $query = "SELECT * from `company_details` where `id`='".$id."'"; 
  $result = mysql_query($query) or die ( mysql_error());
  $row = mysql_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
  	<meta charset="utf-8">
  	<title><?php echo SITE_NAME ?></title>
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link id="bs-css" href="css/bootstrap-weed.css" rel="stylesheet">
  	<link rel="stylesheet" href="css/table.css" type="text/css">
  </head>
  <body>
  <?php require_once('header.php');
    if(isset($_POST['new']) && $_POST['new']==1)
    {
      $id =$_REQUEST['id'];
      $name =$_REQUEST['name'];
      $url  =$_REQUEST['url'];
      $img_url =$_REQUEST['img_url'];
      $money =$_REQUEST['money'];
      $usd_jpy =$_REQUEST['usd_jpy'];
      $euro_usd =$_REQUEST['euro_usd'];
      $euro_jpy =$_REQUEST['euro_jpy'];
      $aud_jpy =$_REQUEST['aud_jpy'];
      $graph1 =$_REQUEST['graph1'];
      $graph2 =$_REQUEST['graph2'];
      $graph3 =$_REQUEST['graph3'];
      $graph4 =$_REQUEST['graph4'];
      $graph5 =$_REQUEST['graph5'];
      $graph6 =$_REQUEST['graph6'];
      $point_text1 =$_REQUEST['point_text1'];
      $point_text2 =$_REQUEST['point_text2'];
      $point_text3 =$_REQUEST['point_text3'];
      $point =$_REQUEST['point'];
      $description =$_REQUEST['description'];
      $redirect_link =$_REQUEST['redirect_link'];
      $short_name =$_REQUEST['short_name'];
      $sql_update="UPDATE `company_details` SET `name`='".$name."', `url`='".$url."', `img_url`='".$img_url."', `money`='".$money."', `usd_jpy`='".$usd_jpy."', `euro_usd`='".$euro_usd."', `euro_jpy`='".$euro_jpy."', `graph1`='".$graph1."', `graph2`='".$graph2."', `graph3`='".$graph3."', `graph4`='".$graph4."', `graph5`='".$graph5."', `graph6`='".$graph6."', `point_text1`='".$point_text1."', `point_text2`='".$point_text2."', `point_text3`='".$point_text3."', `point`='".$point."', `description`='".$description."', `redirect_link`='".$redirect_link."', `short_name`='".$short_name."' WHERE `id`='".$id."'";
      // die(var_dump($sql_update));
      mysql_query($sql_update) or die(mysql_error());
      $status = "Record Update Successfully.";
      echo "<script>";
      echo "alert($status)";
      echo "</script>";
      header('Location:company.php');
    } else {
  ?>
  <div align="center">
    <h1>Update FX Company Information</h1>
    <form name="form" method="post" action=""> 
      <input type="hidden" name="new" value="1" />
      <input name="id" type="hidden" value="<?php echo $row['id'];?>" />
      <p><input type="text" name="name" placeholder="Enter Name" required value="<?php echo $row['name'];?>" /></p>
      <p><input type="text" name="url" placeholder="Enter Url" required value="<?php echo $row['url'];?>" /></p>
      <p><input type="text" name="img_url" placeholder="Enter Image Url" required value="<?php echo $row['img_url'];?>" /></p>
      <p><input type="text" name="money" placeholder="Enter Money Pack" required value="<?php echo $row['money'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="usd_jpy" placeholder="Enter USD/JPY rate" required value="<?php echo $row['usd_jpy'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="euro_usd" placeholder="Enter EURO/USD rate" required value="<?php echo $row['euro_usd'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="euro_jpy" placeholder="Enter EURO/JPY rate" required value="<?php echo $row['euro_jpy'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="aud_jpy" placeholder="Enter AUD/JPY rate" required value="<?php echo $row['aud_jpy'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="graph1" placeholder="Enter Graph value 1" required value="<?php echo $row['graph1'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="graph2" placeholder="Enter Graph value 2" required value="<?php echo $row['graph2'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="graph3" placeholder="Enter Graph value 3" required value="<?php echo $row['graph3'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="graph4" placeholder="Enter Graph value 4" required value="<?php echo $row['graph4'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="graph5" placeholder="Enter Graph value 5" required value="<?php echo $row['graph5'];?>" /></p>
      <p><input type="number" step="0.01" min=0 name="graph6" placeholder="Enter Graph value 6" required value="<?php echo $row['graph6'];?>" /></p>
      <p><input type="text" name="point_text1" placeholder="Enter the first Point" required value="<?php echo $row['point_text1'];?>" /></p>
      <p><input type="text" name="point_text2" placeholder="Enter the second Point" required value="<?php echo $row['point_text2'];?>" /></p>
      <p><input type="text" name="point_text3" placeholder="Enter the third Point" required value="<?php echo $row['point_text3'];?>" /></p>
      <p><input type="number" min=0 name="point" placeholder="Enter Point rate" required value="<?php echo $row['point'];?>" /></p>
      <p><input type="text" name="description" placeholder="Enter the description" required value="<?php echo $row['description'];?>" /></p>
      <p><input type="text" name="redirect_link" placeholder="Enter the short redirect link name" required value="<?php echo $row['redirect_link'];?>" /></p>
      <p><input type="text" name="short_name" placeholder="Enter the short view name" required value="<?php echo $row['short_name'];?>" /></p>
      <p></p>
      <p>
        <input name="submit" type="submit" value=" Update " style="color:#FF0000;" />
        <input name="reset" type="reset" value=" Reset " style="color:#FF0000;" />
        <button><a href="company.php"> Cancel </a></button>
      </p>
    </form>
    <p style="color:#FF0000;"><?php echo $status; ?></p>
  </div>
  <?php } ?>
  </body>
</html>
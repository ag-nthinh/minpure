<div class="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="./"> <img src="img/title.png" height="30"></a>
            <div class="top-nav nav-collapse">
                <ul class="nav">
                    <li><a href="promotions.php?timesale=0">通常広告一覧</a></li>
                    <li><a href="promotions.php?timesale=1">タイムセール一覧</a></li>
                    <li><a href="sales.php">売上</a></li>
                    <li>CSV出力1
<form action="exportcsv.php" method="get" target="_blank">

<select name="m" style="width:80px">
<?php
	$first_month = "2014-10";
	$i = 0;
	while($first_month <= $month = date("Y-m", strtotime("-$i month"))){
?>
<option value="<?php echo $month ?>"><?php echo $month ?></option>
<?php $i++; } ?>
</select>
<input type="submit" value="出力する">
</form>
</li>

                    <li>CSV出力2
<form action="exportcsv2.php" method="get" target="_blank">

<select name="m" style="width:80px">
<?php
	$i = 0;
	while($first_month <= $month = date("Y-m", strtotime("-$i month"))){
?>
<option value="<?php echo $month ?>"><?php echo $month ?></option>
<?php $i++; } ?>
</select>
<input type="submit" value="出力する">
</form>
</li>

                    <li><a href="http://wansaka.co/fmmyadmin/sales.php">wansaka</a></li>

                </ul>
            </div>
        </div>
    </div>
</div>


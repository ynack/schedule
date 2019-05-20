<?php

	session_start();

	$staffid = $_SESSION["staffid"]; 

	include("../sec/acc.php");
	
	try
	{
		$pdo = new PDO($dsn,$user,$pass);
	}
	catch (PODException $e)
	{
		exit('データベース接続に失敗しました'.$e->getMessage());
	}

	/* スタッフIDから名前を取得	 */	
	$stmt = $pdo->query("select * from account where staffid = ".$staffid);
	foreach($stmt as $row)
	{
		$ac_name = $row["name"];
		$ac_fname = $row["f_name"];
	}
?>
<table class="header">
	<tr>
		<td class="head">
			<a class="header" href="./schedule.php">個人</a>
		</td>
		<td class="head">
			<a class="header" href="./manage.php">全体</a>
		</td>
		<?php
	if($staffid == 1216)
	{
?>
		<td  class="head">
			<a class="header" href="../top.php">topへ</a>
		</td>
<?php
	}
?>
		<td class="text-align-right head">
<?php
	if(isset($_SESSION["staffid"]))
	{
		echo "Login:".$ac_name;
		echo "<a class='header' style='margin-left:30px;margin-right:15px;' href='./logout.php'>Logout</a>";
		
	}
?>
		</td>
	</tr>
</table>
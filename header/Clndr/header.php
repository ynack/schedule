<?php

	session_start();

	$staffid = $_SESSION["staffid"]; 

	include("../../sec/acc.php");
	
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

	$mng = $pdo->query("select mngflg from staff where staffid = ".$staffid);
	foreach($mng as $flg)
	{
		$mng_flg = $flg["mngflg"];
	}
?>
	<table class="header">
		<tr>
			<td class="text-align-left head">
				<a class="header" href="./schedule.php">個人スケジュール</a>
			</td>
			<td class="text-align-left head">
				<a class="header" href="./manage.php">全体スケジュール</a>
			</td>
		<?php
		if($mng_flg == 1 || $staffid == 1216)
		{
		?>
			<td  class="text-align-left head">
				<a class="header" href="../../top.php">topへ</a>
			</td>
		<?php
		}
		?>
			<td class="text-align-right head">
		<?php
		if(isset($_SESSION["staffid"]))
		{
			echo "ログインユーザ：".$ac_name." ".$ac_fname;
			echo "<a class='header' style='margin-left:20px;margin-right:15px;' href='./logout.php'>ログアウト</a>";
		}
		?>
			</td>
		</tr>
	</table>

<?php 
	session_start();		//セッションの開始
?>
<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0" />
		<title>スケジュールサンプル</title>
		<script src="../js/jquery.js"></script>
		<?php
			$ua = $_SERVER["HTTP_USER_AGENT"];
			if(strpos($ua,"iPhone"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/main.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/ui.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/layout.css\" />\n";
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/main.css\" />";
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/main.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/ui.css\" />˜\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/layout.css\" />\n";
			}
		?>
	</head>
	<body>
	
<?php
	$_SESSION["staffid"]=$_POST["staffid"];
	$_SESSION["passwd"]=$_POST["passwd"];

	if(empty($_POST["passwd"]))
	{
		print("<div class='title-md' style='margin-top:30px;'>Authentication Failure</div>\n");
		print("<div class='content-hg'>パスワードが入力されていません。</div>\n");
		print("<div class='button_form'><button class='btn' onClick='history.back()'>戻る</button>\n</div>\n");
		print("</div>\n");
		exit;
	}

	$staffid=$_SESSION["staffid"];
	$passwd=$_SESSION["passwd"];
	
	include("./acc.php");
	include("./spid.php");
	include("./enc.php");
	
	try
	{
		$pdo = new PDO($dsn,$user,$pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		//$sql = "select convert(aes_decrypt(unhex(schedulepass),'passwrd')using utf8) from account where staffid=".$staffid;
		$sql = "select convert(aes_decrypt(unhex(schedulepass),'".$enc_wrd."')using utf8),staff.mngflg from account join staff on account.staffid = staff.staffid where account.staffid=".$staffid;
		$stmt = $pdo->query($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		
	}
	catch (PODException $e)
	{
		exit('データベース接続に失敗しました'.$e->getMessage());
	}

	if($rs[0]["convert(aes_decrypt(unhex(schedulepass),'".$enc_wrd."')using utf8)"] != $passwd)
	{
		print("<div class='title-md' style='margin-top:30px;'>Authentication Failure</div>\n");
		print("<div class='content-ng'>パスワードが違います。<br />認証に失敗しました</div>\n");
		print("<div class='button_form'><button class='btn' onClick='history.back()'>戻る</button>\n</div>\n");
		print("</div>\n");
		exit;
	}
	else
	{/*
		for($i = 0; $i < count($spid); $i++)
		{
			if($staffid == $spid[$i])
			{
				$rs[0]["mngflg"] = 0;
			}
		}

		if($rs[0]["mngflg"] == 0 )
		{*/
			//header("Location:./Calendar/schedule.php");
			header("Location:../top.php");
		/*}
		else if($rs[0]["mngflg"] == 1)
		{
			header("Location:./Calendar/manage.php");
		}*/
	}
?>
	</body>
</html>
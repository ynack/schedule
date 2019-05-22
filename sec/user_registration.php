<?php
	if( !strcmp($_POST["operation"],"add") && (empty($_POST["staffid"]) || empty($_POST["name"]) || empty($_POST["pass"])))
	{
		if(empty($_POST["staffid"]))
		{
			$not_id = "・社員番号が入力されていません。<br />";
		}

		if(empty($_POST["name"]))
		{
			$not_name = "・名前が入力されていません。<br />";
		}

		if(empty($_POST["pass"]))
		{
			$not_pass = "・パスワードが入力されていません。<br />";
		}
	}
	else if(!strcmp($_POST["operation"],"update") && empty($_POST["staffid"]))
	{
		$not_id = "・社員番号が入力されていません。<br />";
	}
	else
	{
		if(!strcmp($_POST["operation"],"add"))
		{
			/* create_user.phpからPOSTされてきたデータ	*/
			$staffid = $_POST["staffid"];
			$name = $_POST["name"];
			$division = $_POST["division"];
			//$place = $_POST["place"];
			$passwd = $_POST["pass"];
			$f_name = $_POST["f_name"];
			/*
			echo $staffid."<br />";
			echo $name."<br />";
			echo $f_name."<br />";
			echo $division."<br />";
			echo $passwd."<br />";
			*/
		}
		else if(!strcmp($_POST["operation"],"update"))
		{
			/* change_pass.phpからPOSTされてきたデータ	*/
			$staffid = $_POST["staffid"];
			$passwd = $_POST["pass"];
			/*
			echo $staffid."<br />";
			echo $passwd."<br />";
			*/
		}

		include("./acc.php");
		include("./enc.php");
		
		try
		{
			$pdo = new PDO($dsn,$user,$pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

			if(!strcmp($_POST["operation"],"add"))
			{
				$stmt = $pdo->prepare("insert into account values(:id,:l_name,:f_name,:div,HEX(AES_ENCRYPT(:pass,'".$enc_wrd."')))");
				$stmt->bindParam(':id',$staffid,PDO::PARAM_INT);
				$stmt->bindParam(':l_name',$name,PDO::PARAM_STR);
				$stmt->bindParam(':f_name',$f_name,PDO::PARAM_STR);
				//$stmt->bindParam(':place',$place,PDO::PARAM_STR);
				$stmt->bindParam(':div',$division,PDO::PARAM_STR);
				$stmt->bindParam(':pass',$passwd,PDO::PARAM_STR);
			}
			else if(!strcmp($_POST["operation"],"update"))
			{
				$stmt = $pdo->prepare("update account set schedulepass = HEX(AES_ENCRYPT(:pass,'".$enc_wrd."')) where staffid = :id");
				$stmt->bindParam(':id',$staffid,PDO::PARAM_INT);
				$stmt->bindParam(':pass',$passwd,PDO::PARAM_STR);
			}
			$stmt->execute();
		}
		catch (PODException $e)
		{
			exit('データベース接続に失敗しました'.$e->getMessage());
		}
	}
?>

<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0" />
		<title>勤怠管理　試用版</title>
		<script src="../js/jquery.js"></script>
		<?php
			$ua = $_SERVER["HTTP_USER_AGENT"];
			if(strpos($ua,"iPhone"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/layout.css\" />"; 
				echo "<link rel=\"stylesheet\" href=\"../css/font/style.css\" />";
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android_header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android_main.css\" />\n";				
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"../css/header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/main.css\" />\n";
			}
		?>
	</head>
	<body>
		<div style="margin-top:60px"></div>
<?php
		if(!strcmp($_POST["operation"],"add") && (empty($staffid) || empty($name) || empty($passwd)))
		{
			echo "<div class='content-outer'>";
			if(empty($staffid))
			{
				echo $not_id;
			}

			if(empty($name))
			{
				echo $not_name;
			}

			if(empty($passwd))
			{
				echo $not_pass;
			}
?>		
			<div class="button_form-md">
				<button type="button" name="return" class="btn" onclick="location.href='./create_user.php'">戻る</button>
			</div>
<?php
		}
		else if(!strcmp($_POST["operation"],"update") && (empty($staffid)))
		{
			echo "<div class='content-outer'>";
			if(empty($staffid))
			{
				echo $not_id;
			}
?>		
			<div class="button_form-md">
				<button type="button" name="return" class="btn" onclick="location.href='./change_pass.php'">戻る</button>
			</div>
<?php
		}
		else if(!strcmp($_POST["operation"],"add"))
		{
?>	
		<div class="title-nm">
			<h3>登録が完了しました。</h3>
			<div class="button_form-md">
				<button type="button" name="return" class="btn" onclick="location.href='../login.php'">ログイン画面へ</button>
			</div>
<?php
		}
		else if(!strcmp($_POST["operation"],"update"))
		{
?>	
		<div class="title-nm">
			<h3>変更が完了しました。</h3>
			<div class="button_form-md">
				<button type="button" name="return" class="btn" onclick="location.href='../login.php'">ログイン画面へ</button>
			</div>
<?php
		}
?>
		</div>
	</body>
</html>
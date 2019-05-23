<?php 
	session_start();		//セッションの開始

	if(!isset($_SESSION["staffid"]))
	{
		header("Location:./login.php");
	}
	else
	{
		$staffid=$_SESSION["staffid"];
		$passwd=$_SESSION["passwd"];
		
		include("./sec/acc.php");
		include("./sec/spid.php");
		include("./sec/enc.php");

		try
		{
			$pdo = new PDO($dsn,$user,$pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

			/*	管理職フラグの取り出し　のはず。一部不要かも*/
			$sql = "select convert(aes_decrypt(unhex(schedulepass),'".$enc_wrd."')using utf8),staff.mngflg from account join staff on account.staffid = staff.staffid where account.staffid=".$staffid;
			$stmt = $pdo->query($sql);
			$stmt->execute();
			$rs = $stmt->fetchAll();

		/*	未承認フラグの取り出し	*/
			$appsql = "select apploval from schedule where apploval = 0 and staffid=".$staffid;
			$app = $pdo->query($appsql);
			$app->execute();
			$app_count = $app->rowCount();
			$no_app = $app->fetch();
		}
		catch (PODException $e)
		{
			exit('データベース接続に失敗しました'.$e->getMessage());
		}

		$php_json = json_encode($app_count);
		//echo $php_json;
?>
	<!doctype html>
	<html lang="ja">
		<head>
			<meta charset="utf8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<title>勤怠管理　試用版</title>
			<script src="./js/jquery.js"></script>
			<?php	include("./include_php/ua_css_highrank.php"); ?>
			<link rel="stylesheet" href="./css/Clndr/font/style.css" />
			<script>
				window.onload = function()
				{
					var no_app = <?php echo $php_json; ?>;
					if( no_app > 0)
					{
						alert("未承認の業務が"+no_app+"件あります\nスケジュールを確認してください");
					}
				}
			</script>
		</head>
		<body>
			<header>
				<?php
					if(strpos($ua,"iPhone"))
					{
						include("./header/Clndr/iphone_topheader.php");
					}
					else if(strpos($ua,"Android"))
					{
						include("./header/Clndr/android_topheader.php");
					}
					else
					{
						include("./header/Clndr/topheader.php");
					}
				?>
			</header>
			<div class="top-space""></div>
			<div class="content-inner-md">
				<div>
					<?php
						for($i = 0; $i < count($spid); $i++)
						{
							if($staffid == $spid[$i])
							{
								$rs[0]["mngflg"] = 0;
							}
						}

						if($rs[0]["mngflg"] == 0 )
						{
							/*
							if($staffid == 1216)
							{
								if(strpos($ua,"iPhone"))
								{
									echo "<button class=\"selectBtn\" onclick=\"location.href='./Calendar/iphone/schedule.php'\">勤怠予定</button>";
								}
								else if(strpos($ua,"iPad"))
								{
									echo "<button class=\"selectBtn\" onclick=\"location.href='./Calendar/ipad/schedule.php'\">勤怠予定</button>";
								}
								else if(strpos($ua,"Macintosh"))
								{
									echo "<button class=\"selectBtn\" onclick=\"location.href='./Calendar/schedule.php'\">勤怠予定</button>";	
								}
								else if(strpos($ua,"Windows"))
								{
									echo "<button class=\"selectBtn\" onclick=\"location.href='./Calendar/schedule.php'\">勤怠予定</button>";
								}
								else if(strpos($ua,"Android"))
								{
									echo "<button class=\"selectBtn\" onclick=\"location.href='./Calendar/schedule.php'\">勤怠予定</button>";
								}
								else
								{
									echo "<button class=\"selectBtn\" onclick=\"location.href='./Calendar/schedule.php'\">勤怠予定</button>";
								}
							}
							else
							{*/
								echo "<button class=\"selectBtn\" onclick=\"location.href='./Calendar/schedule.php'\">勤怠予定</button>";
							//}
							
						}
						else if($rs[0]["mngflg"] == 1)
						{
							echo "<button class=\"selectBtn\" onclick=\"location.href='./Calendar/manage.php'\">勤怠予定</button>";
						}
					?>
				</div>
				<div>
<?php
						if(!strcmp($staffid,"1216"))
						{
							echo "<button type=\"button\" class=\"selectBtn\" onclick=\"location.href='./Record/calendar.php'\">勤怠メモ</button>";
						}

						for($i = 0; $i < count($spid); $i++)
						{
							if($staffid == $spid[$i])
							{
								$rs[0]["mngflg"] = 1;
							}
						}

						if($rs[0]["mngflg"] == 1 || $staffid==1216)
						{
							echo "<button type=\"button\" class=\"selectBtn\" onclick=\"location.href='./Calendar/manage.php'\">勤務指定</button>";
						}					
?>
				</div>
			</div>
		</body>
	</html>
<?php
	}
?>	
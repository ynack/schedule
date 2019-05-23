<?php
	session_start();

	if(!isset($_SESSION["staffid"]))
	{
		header("Location:../login.php");
	}
	else
	{
		/* ログインページから社員番号を保持しておく	*/
		$staffid = $_SESSION["staffid"];
		$regist_date = $_GET["regDay"];

		if($regist_date == null)
		{
			$display_date =  date("Y-m-01");
		}
		else
		{
			$display_date = $regist_date;
		}

		$yn = explode("-", $display_date);
		$year = $yn[0];
		$month = $yn[1];

		$last_day = date('j',mktime(0,0,0,$month + 1, 0, $year));
		$calender = array();
		$j = 0;

		for($i = 1; $i < $last_day + 1; $i++)
		{
			$week = date('w', mktime(0,0,0,$month,$i,$year));
			if($i == 1)
			{
				for($s = 1; $s <= $week; $s++)
				{
					$calender[$j]['day'] = '';
					$j++;
				}
			}

			$calender[$j]['day'] = $i;
			$j++;

			if($i == $last_day)
			{
				for($e = 1; $e <= 6 - $week; $e++)
				{
					$calender[$j]['day'] = '';
					$j++;
				}
			}
		}

		/* データベースの接続処理	*/
		include("../sec/acc.php");
		
		try
		{
			$pdo = new PDO($dsn,$user,$pass);
		}
		catch (PODException $e)
		{
			exit('データベース接続に失敗しました'.$e->getMessage());
		}

		$stmt = $pdo->query("select * from account where staffid = ".$staffid);
		foreach($stmt as $row)
		{
			$name = $row["name"];
		}

		$stmt2 = $pdo->query("select * from schedule where staffid = ".$staffid." && date = '".$regist_date."'");
		$count = $stmt2->rowCount();
		$alldata = $stmt2->fetchAll();
		
		for($w = 0; $w < $count; $w++)
		{
			$sid[$w] = $alldata[$w]["staffid"];
			$today_start[$w] = $alldata[$w]["starttime"];
			$today_finish[$w] = $alldata[$w]["finishtime"];
			$today_allday[$w] = $alldata[$w]["allday"];
			$today_am[$w] = $alldata[$w]["am"];
			$today_pm[$w] = $alldata[$w]["pm"];
			$today_work[$w] = $alldata[$w]["work"];
	/*
		echo $sid[0]."<br />";
		echo $today_start[0]."<br />";
		echo $today_finish[0]."<br />";
		echo $today_allday[0]."<br />";
		echo $today_am[0]."<br />";
		echo $today_pm[0]."<br />";
		echo $today_work[0]."<br />";
		echo $sid[1]."<br />";
		echo $today_start[1]."<br />";
		echo $today_finish[1]."<br />";
		echo $today_allday[1]."<br />";
		echo $today_am[1]."<br />";
		echo $today_pm[1]."<br />";
		echo $today_work[1]."<br />";
	*/
			if($today_start[$w])
			{
				if(substr($today_start[$w],0,1) == 0)
				{
					$today_start[$w] = substr($today_start[$w],1,4);
				}
				else
				{
					$today_start[$w] = substr($today_start[$w],0,5);
				}
			}

			if($today_finish[$w])
			{
				if(substr($today_finish[$w],0,1) == 0)
				{
					$today_finish[$w] = substr($today_finish[$w],1,4);
				}
				else
				{
					$today_finish[$w] = substr($today_finish[$w],0,5);
				}
			}
		}

		/* ログインしているstaffidが管理者かどうかチェックするのにmngflgの値を取得	*/
		include("./php/mngck.php");
?>
<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>勤怠管理　試用版</title>
		<script src="../js/jquery-3.3.1.js"></script>
		<?php
			$ua = $_SERVER["HTTP_USER_AGENT"];
			if(strpos($ua,"iPhone"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/layout.css\" />";
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/layout.css\" />"; 
			}
			else if(strpos($ua,"Windows"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/layout.css\" />"; 
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/layout.css\" />"; 
			}
		?>	
		<link rel="stylesheet" href="../css/font/style.css" />
		<script>
			/* チェックボックスでセレクトボックスの有効化/無効化	*/
			function AllDayChk(selectid1,selectid2,check1,check2,ischecked)
			{
				if(ischecked == true)
				{
					document.getElementById(selectid1).disabled = true;
					document.getElementById(selectid2).disabled = true;
					document.getElementById(check1).checked = false;
					document.getElementById(check2).checked = false;
				}
				else
				{
					document.getElementById(selectid1).disabled = false;
					document.getElementById(selectid2).disabled = false;
				}
			}
			
			function Continuity(select1,select2,ischecked)
			{
				if(ischecked == true)
				{
					document.getElementById(select1).checked = false;
					document.getElementById(select2).checked = false;
				}
			}
			/*	submit前にアラート	*/
			function reg_check()
			{
				var start = document.workplan.start.value;	/*	開始時間	*/
				var finish = document.workplan.finish.value;	/* 終了時間	*/
				var dt = document.workplan.reg_date.value;	/* 日付	*/
				var work = document.workplan.work.value;	/* 業務内容 */
				var ad = document.workplan.AllDay.checked;	/* 終日フラグ	*/
				var am = document.workplan.am.checked;	/* 午前フラグ	*/
				var pm = document.workplan.pm.checked;	/* 午後フラグ	*/

				var data = dt + "\n" + work + "\n";

				if( ad == true)
				{
					data = data + "終日";
				}
				else if( am == true)
				{
					data = data + "午前";
				}
				else if( pm == true)
				{
					data = data + "午後";
				}
				else
				{
					data = data + start + " - " + finish;
				}

				if(window.confirm("以下の内容で登録します\n\n"+data))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		</script>
	</head>
	<body>
		<header>
		<?php include("../include_php/device_header.php");	?>
		</header>
		<div style="margin-top:60px;"></div>
			<form name="workplan" action="./work_regist.php" method="POST" onsubmit="return reg_check()"> 
				<div class="title-sm">
					申請メール対応登録
				</div>

				<div class="content-hg">
					<div>
						<?php
							if($mngcc["mngflg"] == 1)
							{
						?>
							<select name="sid">
								<script src="../js/MediaStaff.js"></script>
							</select>
						<?php
							}
						?>
					</div>
					<div style="margin-bottom:10px;"> 
						<select name="s_year" id="y">
							<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
							<script src="../js/year.js"></script>
						</select>
						-
						<select name="s_month" id="m">
							<option value="<?php echo $month; ?>"><?php echo $month; ?></option>
							<script src="../js/month.js"></script>
						</select>
						-
						<select name="s_day" id="d">
							<script src="../js/day.js"></script>
						</select>
						から
					</div>
					<div>
						<select name="e_year" id="y">
							<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
							<script src="../js/year.js"></script>
						</select>
						-
						<select name="e_month" id="m">
							<option value="<?php echo $month; ?>"><?php echo $month; ?></option>
							<script src="../js/month.js"></script>
						</select>
						-
						<select name="e_day" id="d">
							<script src="../js/day.js"></script>
						</select>
						まで
					</div>
					
						<input type="radio" name="role" value="main" checked>メイン
						<input type="radio" name="role" value="sub">サブ
					
					<input type="hidden" name="reg_date" value="<?php echo $regist_date; ?>" />
					<input type="hidden" name="operation" value="mail" />
					<div class="button_form">
						<button type="button" class="btn" onclick="history.back();">戻る</button>
						<button type="submit" class="btn">登録</button>
					</div>
				</div>
			</form>
		</div>
		<!--
		<div class="reg_link">
			<span style="font-size:12px;">■登録済みメール対応 -></span>
			<span class="textposition" style="font-size:11px;text-decoration: underline;"><a href="./mail_regist.php">確認</a></span>
		</div>
		-->
	</body>
</html>
<?php
	}
?>

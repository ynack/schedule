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

		if(isset($_GET["id"]))
		{
			$regist_id = $_GET["id"];
			$_SESSION["id"] = $_GET["id"];
		}

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

		$stmt = $pdo->query("select * from account where staffid = ".$regist_id);
		foreach($stmt as $row)
		{
			$name = $row["name"];
			$f_name = $row["f_name"];
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
			$apploval[$w] = $alldata[$w]["apploval"];
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

		if(isset($_POST["apploval"]))
		{
			$work = $_POST["appwork"];
			$apvsql = "update schedule set apploval=1 where staffid = ".$staffid." && date = '".$regist_date."' && work = '".$work."'";
			$apvchg = $pdo->query($apvsql);
			$apvchg->execute();
		}

		include("./php/mngflg.php");

		echo $mngflg_rs[0]["staffid"];
?>
<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/main.css\" />";
			}
			else if(strpos($ua,"Windows"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/main.css\" />";
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/main.css\" />";
			}
		?>	
		<style>
			table
			{
				border: solid 1px;
				border-collapse: collapse;
				width:90%;
				height:520px;
				margin-right: auto;
				margin-left: auto;
			}

			td.head
			{
				border: solid 1px;
				text-align: right;
				vertical-align: top;
			}

			a.days
			{
				display: block;
				width: 100%;
				height:100%;
				text-decoration: none;
			}

		</style>
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
		<?php
			if(strpos($ua,"iPhone"))
			{
				include("../header/Clndr/iphone_header.php");
			}
			else if(strpos($ua,"Android"))
			{
				include("../header/Clndr/android_header.php");
			}
			else if(strpos($ua,"Windows"))
			{
				include("../header/Clndr/win_header.php");
			}
			else
			{
				include("../header/Clndr/header.php");
			}
		?>
		</header>
		<div class="top-space"></div>
				
		<form name="workplan" action="./work_regist.php" method="POST" onsubmit="return reg_check()"> 		
			<?php
				if(isset($regist_id))
				{
					echo "<div class=\"title-lg\"><h3>".$name.$f_name."の<br />".$regist_date."のスケジュール</h3>";
				}
				else
				{
					echo "<div class=\"title-md\">\n<h3>".$regist_date."のスケジュール</h3>";
				}
			?>

			<div class="content content_reg">
				<div>
					■開始予定時刻：
					<select name="start" id="strt">
						<script src="../js/time.js"></script>
					</select>
				</div>
				<div>
					■終了予定時刻：
					<select name="finish" id="fin">
						<script src="../js/time.js"></script>
					</select>
				</div>
				<div style="margin-top:5px;margin-bottom: 15px;">
					<input type="checkbox" name="AllDay" id="ad" onclick="AllDayChk('strt','fin','am','pm',this.checked);" />終日&nbsp;&nbsp;
					<input type="checkbox" name="am" id="am" onclick="AllDayChk('strt','fin','pm','ad',this.checked);" />午前&nbsp;&nbsp;
					<input type="checkbox" name="pm" id="pm" onclick="AllDayChk('strt','fin','am','ad',this.checked);" />午後
				</div>
				業務内容：
				<div>
					<textarea name="work" rows="3" cols="30"></textarea>
				</div>
			<?php
					if(isset($regist_id))
					{
						echo "<div>";
						echo "<span class=\"textposition\" style=\"font-size:12px;text-decoration: underline;\"><a href=\"./mail_regist.php\">申請メール対応登録</a></span>";
						echo "</div>";
					}
			?>
				<div style="margin-top:15px;">
					<input type="checkbox" name="evweek" id="evw" onclick="Continuity('evow','trm',this.checked);"/>毎週&nbsp;&nbsp;
					<input type="checkbox" name="evoweek" id="evow" onclick="Continuity('evw','trm',this.checked);"/>隔週&nbsp;&nbsp;
					<input type="checkbox" name="term" id="trm" onclick="Continuity('evw','evow',this.checked);"/>期間指定
					<select name="r_year" id="y">
						<script src="../js/year.js"></script>
					</select>
					-
					<select name="r_month" id="m">
						<script src="../js/month.js"></script>
					</select>
					-
					<select name="r_day" id="d">
						<script src="../js/day.js"></script>
					</select>
					まで
				</div>
				<input type="hidden" name="reg_date" value="<?php echo $regist_date; ?>" />
				<input type="hidden" name="operation" value="add" />
				<input type="hidden" name="mng_reg" value="<?php echo $regist_id; ?>" />
				<div class="button_form">
					<button type="button" class="btn" onclick="location.href='./schedule.php'">戻る</button>
					<button type="submit" class="btn">登録</button>
				</div>
			</div>
		</form>

		<?php
			if($today_work[0])
			{
				echo "<div class='alreadyWork'>";
				echo "<div style='width:280px;height:30px; background-color:#000;'>";
				echo "<h4 style='color:#fff;'>登録済みの予定</h4>\n";
				echo "</div>";
				for($x = 0; $x < $count; $x++)
				{
					if($apploval[$x] == 0 && !$_POST["apploval"])
					{
						echo "<div class='alreadyCont' style='color:red;'>\n";
					}
					else
					{
						echo "<div class='alreadyCont'>\n";
					}
					
					if($today_allday[$x] == "on")
					{
						echo "終日<br />\n";
					}
					else if($today_am[$x] == "on")
					{
						echo "午前<br />\n";
					}
					else if($today_pm[$x] == "on")
					{
						echo "午後<br />\n";
					}
					else
					{
						echo $today_start[$x] ." - ".$today_finish[$x]."<br />\n";
					}

					echo $today_work[$x]."</div>\n";
					
					if($apploval[$x] == 0)
					{
						echo "<div class=\"alreadyBtn_lg\">";
					}
					else
					{
						echo "<div class=\"alreadyBtn_md\">";
					}
		?>
				<form name="change" action="./change.php" method="POST" style="margin: 0px; float: left;">
					<input type="hidden" name="reg_date" value="<?php echo $regist_date; ?>" />
					<input type="hidden" name="operation" value="change" />
					<button type="submit" class="btn">修正・削除</button>
				</form>
		<?php
			if($apploval[$x] == 0 && !$_POST["apploval"])
			{
		?>
				<form action="./result.php?regDay=<?php echo $regist_date; ?>" method="POST">
					<input type="hidden" name = "apploval" value="on" />
					<input type="hidden" name = "appwork" value="<?php echo $today_work[$x]; ?>" />
					<button type="submit" class="btn">承認</button>
				</form>
		<?php
			}
		?>
			</div>
		</div>
		<?php
				}
			}
		?>
	</body>
</html>
<?php
	}
?>

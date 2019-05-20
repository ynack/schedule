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

		$change_date = $_POST["reg_date"];
		$today_start = $_POST["today_start"];
		$today_finish = $_POST["today_finish"];
		$today_allday = $_POST["today_allday"];
		$today_am = $_POST["today_am"];
		$today_pm = $_POST["today_pm"];
		$today_work = $_POST["today_work"];
		$old_work = $_POST["today_work"];
	/*
		echo $today_start;
		echo $today_finish;
		echo $today_allday;
		echo $today_am;
		echo $today_pm;
		echo $today_work;
	*/
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

		$stmt2 = $pdo->query("select * from schedule where staffid = ".$staffid." && date = '".$change_date."'");
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
			$old_work[$w] = $alldata[$w]["work"];

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
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone_header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone_main.css\" />";
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android_header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android_main.css\" />";
			}
			else if(strpos($ua,"Windows"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win_header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win_main.css\" />";
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
				height:320px;
				margin-right: auto;
				margin-left: auto;
			}

			table.cnt
			{
				border: solid 1px;
				border:none;
				width:90%;
				height:10px;
				margin-right: auto;
				margin-left: auto;
			}
			tr
			{
				border: solid 1px;
				margin-top:20px;
			}

			td
			{
				border: solid 1px;
				text-align: right;
				vertical-align: top;
				width:14%;
			}
			tr.none
			{
				border:none;
			}
			td.none
			{
				border: solid 1px;
				text-align: left;
				border:none;
			}
			.c_button
			{
				text-align: center;
				vertical-align: middle;
			}

			th
			{
				//border: solid 1px;
				height:20px;
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
			/*チェックボタンそれぞれで違うfunctionか？	*/

			/*	submit前にアラート	*/
			function reg_check()
			{
				var start = document.workplan.start.value;	//	開始時間	
				var finish = document.workplan.finish.value;	// 終了時間
				var dt = document.workplan.reg_date.value;	// 日付
				var work = document.workplan.work.value;	// 業務内容
				var ad = document.workplan.AllDay.checked;	// 終日フラグ	
				var am = document.workplan.am.checked;	// 午前フラグ	
				var pm = document.workplan.pm.checked;	// 午後フラグ	

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

			/*	削除前にアラート	*/
			function del_check()
			{
				var del_date = document.delform.d_date.value;
				var del_work = document.delform.d_work.value;

				var delete_info = del_date + "\n" + del_work + "\n";

				if(window.confirm("以下の内容を削除します\n\n"+delete_info))
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
				include("./CntHeader/iphone_header.php");
			}
			else if(strpos($ua,"Android"))
			{
				include("./CntHeader/android_header.php");
			}
			else if(strpos($ua,"Windows"))
			{
				include("./CntHeader/win_header.php");
			}
			else
			{
				include("./CntHeader/header.php");
			}
		?>
		</header>
		<div style="margin-top:60px;"></div>
		<!--<form name="workplan" action="./work_regist.php" method="POST" onsubmit="return reg_check()">-->
			<div class="title-md">
				<?php echo "<h3>".$change_date."のスケジュール</h3>"; ?>
			</div>
			<div class="content">
<?php
		for($i = 0; $i < $count; $i++)
		{
?>
			<table class="cnt">
				<tr class="none">
					<form name="workplan" action="./work_regist.php" method="POST" onsubmit="return reg_check()">				
						<div>
							■開始予定時刻：
							<select name="start" id="strt<?php echo $i;?>">
							<?php
								if($today_start[$i])
								{
									echo "<option value='".$today_start[$i]."'>".$today_start[$i]."</option>";
									echo "<script src=\"../js/time.js\"></script>";
								}
								else
								{
									echo "<script src=\"../js/time.js\"></script>";
								}
							?>
							</select>
						</div>
						<div>
							■終了予定時刻：
							<select name="finish" id="fin<?php echo $i;?>">
							<?php
								if($today_finish[$i])
								{
									echo "<option value='".$today_finish[$i]."'>".$today_finish[$i]."</option>";
									echo "<script src=\"../js/time.js\"></script>";
								}
								else
								{
									echo "<script src=\"../js/time.js\"></script>";
								}
							?>
							</select>
						</div>
						<div style="margin-top:5px;margin-bottom: 15px;">
							<?php
								if($today_allday[$i])
								{
									echo "<input type=\"checkbox\" name=\"AllDay\" id=\"ad".$i."\" checked = 'checked' onclick=\"AllDayChk('strt".$i."','fin".$i."','am".$i."','pm".$i."',this.checked);\" />終日&nbsp;&nbsp;";
									echo "<input type=\"checkbox\" name=\"am\" id=\"am".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','ad".$i."','pm".$i."',this.checked);\" />午前&nbsp;&nbsp;";
									echo "<input type=\"checkbox\" name=\"pm\" id=\"pm".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','am".$i."','ad".$i."',this.checked);\" />午後";

								}
								else if($today_am[$i])
								{
									echo "<input type=\"checkbox\" name=\"AllDay\" id=\"ad".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','am".$i."','pm".$i."',this.checked);\" />終日&nbsp;&nbsp;";
									echo "<input type=\"checkbox\" name=\"am\" id=\"am".$i."\" checked = 'checked'  onclick=\"AllDayChk('strt".$i."','fin".$i."','pm".$i."','ad".$i."',this.checked);\" />午前&nbsp;&nbsp;";
									echo "<input type=\"checkbox\" name=\"pm\" id=\"pm".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','am".$i."','ad".$i."',this.checked);\" />午後";

								}
								else if($today_pm[$i])
								{
									echo "<input type=\"checkbox\" name=\"AllDay\" id=\"ad".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','am".$i."','pm".$i."',this.checked);\" />終日&nbsp;&nbsp;";
									echo "<input type=\"checkbox\" name=\"am\" id=\"am".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','pm".$i."','ad".$i."',this.checked);\" />午前&nbsp;&nbsp;";
									echo "<input type=\"checkbox\" name=\"pm\" id=\"pm".$i."\" checked = 'checked' onclick=\"AllDayChk('strt".$i."','fin".$i."','am".$i."','ad".$i."',this.checked);\" />午後";

								}
								else
								{
									echo "<input type=\"checkbox\" name=\"AllDay\" id=\"ad".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','am".$i."','pm".$i."',this.checked);\" />終日&nbsp;&nbsp;";
									echo "<input type=\"checkbox\" name=\"am\" id=\"am".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','pm".$i."','ad".$i."',this.checked);\" />午前&nbsp;&nbsp;";
									echo "<input type=\"checkbox\" name=\"pm\" id=\"pm".$i."\" onclick=\"AllDayChk('strt".$i."','fin".$i."','am".$i."','ad".$i."  ',this.checked);\" />午後";
								}
							?>				
						</div>
						業務内容：
						<div>
							<?php
								if($today_work[$i])
								{
									echo "<textarea name=\"work\" rows=\"3\" cols=\"30\">".$today_work[$i]."</textarea>";
								}
								else
								{
									echo "<textarea name=\"work\" rows=\"3\" cols=\"30\"></textarea>";
								}
							?>
						</div>
					</tr>
					<input type="hidden" name="operation" value="update" />
					<input type="hidden" name="reg_date" value="<?php echo $change_date; ?>" />
					<input type="hidden" name="oldwork" value="<?php echo $old_work[$i]; ?>" />
					<tr class="none">
						<div class="alreadyBtn_lg">
								<button type="submit" class="btn">修正</button>
					</form>
					<form name="delform" action="./work_regist.php" method="POST" style="margin:0px;float:left;" onsubmit="return del_check()">
						<input type="hidden" name="d_date" value="<?php echo $change_date; ?>" />
						<input type="hidden" name="d_work" value="<?php echo $today_work[$i]; ?>" />
						<input type="hidden" name="operation" value="del" />
						<button type="submit" class="btn" style="margin-left:8px;">削除</button>
					</form>
				</div>
			</tr>
		</table>
<?php
		}			
?>

					<!--<input type="hidden" name="reg_date" value="<?php echo $change_date; ?>" />
					<input type="hidden" name="operation" value="update" />-->
				<div class="alreadyBtn_sm">
					<button type="button" class="btn" onclick="history.back();">戻る</button>
				</div>
			</div>
		
	</body>
</html>
<?php
	}
?>
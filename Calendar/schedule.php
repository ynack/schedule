<?php
	session_start();
	if(!isset($_SESSION["staffid"]))
	{
		header("Location:../login.php");
	}
	else
	{
		/* ログインページから社員番号を保持しておく	*/
		/* セッションで保持する	*/
		$staffid = $_SESSION["staffid"]; 

		$get_display_date = $_GET["first_day_of_month"];
		if($get_display_date == null)
		{
			$display_date =  date("Y-m-01");
		}
		else
		{
			$display_date = $get_display_date;
		}

		$week_jp = ['（日）','（月）','（火）','（水）','（木）','（金）','（土）'];	//	曜日対応（数字から日本語にする）

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

		/*	年間祝日用配列の読み込み	*/
		include("./holiday.php");

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

		/* スタッフIDから名前を取得	 */	
		$stmt = $pdo->query("select * from staff where staffid = ".$staffid);
		foreach($stmt as $row)
		{
			$name = $row["name"];
		}
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
		<link rel="stylesheet" href="../css/Clndr/font/style.css" />
		<style>
			a.days
			{
				display: block;
				width: 100%;
				height:100%;

				text-decoration: none;
			}
		</style>
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
		<div style="text-align:center; margin-bottom:-10px;">
			<h3>
				<a href="schedule.php?first_day_of_month=<?php echo date('Y-m-01',strtotime("-1 month",strtotime($display_date))); ?>" class="def_a"><<</a>
				<?php echo date('Y-n',strtotime($display_date)); ?>
				<a href="schedule.php?first_day_of_month=<?php echo date('Y-m-01',strtotime("+1 month",strtotime($display_date))); ?>" class="def_a">>></a>
			</h3>
		</div>

		<div class="mail_work">		
			<span class="textposition" style="font-size:12px;text-decoration: underline;"><a href="./mail_regist.php">■申請メール対応登録</a></span>
		</div>
<?php
	if(strpos($ua,"iPhone")||strpos($ua,"Android"))
	{
?>
		<table>
			<thead>
			</thead>
			<tbody>
				<?php
					for($i = 1; $i <= $last_day; $i++)
					{
						echo "<tr>";

						if( $i >=1 && $i <= 9)
						{
							$days = date('Y-m',strtotime($display_date))."-0".$i;
						}
						else
						{
							$days = date('Y-m',strtotime($display_date))."-".$i;
						}

						$dt = mktime(0,0,0,$month,$i,$year);
						$dotw = date("w",$dt);

						if($dotw == 0)
						{
							echo "<td class='sunday work'>\n";
						}
						else if($dotw == 6)
						{
							echo "<td class='saturday work'>\n";
						}
						else
						{
							$holiflg = 0;

							for($h = 0; $h < count($holiday); $h++)
							{
								if(!strcmp($days,$holiday[$h])){	$holiflg = 1;	}
								if($holiflg == 1){	break;	}
							}
							if($holiflg == 1)
							{
								echo "<td class='holiday work'>\n";
							}
							else
							{
								echo "<td class='weekday work'>\n";
							}
						}

						echo $i;
						echo $week_jp[$dotw];
						echo "</td>\n";

						$stmt = $pdo->prepare("select work,apploval from schedule where staffid = :staffid and date=:date");
						$stmt->bindParam(':staffid',$staffid);					
						$stmt->bindParam(':date',$days);
						$stmt->execute();
						$wrkCnt = $stmt->rowCount();
						$rs = $stmt->fetchAll();

						if($dotw == 0)
						{
							echo "<td class='sunday work'>\n";
						}
						else if($dotw == 6)
						{
							echo "<td class='saturday work'>\n";
						}
						else
						{
							for($h = 0; $h < count($holiday); $h++)
							{
								if(!strcmp($days,$holiday[$h]))
								{
									$holiflg = 1;
								}

								if($holiflg == 1)
								{
									break;
								}
							}

							if($holiflg == 1)
							{
								echo "<td class='holiday work'>\n";
							}
							else
							{
								echo "<td class='work'>\n";
								
							}
							$holiflg = 0;
						}

						echo "<a class='days' href='./result.php?regDay=".$days."'>";

						if(empty($rs[0]['work']) && ($dotw == 0 || $dotw == 6))	//業務登録がなく、曜日値が0（日）か6（土）だったら「休」
						{
							echo "休";
						}
						else if(empty($rs[0]['work'])&& ($dotw >= 1 || $dotw <=5))
						{
							for($h = 0; $h < count($holiday); $h++)
							{
								if(!strcmp($days,$holiday[$h]))	//祝日用配列の要素と$daysの値が一緒だったら祝日フラグを1に
								{
									$holiflg = 1;
								}

								if($holiflg == 1)	//祝日フラグが立ったらfor文からbreak;
								{
									break;
								}
							}
							if($holiflg == 1)	//祝日フラグが1の場合はデフォルトが「休」
							{
								echo "休";
							}
							else
							{
								echo "出";
							}
						}
						else
						{
							for($l = 0; $l < $wrkCnt; $l++)
							{
								if($rs[$l]["apploval"] == 0)	//未承認の場合は赤文字にする
								{
									echo "<span style='color:red'>";
								}
								
								if(!strcmp($rs[$l]['work'],"CX電話番"))
								{	
									echo "<i class='icon-phone' style='font-size:18px;vertical-align:middle;'></i>";
								}
								else if(!strcmp($rs[$l]['work'],"mail_main"))
								{
									echo "<i class='icon-mail_main' style='font-size:19px;margin-right:12px;margin-left:8px;'></i>";
								}
								else if(!strcmp($rs[$l]['work'],"mail_sub"))
								{
									echo "<i class='icon-mail_sub' style='font-size:19px;margin-right:12px;margin-left:8px;'></i>";
								}
								else
								{
									echo $rs[$l]["work"];
								}
								
								if($rs[$l]["apploval"] == 0){	echo "</span>";	}

								if($l != $wrkCnt - 1)
								{
									if(strcmp($rs[$l]['work'],"CX電話番"))
									{
										echo "<br />";
									}
								}
							}
						}
						echo "</a></td>\n";		
						echo "</tr>\n";
					}
				?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
<?php
	}
	else
	{
?>
		<table>
			<tr>
				<th><div style="color:red;background-color:#ffeaea;'">日</div></th>
				<th>月</th>
				<th>火</th>
				<th>水</th>
				<th>木</th>
				<th>金</th>
				<th><div style="color:blue;background-color:#eaeaff;">土</div></th>
			</tr>
			<tr>
			<?php
				$cnt = 0;
				foreach ($calender as $key => $value)
				{
					$holiflg = 0;
					if( $value['day'] >=1 && $value['day'] <= 9)
					{
						$days = date('Y-m',strtotime($display_date))."-0".$value['day'];
					}
					else
					{
						$days = date('Y-m',strtotime($display_date))."-".$value['day'];
					}

					$stmt = $pdo->prepare("select work,apploval from schedule where staffid = :staffid and date = :date");

					$stmt->bindParam(':staffid',$staffid);					
					$stmt->bindParam(':date',$days);
					$stmt->execute();
					$rs = $stmt->fetchAll();
					$rs_cnt = $stmt->rowCount();

					/* 日の背景色を赤、土の背景色を青に	*/
					if($cnt == 0)
					{
						echo "<td style='background-color:#ffefef;'>";
					}
					else if($cnt == 6)
					{
						for($h = 0; $h < count($holiday); $h++)
						{
							if(!strcmp($days,$holiday[$h]))
							{
								$holiflg = 1;
							}

							if($holiflg == 1)
							{
								break;
							}
						}

						if($holiflg == 1)
						{
							echo "<td style='background-color:#ffefef;'>";
						}
						else
						{
							echo "<td style='background-color:#efefff;'>";
						}
					}
					else
					{
						/*	平日	*/
						for($h = 0; $h < count($holiday); $h++)
						{
							if(!strcmp($days,$holiday[$h]))
							{
								$holiflg = 1;
							}

							if($holiflg == 1)
							{
								break;
							}
						}

						if($holiflg == 1)
						{
							echo "<td style='background-color:#ffefef;'>";
						}
						else
						{
							echo "<td>\n";
						}
						$holiflg = 0;
					}
					

					if($value['day'])	//日付があるカラムはクリックしてスケジュール登録へ
					{
						echo "<a class='days' href='./result.php?regDay=".$days."'>";
					}

					for($y = 0; $y < $rs_cnt; $y++)
					{
						if(!strcmp($rs[$y]['work'],"CX電話番"))
						{
							echo "<i class='icon-phone' style='font-size:14px;margin-right:10px;'></i>";
						}
						else if(!strcmp($rs[$y]['work'],"mail_main"))
						{
							echo "<i class='icon-mail_main' style='font-size:19px;margin-right:12px;'></i>";
						}
						else if(!strcmp($rs[$y]['work'],"mail_sub"))
						{
							echo "<i class='icon-mail_sub' style='font-size:19px;margin-right:12px;'></i>";
						}
					}
					echo $value['day']."<br />";	//日付の表示

					$holiflg = 0;	//祝日フラグ	デフォルトは0

					/* 特別な予定がない場合、土日は休、それ以外は出を表示	*/
					if((empty($rs[0]['work']) && !empty($value['day'])) && ($cnt == 0 || $cnt == 6)) 	//業務が空で日付が空でなく、曜日値が0（日）か6（土）であれば「休」
					{
						echo "<p style='font-size:12px;'>休</p></a>";
					}
					else if((empty($rs[0]['work']) && !empty($value['day'])) && ($cnt >= 1 && $cnt <= 5)) 
					{
						for($h = 0; $h < count($holiday); $h++)
						{
							if(!strcmp($days,$holiday[$h]))
							{
								$holiflg = 1;
							}

							if($holiflg == 1)
							{
								break;
							}
						}

						if($holiflg == 1)
						{
							echo "<p style='font-size:12px;'>休</p></a>";
						}
						else
						{
							echo "<p style='font-size:12px;'>出</p></a>";
						}
						$holiflg = 0;
					}
					else
					{
						echo "<p style='font-size:12px; margin-bottom:3px;'>";
						for($w = 0; $w < $rs_cnt; $w++)
						{
							if($rs[$w]['apploval'] == 0){ echo "<span style='color:red;'>"; }	//承認フラグが0なら未承認なので赤文字にする
							
							if(strcmp($rs[$w]['work'],"CX電話番") && strcmp($rs[$w]['work'],"mail_main") && strcmp($rs[$w]['work'],"mail_sub"))
							{
								echo $rs[$w]['work'];
							}

							
							if($rs[$w]['apploval'] == 0){ echo "</span>"; }
							if($w != $rs_cnt)
							{
								if(strcmp($rs[$w]['work'],"mail_main")&& strcmp($rs[$w]['work'],"mail_sub"))
								{
									echo "<br />";
								}
							}
						}
						echo "</p></a>";
					}

					$cnt++;
					echo "</td>";
					if($cnt == 7)
					{
						echo "</tr>";
						echo "<tr>";
						$cnt = 0;
					}
				}
			?>
			</tr>
		</table>
<?php
	}
?>
	</body>
</html>
<?php
	}
?>
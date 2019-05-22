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
<?php
    $ua = $_SERVER["HTTP_USER_AGENT"];
    if(strpos($ua,"iPhone"))
    {
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/header.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/main.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/ui.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/table.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/layout.css\" />"; 
    }
    else if(strpos($ua,"Android"))
    {
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/android/header.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/android/main.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/android/ui.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/android/table.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/android/layout.css\" />"; 
    }
    else if(strpos($ua,"Windows"))
    {
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/win/header.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/win/main.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/win/ui.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/win/table.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/win/layout.css\" />"; 
    }
    else if(strpos($ua,"Chrome"))
    {
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/chrm/header.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/chrm/main.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/chrm/ui.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/chrm/table.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/chrm/layout.css\" />"; 
    }
    else
    {
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/header.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/main.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/ui.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/table.css\" />";
        echo "<link rel=\"stylesheet\" href=\"./css/Clndr/layout.css\" />"; 
    }
?>
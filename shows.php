<?php
/**
 * Created by PhpStorm.
 * User: umarbradshaw
 * Date: 10/13/15
 * Time: 10:46 PM
 */

$showOne = "GB vs. SE";
$showOneTime = "5:35pm ET";

$showTwo = "BUF vs. NE";
$showTwoTime = "3:35pm PT";

$showThree = "NYJ vs. MIA";
$showThreeTime = "8:35pm ET";

$showFour = "OAK vs. ATL";
$showFourTime = "2:05pm ET";

$showFive = "CHI vs. DAL";
$showFiveTime = "1:35pm ET";

$backup = "BackupChannel";
$backupTime = " ";

//This is to build the JSON object with the JSON array of objects
$results = array(
    "results" => array(
        array(
            "show_name" => $showOne,
            "show_time" => $showOneTime
        ),
        array(
            "show_name" => $showTwo,
            "show_time" => $showTwoTime
        )
    ,
        array(
            "show_name" => $showThree,
            "show_time" => $showThreeTime
        )
    ,
        array(
            "show_name" => $showFour,
            "show_time" => $showFourTime
        )
    ,
        array(
            "show_name" => $showFive,
            "show_time" => $showFiveTime
        )
    ,
        array(
            "show_name" => $backup,
            "show_time" => $backupTime
        )
    )
);
echo json_encode($results);
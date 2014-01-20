<?php
/*
from = B
to = c

NNNNN:CCC
	C = country
	N = Stationid

depDate = f (g=DEPARTURE_DATE_TIME)				arrDate = f (g=ARRIVAL_DATE_TIME)
depTime = F										arrTime = F

YYYYMMDD
HHMM

	(G=true, G=false vid enkelresa)
depDateReturn = h (i=DEPARTURE_DATE_TIME)		arrDateReturn = h (i=ARRIVAL_DATE_TIME) 
depTimeReturn = H								arrTimeReturn = H

travelers = N (mA,MA,nA,NA,oA,OA,pA,PA,qA)

VU Vuxen   (25---)
UN Ungdom  (20-25)
U1 Ungdom  (16-19)
ST Student (75%ST)
B6 Barn    (07-15)
BA Barn    (00-06)

link.php?from=7400001&to=7400002&depDate=2013-03-03&depTime=12:00&travelers=VU,UN,U1
*/

// Convert from national number to split id and national number
$from = substr($_GET['from'], -5).'%3A0'.substr($_GET['from'], 0,2); 
$to = substr($_GET['to'], -5).'%3A0'.substr($_GET['to'], 0,2);

// Generate url
$url = 	'http://www.sj.se/microsite/microsite/submit.form?'. 	// Base url
		'&B='.$from.											// From stop NNNNN:CCC, C = country, N = Stationid
		'&c='.$to. 												// To stop NNNNN:CCC, C = country, N = Stationid
		'&header.key=K253891809275136476'.						// Some key identifying app (May change)
		'&l=sv'.												// Lanuage
		'&3A=false'; 											// Dont know whats this is but it seams to be nessesary.
		
// Gheck if departure or arrival:
if(isset($_GET['depDate']) AND isset($_GET['depTime']))
	{
	$time = substr($_GET['depTime'],0,2).'00';
	$date = preg_replace('/-/','',$_GET['depDate']);
	$url = $url.'&g=DEPARTURE_DATE_TIME'.
				'&f='.$date;
				'&F='.$time;
	}
	
elseif(isset($_GET['arrDate']) AND isset($_GET['arrTime']))
	{
	$time = substr($_GET['arrTime'],0,2).'00';
	$date = preg_replace('/-/','',$_GET['arrDate']);
	$url = $url.'&g=ARRIVAL_DATE_TIME'.
				'&f='.$date;
				'&F='.$time;
	}
	
else
	{
	die('Error: no depDate/arrDate or depTime/arrTime set.');
	}

// Check if returntrip is set:
if(isset($_GET['depDateReturn']) AND isset($_GET['depTimeReturn']))
	{
	$time = substr($_GET['depTimeReturn'],0,2).'00';
	$date = preg_replace('/-/','',$_GET['depDateReturn']);
	$url =$url.'&i=DEPARTURE_DATE_TIME'.
			'&G=true'.
			'&h='.$date;
			'&H='.$time;
	}
	
elseif(isset($_GET['arrDateReturn']) AND isset($_GET['arrTimeReturn']))
	{
	$time = substr($_GET['arrTimeReturn'],0,2).'00';
	$date = preg_replace('/-/','',$_GET['arrDate']);
	$url = $url.'&i=ARRIVAL_DATE_TIMEReturn'.
				'&G=true'.
				'&h='.$date;
				'&H='.$time;
	}
	
else
	{
	$url = $url.'&G=false';
	}

//Handle travelers:	
if(isset($_GET['travelers']) == false){
	die('Error: value of travelers is not set.');
}

$travelers = preg_split('/,/',$_GET['travelers']);
$url = $url.'&N='.count($travelers);

$codename = array('mA','MA','nA','NA','oA','OA','pA','PA','qA');

foreach($travelers as $key => $traveler){
$url = $url.'&'.$codename[$key].'='.$traveler;
}

die($url);	
header('Location: '.$url);
?>
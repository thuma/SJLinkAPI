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
if(isset($_GET['depDate']) AND isset($_GET['depTime']))			// Deparature
	{
	$time = substr($_GET['depTime'],0,2).'00';					// Use only hours.
	$date = preg_replace('/-/','',$_GET['depDate']);			// Remove - if any.
	$url = $url.'&g=DEPARTURE_DATE_TIME'.						// Set to Deparature Time.
				'&f='.$date;									// Set Date 
				'&F='.$time;									// Set Time	
	}

elseif(isset($_GET['arrDate']) AND isset($_GET['arrTime']))		// Arrival
	{
	$time = substr($_GET['arrTime'],0,2).'00';					// Use only hours.
	$date = preg_replace('/-/','',$_GET['arrDate']);			// Remove - if any.
	$url = $url.'&g=ARRIVAL_DATE_TIME'.							// Set to Arrival Time.
				'&f='.$date;									// Set Date 
				'&F='.$time;									// Set Time	
	}

else
	{
	die('Error: no depDate/arrDate or depTime/arrTime set.');	// If not set skipp exec.
	}
	
// Check if returntrip is set:
if(isset($_GET['depDateReturn']) AND isset($_GET['depTimeReturn']))	// Returntrip with Deparature Time
	{
	$time = substr($_GET['depTimeReturn'],0,2).'00';				// Use only hours.
	$date = preg_replace('/-/','',$_GET['depDateReturn']);			// Remove - if any.
	$url =$url.'&i=DEPARTURE_DATE_TIME'.							// Set to Arrival Time.
			'&G=true'.												// Set book return trip to true.
			'&h='.$date;											// Set Date 
			'&H='.$time;											// Set Time
	}

elseif(isset($_GET['arrDateReturn']) AND isset($_GET['arrTimeReturn'])) // Returntrip with Arrival Time
	{
	$time = substr($_GET['arrTimeReturn'],0,2).'00';				// Use only hours.
	$date = preg_replace('/-/','',$_GET['arrDate']);				// Remove - if any.
	$url = $url.'&i=ARRIVAL_DATE_TIMEReturn'.						// Set to Arrival Time.
				'&G=true'.											// Set book return trip to true.
				'&h='.$date;										// Set Date 
				'&H='.$time;										// Set Time
	}

else
	{
	$url = $url.'&G=false';											// If no return trip set to False.
	}

//Handle travelers set default to one adult.
if(isset($_GET['travelers']) == false){
	$travelers = 'VU';
}
else{
	$travelers = preg_split('/,/',$_GET['travelers']);
}

// Split to array
$url = $url.'&N='.count($travelers);

// List of traveler ids:
$codename = array('mA','MA','nA','NA','oA','OA','pA','PA','qA');

// Loop all travelers
foreach($travelers as $key => $traveler){
	$url = $url.'&'.$codename[$key].'='.$traveler;
}

// Push user to SJ site.
header('Location: '.$url);
?>
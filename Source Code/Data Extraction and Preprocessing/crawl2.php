                                                                     
                                                                     
                                                                     
                                             



<?php
// example of how to use basic selector to retrieve HTML contents
include('simple_html_dom.php');
$urlname = $_GET["fname"];
$fileName = $_GET["age"];
parseResults($urlname,$fileName);
//echo "returned";
//parseResults('http://www.cricinfo.com/iccct2009/engine/match/415275.html?innings=2;page=1;view=commentary');


function parseResults($seedUrl,$fileToBeWritten){
// get DOM from URL or file
//$html = file_get_html('http://www.cricinfo.com/iccct2009/engine/match/415275.html?innings=1;page=1;view=commentary');
$html = file_get_html($seedUrl);	
//echo $html->plaintext;
$fileToBeWritten= $fileToBeWritten . ".txt";
// find all td tags with attribite align=center
$i = 0;
$fh = fopen($fileToBeWritten, 'a');
foreach($html->find('title') as $e){
	$title = $e->plaintext;
	fwrite($fh, $title);
	fwrite($fh, "\n");
}

foreach($html->find('td[class="endofover"]') as $e){
    $i++;	
    $overDetails = $e->plaintext ;
    //echo $overDetails . '<br>';
    $overDetailsCorrected = preg_replace('/\(maiden\)/', 'maiden over', $overDetails). '<br>';
    //echo $overDetails . '<br>';
    $lineByLine = explode(" ",$overDetailsCorrected);
    //echo $lineByLine[7] . '<br>';
    $scoreRecord = explode("/",$lineByLine[7]);
    $score[$i] = $scoreRecord[0];
    $wickets [$i] = $scoreRecord[1];
    //echo $score[$i] . '<br>';
}

//echo "Size " . sizeof($score). '<br>';
for ($y = 0; $y < sizeof($score); $y++) {
    //echo $score[$y] . '<br>';
}
  

// find all div tags with id=gbar
$scoreEOV = 0;
$found = false;
$calculatedWickets = 0;
foreach($html->find('p[class="commsText"]') as $e){
	
	$outer=  $e->outertext."\n";
	$pattern = '/^(\d)*\.(\d)*/';
	if (preg_match($pattern,$plainHTML,$currentBall)) {
		//echo $plainHTML.'<br>';
		$found = true;
		$currentOver = explode(".",$currentBall[0]);
		//echo "current over " .$currentOver[0] .'<br>';
	}
	if (preg_match("/powerplay/i",$plainHTML)) {
	    //echo $plainHTML."\n";
	    //echo $outer."\n";
	    if (preg_match('/\.1/',$outer)) {
		    $outer."Powerplay\n";
		    $powerplayFound = true;
	    }
	    else {
	    echo "A match was not found.";
	    }
	}
	$plainHTML= $e->plaintext;
	if($found == true){
		//echo $plainHTML. 'END' .'<br>';
		$numPattern = '/^(\d)*/';
		$ballDetails = explode(",",$plainHTML);
		$batsman1 = $ballDetails[0];
		$batman = explode("to",$batsman1);
		$batsman = str_replace(" ", "",$batman[0]);
		$bowler = $batman[1];
		$runScored = $ballDetails[1];
		$toAdd = 0;
		//echo "Runs :" . $runScored .'<br>';
		if(preg_match('/(\d)+/',$runScored,$actualRun)){
			//echo "Match \n";
			$toAdd = $actualRun[0];
		}
		if(preg_match('/FOUR/',$runScored)){
			$toAdd = 4;
		}
		if(preg_match('/SIX/',$runScored)){
			$toAdd = 6;
		}
		if(preg_match('/(no ball)/',$runScored)){
			//if(preg_match('/(no ball) 1/',$runScored) != 0)
				$toAdd =$toAdd + 1;
		}
		if(preg_match('/OUT/',$runScored)){
			$calculatedWickets ++ ;
		}
		 
		//echo "to add :" . $toAdd . '<br>';
		//echo "Score :" . $scoreEOV . '<br>';
		$scoreEOV = $scoreEOV + $toAdd;
		$found = false;
		$outToFile = $currentBall[0] .  " " . $scoreEOV .  " " . $calculatedWickets . " " . $batsman . $bowler; /*$wickets[$currentOver[0]+1]*/ ;
		//if ($powerplayFound == true){
		//	$outToFile = $outToFile . $outer;
		//	$powerplayFound = false;
		//}
		if($currentOver[1] == 6 && (preg_match('/(no ball)/',$runScored) == 0) ){
			//echo "cheking match" . "from data :" . $score[$currentOver[0]+1] . "  -- Added Score :" . $scoreEOV . '<br>';
			$scoreEOV = $score[$currentOver[0]+1];
		}
		//echo $outToFile .'<br>';
		fwrite($fh, $outToFile);
		fwrite($fh, "\r\n");			
		
		//fwrite($fh, $outToFile);
		//fwrite($fh, "\r\n");			
	}
	//echo $plainHTML. 'END' .'<br>';
	//fclose($fh);
}
fclose($fh);
}

?>


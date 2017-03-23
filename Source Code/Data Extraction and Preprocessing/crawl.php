<?php
// example of how to use basic selector to retrieve HTML contents
include('simple_html_dom.php');
$file=fopen("urllist.txt","r")or exit("Unable to open file!");
$i1 = 0;
/*while (!feof($file)){
	$i1++;
	parseResults(fgets($file),$i1);
}*/
//parseResults(fgets($file),'test');
parseResults(fgets($file),'test2');
fclose($file);

function parseResults($seedUrl,$fileToBeWritten){
$fileToBeWritten= $fileToBeWritten.".txt";
echo $fileToBeWritten;
// get DOM from URL or file
//$html = file_get_html('http://www.cricinfo.com/iccct2009/engine/match/415275.html?innings=1;page=1;view=commentary');
$html = file_get_html($seedUrl);	
echo $html->plaintext;

// find all link
//foreach($html->find('a') as $e) 
//    echo $e . '<br>';


// find all div tags with id=gbar
//foreach($html->find('p[class="commsText"]') as $e)
//    echo $e . '<br>';
//echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";

// find all span tags with class=gb1
//foreach($html->find('span.commsImportant') as $e)
//    echo $e->outertext . '<br>';

// find all td tags with attribite align=center
$i = 0;
foreach($html->find('title') as $e){
	$title = $e->plaintext;
}

foreach($html->find('td[class="endofover"]') as $e){
    echo "entering" .'<br>';
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
$fh = fopen($fileToBeWritten, 'w');
fwrite($fh,$title);
fwrite($fh, "\r\n");
foreach($html->find('p[class="commsText"]') as $e){
	$pattern = '/^(\d)*\.(\d)*/';
	if (preg_match($pattern,$plainHTML,$currentBall)) {
		//echo $plainHTML.'<br>';
		$found = true;
		$currentOver = explode(".",$currentBall[0]);
		//echo "current over " .$currentOver[0] .'<br>';
	}
	$plainHTML= $e->plaintext;
	if($found == true){
		//echo $plainHTML. 'END' .'<br>';
		$numPattern = '/^(\d)*/';
		$ballDetails = explode(",",$plainHTML);
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
		$outToFile = "Current over" . $currentBall[0] . "  :Score : " . $scoreEOV . "  :Wickets : " . $calculatedWickets /*$wickets[$currentOver[0]+1]*/ ;
		if($currentOver[1] == 6 && (preg_match('/(no ball)/',$runScored) == 0) ){
			//echo "cheking match" . "from data :" . $score[$currentOver[0]+1] . "  -- Added Score :" . $scoreEOV . '<br>';
			$scoreEOV = $score[$currentOver[0]+1];
		}
	
		fwrite($fh, $outToFile);
		echo $outToFile .'<br>';
		fwrite($fh, "\r\n");
			
	}
	//echo $plainHTML. 'END' .'<br>';

}
fclose($fh);
echo "closing " .'<br>';
}

//parseResults('http://www.cricinfo.com/iccct2009/engine/match/415275.html?innings=1;page=1;view=commentary');

?>

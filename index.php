<?php

## TWIG ##

// include and register Twig auto-loader
require_once './vendor/autoload.php';

// LOAD TEMPLATE ------ 
// specify where to look for templates
$loader = new Twig_Loader_Filesystem('templates');
 
// initialize Twig environment
$twig = new Twig_Environment($loader);

// load template
$template = $twig->loadTemplate('teplado.html');

## END TWIG >>##
	
// set rss url variable
if (isset($_POST['url'])) {

	$url = $_POST['url'];	

} else {

	$url = NULL;
}

try{

	## FEED IO <<##

	// create a simple FeedIo instance
	$feedIo = \FeedIo\Factory::create()->getFeedIo();

	// read a feed


	// or read a feed since a certain date
	$result = $feedIo->readSince($url, new \DateTime('-7 days'));
	
	// get title
	$feedTitle = $result->getFeed()->getTitle();

	$feedSite = $result->getFeed()->getLink();

	// iterate through items
	$blocks = array();
	$blockNum = 1;

	#store feed items in $blocks array 
	foreach( $result->getFeed() as $item) {

	    $blockTitle = $item->getTitle();
	    $blockLink = $item->getLink();
	    $blockDescrp = $item->getDescription();
	    
	    // check if item has media
	    $medias = $item->hasMedia();

	    if ($medias > 0) {

	    	// when media is present	    
		    $medias = $item->getMedias();		
			foreach ( $medias as $media ) {
				$med = $media->getUrl();
			}

		} else {
			// when there is no media
			$med = 'no';
		}

		// putting item(n) values in array $block(n) inside $blocks
	    ${"block".$blockNum} = array('title' => $blockTitle,
	    							 'link' => $blockLink,
	    							 'descrp' => $blockDescrp,
	    							 'med' => $med,
	    							 );

	    array_push($blocks, ${"block".$blockNum});
	    $blockNum = $blockNum + 1;

	}
	
	// count number items
	$numOfblocks = count($blocks);
	// pages have 20 items
	$pages = $numOfblocks / 20; //number of pages


	#check if number of pages is a floating number
	if (is_float($pages)){

		//if not then add one page for remainder items
		$pages = (int)$pages + 1;

	}

	## END FEED ##

}catch (Exception $e){
		//die("Error: ".$e);
}

	## RENDERING TEMPLATE ##

	// when feed has items
	if (isset($feedTitle)) {

		$ok = 'yes';
		$st = $_POST['st'];

		echo $twig->render('teplado.html', [
			'feedTitle' => $feedTitle,
			'feedSite' => $feedSite,
			'blocks' => $blocks,
			'url' => $url,
			'ok' => $ok,
			'st' => $st,
			'numOfblocks' => $numOfblocks,
			'pages' => $pages,
		]);
	
	} else {
		## when no items were received
		
		# when person has not entered url
		if (is_null($url)){
			
			echo $twig->render('teplado.html');

		} else {

			# when person has submited 			
			if ($url != ''){
				// when no info  recived
				$err ='noinfo';
		
			} else {
				// when url is empty
				$err ='nourl';
			
			}

			echo $twig->render('teplado.html', ['url' => $url, 'err' => $err]);
			
		}

	}
	//---- >>>	 

?>
<html>
</html>
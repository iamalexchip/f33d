<?php

// include auto-loader
require_once './vendor/autoload.php';
require_once './Feeder.php';

//Set up error handling
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

/**
 * Global Twig instance
 *
 * @var object
 */
global $twig;

setupTwig();

//CHeck if the user POSTed a URL
if (isset($_POST['url'])) {

	if (validURL($_POST['url'])) {
		//If so, process the URL and show the feed items
		processFeed($_POST['url']);
	} else {
		//ask user for valid url
		
		echo $twig->render('home.html', [
			'url' => $_POST['url'],
			'err' => 'invalid'
		]);
	}

} else {
	//Fetch URL fron the user
	getURLFromUser();
}

function validURL($url)
{
	$vali = filter_var($url, FILTER_VALIDATE_URL);
	return $vali;
}

/**
* Initializes twig and returns reference
*  
* @return void
*/
function setupTwig()
{
	global $twig;

	$loader = new Twig_Loader_Filesystem('templates');
	
	// initialize Twig environment
	$twig = new Twig_Environment($loader);
}

/**
 * Shows URL form to the user
 *
 * @return void
 */
function getURLFromUser()
{
	global $twig;
	echo $twig->render('home.html');
}

/**
 * Given a fetched feed, this function displays the feed items
 *
 * @param \Feeder $feed Feeder instance
 * @return void
 */
function showFeed($feed)
{
	global $twig;
	$st = $_POST['st'];

	//render template for news items
	echo $twig->render('news.html',
	[
		'feedTitle' => $feed->feedTitle, // title of the feed
		'feedSite' => $feed->feedSite, // site of feed origin
		'blocks' => $feed->blocks, // array which holds feed items
		'url' => $feed->url, // url of the rss feed
		'st' => $st, // variable value is the position in the array where extraction will start
		'numOfblocks' => $feed->numOfblocks, // number of feed items
		'pages' => $feed->pages, // number of pages
	]);
}

/**
 * Given a URL, shows the news items
 *
 * @param string $url RSS Feed URL
 * @return void
 */
function processFeed($url)
{
	global $twig;

	$feed = new Feeder($url);

	try {

		$feed->readFeed(); // read rss feed

	} catch (Exception $e){
				
		echo 'Error: <font color="red">'.$e.'</font>';

	}

	// the condition is true if there are items on the url entered
	// this if reflected by the $blocks array being set 

	if ($feed->hasContent()) {
		showFeed($feed);
	} else {
		echo $twig->render('home.html', [
			'url' => $url,
			'err' => 'noinfo'
		]);
	}
}

?>
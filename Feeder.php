<?php

/* TINGS TO BE KNOWN

*internal* :
#variables after this comment are defined within the function

*/

### class for feed content extraction ###

class Feeder {

	/**
	 * URL from which feed which be read
	 *
	 * @param string
	 */
	public $url;

	public $twig;
	public $blocks;
	public $result;
	public $feedTitle;
	public $feedSite;

	public function __construct($url)
	{
		$this->url = $url;
	}

	public function hasUrl()
	{
		return is_null($this->url); 
	}

	## READ RSS FEED
	public function readFeed() {
		# create a simple FeedIo instance
		$feedIo = \FeedIo\Factory::create()->getFeedIo();

		# or read a feed since a certain date
		$this->result = $feedIo->readSince($this->url, new \DateTime('-7 days'));
			
		// get Feed title
		$this->feedTitle = $this->result->getFeed()->getTitle();

		// get feed site
		$this->feedSite = $this->result->getFeed()->getLink();

		$this->getBlocks(); // store feed content
		$this->getStats(); // get feed stats

		## LOOP which stores feed items in $this->blocks array 
	}# END FUNCTION


	## GET FEED CONTENT
	public function getBlocks()
	{
		$this->blocks = array();// news item storage
		$this->blockNum = 1;// loop counter

		#LOOP which enters content into $this->blocks array
		foreach( $this->result->getFeed() as $item) {

			# store item elements in variables
		    $blockTitle = $item->getTitle(); // item title
		    $blockLink = $item->getLink(); // item name
		    $blockDescrp = $item->getDescription(); // item description
		    
		    # check if item has media
		    $medias = $item->hasMedia();

		    // when media is present
		    if ($medias > 0) {
		    
			    $medias = $item->getMedias();		
				foreach ( $medias as $media ) {
					$image = $media->getUrl(); // item image
				}

			} else {

				// when there is no media
				$image = 'no';
			}

			// putting item(n) values in array $block(n)
		    ${"block".$this->blockNum} = array('title' => $blockTitle,
		    							 'link' => $blockLink,
		    							 'descrp' => $blockDescrp,
		    							 'image' => $image,
		    							 );

		   	// add $block(n) to $this->blocks
		    array_push($this->blocks, ${"block".$this->blockNum});

		    $this->blockNum = $this->blockNum + 1;// $block(n) increment

		}## END LOOP

	}# END FUNCTION


	## FIND NUMBER ITEMS AND  PAGES
	public function getStats()
	{
		// define global variabless
		$this->numOfblocks = count($this->blocks); // number of items
	    
	    // pages have 20 items so we doth math
		$this->pages = $this->numOfblocks / 20; //number of pages

		#check if number of pages is a floating number
		if (is_float($this->pages)) {

			// then add one page for remainder items
			$this->pages = (int)$this->pages + 1; // number of pages
		
		}
	}

	public function hasContent()
	{
		if (isset($this->blocks)){
			return true;
		} else{
			return false;
		}

	}

}## END CLASS
//---- >>>	 

?>
<?php
// Script that ads ajax funktion to filter out wanted NGO from search form

require_once("../../../wp-load.php");

//get the q parameter from URL
$q=$_GET["q"];

// Function to get output from shortcode [site-list]
function ngol_return_ngo_list()
{
	ob_start();
	do_shortcode('[site-list]');
	$ngos = ob_get_clean();

	return($ngos);
}

// Do shortcode
$input = ngol_return_ngo_list();

//lookup all links from the output we collected if q>0
if (strlen($q)>0) {
	$hint="";
//  echo "Tab->Enter or click on the NGO you wan't to visit;<br />";
	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
	if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
	// $match[2] = link address
	// $match[3] = link title
		foreach($matches as $match) {
			$link_title = preg_replace('#<img[^>]*>#i', '', $match[3]);
			// Match with user input and list the result
			if (stristr($link_title,$q)) {

				if ($hint=="") {
					$hint = "<a href='" .
					$match[2] .
					"' target='_blank'>" .
					$match[3] . "</a>";
				}	else {
					$hint = $hint . "<br /><a href='" .
					$match[2] .
					"' target='_blank'>" .
					$match[3] . "</a>";
				}
			}
		}
	}
}

// Set output to "no suggestion" if no hint was found or to the correct values
if ($hint=="") {
	$response= __('Nothing matched your search criteria.', 'ngo-list');
} else {
	$response=$hint;
}

//output the response
echo $response;
?>

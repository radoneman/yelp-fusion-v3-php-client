<?php
define('BASEPATH', str_replace("\\", "/", $system_path));

require_once 'Yelp.php';

$app_id = 'YOUR_APP_ID';
$app_secret = 'YOUR_API_KEY';

$yelp = new Yelp($app_id, $app_secret);

$businesses = $yelp->search([
	'term' => 'coffee shops',
	'location' => 'Los Angeles, CA'
]);

if ($businesess === false) {
	print_r($yelp->get_error());
	exit;
}
print_r($businesses);
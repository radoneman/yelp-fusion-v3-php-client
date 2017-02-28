# yelp-fusion-v3-php-client

One file php client for Yelp Fusion V3 API  
https://www.yelp.com/developers/documentation/v3

## Installation

1. Just place the file into your project and load it.  
2. Login in your help Yelp account, go to https://www.yelp.com/developers/v3/manage_app and create new app. Get the app_id and app_secret 

## Usage

Example

```
$app_id = 'yourappid';
$app_secret = 'yourappsecret';

$yelp = new Yelp($app_id, $app_secret);
$businesses = $this->yelp->search([
    'term' => 'food phoenix',
    'location' => 'AZ'
]);
if ($businesess === false) {
    print_r($yelp->get_error());
    exit;
}
print_r($businesses);
```

## Tests

Nothing here yet
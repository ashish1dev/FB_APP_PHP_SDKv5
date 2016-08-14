<?php
// Must pass session data for the library to work (only if not already included in your app)
session_start();

// Facebook app settings

$app_id = '316924261657284';
$app_secret = '927f395ae62c4294016199fd109b8912';
$redirect_uri = 'http://localhost/listings_magic/';

putenv("FACEBOOK_APP_ID=316924261657284");
putenv("FACEBOOK_APP_SECRET=927f395ae62c4294016199fd109b8912");


// Requested permissions for the app - optional
$permissions = array(
  'manage_pages','read_page_mailboxes','public_profile','publish_actions','publish_pages','email'
);

// Define the root directoy
define( 'ROOT', dirname( __FILE__ ) . '/' );

// Autoload the required files
require_once( ROOT . 'facebook-php-sdk-v4-4.0-dev/autoload.php' );


use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;


use Facebook\Facebook;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;
use Facebook\Exceptions\FacebookResponseException;


$fb = new Facebook(array(
  'appId'  => '316924261657284',
  'secret' => '927f395ae62c4294016199fd109b8912',
  'cookie' => true,
));


// // Initialize the SDK
// FacebookSession::setDefaultApplication( $app_id, $app_secret );

// Create the login helper and replace REDIRECT_URI with your URL
// Use the same domain you set for the apps 'App Domains'
// e.g. $helper = new FacebookRedirectLoginHelper( 'http://mydomain.com/redirect' );
// $helper = new FacebookRedirectLoginHelper( $redirect_uri );
$helper = $fb->getRedirectLoginHelper();


try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // There was an error communicating with Graph
  echo "<br/>".$e->getMessage();
  exit;
}



if (isset($accessToken)) {



    // Response example.
  $res = $fb->get('/me', $accessToken);
  $node = $res->getGraphObject();
  //var_dump($node);

  // Get response as an array
  $user = $node->asArray();
  echo "<br/> User Id = ".$user['id'];
  echo "<br/>Name = ".$user['name']."<br/>";

  $_SESSION['facebook_access_token'] = (string) $accessToken;
  //echo 'Successfully logged in!';


  //publish on your wall
  $res = $fb->post( '/me/feed', array(
    "message" => "Message",
    "link" => "http://www.example.com",
    "picture" => "https://upload.wikimedia.org/wikipedia/commons/d/d6/MicroQR_Example.png",
    "name" => "Title",
    "caption" => "www.example.com",
    "description" => "Description example"
  ), $accessToken);

  $post = $res->getGraphObject();

  //  var_dump( $post );
  echo "<br/><br/>A post was posted by you on your news feed";


    $client = $fb->getOAuth2Client();

    try {
      $accessToken = $client->getLongLivedAccessToken($accessToken);
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      echo $e->getMessage();
      exit;
    }

    //show a list of all your pages.
    $response = $fb->get('/me/accounts', (string) $accessToken);

    echo "<br/><br/> Your page List: ";
    foreach ($response->getDecodedBody() as $allPages) {
        foreach ($allPages as $page ) {

          if(isset($page['name'])){
            echo "<br/>".$page['id']." - ".$page['name'];

            $pageId = $page['id'];
            $pageName = $page['name'];
        }

        }
    }


    //publish on a page you are an admin of
      $response = $fb->post(
        '/'.$pageId.'/feed',
        array(
            "message" => "Message",
            "link" => "http://www.example.com",
            "picture" => "https://upload.wikimedia.org/wikipedia/commons/d/d6/MicroQR_Example.png",
            "name" => "Title",
            "caption" => "www.example.com",
            "description" => "Description example"
        ),
        $accessToken
    );

    // Success
    $postId = $response->getGraphNode();
    // echo $postId;
    echo "<br/><br/>A post posted by you on page with name ->".$pageName;

    // $helper = $fb->getRedirectLoginHelper();
    $logoutUrl = $helper->getLogoutUrl(  $accessToken,  $redirect_uri.'logout.php');
    $logoutUrl = $redirect_uri.'logout.php';
    // echo $logoutUrl;

    echo '<br/><br/><a href="' . $logoutUrl . '">Logout of Facebook!</a>';

    exit;
}
else {
  // No session

  $callback    = 'http://localhost/listings_magic/';
  $loginUrl    = $helper->getLoginUrl($callback, $permissions);

  unset($accessToken);
  // Get login URL
//  $loginUrl = $helper->getLoginUrl( $permissions );

  echo '<a href="' . $loginUrl . '">Log in</a>';
 }
// elseif ($helper->getError()) {
//   // The user denied the request
//   // You could log this data . . .
//   var_dump($helper->getError());
//   var_dump($helper->getErrorCode());
//   var_dump($helper->getErrorReason());
//   var_dump($helper->getErrorDescription());
//   // You could display a message to the user
//   // being all like, "What? You don't like me?"
//   exit;
// }

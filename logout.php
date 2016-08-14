<?php
session_start();
session_unset();
session_destroy();
    unset($accessToken);
    unset($_SESSION['facebook_access_token']) ;
    // $_SESSION['FULLNAME'] = NULL;
    // $_SESSION['EMAIL'] =  NULL;
header("Location: http://localhost/listings_magic/");        // you can enter home page here ( Eg : header("Location: " ."http://www.krizna.com");
?>

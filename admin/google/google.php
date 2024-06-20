<html>

<head>
    <title>Login Form</title>
</head>
<style>
    .box {
        width: 100%;
        max-width: 400px;
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 16px;
        margin: 0 auto;
    }
</style>

<body>
    <?php
    require_once 'vendor/autoload.php';

    // init configuration
    $clientID = '976828190850-r8osq674brhjshnnjt2ch8ulbfehk14p.apps.googleusercontent.com'; // your client id
    $clientSecret = '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'; // your client secret
    $redirectUri = 'http://localhost/Phpadminpanel/Funda-service/admin/Loginform.php';

    // create Client Request to access Google API
    $client = new Google_Client();
    $client->setClientId($clientID);
    client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->addScope("email");
    $client->addScope("profile");

    // authenticate code from Google OAuth Flow
    if (isset($_GET['code'])) {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);

        // get profile info
        //$google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        $email =  $google_account_info->email;
        $name =  $google_account_info->name;
    ?>
        <div class="container">
            <div class="box">
                <div class="form-group">
                    <label for="email">Emailid: <?php echo $email; ?></label>
                    <label for="name">Name: <?php echo $name; ?></label>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="container">
            <div class="table-responsive">
                <h3 align="center">Login using Google with PHP</h3>
                <div class="box">
                    <div class="form-group">
                        <label for="email">Emailid</label>
                        <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="pwd" id="pwd" placeholder="Enter Password" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input type="submit" id="login" name="login" value="Login" class="btn btn-success form-control" />
                        <hr>
                        <center><a href="<?php echo $client->createAuthUrl() ?>"><img src="google-signin.png" width="256"></a></center>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</body>

</html>
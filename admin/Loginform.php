<?php
session_start();
include('includes/header.php');
if (isset($_SESSION['auth'])) {
    $_SESSION['status'] = "You are already logged In";
    header('Location: Dashboard.php');
    exit(0);
}
include('config/db_conn.php');
require_once('../vendor/autoload.php');

// Google OAuth configuration
$clientID = '976828190850-r8osq674brhjshnnjt2ch8ulbfehk14p.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost:3000/admin/Loginform.php';

// Create Google Client
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Handle Google OAuth callback
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        // Get profile info
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        $email = $google_account_info->email;
        $full_name = $google_account_info->name;
        $profile_picture = 'user.png';
        $password = '12345';
        $Status = 'Verified';
        $Active = 'Online';

        // Insert or update user profile data in the database
        $sql = "INSERT INTO user_profile (email, full_name, password, profile_picture, Status, Active) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE full_name=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $email, $full_name, $password, $profile_picture, $Status, $Active, $full_name);

        if ($stmt->execute()) {
            // Fetch the user_id from the database
            $sql = "SELECT user_id FROM user_profile WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $_SESSION['auth'] = true;
            $_SESSION['auth_user'] = [
                'user_id' => $user['user_id'],
                'email' => $email,
                'full_name' => $full_name,
                'password' => $password,
                'profile_picture' => $profile_picture,
                'Status' => $Status,
                'Active' => $Active

            ];
            header('Location: Dashboard.php');
            exit(0);
        } else {
            $_SESSION['status'] = "Database error: " . $stmt->error;
            header('Location: Loginform.php');
            exit(0);
        }
    } else {
        $_SESSION['status'] = "Google OAuth error: " . $token['error'];
        header('Location: Loginform.php');
        exit(0);
    }
}
?>

<div class="section" style="background-color: #FFA500; height: 100vh; display: flex; justify-content: center; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h5>Login Form</5>
                    </div>
                    <div class="card-body">

                        <?php
                        include('message.php');
                        ?>

                        <form action="logincode.php" method="POST">
                            <div class="form-group">
                                <label for="">Email</label>
                                <span></span>
                                <input type="text" name="email" class="form-control" placeholder="Email" required>
                            </div>

                            <div class="form-group">
                                <label for="">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>

                            <hr>

                            <div class="modal-footer">
                                <button type="submit" name="login_btn" class="btn btn-primary btn-block">Login</button>
                            </div>
                        </form>
                        <div class="form-group">
                            <?php
                            // Display Google Login URL
                            echo "<a href='" . $client->createAuthUrl() . "' class='btn btn-danger btn-block'>Login with Google</a>";
                            ?>
                        </div>
                        <div class="text-center">
                            <p>Don't have an account? <a href="signupform.php" class="btn-sm">Sign Up</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/script.php'); ?>
<?php include('includes/footer.php'); ?>
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
session_start();

include("dbcon.php");

if(isset($_POST['login_now_btn']))
{
    $email = $_POST['email'];
    $clearTextPassword = $_POST['password'];

    try {
        $user = $auth->getUserByEmail($email);
        $signInResult = $auth->signInWithEmailAndPassword($email, $clearTextPassword);
        $idTokenString = $signInResult->idToken();

        try {
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
            $uid = $verifiedIdToken->claims()->get('sub');
            
            $userRecord = $auth->getUser($uid);
            $displayName = $userRecord->displayName;

            $_SESSION['verified_user_id'] =$uid;
            $_SESSION['idTokenString'] =$idTokenString;

            $_SESSION['status'] = "<div class='error'>You are Logged in Successfully</div>";
            $_SESSION['username'] = $displayName;
            echo "User ID: {$_SESSION['verified_user_id']}";
            header("Location: http://localhost:3000");
            exit();

        } catch (Exception $e) {
            echo 'The token is invalid: '.$e->getMessage();
        }
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        //echo $e->getMessage();
        $_SESSION['status'] = "<div class='error'>No Email Found</div>";
        header("Location: login.php");
        exit();
    } catch (\Kreait\Firebase\Auth\SignIn\FailedToSignIn $e) {
        $_SESSION['status'] = "<div class='error'>Invalid Email or Password</div>";
        header("Location: login.php");
        exit();
    }
}
?>
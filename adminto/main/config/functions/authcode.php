<?php 

  session_start();

  include("../dbcon.php");
  include(__DIR__ . "/../functions/myfunctions.php");


    



 if(isset($_POST['login_btn']))
{
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $login_query = "SELECT * FROM admin WHERE email= '$email' AND password='$password'";
    $login_query_run = mysqli_query($con, $login_query);

    if(mysqli_num_rows($login_query_run)){
        $_SESSION['auth'] = true;

        $userdata = mysqli_fetch_array($login_query_run);
        $username = $userdata['name'];
        $useremail = $userdata['email'];
       

        $_SESSION['auth_user'] = [
            'username'=> $username,
            'email'=> $useremail
        ];
        

      

        redirect( "../../../adminDashboard/main_page.php", "connecté avec succès");
        

        

    }
    else {
        redirect("../../login.php", "E-mail ou mot de passe incorrect");
      
    }
}
?>
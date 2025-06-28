<?php
session_start(); 
include("../middleware/adminMiddlware.php");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sign'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpasswrd = $_POST['cpasswrd'];

    if ($password !== $cpasswrd) {
        $_SESSION['register_message'] = "Les mots de passe ne correspondent pas.";     
        header("Location: login-register.php");  
        exit;
    } else {
        $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
        $emailResult = mysqli_query($con, $checkEmailQuery);

        if (mysqli_num_rows($emailResult) > 0) {
            $_SESSION['register_message'] = "L'email existe déjà. Veuillez utiliser un autre email.";        
            header("Location: login-register.php");         
            exit;
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertQuery = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
            $insertResult = mysqli_query($con, $insertQuery);

            if ($insertResult) {
                $newUserId = mysqli_insert_id($con);

                $_SESSION['user_id'] = $newUserId;       
                $_SESSION['username'] = $username; 
                $_SESSION['register_message'] = "Inscription réussie !";    
                header("Location: accounts.php");   
                exit;
            } else {
                $_SESSION['register_message'] = "Erreur : " . mysqli_error($con);     
                header("Location: login-register.php");            
                exit;
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $checkUserQuery = "SELECT * FROM users WHERE email = '$email'";
    $userResult = mysqli_query($con, $checkUserQuery);

    if (mysqli_num_rows($userResult) > 0) {
        $user = mysqli_fetch_assoc($userResult);

        if ($user['status'] == 1) {
            header("Location: Access_deneid.html");
            exit;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_message'] = "Connexion réussie !";
            header("Location: accounts.php");
            exit;
        } else {
            $_SESSION['login_message'] = "Mot de passe incorrect. Veuillez réessayer.";
            header("Location: login-register.php");
            exit;
        }
    } else {
        $_SESSION['login_message'] = "Aucun compte trouvé avec cet email.";
        header("Location: login-register.php");
        exit;
    }
}

if (isset($_POST["cUsername"])) {
    $username = trim($_POST["username"]);
    $user_id = $_SESSION['user_id'];

    if (empty($username)) {
        $_SESSION['message'] = "Le nom d'utilisateur ne peut pas être vide";     
        header("Location: accounts.php");         
        exit;
    }

    $stmt = $con->prepare("UPDATE users SET username=? WHERE id=?");  
    $stmt->bind_param("si", $username, $user_id);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;       
        redirect("accounts.php", "Nom d'utilisateur changé avec succès");     
    } else {
        redirect("accounts.php", "Échec du changement de nom d'utilisateur");     
    }

    $stmt->close();
}

if (isset($_POST['cPassword'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $user_id = $_SESSION['user_id'];

    if (empty($currentPassword) || empty($newPassword)) {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
        header("Location: accounts.php");
        exit;
    }

    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($currentPassword, $user['password'])) {
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
        $updateStmt = $con->prepare($updateQuery);
        $updateStmt->bind_param("si", $hashedNewPassword, $user_id);

        if ($updateStmt->execute()) {
            redirect("accounts.php", "Changement de mot de passe réussi");
            exit;
        } else {
            redirect("accounts.php", "Échec de la mise à jour du mot de passe");
            exit;
        }
    } else {
        redirect("accounts.php", "Le mot de passe actuel est incorrect.");
        exit;
    }
}
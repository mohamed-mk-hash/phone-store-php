<?php
include("../adminto/main/config/functions/myfunctions.php");

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        $email = $_SESSION['email'];
        $user_id = $_SESSION['user_id'];
        $redirect_to = $_SESSION['redirect_to'];

        $news_query = "INSERT INTO newsletter (user_id, email) VALUES ('$user_id', '$email')";
        $news_query_run = mysqli_query($con, $news_query);

        if ($news_query_run) {
            unset($_SESSION['otp']);
            redirect($redirect_to, "Votre email a été enregistré avec succès.");
        } else {
            redirect($redirect_to, "Erreur lors de l'enregistrement.");
        }
    } else {
        $error = "Le code OTP est incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification OTP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            color: black;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .otp-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h2 {
            margin-bottom: 10px;
            color: #333;
        }
        .otp-container p {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
            text-align: center;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: black;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="otp-container">
    <h2>Vérification OTP</h2>
    <p>Entrez le code OTP envoyé à votre e-mail</p>
    <form method="POST">
        <input type="text" name="otp" placeholder="Entrez le code OTP" required>
        <button type="submit">Vérifier</button>
    </form>
    <?php if (isset($error)) echo "<p class='error' style='color: red;'>$error</p>"; ?>
</div>

</body>
</html>

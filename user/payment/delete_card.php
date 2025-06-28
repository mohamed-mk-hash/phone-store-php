<?php
session_start();
include("../../middleware/adminMiddlware.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changeCardBtn'])) {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

                $query = "DELETE FROM payment WHERE user_id = '$userId'";
        $result = mysqli_query($con, $query);

        if ($result) {
                        $_SESSION['from_delete_card'] = true;     
                                redirect("../cart/checkout.php", "Card deleted successfully");
        } else {
                        echo "Failed to delete card details: " . mysqli_error($con);
        }
    } else {
                echo "You must be logged in to delete your card details.";
    }
}
?>

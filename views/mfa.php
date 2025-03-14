<?php
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

if (!isset($_SESSION['student_id'])) {
    header("Location: testStudentLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFA PIN Verification</title>
    <link rel="stylesheet" href="../public/css/mfa.css">
    <script src="../public/js/mfa.js"></script>
</head>

<body>

    <div class="container">
        <h2><i class="fas fa-key"></i> Multi-Factor Authentication</h2>
        <p class="subtitle">Enter the 6-digit PIN to mark attendance</p>

        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message']['type'] ?>">
                <?= $_SESSION['message']['text'] ?>
            </div>
            <?php unset($_SESSION['message']); // Clear message after displaying ?>
        <?php endif; ?>

        <form action="../models/verifyMFA.php" method="POST">
            <label for="class_id">Class ID:</label>
            <input type="text" name="class_id" id="class_id" required>

            <div class="pin-container">
                <input type="text" class="pin-input" name="pin1" maxlength="1" required>
                <input type="text" class="pin-input" name="pin2" maxlength="1" required>
                <input type="text" class="pin-input" name="pin3" maxlength="1" required>
                <input type="text" class="pin-input" name="pin4" maxlength="1" required>
                <input type="text" class="pin-input" name="pin5" maxlength="1" required>
                <input type="text" class="pin-input" name="pin6" maxlength="1" required>
            </div>

            <input type="hidden" name="pin" id="full-pin">
            <button type="submit" class="verify-btn">Verify PIN</button>
        </form>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            let pin = '';
            document.querySelectorAll('.pin-input').forEach(input => {
                pin += input.value;
            });

            document.getElementById('full-pin').value = pin;

            if (pin.length !== 6) {
                e.preventDefault();
                alert("Please enter all 6 digits.");
            }
        });
    </script>

    <style>
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #fff;
            text-align: center;
        }

        .success {
            background-color: #28a745;
        }

        .error {
            background-color: #dc3545;
        }

        .warning {
            background-color: #ffc107;
            color: #000;
        }
    </style>

</body>

</html>
<?php
session_start();

// Simple authentication
$username = "admin";
$password = "your_secure_password"; // Change this to a secure password

if (!isset($_SESSION['logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['username'] === $username && $_POST['password'] === $password) {
            $_SESSION['logged_in'] = true;
        } else {
            $error = "نام کاربری یا رمز عبور اشتباه است.";
        }
    }
    
    if (!isset($_SESSION['logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html lang="fa" dir="rtl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ورود به پنل مدیریت</title>
            <link href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
            <style>
                body {
                    font-family: 'Vazirmatn', sans-serif;
                    background-color: #f5f5f5;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                }
                .login-container {
                    background-color: white;
                    padding: 2rem;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    width: 100%;
                    max-width: 400px;
                }
                h1 {
                    text-align: center;
                    color: #333;
                    margin-bottom: 2rem;
                }
                .form-group {
                    margin-bottom: 1rem;
                }
                label {
                    display: block;
                    margin-bottom: 0.5rem;
                    color: #666;
                }
                input {
                    width: 100%;
                    padding: 0.5rem;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-family: 'Vazirmatn', sans-serif;
                }
                button {
                    width: 100%;
                    padding: 0.75rem;
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-family: 'Vazirmatn', sans-serif;
                }
                button:hover {
                    background-color: #45a049;
                }
                .error {
                    color: #f44336;
                    text-align: center;
                    margin-bottom: 1rem;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <h1>ورود به پنل مدیریت</h1>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="username">نام کاربری:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">رمز عبور:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">ورود</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Read messages
$messages = [];
if (file_exists('messages.json')) {
    $messages = json_decode(file_get_contents('messages.json'), true) ?? [];
}

// Sort messages by timestamp (newest first)
usort($messages, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پیام‌های دریافتی</title>
    <link href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            margin-bottom: 2rem;
        }
        .messages {
            display: grid;
            gap: 1rem;
        }
        .message {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: #666;
        }
        .message-content {
            color: #333;
            line-height: 1.6;
        }
        .message-email {
            color: #4CAF50;
            text-decoration: none;
        }
        .message-email:hover {
            text-decoration: underline;
        }
        .logout {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .logout:hover {
            background-color: #d32f2f;
        }
        .no-messages {
            text-align: center;
            color: #666;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="?logout=1" class="logout">خروج</a>
        <h1>پیام‌های دریافتی</h1>
        <?php if (empty($messages)): ?>
            <div class="no-messages">هیچ پیامی دریافت نشده است.</div>
        <?php else: ?>
            <div class="messages">
                <?php foreach ($messages as $message): ?>
                    <div class="message">
                        <div class="message-header">
                            <span><?php echo htmlspecialchars($message['name']); ?></span>
                            <span><?php echo $message['timestamp']; ?></span>
                        </div>
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </div>
                        <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="message-email">
                            <?php echo htmlspecialchars($message['email']); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: messages.php');
    exit;
}
?> 
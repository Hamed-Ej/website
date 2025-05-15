<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['name']) || !isset($data['email']) || !isset($data['message'])) {
    echo json_encode([
        'success' => false,
        'message' => 'لطفاً تمام فیلدها را پر کنید.'
    ]);
    exit;
}

// Sanitize input
$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$message = filter_var($data['message'], FILTER_SANITIZE_STRING);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'لطفاً یک ایمیل معتبر وارد کنید.'
    ]);
    exit;
}

// Create message object
$messageData = [
    'name' => $name,
    'email' => $email,
    'message' => $message,
    'timestamp' => date('Y-m-d H:i:s')
];

// Read existing messages
$messagesFile = 'messages.json';
$messages = [];
if (file_exists($messagesFile)) {
    $messages = json_decode(file_get_contents($messagesFile), true) ?? [];
}

// Add new message
$messages[] = $messageData;

// Save messages
$saved = file_put_contents($messagesFile, json_encode($messages, JSON_PRETTY_PRINT));

// Send email to site owner
$to = 'hamedejbary@gmail.com';
$subject = 'پیام جدید از وب‌سایت شخصی';
$body = "نام: $name\nایمیل: $email\n\nپیام:\n$message";
$headers = "From: noreply@" . $_SERVER['SERVER_NAME'] . "\r\n" .
           "Reply-To: $email\r\n" .
           "Content-Type: text/plain; charset=UTF-8";

$mailSent = mail($to, $subject, $body, $headers);

if ($saved && $mailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'پیام شما با موفقیت ارسال شد و به ایمیل مدیر سایت نیز ارسال گردید.'
    ]);
} elseif ($saved) {
    echo json_encode([
        'success' => true,
        'message' => 'پیام شما ذخیره شد اما ارسال ایمیل با خطا مواجه شد.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'خطا در ذخیره پیام. لطفاً دوباره تلاش کنید.'
    ]);
}
?> 
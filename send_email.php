<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'لطفاً تمام فیلدها را پر کنید']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'لطفاً یک ایمیل معتبر وارد کنید']);
        exit;
    }
    
    // Email content
    $to = 'hamedejbary@gmail.com';
    $subject = 'پیام جدید از وب‌سایت شخصی';
    $email_content = "نام: $name\n";
    $email_content .= "ایمیل: $email\n\n";
    $email_content .= "پیام:\n$message";
    
    // Headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email
    if (mail($to, $subject, $email_content, $headers)) {
        // Save to JSON file as backup
        $data = [
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'date' => date('Y-m-d H:i:s')
        ];
        
        $file = 'messages.json';
        $current = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        $current[] = $data;
        file_put_contents($file, json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo json_encode(['success' => true, 'message' => 'پیام شما با موفقیت ارسال شد']);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطا در ارسال پیام. لطفاً دوباره تلاش کنید']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'درخواست نامعتبر']);
}
?> 
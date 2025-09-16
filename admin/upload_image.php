<?php
// 确保这是一个POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => '只接受POST请求']);
    exit;
}

// 检查是否有文件上传
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => '没有接收到图片文件']);
    exit;
}

// 设置上传目录
$targetDir = '../assets/images/';

// 确保上传目录存在
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// 获取文件信息
$fileName = basename($_FILES['image']['name']);
$fileSize = $_FILES['image']['size'];
$fileTmpPath = $_FILES['image']['tmp_name'];
$fileType = strtolower(pathinfo($targetDir . $fileName, PATHINFO_EXTENSION));

// 生成唯一文件名，避免覆盖现有文件
$timestamp = time();
$uniqueFileName = $timestamp . '_' . uniqid() . '.' . $fileType;
$targetFilePath = $targetDir . $uniqueFileName;

// 允许的文件类型
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

// 检查文件类型
if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => '不支持的图片格式，仅支持JPG, JPEG, PNG, GIF']);
    exit;
}

// 检查文件大小（限制为5MB）
$maxFileSize = 5 * 1024 * 1024; // 5MB
if ($fileSize > $maxFileSize) {
    echo json_encode(['success' => false, 'error' => '文件大小超过限制（5MB）']);
    exit;
}

// 移动上传的文件到目标目录
if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
    // 构建图片URL
    $imageUrl = '../assets/images/' . $uniqueFileName;
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'imageUrl' => $imageUrl
    ]);
} else {
    echo json_encode(['success' => false, 'error' => '文件上传失败']);
}

?>
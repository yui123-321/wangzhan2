<?php
// 包含配置文件
require_once 'config.php';

// 检查是否有表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $title = isset($_POST["title"]) ? $_POST["title"] : "";
    $category = isset($_POST["category"]) ? $_POST["category"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";
    $creator = isset($_POST["creator"]) ? $_POST["creator"] : "匿名用户";
    $progress = isset($_POST["progress"]) ? intval($_POST["progress"]) : 0;
    
    // 数据验证
    $errors = [];
    if (empty($title)) {
        $errors[] = "项目标题不能为空";
    }
    if (empty($category)) {
        $errors[] = "请选择项目类别";
    }
    if (empty($description)) {
        $errors[] = "项目描述不能为空";
    }
    
    // 如果有错误，返回错误信息
    if (!empty($errors)) {
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: submit_project.php");
        exit();
    }
    
    // 处理上传的图片
    $image_url = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . '_' . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // 允许的图片格式
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        
        if (in_array($imageFileType, $allowed_types)) {
            // 创建上传目录（如果不存在）
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $target_file;
            } else {
                $errors[] = "上传图片失败。";
            }
        } else {
            $errors[] = "不支持的图片格式。请上传JPG、JPEG、PNG或GIF格式的图片。";
        }
    } else {
        // 如果没有上传图片，使用默认图片
        $image_url = "images/default_project.jpg";
    }
    
    // 如果有图片上传错误，返回错误信息
    if (!empty($errors)) {
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: submit_project.php");
        exit();
    }
    
    // 创建数据库连接
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // 检查连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    
    // 准备SQL语句
    $sql = "INSERT INTO projects (title, category, description, creator, image_url, progress, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    // 检查SQL准备是否成功
    if (!$stmt) {
        die("SQL准备失败: " . $conn->error);
    }
    
    // 绑定参数
    $stmt->bind_param("sssssi", $title, $category, $description, $creator, $image_url, $progress);
    
    // 执行SQL语句
    if ($stmt->execute()) {
        // 获取新插入的项目ID
        $project_id = $conn->insert_id;
        
        // 关闭连接
        $stmt->close();
        $conn->close();
        
        // 重定向到成功页面，并传递项目ID
        header("Location: success.php?id=$project_id");
        exit();
    } else {
        // 记录错误并返回
        $errors[] = "提交项目失败: " . $stmt->error;
        $stmt->close();
        $conn->close();
        
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: submit_project.php");
        exit();
    }
} else {
    // 如果不是POST请求，重定向到首页
    header("Location: index.php");
    exit();
}
?>

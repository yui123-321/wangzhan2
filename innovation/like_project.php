<?php
// 包含配置文件
require_once 'config.php';

// 初始化返回数据
$response = [
    'success' => false,
    'message' => '请求失败',
    'likes' => 0
];

// 检查是否有POST请求
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 检查项目ID是否存在
    if (!isset($_POST['project_id']) || !is_numeric($_POST['project_id'])) {
        $response['message'] = '无效的项目ID';
        echo json_encode($response);
        exit();
    }
    
    $project_id = intval($_POST['project_id']);
    
    // 创建数据库连接
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // 检查连接
    if ($conn->connect_error) {
        $response['message'] = '数据库连接失败: ' . $conn->connect_error;
        echo json_encode($response);
        exit();
    }
    
    // 检查用户是否已经点赞过（使用会话跟踪）
    session_start();
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $session_key = 'liked_project_' . $project_id;
    
    // 如果用户已经点赞过，不允许重复点赞
    if (isset($_SESSION[$session_key]) && $_SESSION[$session_key] == $user_ip) {
        $response['message'] = '您已经点赞过该项目';
        echo json_encode($response);
        exit();
    }
    
    // 开始事务
    $conn->begin_transaction();
    
    try {
        // 获取当前点赞数
        $sql = "SELECT likes FROM projects WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception('项目不存在');
        }
        
        $row = $result->fetch_assoc();
        $current_likes = $row['likes'];
        
        // 更新点赞数
        $new_likes = $current_likes + 1;
        $update_sql = "UPDATE projects SET likes = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_likes, $project_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception('更新点赞数失败');
        }
        
        // 提交事务
        $conn->commit();
        
        // 标记用户已点赞
        $_SESSION[$session_key] = $user_ip;
        
        // 返回成功信息
        $response['success'] = true;
        $response['message'] = '点赞成功';
        $response['likes'] = $new_likes;
        
    } catch (Exception $e) {
        // 回滚事务
        $conn->rollback();
        $response['message'] = '操作失败: ' . $e->getMessage();
    } finally {
        // 关闭连接
        if (isset($stmt)) $stmt->close();
        if (isset($update_stmt)) $update_stmt->close();
        $conn->close();
    }
}

// 返回JSON响应
header('Content-Type: application/json');
echo json_encode($response);
?>

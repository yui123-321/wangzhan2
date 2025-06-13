<?php
// 包含配置文件
require_once 'config.php';

// 检查项目ID是否存在
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$project_id = intval($_GET['id']);

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 准备SQL语句
$sql = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);

// 执行SQL语句
if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    // 检查是否找到项目
    if ($result->num_rows == 0) {
        header("Location: index.php");
        exit();
    }
    
    // 获取项目数据
    $project = $result->fetch_assoc();
    
    // 增加浏览量
    $views = $project['views'] + 1;
    $update_sql = "UPDATE projects SET views = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $views, $project_id);
    $update_stmt->execute();
    $update_stmt->close();
} else {
    die("查询失败: " . $stmt->error);
}

// 检查用户是否已经点赞过（用于前端显示）
session_start();
$user_ip = $_SERVER['REMOTE_ADDR'];
$session_key = 'liked_project_' . $project_id;
$has_liked = isset($_SESSION[$session_key]) && $_SESSION[$session_key] == $user_ip;

// 关闭项目查询的语句
$stmt->close();

// 获取同类别项目作为推荐
$related_sql = "SELECT * FROM projects WHERE category = ? AND id != ? ORDER BY RAND() LIMIT 3";
$related_stmt = $conn->prepare($related_sql);
$related_stmt->bind_param("si", $project['category'], $project_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();

// 项目详情页HTML代码
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - 项目详情</title>
    <!-- 引入Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- 引入Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- 配置Tailwind自定义颜色和字体 -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F4C81',
                        secondary: '#22C55E',
                        accent: '#FF9F1C',
                        dark: '#1E293B',
                        light: '#F8FAFC'
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- 自定义工具类 -->
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
            .card-hover {
                @apply transition-all duration-300 hover:shadow-lg hover:-translate-y-1;
            }
            .btn-primary {
                @apply bg-secondary text-white font-medium py-3 px-8 rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300;
            }
            .text-shadow {
                text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .like-button-active {
                @apply bg-red-500 text-white;
            }
        }
    </style>
    
    <style>
        /* 平滑滚动 */
        html {
            scroll-behavior: smooth;
        }
        
        /* 项目卡片渐变覆盖层 */
        .project-gradient {
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.6) 100%);
        }
        
        /* 项目详情页样式 */
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #E2E8F0;
            overflow: hidden;
        }
        
        .progress-value {
            height: 100%;
            border-radius: 4px;
            background-color: #22C55E;
        }
        
        /* 点赞按钮动画 */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .like-animation {
            animation: pulse 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-light font-sans">
    <!-- 导航栏 -->
    <header id="navbar" class="fixed w-full top-0 z-50 transition-all duration-300">
        <nav class="bg-primary text-white py-4 px-6 md:px-12">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fa fa-rocket text-2xl text-secondary"></i>
                    <div class="text-xl font-bold">创梦空间</div>
                </div>
                
                <!-- 桌面导航 -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="hover:text-secondary transition-colors">首页</a>
                    <a href="project_list.php" class="hover:text-secondary transition-colors">项目库</a>
                    <a href="#" class="hover:text-secondary transition-colors">资源中心</a>
                    <a href="#" class="hover:text-secondary transition-colors">热门项目</a>
                    <a href="submit_project.php" class="btn-primary">创建项目</a>
                </div>
                
                <!-- 移动端菜单按钮 -->
                <div class="md:hidden">
                    <button id="menu-toggle" class="text-white focus:outline-none">
                        <i class="fa fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- 移动端导航菜单 -->
            <div id="mobile-menu" class="md:hidden hidden bg-primary/95 backdrop-blur-sm">
                <div class="container mx-auto py-4 px-6 flex flex-col space-y-4">
                    <a href="index.php" class="py-2 hover:text-secondary transition-colors">首页</a>
                    <a href="project_list.php" class="py-2 hover:text-secondary transition-colors">项目库</a>
                    <a href="#" class="py-2 hover:text-secondary transition-colors">资源中心</a>
                    <a href="#" class="py-2 hover:text-secondary transition-colors">热门项目</a>
                    <a href="submit_project.php" class="btn-primary text-center mt-2">创建项目</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- 主要内容区域 -->
    <main class="container mx-auto px-4 py-24 min-h-screen">
        <!-- 返回按钮 -->
        <div class="mb-6">
            <a href="javascript:history.back()" class="inline-flex items-center text-primary hover:text-primary/80 transition-colors">
                <i class="fa fa-arrow-left mr-2"></i> 返回项目列表
            </a>
        </div>
        
        <!-- 项目详情卡片 -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- 项目封面 -->
            <div class="relative h-64 md:h-80">
                <img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 project-gradient flex flex-col justify-end p-6 md:p-10">
                    <h1 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold text-white text-shadow mb-2">
                        <?php echo htmlspecialchars($project['title']); ?>
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-white/90">
                        <span class="flex items-center">
                            <i class="fa fa-folder mr-2"></i>
                            <?php echo htmlspecialchars($project['category']); ?>
                        </span>
                        <span class="flex items-center">
                            <i class="fa fa-user mr-2"></i>
                            <?php echo htmlspecialchars($project['creator']); ?>
                        </span>
                        <span class="flex items-center">
                            <i class="fa fa-eye mr-2"></i>
                            <?php echo $project['views']; ?> 次浏览
                        </span>
                        <span class="flex items-center">
                            <i class="fa fa-heart mr-2"></i>
                            <?php echo $project['likes']; ?> 个点赞
                        </span>
                        <span class="flex items-center">
                            <i class="fa fa-calendar mr-2"></i>
                            <?php echo date('Y-m-d', strtotime($project['created_at'])); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- 项目内容 -->
            <div class="p-6 md:p-10">
                <!-- 项目进度 -->
                <div class="mb-8">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">项目进度</span>
                        <span class="text-secondary font-medium"><?php echo $project['progress']; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-value" style="width: <?php echo $project['progress']; ?>%"></div>
                    </div>
                </div>
                
                <!-- 项目描述 -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 text-dark">项目描述</h2>
                    <p class="text-gray-600 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                    </p>
                </div>
                
                <!-- 互动按钮 -->
                <div class="flex flex-wrap gap-4 mt-10">
                    <button id="like-button" class="flex items-center py-3 px-8 rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 <?php echo $has_liked ? 'like-button-active' : 'bg-gray-100 text-gray-600'; ?>" onclick="likeProject(<?php echo $project_id; ?>)">
                        <i id="like-icon" class="fa <?php echo $has_liked ? 'fa-heart' : 'fa-heart-o'; ?> mr-2"></i>
                        <span>点赞</span>
                        <span id="like-count" class="ml-2">(<?php echo $project['likes']; ?>)</span>
                    </button>
                    <button class="bg-primary text-white font-medium py-3 px-8 rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center">
                        <i class="fa fa-share-alt mr-2"></i> 分享
                    </button>
                    <a href="submit_project.php?edit=<?php echo $project_id; ?>" class="bg-accent text-white font-medium py-3 px-8 rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center">
                        <i class="fa fa-edit mr-2"></i> 编辑项目
                    </a>
                </div>
            </div>
        </div>
        
        <!-- 相关项目推荐 -->
        <?php
        if ($related_result->num_rows > 0) {
        ?>
        <div class="mt-16">
            <h2 class="text-2xl font-bold mb-8 text-dark">相关项目</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                while ($related_project = $related_result->fetch_assoc()) {
                ?>
                <a href="project_detail.php?id=<?php echo $related_project['id']; ?>" class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
                    <div class="relative h-40">
                        <img src="<?php echo htmlspecialchars($related_project['image_url']); ?>" alt="<?php echo htmlspecialchars($related_project['title']); ?>" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 project-gradient flex flex-col justify-end p-4">
                            <span class="bg-primary/80 text-white text-xs font-medium py-1 px-3 rounded-full inline-block w-fit mb-2">
                                <?php echo htmlspecialchars($related_project['category']); ?>
                            </span>
                            <h3 class="text-white font-bold text-lg"><?php echo htmlspecialchars($related_project['title']); ?></h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span class="flex items-center">
                                <i class="fa fa-eye mr-1"></i> <?php echo $related_project['views']; ?>
                            </span>
                            <span class="flex items-center">
                                <i class="fa fa-heart mr-1"></i> <?php echo $related_project['likes']; ?>
                            </span>
                        </div>
                    </div>
                </a>
                <?php
                }
                ?>
            </div>
        </div>
        <?php
        }
        // 关闭相关项目查询的语句
        $related_stmt->close();
        
        // 最后关闭数据库连接
        $conn->close();
        ?>
    </main>

    <!-- 页脚 -->
    <footer class="bg-dark text-white py-12 mt-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <i class="fa fa-rocket text-2xl text-secondary"></i>
                        <div class="text-xl font-bold">创梦空间</div>
                    </div>
                    <p class="text-gray-400 mb-6">
                        大学生创新创业平台，汇聚创意与资源，助力梦想起航。
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-secondary transition-colors">
                            <i class="fa fa-weibo text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-secondary transition-colors">
                            <i class="fa fa-wechat text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-secondary transition-colors">
                            <i class="fa fa-github text-xl"></i>
                        </a>
                    </div>
                
<div>
                    <h3 class="text-lg font-bold mb-6">联系我们</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fa fa-map-marker mt-1 mr-3 text-secondary"></i>
                            <span class="text-gray-400">北京市海淀区清华大学科技园</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fa fa-envelope mr-3 text-secondary"></i>
                            <span class="text-gray-400">contact@dreamspace.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fa fa-phone mr-3 text-secondary"></i>
                            <span class="text-gray-400">010-12345678</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-10 pt-6 text-center text-gray-500">
                <p>© 2023 创梦空间 版权所有 | 京ICP备12345678号</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // 导航栏滚动效果
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('nav-scrolled');
            } else {
                navbar.classList.remove('nav-scrolled');
            }
        });
        
        // 移动端菜单切换
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        // 平滑滚动到锚点
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // 如果是移动端，点击后关闭菜单
                    if (window.innerWidth < 768) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });
        
        // 点赞功能
        function likeProject(projectId) {
            const likeButton = document.getElementById('like-button');
            const likeIcon = document.getElementById('like-icon');
            const likeCount = document.getElementById('like-count');
            
            // 检查按钮状态
            if (likeButton.classList.contains('like-button-active')) {
                // 已点赞，提示用户
                alert('您已经点赞过该项目了');
                return;
            }
            
            // 显示加载状态
            likeButton.disabled = true;
            likeIcon.classList.remove('fa-heart-o');
            likeIcon.classList.add('fa-heart');
            likeButton.classList.add('like-button-active');
            
            // 创建点赞动画
            likeButton.classList.add('like-animation');
            setTimeout(() => {
                likeButton.classList.remove('like-animation');
            }, 500);
            
            // 发送AJAX请求到后端
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'like_project.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    likeButton.disabled = false;
                    
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            
                            if (response.success) {
                                // 更新点赞数
                                likeCount.textContent = '(' + response.likes + ')';
                                
                                // 显示成功提示
                                showNotification('点赞成功！感谢您的支持。', 'success');
                            } else {
                                // 操作失败，恢复按钮状态
                                likeIcon.classList.remove('fa-heart');
                                likeIcon.classList.add('fa-heart-o');
                                likeButton.classList.remove('like-button-active');
                                
                                // 显示错误提示
                                showNotification(response.message || '点赞失败，请重试', 'error');
                            }
                        } catch (e) {
                            // JSON解析错误
                            likeIcon.classList.remove('fa-heart');
                            likeIcon.classList.add('fa-heart-o');
                            likeButton.classList.remove('like-button-active');
                            
                            showNotification('服务器响应错误，请重试', 'error');
                        }
                    } else {
                        // 请求失败，恢复按钮状态
                        likeIcon.classList.remove('fa-heart');
                        likeIcon.classList.add('fa-heart-o');
                        likeButton.classList.remove('like-button-active');
                        
                        showNotification('网络请求失败，请重试', 'error');
                    }
                }
            };
            
            // 发送请求
            xhr.send('project_id=' + projectId);
        }
        
        // 显示通知提示
        function showNotification(message, type = 'info') {
            // 创建通知元素
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-y-20 opacity-0';
            
            // 设置通知样式
            if (type === 'success') {
                notification.classList.add('bg-green-500', 'text-white');
            } else if (type === 'error') {
                notification.classList.add('bg-red-500', 'text-white');
            } else {
                notification.classList.add('bg-blue-500', 'text-white');
            }
            
            // 设置通知内容
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            // 添加到页面
            document.body.appendChild(notification);
            
            // 显示通知
            setTimeout(() => {
                notification.classList.remove('translate-y-20', 'opacity-0');
            }, 10);
            
            // 自动关闭
            setTimeout(() => {
                notification.classList.add('translate-y-20', 'opacity-0');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>

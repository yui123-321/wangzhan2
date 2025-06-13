<?php
// 包含配置文件
require_once 'config.php';

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 查询项目数据（按创建时间排序，最新的在前）
$sql = "SELECT * FROM projects ORDER BY created_at DESC";
$result = $conn->query($sql);

// 分类筛选（如果有分类参数）
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $sql = "SELECT * FROM projects WHERE category = '$category' ORDER BY created_at DESC";
    $result = $conn->query($sql);
}

// 搜索功能（如果有搜索参数）
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM projects WHERE title LIKE '%$search%' OR description LIKE '%$search%' ORDER BY created_at DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>创新项目库 - 创梦空间</title>
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
                @apply bg-secondary text-white font-medium py-2 px-6 rounded-full shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300;
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
        <!-- 页面标题 -->
        <div class="text-center mb-12">
            <h1 class="text-[clamp(1.8rem,5vw,3rem)] font-bold text-dark mb-4">创新项目库</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                探索大学生创新创业项目，发现创意灵感，寻找合作伙伴
            </p>
        </div>
        
        <!-- 搜索和筛选 -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 搜索框 -->
                <div class="md:col-span-2">
                    <form action="index.php" method="GET">
                        <div class="relative">
                            <input type="text" name="search" placeholder="搜索项目名称或描述..." 
                                   class="w-full py-3 px-4 pl-12 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </form>
                </div>
                
                <!-- 分类筛选 -->
                <div>
                    <select id="category-filter" class="w-full py-3 px-4 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="all">所有分类</option>
                        <?php
                        // 获取所有分类
                        $category_sql = "SELECT DISTINCT category FROM projects";
                        $category_result = $conn->query($category_sql);
                        
                        if ($category_result->num_rows > 0) {
                            while ($category_row = $category_result->fetch_assoc()) {
                                $selected = (isset($_GET['category']) && $_GET['category'] == $category_row['category']) ? 'selected' : '';
                                echo '<option value="' . $category_row['category'] . '" ' . $selected . '>' . $category_row['category'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- 项目列表 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            if ($result->num_rows > 0) {
                // 输出数据
                while ($row = $result->fetch_assoc()) {
            ?>
            <!-- 项目卡片 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
                <!-- 项目封面 -->
                <div class="relative h-48">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 project-gradient flex flex-col justify-end p-4">
                        <span class="bg-primary/80 text-white text-xs font-medium py-1 px-3 rounded-full inline-block w-fit mb-2">
                            <?php echo htmlspecialchars($row['category']); ?>
                        </span>
                        <h3 class="text-white font-bold text-lg"><?php echo htmlspecialchars($row['title']); ?></h3>
                    </div>
                </div>
                
                <!-- 项目信息 -->
                <div class="p-5">
                    <!-- 项目进度 -->
                    <div class="flex justify-between mb-2">
                        <span class="text-xs text-gray-500">项目进度</span>
                        <span class="text-xs font-medium text-secondary"><?php echo $row['progress']; ?>%</span>
                    </div>
                    <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-secondary" style="width: <?php echo $row['progress']; ?>%"></div>
                    </div>
                    
                    <!-- 项目描述 -->
                    <p class="text-gray-600 text-sm mt-4 line-clamp-3">
                        <?php echo htmlspecialchars($row['description']); ?>
                    </p>
                    
                    <!-- 作者和日期 -->
                    <div class="flex justify-between items-center mt-4 text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fa fa-user mr-1"></i> <?php echo htmlspecialchars($row['creator']); ?>
                        </span>
                        <span class="flex items-center">
                            <i class="fa fa-calendar mr-1"></i> <?php echo date('Y-m-d', strtotime($row['created_at'])); ?>
                        </span>
                    </div>
                    
                    <!-- 互动数据 -->
                    <div class="flex justify-between items-center mt-4 text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fa fa-eye mr-1"></i> <?php echo $row['views']; ?>
                        </span>
                        <span class="flex items-center">
                            <i class="fa fa-heart mr-1"></i> <?php echo $row['likes']; ?>
                        </span>
                    </div>
                    
                    <!-- 查看详情按钮 -->
                    <div class="mt-6">
                        <a href="project_detail.php?id=<?php echo $row['id']; ?>" class="btn-primary w-full text-center inline-block">
                            查看详情
                        </a>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo '<div class="col-span-full text-center py-12">';
                echo '<i class="fa fa-search text-5xl text-gray-300 mb-4"></i>';
                echo '<h3 class="text-xl font-medium text-gray-500">没有找到相关项目</h3>';
                echo '<p class="text-gray-400 mt-2">尝试使用不同的搜索词或分类筛选</p>';
                echo '<a href="index.php" class="btn-primary mt-6">查看所有项目</a>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- 分页（如果需要） -->
        <?php
        // 简单分页示例（实际项目中可能需要更复杂的分页逻辑）
        if ($result->num_rows > 12) {
        ?>
        <div class="mt-12 flex justify-center">
            <nav class="flex items-center space-x-1">
                <a href="#" class="px-3 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">
                    <i class="fa fa-angle-left"></i>
                </a>
                <a href="#" class="px-4 py-2 rounded-lg bg-primary text-white">1</a>
                <a href="#" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">2</a>
                <a href="#" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">3</a>
                <span class="px-3 py-2 text-gray-500">...</span>
                <a href="#" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">8</a>
                <a href="#" class="px-3 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">
                    <i class="fa fa-angle-right"></i>
                </a>
            </nav>
        </div>
        <?php
        }
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
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-6">快速链接</h3>
                    <ul class="space-y-3">
                        <li><a href="index.php" class="text-gray-400 hover:text-secondary transition-colors">首页</a></li>
                        <li><a href="project_list.php" class="text-gray-400 hover:text-secondary transition-colors">项目库</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-secondary transition-colors">资源中心</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-secondary transition-colors">热门项目</a></li>
                        <li><a href="submit_project.php" class="text-gray-400 hover:text-secondary transition-colors">创建项目</a></li>
                    </ul>
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
        
        // 分类筛选功能
        const categoryFilter = document.getElementById('category-filter');
        categoryFilter.addEventListener('change', () => {
            const selectedCategory = categoryFilter.value;
            if (selectedCategory === 'all') {
                window.location.href = 'index.php';
            } else {
                window.location.href = `index.php?category=${selectedCategory}`;
            }
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
    </script>
</body>
</html>

<?php
// 关闭数据库连接
$conn->close();
?>

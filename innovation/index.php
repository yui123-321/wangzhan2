<?php
// 包含配置文件
require_once 'config.php';

// 获取项目数据
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 获取所有项目
$sql = "SELECT * FROM projects ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);
$projects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

// 获取热门项目
$hotSql = "SELECT * FROM projects ORDER BY views DESC LIMIT 3";
$hotResult = $conn->query($hotSql);
$hotProjects = [];
if ($hotResult->num_rows > 0) {
    while ($row = $hotResult->fetch_assoc()) {
        $hotProjects[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>大学生创新创业平台</title>
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
        }
    </style>
    
    <style>
        /* 平滑滚动 */
        html {
            scroll-behavior: smooth;
        }
        
        /* 导航栏滚动效果 */
        .nav-scrolled {
            background-color: rgba(15, 76, 129, 0.95);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
                    <a href="#hero" class="hover:text-secondary transition-colors">首页</a>
                    <a href="#projects" class="hover:text-secondary transition-colors">项目库</a>
                    <a href="#resources" class="hover:text-secondary transition-colors">资源中心</a>
                    <a href="#hot" class="hover:text-secondary transition-colors">热门项目</a>
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
                    <a href="#hero" class="py-2 hover:text-secondary transition-colors">首页</a>
                    <a href="#projects" class="py-2 hover:text-secondary transition-colors">项目库</a>
                    <a href="#resources" class="py-2 hover:text-secondary transition-colors">资源中心</a>
                    <a href="#hot" class="py-2 hover:text-secondary transition-colors">热门项目</a>
                    <a href="submit_project.php" class="btn-primary text-center mt-2">创建项目</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero区域 -->
    <section id="hero" class="pt-24 md:pt-0 min-h-screen flex items-center bg-gradient-to-br from-primary to-primary/80 text-white">
        <div class="container mx-auto px-6 py-20 md:py-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h1 class="text-[clamp(2.5rem,5vw,4rem)] font-bold leading-tight text-shadow">
                        点燃创意，<br>成就创业梦想
                    </h1>
                    <p class="text-[clamp(1rem,2vw,1.25rem)] text-white/90 max-w-lg">
                        大学生创新创业平台致力于为有志青年提供项目展示、资源对接和团队协作的一站式服务，助您实现创业理想。
                    </p>
                    <div class="flex flex-wrap gap-4 pt-4">
                        <a href="#projects" class="btn-primary bg-white text-primary hover:bg-white/90">
                            浏览项目
                        </a>
                        <a href="submit_project.php" class="btn-primary bg-secondary hover:bg-secondary/90">
                            创建项目
                        </a>
                    </div>
                    <div class="flex items-center space-x-6 pt-4">
                        <div class="flex -space-x-2">
                            <?php for ($i = 0; $i < 5; $i++) { ?>
                                <img src="https://picsum.photos/200/200?random=<?php echo $i; ?>" alt="用户头像" class="w-10 h-10 rounded-full border-2 border-white">
                            <?php } ?>
                        </div>
                        <div class="text-sm">
                            <span class="font-bold">1,000+</span> 大学生已加入
                        </div>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-accent/20 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-60 h-60 bg-secondary/20 rounded-full blur-3xl"></div>
                    <img src="https://picsum.photos/800/600?random=1" alt="大学生创新创业" class="relative z-10 rounded-2xl shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    <!-- 功能模块 -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold text-dark mb-4">
                    一站式创业服务平台
                </h2>
                <p class="text-gray-600 text-lg">
                    我们提供全方位的创业支持，帮助大学生将创意转化为实际项目
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- 模块1 -->
                <div class="bg-light rounded-xl p-8 shadow-md card-hover">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <i class="fa fa-briefcase text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">项目展示</h3>
                    <p class="text-gray-600">
                        展示您的创新项目，吸引志同道合的伙伴和投资者
                    </p>
                </div>
                
                <!-- 模块2 -->
                <div class="bg-light rounded-xl p-8 shadow-md card-hover">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <i class="fa fa-users text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">团队组建</h3>
                    <p class="text-gray-600">
                        寻找技术、设计、市场等领域的合作伙伴，共同实现创业梦想
                    </p>
                </div>
                
                <!-- 模块3 -->
                <div class="bg-light rounded-xl p-8 shadow-md card-hover">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <i class="fa fa-book text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">资源共享</h3>
                    <p class="text-gray-600">
                        获取创业指南、技术文档、资金申请等丰富资源支持
                    </p>
                </div>
                
                <!-- 模块4 -->
                <div class="bg-light rounded-xl p-8 shadow-md card-hover">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <i class="fa fa-comments text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">交流社区</h3>
                    <p class="text-gray-600">
                        加入创业交流社区，分享经验，解决问题，共同成长
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 项目库 -->
    <section id="projects" class="py-20 bg-light">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
                <div>
                    <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold text-dark mb-4">
                        创新项目库
                    </h2>
                    <p class="text-gray-600 text-lg max-w-2xl">
                        探索大学生们的创新项目，寻找合作机会或获取灵感
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="flex flex-wrap gap-2">
                        <button class="px-4 py-2 rounded-full bg-primary text-white text-sm">全部</button>
                        <button class="px-4 py-2 rounded-full bg-white text-gray-700 text-sm hover:bg-gray-100 transition-colors">科技</button>
                        <button class="px-4 py-2 rounded-full bg-white text-gray-700 text-sm hover:bg-gray-100 transition-colors">教育</button>
                        <button class="px-4 py-2 rounded-full bg-white text-gray-700 text-sm hover:bg-gray-100 transition-colors">健康</button>
                        <button class="px-4 py-2 rounded-full bg-white text-gray-700 text-sm hover:bg-gray-100 transition-colors">环保</button>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($projects as $project) { ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-md card-hover group">
                    <div class="relative h-60 overflow-hidden">
                        <img src="<?php echo $project['image_url']; ?>" alt="<?php echo $project['title']; ?>" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 project-gradient"></div>
                        <div class="absolute bottom-0 left-0 p-4">
                            <span class="inline-block px-3 py-1 bg-secondary text-white text-sm rounded-full mb-2">
                                <?php echo $project['category']; ?>
                            </span>
                            <h3 class="text-white text-xl font-bold text-shadow">
                                <?php echo $project['title']; ?>
                            </h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            <?php echo $project['description']; ?>
                        </p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <img src="https://picsum.photos/100/100?random=<?php echo $project['id']; ?>" alt="用户头像" class="w-8 h-8 rounded-full">
                                <span class="text-gray-700 text-sm"><?php echo $project['creator']; ?></span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-500 text-sm"><i class="fa fa-eye mr-1"></i> <?php echo $project['views']; ?></span>
                                <span class="text-gray-500 text-sm"><i class="fa fa-heart mr-1"></i> <?php echo $project['likes']; ?></span>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-gray-500 text-sm"><?php echo date('Y-m-d', strtotime($project['created_at'])); ?></span>
                            <a href="project_detail.php?id=<?php echo $project['id']; ?>" class="text-primary font-medium hover:text-primary/80 transition-colors">
                                查看详情 <i class="fa fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="project_list.php" class="btn-primary bg-primary hover:bg-primary/90">
                    查看更多项目 <i class="fa fa-long-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 资源中心 -->
    <section id="resources" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold text-dark mb-4">
                    创业资源中心
                </h2>
                <p class="text-gray-600 text-lg">
                    提供创业所需的各类资源，助您快速启动和发展项目
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- 资源1 -->
                <div class="bg-light rounded-xl overflow-hidden shadow-md card-hover">
                    <div class="h-48 bg-primary/10 flex items-center justify-center">
                        <i class="fa fa-file-text-o text-6xl text-primary/50"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-dark mb-3">创业指南</h3>
                        <p class="text-gray-600 mb-4">
                            从创意到落地的完整指南，包含商业计划书模板、市场分析方法等
                        </p>
                        <a href="#" class="inline-flex items-center text-primary font-medium hover:text-primary/80 transition-colors">
                            查看资源 <i class="fa fa-angle-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- 资源2 -->
                <div class="bg-light rounded-xl overflow-hidden shadow-md card-hover">
                    <div class="h-48 bg-secondary/10 flex items-center justify-center">
                        <i class="fa fa-graduation-cap text-6xl text-secondary/50"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-dark mb-3">在线课程</h3>
                        <p class="text-gray-600 mb-4">
                            提供创业、技术、营销等领域的免费和付费课程，帮助您提升技能
                        </p>
                        <a href="#" class="inline-flex items-center text-primary font-medium hover:text-primary/80 transition-colors">
                            查看课程 <i class="fa fa-angle-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- 资源3 -->
                <div class="bg-light rounded-xl overflow-hidden shadow-md card-hover">
                    <div class="h-48 bg-accent/10 flex items-center justify-center">
                        <i class="fa fa-line-chart text-6xl text-accent/50"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-dark mb-3">融资信息</h3>
                        <p class="text-gray-600 mb-4">
                            整合各类大学生创业基金、天使投资和风险投资信息，帮助您获取资金支持
                        </p>
                        <a href="#" class="inline-flex items-center text-primary font-medium hover:text-primary/80 transition-colors">
                            了解详情 <i class="fa fa-angle-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 热门项目 -->
    <section id="hot" class="py-20 bg-gradient-to-br from-primary/5 to-primary/10">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold text-dark mb-4">
                    热门项目推荐
                </h2>
                <p class="text-gray-600 text-lg">
                    这些项目获得了广泛关注，或许能给您带来新的灵感和合作机会
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <?php foreach ($hotProjects as $project) { ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-lg card-hover relative">
                    <div class="absolute top-4 right-4 bg-accent text-white text-sm font-bold px-3 py-1 rounded-full">
                        热门
                    </div>
                    <div class="relative h-80 overflow-hidden">
                        <img src="<?php echo $project['image_url']; ?>" alt="<?php echo $project['title']; ?>" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6">
                            <span class="inline-block px-3 py-1 bg-secondary text-white text-sm rounded-full mb-3">
                                <?php echo $project['category']; ?>
                            </span>
                            <h3 class="text-white text-2xl font-bold mb-2">
                                <?php echo $project['title']; ?>
                            </h3>
                            <p class="text-white/80 mb-4">
                                <?php echo mb_substr($project['description'], 0, 80) . '...'; ?>
                            </p>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <img src="https://picsum.photos/100/100?random=<?php echo $project['id']; ?>" alt="用户头像" class="w-10 h-10 rounded-full border-2 border-white">
                                    <span class="text-white"><?php echo $project['creator']; ?></span>
                                </div>
                                <span class="text-white flex items-center">
                                    <i class="fa fa-eye mr-1"></i> <?php echo $project['views']; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="text-gray-500 text-sm">项目进度</p>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                                    <div class="bg-secondary h-2.5 rounded-full" style="width: <?php echo $project['progress']; ?>%"></div>
                                </div>
                            </div>
                            <span class="text-gray-700 font-medium"><?php echo $project['progress']; ?>%</span>
                        </div>
                        <a href="project_detail.php?id=<?php echo $project['id']; ?>" class="btn-primary w-full text-center">
                            了解详情
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- 号召性行动 -->
    <section class="py-20 bg-primary text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold mb-6">
                你的创意值得被实现
            </h2>
            <p class="text-white/80 text-lg max-w-2xl mx-auto mb-10">
                无论你是有想法的创业者，还是寻找合作机会的学生，我们都能为你提供支持
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="submit_project.php" class="btn-primary bg-white text-primary hover:bg-white/90">
                    发布项目
                </a>
                <a href="project_list.php" class="btn-primary bg-transparent border-2 border-white hover:bg-white/10">
                    浏览项目
                </a>
            </div>
        </div>
    </section>

    <!-- 页脚 -->
    <footer class="bg-dark text-white py-16">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <i class="fa fa-rocket text-2xl text-secondary"></i>
                        <div class="text-xl font-bold">创梦空间</div>
                    </div>
                    <p class="text-gray-400 mb-6">
                        大学生创新创业平台致力于为有志青年提供项目展示、资源对接和团队协作的一站式服务。
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center hover:bg-secondary transition-colors">
                            <i class="fa fa-weixin"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center hover:bg-secondary transition-colors">
                            <i class="fa fa-qq"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center hover:bg-secondary transition-colors">
                            <i class="fa fa-weibo"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-6">快速链接</h3>
                    <ul class="space-y-4">
                        <li><a href="#hero" class="text-gray-400 hover:text-white transition-colors">首页</a></li>
                        <li><a href="#projects" class="text-gray-400 hover:text-white transition-colors">项目库</a></li>
                        <li><a href="#resources" class="text-gray-400 hover:text-white transition-colors">资源中心</a></li>
                        <li><a href="#hot" class="text-gray-400 hover:text-white transition-colors">热门项目</a></li>
                        <li><a href="submit_project.php" class="text-gray-400 hover:text-white transition-colors">发布项目</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-6">联系我们</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fa fa-map-marker mt-1 mr-3 text-secondary"></i>
                            <span class="text-gray-400">北京市海淀区中关村大街1号</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fa fa-phone mr-3 text-secondary"></i>
                            <span class="text-gray-400">010-12345678</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fa fa-envelope mr-3 text-secondary"></i>
                            <span class="text-gray-400">contact@example.com</span>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-6">订阅更新</h3>
                    <p class="text-gray-400 mb-4">
                        订阅我们的邮件，获取最新项目和创业资讯
                    </p>
                    <form class="flex">
                        <input type="email" placeholder="您的邮箱地址" class="px-4 py-3 rounded-l-lg bg-gray-800 text-white w-full focus:outline-none focus:ring-2 focus:ring-secondary">
                        <button type="submit" class="bg-secondary px-4 rounded-r-lg hover:bg-secondary/90 transition-colors">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm mb-4 md:mb-0">
                    © 2025 大学生创新创业平台 版权所有
                </p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors">隐私政策</a>
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors">使用条款</a>
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors">京ICP备12345678号</a>
                </div>
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
    </script>
</body>
</html>

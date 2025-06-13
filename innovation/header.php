<?php
// 包含配置文件
require_once 'config.php';

// 开始会话（如果需要）
if (!isset($_SESSION)) {
    session_start();
}
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

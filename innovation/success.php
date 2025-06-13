<?php
// 包含配置文件和头部
require_once 'config.php';
require_once 'header.php';
?>

<!-- 主要内容区域 -->
<main class="container mx-auto px-4 py-20 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl p-12 max-w-md w-full mx-auto text-center transform transition-all duration-300 hover:scale-105">
        <!-- 成功图标 -->
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa fa-check text-4xl text-green-500"></i>
        </div>
        
        <!-- 成功标题 -->
        <h2 class="text-[clamp(1.8rem,5vw,2.5rem)] font-bold text-gray-800 mb-4">提交成功！</h2>
        
        <!-- 成功消息 -->
        <p class="text-gray-600 mb-8 leading-relaxed">
            您的项目已成功提交到平台，我们将尽快审核。感谢您的参与！
        </p>
        
        <!-- 项目信息（可选） -->
        <?php if (isset($_GET['id'])): ?>
            <div class="bg-blue-50 rounded-lg p-4 mb-8">
                <p class="text-blue-700 font-medium">项目ID: <?php echo htmlspecialchars($_GET['id']); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- 返回主页按钮 -->
        <a href="index.php" class="inline-flex items-center justify-center bg-primary hover:bg-primary/90 text-white font-medium py-3 px-8 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <i class="fa fa-home mr-2"></i> 返回主页
        </a>
    </div>
</main>

<?php
// 包含页脚
require_once 'footer.php';
?>

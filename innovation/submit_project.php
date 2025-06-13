<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>提交创业项目</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .project-form {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #0F4C81;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        button {
            background: #0F4C81;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #22C55E;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: #0F4C81;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <section class="container">
        <div class="project-form">
            <h2 class="form-title">提交创业项目</h2>
            <form action="process_project.php" method="POST">
                <div class="form-group">
                    <label for="category">项目分类</label>
                    <select id="category" name="category" required>
                        <option value="">请选择分类</option>
                        <option value="科技">科技</option>
                        <option value="文创">文创</option>
                        <option value="社会企业">社会企业</option>
                        <option value="教育">教育</option>
                        <option value="医疗">医疗</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="title">项目标题</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">项目描述</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image_url">封面图片URL</label>
                    <input type="text" id="image_url" name="image_url" required>
                </div>
                
                <div class="form-group" style="text-align: center;">
                    <button type="submit">提交项目</button>
                </div>
            </form>
        </div>
    </section>
    
    <?php include 'footer.php'; ?>
</body>
</html>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章详情 - Qcelery's blog</title>
    <!-- 引入导航栏样式 -->
    <link rel="stylesheet" href="assets/nav-styles.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
            background-image: url('assets/images/cheerio.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        
        /* 确保主要内容区域的可读性 */
        article,
        .comments {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 20px;
        }

        .article-content {
            margin-bottom: 40px;
        }
        .article-header {
            margin-bottom: 30px;
        }
        .article-title {
            font-size: 32px;
            margin-bottom: 15px;
            text-align: center;
        }
        .article-meta {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: #999;
            margin-bottom: 20px;
        }
        .article-image {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .article-text {
            font-size: 16px;
            line-height: 1.8;
        }
        .article-text p {
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 30px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        .not-found {
            text-align: center;
            padding: 60px 20px;
        }
        footer {
            text-align: center;
            padding: 40px 0;
            margin-top: 60px;
            border-top: 1px solid #eee;
            color: #666;
        }
        @media (max-width: 768px) {
            .article-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <?php include 'assets/nav.php'; ?>
        <?php
        // 获取文章ID
        $articleId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // 读取文章数据
        $article = null;
        $dataFile = 'data/articles.json';
        
        if (file_exists($dataFile)) {
            $jsonContent = file_get_contents($dataFile);
            $articles = json_decode($jsonContent, true) ?: [];
            
            // 查找指定ID的文章
            foreach ($articles as $key => $item) {
                if ($item['id'] == $articleId) {
                    $article = $item;
                    // 更新浏览量
                    if (!isset($article['views'])) {
                        $article['views'] = 0;
                    }
                    $article['views']++;
                    $articles[$key] = $article;
                    file_put_contents($dataFile, json_encode($articles, JSON_PRETTY_PRINT));
                    break;
                }
            }
        }
        
        if ($article) {
            ?>
            <a href="articles.php" class="back-link">&larr; 返回文章列表</a>
            
            <article class="article-content">
                <div class="article-header">
                    <h1 class="article-title"><?= $article['title'] ?></h1>
                    <div class="article-meta">
                        <span>发布日期: <?= $article['date'] ?></span>
                        <span>浏览: <?= $article['views'] ?></span>
                    </div>
                </div>
                
                <img src="<?= $article['image'] ?>" alt="<?= $article['title'] ?>" class="article-image">
                
                <div class="article-text">
                    <?= nl2br($article['content']) ?>
                </div>
            </article>
            <?php
        } else {
            ?>
            <div class="not-found">
                <h2>文章不存在</h2>
                <p>您访问的文章可能已被删除或不存在。</p>
                <a href="articles.php">返回文章列表</a>
            </div>
            <?php
        }
        ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Qcelery's blog | 用❤️制作</p>
    </footer>
</body>
</html>
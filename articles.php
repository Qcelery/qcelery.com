<?php session_start(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章列表 - Qcelery's blog</title>
    <!-- 引入导航栏样式 -->
    <link rel="stylesheet" href="assets/nav-styles.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            background-image: url('assets/images/cheerio.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        
        /* 确保主要内容区域的可读性 */
        .article-grid {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 20px;
        }
        
        .article-item {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        .page-title {
            text-align: center;
            margin-bottom: 40px;
        }
        .page-title h1 {
            font-size: 32px;
            text-align: center;
        }
        .article-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        .article-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .article-card:hover {
            transform: translateY(-5px);
        }
        .article-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .article-content {
            padding: 20px;
        }
        .article-title {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .article-title a {
            text-decoration: none;
            color: #333;
        }
        .article-excerpt {
            color: #666;
            margin-bottom: 15px;
        }
        .article-meta {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #999;
        }
        .no-articles {
            text-align: center;
            padding: 60px 20px;
            grid-column: 1 / -1;
        }
        footer {
            text-align: center;
            padding: 40px 0;
            margin-top: 60px;
            border-top: 1px solid #eee;
            color: #666;
        }
        @media (max-width: 768px) {
            .article-list {
                grid-template-columns: 1fr;
            }
            .page-title h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- 引入导航组件 -->
    <?php include 'assets/nav.php'; ?>

    <main>
        <section class="page-title">
            <h1>全部博客</h1>
        </section>

        <section class="article-list">
            </div>
        <?php
        // 读取文章数据
        $articles = [];
        $dataFile = 'data/articles.json';
            
            if (file_exists($dataFile)) {
                $jsonContent = file_get_contents($dataFile);
                $articles = json_decode($jsonContent, true) ?: [];
                
                // 按时间排序，最新的在前
                usort($articles, function($a, $b) {
                    $dateA = strtotime($a['date']);
                    $dateB = strtotime($b['date']);
                    return $dateB - $dateA;
                });
            }
            
            if (!empty($articles)) {
                foreach ($articles as $article) {
                ?>
                <article class="article-card">
                    <img src="<?= $article['image'] ?>" alt="<?= $article['title'] ?>">
                    <div class="article-content">
                        <h3 class="article-title"><a href="article.php?id=<?= $article['id'] ?>"><?= $article['title'] ?></a></h3>
                        <p class="article-excerpt"><?= $article['excerpt'] ?></p>
                        <div class="article-meta">
                            <span><?= $article['date'] ?></span>
                            <span>浏览: <?= $article['views'] ?? 0 ?></span>
                        </div>
                    </div>
                </article>
                <?php }
            } else {
                ?>
                <div class="no-articles">
                    <h3>暂无文章</h3>
                    <p>敬请期待更多精彩内容...</p>
                </div>
                <?php
            }
            ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Qcelery's blog | 用❤️制作</p>
    </footer>
</body>
</html>
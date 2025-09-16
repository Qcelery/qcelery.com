<?php session_start(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qcelery's blog - 首页</title>
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
        .hero {
            background-color: rgba(255, 255, 255, 0.95);
        }
        .latest-articles {
            background-color: transparent;
        }
        .article-card {
            background-color: rgba(255, 255, 255, 0.7);
        }
        .hero {
            text-align: center;
            padding: 60px 20px;
            background-color: #f5f5f5;
            border-radius: 8px;
            margin-bottom: 40px;
        }
        .hero h1 {
            font-size: 42px;
            margin-bottom: 20px;
            text-align: center;
        }
        .hero p {
            font-size: 18px;
            color: #666;
            max-width: 800px;
            margin: 0 auto;
        }
        .latest-articles {
            margin-bottom: 40px;
        }
        .latest-articles h2 {
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }
        .article-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        .article-card {
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
            .hero h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <!-- 引入导航组件 -->
    <?php include 'assets/nav.php'; ?>

    <main>
        <section class="hero">
            <h1>欢迎来到我的博客</h1>
            <p>记录生活的点滴，分享学习的心得</p>
        </section>

        <section class="latest-articles">
            <h2>最新博客</h2>
            <div class="article-list">
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
                
                // 如果没有文章，创建一些示例文章
                if (empty($articles)) {
                    $articles = [
                        [
                            'id' => 1,
                            'title' => '如何开始你的个人博客之旅',
                            'excerpt' => '在这篇文章中，我将分享如何搭建一个属于自己的个人博客，包括选择平台、设计风格和内容创作等方面的建议。',
                            'content' => '<p>博客是分享知识和经验的绝佳平台...</p>',
                            'date' => date('Y-m-d'),
                            'image' => 'https://picsum.photos/seed/blog1/800/600',
                            'views' => 120
                        ],
                        [
                            'id' => 2,
                            'title' => '前端开发的五个实用技巧',
                            'excerpt' => '作为一名前端开发者，掌握一些实用技巧可以大大提高工作效率。本文将介绍五个我在日常工作中经常使用的技巧。',
                            'content' => '<p>前端开发日新月异，掌握一些技巧很重要...</p>',
                            'date' => date('Y-m-d', strtotime('-1 day')),
                            'image' => 'https://picsum.photos/seed/blog2/800/600',
                            'views' => 95
                        ]
                    ];
                    
                    // 保存示例文章
                    if (!file_exists('data')) {
                        mkdir('data', 0755, true);
                    }
                    file_put_contents($dataFile, json_encode($articles, JSON_PRETTY_PRINT));
                }
                
                // 显示最新的3篇文章
                $latestArticles = array_slice($articles, 0, 3);
                foreach ($latestArticles as $article) {
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
                if (empty($latestArticles)): ?>
                <p style="text-align: center; grid-column: 1 / -1; padding: 40px;">暂无文章</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Qcelery's blog | 用❤️制作</p>
    </footer>
</body>
</html>
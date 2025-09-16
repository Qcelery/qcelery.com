# 图片库说明

此文件夹包含博客网站所需的所有图片资源。请按照以下结构组织图片文件：

## 文件夹结构

- **images/** - 存放文章图片、展示图片等一般图片
- **backgrounds/** - 存放页面背景图片
- **icons/** - 存放网站图标、按钮图标等小尺寸图标

## 图片使用规范

1. 请确保所有图片都有合适的尺寸和格式（推荐使用JPG、PNG或WebP格式）
2. 图片文件名请使用英文、数字和下划线，避免使用中文和特殊字符
3. 为了网站加载速度，建议对图片进行适当压缩
4. 在HTML中引用图片时，请使用相对路径，例如：
   ```html
   <img src="../assets/images/filename.jpg" alt="描述文字">
   ```

## 背景图片设置

如果需要为页面设置背景图片，可以在CSS中添加如下代码：
```css
body {
    background-image: url('../assets/backgrounds/your-background.jpg');
    background-size: cover; /* 覆盖整个页面 */
    background-position: center; /* 居中显示 */
    background-repeat: no-repeat; /* 不重复 */
    background-attachment: fixed; /* 固定背景，不随页面滚动 */
}
```

## 图标使用

网站图标请统一存放在icons文件夹中，并保持一致的风格和尺寸。
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>/var/www/localhost/htdocs/project_status/README.md.text.html</title>
<meta name="Generator" content="Vim/7.4">
<meta name="plugin-version" content="vim7.4_v1">
<meta name="syntax" content="none">
<meta name="settings" content="use_css,pre_wrap,no_foldcolumn,expand_tabs,prevent_copy=">
<meta name="colorscheme" content="delek">
<style type="text/css">
</style>

<script type='text/javascript'>
<!--

-->
</script>
</head>
<body>
<pre id='vimCodeElement'>
                            Project Status 项目状态系统


说明:
    这是个工具用来显示研发部门各个项目状态(PreDEV,DEV,PreAlpha,Production)的系统,可以过滤时间和颜, 导出.

使用:
    1. 点击过滤图标,显示出过滤面板.
    2.
    点击搜索后,会显示一个保存为全局默认的功能,这个可以使每次进入系统,第一时间显示的是己过滤的数据.
    3. 点击 只读模式 时可以进入 编辑模式, 双击标头可以添加行, 双击 表内的行,
       可以编辑当前行.
    4. 导出 Excel 可以将当前看到的表格,导出成 Excel文档.

安装:
    1. 新建 Mysql 数据库 pro_status
    2. 导入 mysql 数据表.
        $ mysql -uroot -p pro_status &lt; database/project_status.bak20150915.sql
    3. 配置数据库,编辑 lib.php
        $config[&quot;SITE_NAME&quot;]    : 站点名称.
        $config[&quot;WHITE_IP&quot;]     : IP访问白名单.
        $config[&quot;WHITE_NET&quot;]    : 网段访问白名单.
        $config[&quot;ADMIN_IP&quot;]     : 管理员IP, 可以添加\编辑表格.
        $config[&quot;STAGE&quot;]        : 各阶段配置(未测试),暂定4个.

        DB_*                    : 根据实际配置.

    注: 系统会在当前目录创建一个 global_filter.set
    文件,用于保存全局默认Filter, 确保存在并可读写.


相关链接：
    1. jquery导出Excel： <a href="http://jquer.in/random-jquery-plugins-for-superior-websites/tableexport/">http://jquer.in/random-jquery-plugins-for-superior-websites/tableexport/</a>
    2. jquery表格排序： <a href="http://tablesorter.com/docs/">http://tablesorter.com/docs/</a>
    3. jquery日期选择器：<a href="http://www.laoshu133.com/Lab/DatePicker/">http://www.laoshu133.com/Lab/DatePicker/</a>
</pre>
</body>
</html>
<!-- vim: set foldmethod=manual : -->

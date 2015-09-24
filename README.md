<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>/var/www/localhost/htdocs/project_status/README.md.html</title>
<meta name="Generator" content="Vim/7.4">
<meta name="plugin-version" content="vim7.4_v1">
<meta name="syntax" content="markdown">
<meta name="settings" content="number_lines,use_css,pre_wrap,no_foldcolumn,expand_tabs,line_ids,prevent_copy=">
<meta name="colorscheme" content="solarized">
<style type="text/css">
<!--
pre { white-space: pre-wrap; font-family: monospace; color: #93a1a1; background-color: #002b36; }
body { font-family: monospace; color: #93a1a1; background-color: #002b36; }
* { font-size: 1em; }
.LineNr { color: #657b83; background-color: #073642; padding-bottom: 1px; }
.htmlTagName { color: #268bd2; font-weight: bold; }
.Error { color: #d30102; font-weight: bold; }
-->
</style>

<script type='text/javascript'>
<!--

/* function to open any folds containing a jumped-to line before jumping to it */
function JumpToLine()
{
  var lineNum;
  lineNum = window.location.hash;
  lineNum = lineNum.substr(1); /* strip off '#' */

  if (lineNum.indexOf('L') == -1) {
    lineNum = 'L'+lineNum;
  }
  lineElem = document.getElementById(lineNum);
  /* Always jump to new location even if the line was hidden inside a fold, or
   * we corrected the raw number to a line ID.
   */
  if (lineElem) {
    lineElem.scrollIntoView(true);
  }
  return true;
}
if ('onhashchange' in window) {
  window.onhashchange = JumpToLine;
}

-->
</script>
</head>
<body onload='JumpToLine();'>
<pre id='vimCodeElement'>
<span id="L1" class="LineNr"> 1 </span>                            Project Status 项目状态系统
<span id="L2" class="LineNr"> 2 </span>
<span id="L3" class="LineNr"> 3 </span>
<span id="L4" class="LineNr"> 4 </span>说明:
<span id="L5" class="LineNr"> 5 </span>    这是个工具用来显示研发部门各个项目状态(PreDEV,DEV,PreAlpha,Production)的系统,可以过滤时间和颜, 导出.
<span id="L6" class="LineNr"> 6 </span>
<span id="L7" class="LineNr"> 7 </span>使用:
<span id="L8" class="LineNr"> 8 </span><span class="htmlTagName">    1.</span> 点击过滤图标,显示出过滤面板.
<span id="L9" class="LineNr"> 9 </span>    2.
<span id="L10" class="LineNr">10 </span>    点击搜索后,会显示一个保存为全局默认的功能,这个可以使每次进入系统,第一时间显示的是己过滤的数据.
<span id="L11" class="LineNr">11 </span><span class="htmlTagName">    3.</span> 点击 只读模式 时可以进入 编辑模式, 双击标头可以添加行, 双击 表内的行,
<span id="L12" class="LineNr">12 </span>       可以编辑当前行.
<span id="L13" class="LineNr">13 </span><span class="htmlTagName">    4.</span> 导出 Excel 可以将当前看到的表格,导出成 Excel文档.
<span id="L14" class="LineNr">14 </span>
<span id="L15" class="LineNr">15 </span>安装:
<span id="L16" class="LineNr">16 </span><span class="htmlTagName">    1.</span> 新建 Mysql 数据库 pro<span class="Error">_</span>status
<span id="L17" class="LineNr">17 </span><span class="htmlTagName">    2.</span> 导入 mysql 数据表.
<span id="L18" class="LineNr">18 </span>        $ mysql -uroot -p pro_status &lt; database/project_status.bak20150915.sql
<span id="L19" class="LineNr">19 </span><span class="htmlTagName">    3.</span> 配置数据库,编辑 lib.php
<span id="L20" class="LineNr">20 </span>        $config[&quot;SITE_NAME&quot;]    : 站点名称.
<span id="L21" class="LineNr">21 </span>        $config[&quot;WHITE_IP&quot;]     : IP访问白名单.
<span id="L22" class="LineNr">22 </span>        $config[&quot;WHITE_NET&quot;]    : 网段访问白名单.
<span id="L23" class="LineNr">23 </span>        $config[&quot;ADMIN_IP&quot;]     : 管理员IP, 可以添加\编辑表格.
<span id="L24" class="LineNr">24 </span>        $config[&quot;STAGE&quot;]        : 各阶段配置(未测试),暂定4个.
<span id="L25" class="LineNr">25 </span>
<span id="L26" class="LineNr">26 </span>        DB_*                    : 根据实际配置.
<span id="L27" class="LineNr">27 </span>
<span id="L28" class="LineNr">28 </span>    注: 系统会在当前目录创建一个 global_filter.set
<span id="L29" class="LineNr">29 </span>    文件,用于保存全局默认Filter, 确保存在并可读写.
<span id="L30" class="LineNr">30 </span>
<span id="L31" class="LineNr">31 </span>
<span id="L32" class="LineNr">32 </span>相关链接：
<span id="L33" class="LineNr">33 </span><span class="htmlTagName">    1.</span> jquery导出Excel： <a href="http://jquer.in/random-jquery-plugins-for-superior-websites/tableexport/">http://jquer.in/random-jquery-plugins-for-superior-websites/tableexport/</a>
<span id="L34" class="LineNr">34 </span><span class="htmlTagName">    2.</span> jquery表格排序： <a href="http://tablesorter.com/docs/">http://tablesorter.com/docs/</a>
<span id="L35" class="LineNr">35 </span><span class="htmlTagName">    3.</span> jquery日期选择器：<a href="http://www.laoshu133.com/Lab/DatePicker/">http://www.laoshu133.com/Lab/DatePicker/</a>
</pre>
</body>
</html>
<!-- vim: set foldmethod=manual : -->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?= $title; ?></title>

		<link href="/assets/themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
		<link href="/assets/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
		<link href="/assets/themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
		<link href="/assets/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
		<!--[if IE]>
		<link href="themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
		<![endif]-->

		<!--[if lte IE 9]>
		<script src="js/speedup.js" type="text/javascript"></script>
		<![endif]-->

		<script src="/assets/js/jquery-1.7.2.js" type="text/javascript"></script>
		<script src="/assets/js/jquery.cookie.js" type="text/javascript"></script>
		<script src="/assets/js/jquery.validate.js" type="text/javascript"></script>
		<script src="/assets/js/jquery.bgiframe.js" type="text/javascript"></script>
		<script src="/assets/xheditor/xheditor-1.2.1.min.js" type="text/javascript"></script>
		<script src="/assets/xheditor/xheditor_lang/zh-cn.js" type="text/javascript"></script>
		<script src="/assets/uploadify/scripts/jquery.uploadify.js" type="text/javascript"></script>

		<!-- svg图表  supports Firefox 3.0+, Safari 3.0+, Chrome 5.0+, Opera 9.5+ and Internet Explorer 6.0+ -->
		<script type="text/javascript" src="/assets/chart/raphael.js"></script>
		<script type="text/javascript" src="/assets/chart/g.raphael.js"></script>
		<script type="text/javascript" src="/assets/chart/g.bar.js"></script>
		<script type="text/javascript" src="/assets/chart/g.line.js"></script>
		<script type="text/javascript" src="/assets/chart/g.pie.js"></script>
		<script type="text/javascript" src="/assets/chart/g.dot.js"></script>

		<script src="/assets/js/dwz.core.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.util.date.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.validate.method.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.barDrag.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.drag.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.tree.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.accordion.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.ui.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.theme.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.switchEnv.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.alertMsg.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.contextmenu.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.navTab.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.tab.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.resize.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.dialog.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.dialogDrag.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.sortDrag.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.cssTable.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.stable.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.taskBar.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.ajax.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.pagination.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.database.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.datepicker.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.effects.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.panel.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.checkbox.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.history.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.combox.js" type="text/javascript"></script>
		<script src="/assets/js/dwz.print.js" type="text/javascript"></script>

		<!-- 可以用dwz.min.js替换前面全部dwz.*.js (注意：替换是下面dwz.regional.zh.js还需要引入)
		<script src="bin/dwz.min.js" type="text/javascript"></script>
		-->
		<script src="/assets/js/dwz.regional.zh.js" type="text/javascript"></script>

		<script type="text/javascript">
			$(function () {
				DWZ.init("/assets/dwz.frag.xml", {
					loginUrl: "login_dialog.html", loginTitle: "登录", // 弹出登录对话框
					//		loginUrl:"login.html",	// 跳到登录页面
					statusCode: {ok: 200, error: 300, timeout: 301}, //【可选】
					pageInfo: {pageNum: "pageNum", numPerPage: "numPerPage", orderField: "orderField", orderDirection: "orderDirection"}, //【可选】
					keys: {statusCode: "statusCode", message: "message"}, //【可选】
					ui: {hideMode: 'offsets'}, //【可选】hideMode:navTab组件切换的隐藏方式，支持的值有’display’，’offsets’负数偏移位置的值，默认值为’display’
					debug: false, // 调试模式 【true|false】
					callback: function () {
						initEnv();
						$("#themeList").theme({themeBase: "/assets/themes"}); // themeBase 相对于index页面的主题base路径
					}
				});
			});

		</script>
	</head>

	<body scroll="no">
		<div id="layout">

			<div id="header">
				<div class="headerNav">
					<a class="logo" href="http://j-ui.com">标志</a>
					<ul class="nav">
<!--						<li id="switchEnvBox"><a href="javascript:">（<span>北京</span>）切换城市</a>
							<ul>
								<li><a href="sidebar_1.html">北京</a></li>
								<li><a href="sidebar_2.html">上海</a></li>
								<li><a href="sidebar_2.html">南京</a></li>
								<li><a href="sidebar_2.html">深圳</a></li>
								<li><a href="sidebar_2.html">广州</a></li>
								<li><a href="sidebar_2.html">天津</a></li>
								<li><a href="sidebar_2.html">杭州</a></li>
							</ul>
						</li>-->
						<li><a href="<?=  URL("user/logout"); ?>">退出</a></li>
					</ul>
					<ul class="themeList" id="themeList">
						<li theme="default"><div class="selected">蓝色</div></li>
						<li theme="green"><div>绿色</div></li>
						<!--<li theme="red"><div>红色</div></li>-->
						<li theme="purple"><div>紫色</div></li>
						<li theme="silver"><div>银色</div></li>
						<li theme="azure"><div>天蓝</div></li>
					</ul>
				</div>
				<!-- navMenu -->
			</div>

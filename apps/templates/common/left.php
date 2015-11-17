<div id="leftside">
	<div id="sidebar_s">
		<div class="collapse">
			<div class="toggleCollapse"><div></div></div>
		</div>
	</div>
	<div id="sidebar">
		<div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>

		<div class="accordion" fillSpace="sidebar">
			<div class="accordionHeader">
				<h2><span>Folder</span>框架生成器</h2>
			</div>
			<div class="accordionContent">
				<ul class="tree treeFolder">
					<li><a href="<?=URL("generate/index"); ?>" target="navTab" >生成器主页</a></li>
				</ul>
			</div>
			<div class="accordionHeader">
				<h2><span>Folder</span>菜单</h2>
			</div>
			<div class="accordionContent">
				<ul class="tree treeFolder">
					<li><a href="<?=URL("admin/index"); ?>" target="navTab" rel="admin">管理员管理</a></li>
					<li><a href="<?=URL("category/index"); ?>" target="navTab" rel="category">分类管理</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
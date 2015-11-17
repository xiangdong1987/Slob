<?php 
$tableHeader="";
$table_content="";
foreach ($table["fields"] as $key => $value) {
	if($key==$table["primary"]){
		$tableHeader.="<th width='80'>".  strtoupper($key)."</th>";
		$table_content.="<td><?=\$value['$key']?></td>";
	}else{
		if($value["type"]!="int"){
			$tableHeader.="<th width='80'>".  $value["comment"]."</th>";
			$table_content.="<td><?=\$value['$key']?></td>";
		}
	}
}


$str="
<form id='pagerForm' method='post' action='<?= URL('$name/index'); ?>'>
	<input type='hidden' name='keywords' value='\${param.keywords}' />
	<input type='hidden' name='pageNum' value='<?php echo \$page['pageNum']?>' />
	<input type='hidden' name='numPerPage' value='<?php echo \$page['numPerPage']?>' />
</form>
<div class='pageHeader'>
	<form onsubmit='return navTabSearch(this);' action='demo_page1.html' method='post'>
	<div class='searchBar'>
		<table class='searchContent'>
			<tr>
				<td>
					ID：<input type='text' name='keyword' />
				</td>
			</tr>
		</table>
		<div class='subBar'>
			<ul>
				<li><div class='buttonActive'><div class='buttonContent'><button type='submit'>检索</button></div></div></li>
			</ul>
		</div>
	</div>
	</form>
</div>
<div class='pageContent'>
	<div class='panelBar'>
		<ul class='toolBar'>
			<li><a class='add' href='<?= URL('$name/add$name'); ?>' target='navTab' rel='add$name'><span>添加</span></a></li>
			<li><a class='edit' href='<?= URL('$name/update$name'); ?>?$primary={list_id}' target='navTab' rel='update$name'><span>修改</span></a></li>
			<li><a class='delete' href='<?= URL('$name/delete$name'); ?>?$primary={list_id}' target='ajaxTodo' title='确定要删除吗?'><span>删除</span></a></li>
		</ul>
	</div>
	<table class='table' width='100%' layoutH='138'>
		<thead>
			<tr>
				$tableHeader
			</tr>
		</thead>
		<tbody>
			<?php foreach (\$data as \$key => \$value) {?>			
				<tr target='list_id' rel='<?=\$value['$primary']?>'>
					$table_content
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<div class='panelBar'>
		<div class='pages'>
			<span>显示</span>
			<select class='combox' name='numPerPage' onchange='navTabPageBreak({numPerPage:this.value})'>
				<option value='20'>20</option>
				<option value='50'>50</option>
				<option value='100'>100</option>
				<option value='200'>200</option>
			</select>
			<span>条，共<?php echo \$page['total'];?>条</span>
		</div>
		<div class='pagination' targetType='navTab' totalCount='<?php echo \$page['total'];?>' numPerPage='<?php echo \$page['numPerPage']?>' pageNumShown='10' currentPage='<?php echo \$page['pageNum']?>'></div>
	</div>
</div>";
return $str;		



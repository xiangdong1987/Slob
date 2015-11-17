<?php
$form="";
foreach ($table["fields"] as $key => $value) {
	if($key==$table["primary"]&&$value["extra"]!="auto_increment"){
		$form.="
		<dl><dt>".  strtoupper($key)."：</dt>
				<dd>
					<input type='text' name='$key' maxlength='".$value["length"]."' class='required' value='<?=\$data['$key']; ?>'/>
					<span class='info'></span>
				</dd>
		</dl>";
	}else {
		if($key==$table["primary"]){
			$form.="<input type='hidden' name='$key'  value='<?=\$data['$key']; ?>'/>";
			continue;
		}
		$required="";
		if(!$value['isNull']){
			$required="class='required'";
		}
		switch ($value['type']) {
			case 'text':		
				$form.="
					<dl><dt>".$value["comment"]."：</dt>
							<dd>
								<div class='unit'>
									<textarea class='editor' name='$key' rows='30' cols='50' tools='simple' $required><?=\$data['$key']; ?></textarea>
								</div>
								<span class='info'></span>
							</dd>
					</dl>";
				break;
			case 'timestamp':		
				$form.="
					<dl><dt>".$value["comment"]."：</dt>
							<dd>
								<input type='text' name='$key' class='date' dateFmt='yyyy-MM-dd HH:mm:ss' readonly='true' $required value='<?=\$data['$key']; ?>' />
								<a class='inputDateButton' href='javascript:;'>选择</a>
								<span class='info'></span>
							</dd>
					</dl>";
				break;
			case 'datetime':		
				$form.="
					<dl><dt>".$value["comment"]."：</dt>
							<dd>
								<input type='text' name='$key' class='date' dateFmt='yyyy-MM-dd HH:mm:ss' readonly='true' $required value='<?=\$data['$key']; ?>' />
								<a class='inputDateButton' href='javascript:;'>选择</a>
								<span class='info'></span>
							</dd>
					</dl>";
				break;
			case 'date':		
				$form.="
					<dl><dt>".$value["comment"]."：</dt>
							<dd>
								<input type='text' name='$key' class='date' readonly='true' $required value='<?=\$data['$key']; ?>' />
								<a class='inputDateButton' href='javascript:;'>选择</a>
								<span class='info'></span>
							</dd>
					</dl>";
				break;
			case 'time':		
				$form.="
					<dl><dt>".$value["comment"]."：</dt>
							<dd>
								<input type='text' name='$key' class='date' dateFmt='HH:mm:ss' readonly='true' $required value='<?=\$data['$key']; ?>'/>
								<a class='inputDateButton' href='javascript:;'>选择</a>
								<span class='info'></span>
							</dd>
					</dl>";
				break;
			default:
				$form.="
					<dl><dt>".$value["comment"]."：</dt>
							<dd>
								<input type='text' name='$key' maxlength='".$value["length"]."' $required value='<?=\$data['$key']; ?>'/>
								<span class='info'></span>
							</dd>
					</dl>";
				break;
		}
	}
}
$str="<h2 class='contentTitle'><?php echo \$title; ?></h2>
<div class='pageContent'>
	<form method='post' action='<?= URL('$name/update$name'); ?>' class='pageForm required-validate' onsubmit='return validateCallback(this,navTabAjaxDone)'>
		<div class='pageFormContent nowrap' layoutH='97'>
			$form
			<div class='divider'></div>
		</div>
		<div class='formBar'>
			<ul>
				<input type='hidden' name='isPost' value='1' />
				<li><div class='buttonActive'><div class='buttonContent'><button type='submit'>提交</button></div></div></li>
				<li><div class='button'><div class='buttonContent'><button type='button' class='close'>取消</button></div></div></li>
			</ul>
		</div>
	</form>
</div>";
return $str;

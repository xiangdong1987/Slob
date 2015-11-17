<?php
$str="<?php
namespace App\Controller;

use App\BasicController;
use Swoole;

class $controllerName extends BasicController {

	function index() {
		\$numPerPage=  getRequest('numPerPage',20,true);
		\$pageNum=  getRequest('pageNum',1,true);
		\$$name=  model('$controllerName');
		\$params= [
			'order'=>'$primary',
			'limit'=>(\$pageNum-1)*\$numPerPage.','.\$numPerPage
		];	
		\$total=\$".$name."->count(['where'=>1]);
		\$page=[
			'numPerPage'=>\$numPerPage,
			'pageNum'=>\$pageNum,
			'total'=>\$total,
		];

		\$data=\$".$name."->gets(\$params);
		\$this->assign('data', \$data);
		\$this->assign('page', \$page);
		\$this->display('$name/index.php');
		\$this->display(\"$name/index.php\");
	}

	function add$controllerName() {
		if(isPost()){
			\$$name=  model('$controllerName');
			\$data=\$".$name."->getData();
			if(\$".$name."->create(\$data)){
				jsonReturn(\$this->ajaxFromReturn('添加成功',200,'closeCurrent','','$name'));
			}else{
				jsonReturn(\$this->ajaxFromReturn('添加失败',300));
			}
		}
		\$this->assign('title', '添加');
		\$this->display(\"$name/add_$name.php\");
	}

	function update$controllerName() {
		\$$name=  model('$controllerName');
		if(isPost()){
			\$data=\$".$name."->getData();
			if(\$".$name."->set(\$data['$primary'],\$data)){
				jsonReturn(\$this->ajaxFromReturn('修改成功',200,'closeCurrent','','$name'));
			}else{
				jsonReturn(\$this->ajaxFromReturn('修改失败',300));
			}
		}
		\$$primary=  getRequest('$primary');
		\$data=\$".$name."->get(\$$primary);
		\$this->assign('data', \$data);
		\$this->assign('title', '修改');
		\$this->display(\"$name/update_$name.php\");
	}

	function delete$controllerName() {
		\$id = getRequest('$primary');
		\$$name = model('$controllerName');
		if (\$".$name."->del(\$id)) {
			jsonReturn(\$this->ajaxFromReturn('删除成功',200,'','','$name'));
		} else {
			jsonReturn(\$this->ajaxFromReturn('删除失败', 300));
		}
	}

	function search$controllerName() {
		
	}

}	
";
return $str;


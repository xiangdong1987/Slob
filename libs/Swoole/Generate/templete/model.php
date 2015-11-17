<?php
$attribute="";
foreach ($table["fields"] as $key => $value) {
	if($key==$table["primary"]){
		$attribute.="'$key'=>'".strtoupper($key)."',";
	}else{
		$attribute.="'$key'=>'".$value["comment"]."',";
	}
}
$str="<?php
namespace App\Model;
use Swoole;
class $modelName extends Swoole\Model {

	public \$table = '$name';
	
	public \$primary = '$primary';
		
	public function attributeLabels(){
		return [
			$attribute
		];
	}

	public function search(\$param) {
		return \$this->gets(\$param);
	}

	public function create(\$data) {
		return \$this->put(\$data);
	}

	public function update(\$id, \$data) {
		return \$this->set(\$id, \$data);
	}

	public function delete(\$param) {
		return \$this->dels(\$param);
	}
	public function getData(){
		\$data=[];
		\$att=  \$this->attributeLabels();
		foreach (\$att as \$key=>\$value){
			if(getRequest(\$key)){
				\$data[\$key]=  getRequest(\$key);
			}
		}
		return \$data;
	}

}	
";
return $str;

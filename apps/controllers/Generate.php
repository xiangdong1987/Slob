<?php

namespace App\Controller;

use App\BasicController;
use Swoole;

class Generate extends BasicController {

	private $db_name = "master";
	private $generator;

	function __construct(Swoole $swoole) {
		parent::__construct($swoole);
		if (!$this->generator) {
			$this->generator = new \Swoole\Generate\Generator($this->db_name);
		}
	}

	function index() {
		$title = "生成控制器";
		$tables = $this->generator->getAllTables();
		foreach ($tables as $key => $value) {
			$tmp["table"] = $value["Tables_in_" . \Swoole::$php->config["db"][$this->db_name]["name"]];
			$tmp["isModel"] = is_file(WEBPATH . "/apps/models/" . $this->tableNameToFileName($tmp["table"]));
			$tmp["moderName"] = $this->tableNameToFileName($tmp["table"]);
			$tmp["isController"] = is_file(WEBPATH . "/apps/controllers/" . $this->tableNameToFileName($tmp["table"]));
			$tmp["controllerName"] = $this->tableNameToFileName($tmp["table"]);
			$list[] = $tmp;
		}
		$this->assign("title", $title);
		$this->assign("tables", $list);
		$this->display("generate/index.php");
	}

	/**
	 * 文件名 包含后缀
	 * @param type $db_name
	 * @return type
	 */
	private function tableNameToFileName($db_name) {
		return ucwords(str_replace("_", "", $db_name)) . ".php";
	}

	/**
	 * 文件名不包含后缀
	 * @param type $db_name
	 * @return type
	 */
	private function tableNameToName($db_name) {
		return ucwords(str_replace("_", "", $db_name));
	}

	/**
	 * 生成模型确认页面
	 */
	function modelConfirm() {
		$name = getRequest("model");
		$model["modelName"] = $this->tableNameToName($name);
		$model["isModel"] = is_file(WEBPATH . "/apps/models/" . $this->tableNameToFileName($name));
		$model["name"] = $name;
		//jsonReturn($model);
		$this->assign("model", $model);
		$this->assign("page_title", "确认生成模型");
		$this->display("generate/modle_confirm.php");
	}

	/**
	 * 生成模型
	 */
	function modelGenerate() {
		$modelName = getRequest("modelName");
		$name = getRequest("name");
		try {
			$flag = $this->generator->generateModel($modelName, $name);
			return jsonReturn($this->ajaxFromReturn("生成成功"));
		} catch (\Exception $exc) {
			$message = $exc->getMessage();
			return jsonReturn($this->ajaxFromReturn($message, 300));
		}
	}

	/**
	 * 生成模型确认页面
	 */
	function controllerConfirm() {
		$name = getRequest("controller");
		$controller["controllerName"] = $this->tableNameToName($name);
		$controller["isController"] = is_file(WEBPATH . "/apps/controllers/" . $this->tableNameToFileName($name));
		$controller["name"] = $name;
		$this->assign("controller", $controller);
		$this->assign("page_title", "确认生成控制器");
		$this->display("generate/controller_confirm.php");
	}

	/**
	 * 生成控制器以及模板
	 */
	function controllerGenerate() {
		$modelName = getRequest("controllerlName");
		$name = getRequest("name");
		try {
			$flag = $this->generator->generateController($modelName, $name);
			return jsonReturn($this->ajaxFromReturn("生成成功"));
		} catch (\Exception $exc) {
			$message = $exc->getMessage();
			return jsonReturn($this->ajaxFromReturn($message, 300));
		}
	}
	function  test(){
		jsonReturn($this->generator->analysisTable("category"));
	}
}

<?php

$configs = array();

/*************************************************************
 *                          路徑設定
 *************************************************************/

/**
 * 修飾路徑，將反斜線統一成斜線
 * @param string $path 原路徑
 * @return string 修飾後的路徑
 */
function path_refine($path) {
	$path = str_replace("\\", "/", $path);

	// 某些伺服器 SCRIPT_FILENAME 取得的路徑值會與實際路徑不同
	$path = str_replace("home2", "home", $path);

	// 刪除最尾部的斜線
	if (substr($path, strlen($path) - 1, 1) == "/") {
		$path = substr($path, 0, -1);
	}

	return $path;
}

/**
 * 由主機根目錄算起的相對路徑
 */
function machine_relative_path($path) {
	$root = path_refine($_SERVER["DOCUMENT_ROOT"]);
	return str_replace($root, "", $path);
}

/**
 * 由框架目錄為根目錄的相對路徑
 */
function system_relative_path($path) {
	$root = path_refine(dirname(dirname(__FILE__)));
	return str_replace($root, "", $path);
}

/**
 * 目前完整網址(含參數)
 */
function curPageURL() {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

// 主機根目錄
$configs["full_machine_path"] = path_refine($_SERVER["DOCUMENT_ROOT"]);

// 框架系統路徑
$configs["full_system_path"] = path_refine(dirname(__FILE__));

// 框架根目錄
$configs["full_root_path"] = dirname($configs["full_system_path"]);

// 使用者自訂目錄
$configs["full_application_path"] =$configs["full_root_path"] . "/application";

// Controller 目錄
$configs["full_controller_path"] = $configs["full_application_path"] . "/controller";

// View 目錄
$configs["full_view_path"] = $configs["full_application_path"] . "/view";

// Model 目錄
$configs["full_model_path"] = $configs["full_application_path"] . "/model";

// 函式庫路徑
$configs["full_library_path"] = $configs["full_application_path"] . "/jquery-lib";

// 記錄路徑
$configs["full_log_path"] = $configs["full_application_path"] . "/log";

// 檔案上傳路徑
$configs["full_upload_path"] = $configs["full_root_path"] . "/files";

// 開發者介面路徑
$configs["full_master_path"] = $configs["full_root_path"] . "/master";

// 管理者介面路徑
$configs["full_admin_path"] = $configs["full_root_path"] . "/admin";

// 目前網址路徑 (含參數)
$configs["full_url"] = curPageURL(); 

// 目前被執行的PHP檔案路徑
$configs["full_execute_php_path"] = path_refine($_SERVER['SCRIPT_FILENAME']);

// View 目錄相對路徑
$configs["machine_relative_view_path"] = machine_relative_path($configs["full_view_path"]);

// 目前被執行的PHP檔案相對路徑
$configs["system_relative_execute_php_path"] = system_relative_path($configs["full_execute_php_path"]);

// 函式庫 目錄相對路徑
$configs["machine_relative_jquery_lib_path"] = machine_relative_path($configs["full_library_path"]);

// 檔案上傳 目錄相對路徑
$configs["machine_relative_full_upload_path"] = machine_relative_path($configs["full_upload_path"]);

$configs["machine_relative_admin_path"] = machine_relative_path($configs["full_admin_path"]);

$configs["machine_relative_root_path"] = machine_relative_path($configs["full_root_path"]);
/*************************************************************
 *                          主題設定
 *************************************************************/

// 管理者介面版型預設名稱
$configs["admin_theme"] = "AdminLTE";

// 開發者介面版型預設名稱
$configs["master_theme"] = "AdminLTE";

// 前台介面版版型預設名稱
$configs["user_theme"] = "default";


/*************************************************************
 *                          系統開關設定
 *************************************************************/


// 網域限制，啟用之後，後台可根據不同的網址有不同的後台內容
$configs["domain_gate"] = true;

// 語系限制，啟用之後，後台可根據不同的語系有不同的後台內容
$configs["language_gate"] = false;

/*************************************************************
 *                          載入自訂控制器
 *************************************************************/
 
require_once($configs["full_application_path"] . "/custom/core/DomainController.php");
require_once($configs["full_application_path"] . "/custom/core/MasterController.php");
require_once($configs["full_application_path"] . "/custom/core/AdminController.php");
require_once($configs["full_application_path"] . "/custom/core/UserController.php");

/*************************************************************
 *                          模組設定
 *************************************************************/

$configs["modules"] = array(
	"loader",
	"code",
	"ip",
	"logger",
	"go",
	"action",
	"io",
	"db",
	"dao",
	"alert",
	"validation",
	"filter",
	"pagination",
	"httpclient",
	"mailbox",
	"file",
	"image",
	"mime"
);

date_default_timezone_set('Asia/Taipei');
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED); ini_set("display_errors", 1);
error_reporting(E_ALL); ini_set("display_errors", 1);

header('Cache-Control: no-cache, max-age=600');

// 專門讓 CKEDITOR (因在不同支系統) 所使用的 SESSION變數
@session_start();
$_SESSION["full_upload_path"] = $configs["full_upload_path"];
$_SESSION["machine_relative_full_upload_path"] = $configs["machine_relative_full_upload_path"];
@session_write_close();

?>
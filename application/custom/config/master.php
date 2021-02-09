<?php
// 開發者平台帳號密碼
$master_config["account"] = "develop";
$master_config["password"] = "yal641439";

// 模組選單資訊
$master_config["modules"]["login"] = array(
	"title" => "登入資訊",
	"type" => "Config",
	"fields" => array(
		"name" => array(
			"name" => "系統名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "系統管理平台會出現的名字"
		),
		
		"verifycode" => array(
			"name" => "驗證碼",
			"type" => "Switch",
			"element" => "max-length{1};data-source{使用,不使用};",
			"default" => "",
			"tip" => "網站管理平台登入時，是否輸入驗證碼"
		),
		
		"account" => array(
			"name" => "預設帳號",
			"type" => "Text",
			"element" => "required;max-length{10};",
			"default" => "",
			"tip" => "後台預設登入帳號"
		),
		
		"password" => array(
			"name" => "預設密碼",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "後台預設登入密碼"
		),
	)
);


$master_config["modules"]["mailbox"] = array(
	"title" => "信箱設定",
	"type" => "Config",
	"fields" => array(
	
		"mail_info" => array(
			"name" => "信箱資訊",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => ""
		),
		
		"mail" => array(
			"name" => "發信信箱",
			"type" => "Email",
			"element" => "",
			"default" => "",
			"tip" => "發送信件的信箱位址"
		),
		
		"mail_name" => array(
			"name" => "信箱名稱",
			"type" => "Text",
			"element" => "",
			"default" => "",
			"tip" => "寄件者名稱"
		),
		
		"mail_reply" => array(
			"name" => "回覆信箱",
			"type" => "Email",
			"element" => "",
			"default" => "",
			"tip" => "郵件系統回覆時預設信箱"
		),
		
		"smtp_info" => array(
			"name" => "SMTP伺服器資訊",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => ""
		),
	
		"smtp_open" => array(
			"name" => "啟用SMTP",
			"type" => "Switch",
			"element" => "data-source{啟用,關閉};",
			"default" => "",
			"tip" => "是否指定由SMTP伺服器發信"
		),
		
		"smtp_host" => array(
			"name" => "SMTP主機",
			"type" => "Text",
			"element" => "",
			"default" => "",
			"tip" => "請輸入SMTP伺服器網址"
		),
		
		"smtp_port" => array(
			"name" => "SMTP伺服器連接埠",
			"type" => "Number",
			"element" => "",
			"default" => "",
			"tip" => "請輸入SMTP伺服器連接埠"
		),
		
		"smtp_account" => array(
			"name" => "SMTP帳號",
			"type" => "Text",
			"element" => "",
			"default" => "",
			"tip" => "請輸入SMTP伺服器登入帳號"
		),
		
		"smtp_password" => array(
			"name" => "SMTP密碼",
			"type" => "Text",
			"element" => "",
			"default" => "",
			"tip" => "請輸入SMTP伺服器登入密碼"
		),
		
		"smtp_verify" => array(
			"name" => "SMTP驗證",
			"type" => "Text",
			"element" => "",
			"default" => "",
			"tip" => "SMTP伺服器是否需要驗證"
		),
		
		"smtp_secure" => array(
			"name" => "SMTP安全協定",
			"type" => "Text",
			"element" => "",
			"default" => "",
			"tip" => "指定SMTP伺服器的安全協定(ssl, tls)"
		)
		
		
	)
);

// 模組選單資訊
$master_config["modules"]["menu"] = array(
	"title" => "選單管理",
	"type" => "Config",
	"fields" => array(
		"menu" => array(
			"name" => "選單設定",
			"type" => "Menu",
			"element" => "filter-allow{escape};",
			"default" => "",
			"tip" => ""
		)
	)
);

$master_config["modules"]["note"] = array(
	"title" => "說明頁面",
	"type" => "Note",
	"fields" => array(
	
		"title" => array(
			"name" => "頁面名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"code" => array(
			"name" => "模組代碼",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"landing_page" => array(
			"name" => "登入頁",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "是否為登入後出現的第一個頁面",
			"list" => ""
		),
		
		"content" => array(
			"name" => "說明內容",
			"type" => "HtmlEditor",
			"element" => "filter-allow{css,escape};",
			"default" => "",
			"tip" => "管理平台的說明內容",
			"list" => ""
		)
	)
);

$master_config["modules"]["item"] = array(
	"title" => "頁面管理",
	"type" => "Item",
	"fields" => array(
	
	
		"base_bar" => array(
			"name" => "基本設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"title" => array(
			"name" => "模組名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"code" => array(
			"name" => "模組代碼",
			"type" => "Text",
			"element" => "required;max-length{20};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"landing_page" => array(
			"name" => "登入頁",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "是否為登入後出現的第一個頁面",
			"list" => ""
		),
		
		"single" => array(
			"name" => "設定頁面",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "是否為單一設定頁面，無清單功能",
			"list" => ""
		),
		
		"login_self" => array(
			"name" => "僅看見登入者資訊",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "登入者可看到自己建置的資料，無法看見其他帳號資訊，其他帳號亦看不見登入者資訊",
			"list" => ""
		),
		
		"ignore_database" => array(
			"name" => "忽略建置資料表",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "預設會自動產生資料表單，如選擇「是」則會忽略建置資料表",
			"list" => ""
		),
		
		"class_bar" => array(
			"name" => "分類設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"level" => array(
			"name" => "分類層數",
			"type" => "Number",
			"element" => "",
			"default" => "0",
			"tip" => "",
			"list" => ""
		),
		
		"dynamic" => array(
			"name" => "動態層數",
			"type" => "Switch",
			"element" => "data-source{動態層數,固定層數};",
			"default" => "",
			"tip" => "不限定最後一層才可放項目",
			"list" => ""
		),
		
		"class_count_per_page" => array(
			"name" => "每頁筆數",
			"type" => "Number",
			"element" => "",
			"default" => "10",
			"tip" => "",
			"list" => ""
		),
		
		"class_setting" => array(
			"name" => "細部開關",
			"type" => "CheckBox",
			"element" => "data-source{Add:新增分類,View:檢視分類,Fix:修改分類,Clone:複製分類,Delete:刪除分類,Sort:排序分類,Publish:上下架};",
			"default" => "Add:Fix:Delete",
			"tip" => "",
			"list" => ""
		),
		
		"class_fieldMetadata" => array(
			"name" => "分類欄位設定",
			"type" => "FieldsDefine",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"class_special_bar" => array(
			"name" => "分類特殊設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"class_field_use_level" => array(
			"name" => "特殊欄位使用層數",
			"type" => "Number",
			"element" => "",
			"default" => "0",
			"tip" => "預設分類只會有中文名稱及上下架，但可在指定的分類層數使用自訂的欄位",
			"list" => ""
		),
		
		"class_special_fieldMetadata" => array(
			"name" => "特殊欄位設定",
			"type" => "FieldsDefine",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"item_bar" => array(
			"name" => "項目設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"item_count" => array(
			"name" => "項目數量限制",
			"type" => "Number",
			"element" => "",
			"default" => "0",
			"tip" => "每個分類最多可新增幾個項目(0：無限, -1:不需要項目)",
			"list" => ""
		),
		
		"item_count_per_page" => array(
			"name" => "每頁筆數",
			"type" => "Number",
			"element" => "",
			"default" => "10",
			"tip" => "",
			"list" => ""
		),
				
		"item_setting" => array(
			"name" => "細部開關",
			"type" => "CheckBox",
			"element" => "data-source{Add:新增項目,View:檢視項目,Fix:修改項目,Clone:複製項目,Delete:刪除項目,Sort:排序項目,Publish:上下架,Export:匯出,Import:匯入};",
			"default" => "Add:Fix:Delete",
			"tip" => "",
			"list" => ""
		),
		
		"fieldMetadata" => array(
			"name" => "項目欄位設定",
			"type" => "FieldsDefine",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"search_bar" => array(
			"name" => "搜尋設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"fieldSearch" => array(
			"name" => "搜尋設定",
			"type" => "FieldsSearch",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"button_bar" => array(
			"name" => "自訂按鈕",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"default_button" => array(
			"name" => "保留預設按鈕",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "是否保留「儲取」「取消」按鈕？",
			"list" => ""
		),
		
		"fieldButton" => array(
			"name" => "按鈕設定",
			"type" => "FieldsButton",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"ext_bar" => array(
			"name" => "HTML,JS,CSS",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"ext_code" => array(
			"name" => "擴充語法(HTML,JS,CSS)",
			"type" => "TextArea",
			"element" => "filter-allow{all};ext-attrs{rows:10};",
			"default" => "",
			"tip" => "",
			"list" => ""
		)
	)
);

$master_config["modules"]["user"] = array(
		"title" => "用戶管理",
		"type" => "User",
		"fields" => array(
	
		"base_bar" => array(
			"name" => "基本設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
	
		"title" => array(
			"name" => "模組名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"code" => array(
			"name" => "模組代碼",
			"type" => "Text",
			"element" => "required;max-length{10};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"manager" => array(
			"name" => "管理用戶",
			"type" => "Select",
			"element" => "data-source{module_type:user};",
			"default" => "",
			"tip" => "此用戶群歸屬於哪個用戶管理 - 階層管理",
			"list" => ""
		),
		
		"permission" => array(
			"name" => "用戶權限",
			"type" => "Select",
			"element" => "data-source{module_type:permission};",
			"default" => "",
			"tip" => "限制用戶權限",
			"list" => ""
		),
		
		"domain_bar" => array(
			"name" => "多網域設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"admin_domain" => array(
			"name" => "管理者網域名稱",
			"type" => "Text",
			"element" => "max-length{255};",
			"default" => "",
			"tip" => "當有多網域設定(用戶代碼為domain)時，必需指定一個超級管理者的網域，由此網域登入則為超級管理者",
			"list" => ""
		),
		
		"ignore_database" => array(
			"name" => "忽略建置資料表",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "預設會自動產生資料表單，如選擇「是」則會忽略建置資料表",
			"list" => ""
		),
		
		"class_bar" => array(
			"name" => "分類設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"level" => array(
			"name" => "分類層數",
			"type" => "Number",
			"element" => "",
			"default" => "0",
			"tip" => "",
			"list" => ""
		),
		
		"dynamic" => array(
			"name" => "動態層數",
			"type" => "Switch",
			"element" => "data-source{動態層數,固定層數};",
			"default" => "",
			"tip" => "不限定最後一層才可放項目",
			"list" => ""
		),
		
		"class_count_per_page" => array(
			"name" => "每頁筆數",
			"type" => "Number",
			"element" => "",
			"default" => "10",
			"tip" => "",
			"list" => ""
		),
		
		"class_setting" => array(
			"name" => "細部開關",
			"type" => "CheckBox",
			"element" => "data-source{Add:新增分類,View:檢視分類,Fix:修改分類,Clone:複製分類,Delete:刪除分類,Sort:排序分類,Publish:上下架};",
			"default" => "Add:Fix:Delete",
			"tip" => "",
			"list" => ""
		),
		
		"class_fieldMetadata" => array(
			"name" => "分類欄位設定",
			"type" => "FieldsDefine",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"item_bar" => array(
			"name" => "用戶設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"item_count_per_page" => array(
			"name" => "每頁筆數",
			"type" => "Number",
			"element" => "",
			"default" => "10",
			"tip" => "",
			"list" => ""
		),
				
		"item_setting" => array(
			"name" => "細部開關",
			"type" => "CheckBox",
			"element" => "data-source{Add:新增用戶,View:檢視用戶,Fix:修改用戶,Clone:複製用戶,Delete:刪除用戶,Sort:排序用戶,Publish:啟用/封鎖,Export:匯出,Import:匯入};",
			"default" => "Add:Fix:Delete",
			"tip" => "",
			"list" => ""
		),
		
		"fieldMetadata" => array(
			"name" => "欄位設定",
			"type" => "FieldsDefine",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"search_bar" => array(
			"name" => "搜尋設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"fieldSearch" => array(
			"name" => "搜尋設定",
			"type" => "FieldsSearch",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"button_bar" => array(
			"name" => "自訂按鈕",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"default_button" => array(
			"name" => "保留預設按鈕",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "Y",
			"tip" => "是否保留「儲取」「取消」按鈕？",
			"list" => ""
		),
		
		"fieldButton" => array(
			"name" => "按鈕設定",
			"type" => "FieldsButton",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"ext_bar" => array(
			"name" => "HTML,JS,CSS",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"ext_code" => array(
			"name" => "擴充語法(HTML,JS,CSS)",
			"type" => "TextArea",
			"element" => "filter-allow{all};ext-attrs{rows:10};",
			"default" => "",
			"tip" => "",
			"list" => ""
		)
	)
);


$master_config["modules"]["permission"] = array(
		"title" => "用戶權限",
		"type" => "Permission",
		"fields" => array(
	
		"base_bar" => array(
			"name" => "基本設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
	
		"title" => array(
			"name" => "權限名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"code" => array(
			"name" => "模組代碼",
			"type" => "Text",
			"element" => "required;max-length{10};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"manager" => array(
			"name" => "權限控制",
			"type" => "CheckBox",
			"element" => "data-source{module_type:note,module_type:item,module_type:user,module_type:form,module_type:link};",
			"default" => "",
			"tip" => "限制用戶的權限",
			"list" => ""
		)	
		
	)
);

$master_config["modules"]["form"] = array(
	"title" => "表單管理",
	"type" => "Form",
	"fields" => array(
	
		"base_bar" => array(
			"name" => "基本設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
	
		"title" => array(
			"name" => "表單名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"code" => array(
			"name" => "表單代碼",
			"type" => "Text",
			"element" => "required;max-length{20};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"login_self" => array(
			"name" => "僅看見登入者資訊",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "登入者可看到自己建置的資料，無法看見其他帳號資訊，其他帳號亦看不見登入者資訊",
			"list" => ""
		),
		
		"ignore_database" => array(
			"name" => "忽略建置資料表",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "",
			"tip" => "預設會自動產生資料表單，如選擇「是」則會忽略建置資料表",
			"list" => ""
		),
		
		"class_bar" => array(
			"name" => "分類設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"level" => array(
			"name" => "分類層數",
			"type" => "Number",
			"element" => "",
			"default" => "0",
			"tip" => "",
			"list" => ""
		),
		
		"dynamic" => array(
			"name" => "動態層數",
			"type" => "Switch",
			"element" => "data-source{動態層數,固定層數};",
			"default" => "",
			"tip" => "不限定最後一層才可放項目",
			"list" => ""
		),
		
		"class_count_per_page" => array(
			"name" => "每頁筆數",
			"type" => "Number",
			"element" => "",
			"default" => "10",
			"tip" => "",
			"list" => ""
		),
		
		"class_setting" => array(
			"name" => "細部開關",
			"type" => "CheckBox",
			"element" => "data-source{Add:新增分類,View:檢視分類,Fix:修改分類,Clone:複製分類,Delete:刪除分類,Sort:排序分類,Publish:上下架};",
			"default" => "Add:Fix:Delete",
			"tip" => "",
			"list" => ""
		),
		
		"class_fieldMetadata" => array(
			"name" => "分類欄位設定",
			"type" => "FieldsDefine",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"item_bar" => array(
			"name" => "項目設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"item_count_per_page" => array(
			"name" => "每頁筆數",
			"type" => "Number",
			"element" => "",
			"default" => "10",
			"tip" => "",
			"list" => ""
		),
		
		"item_setting" => array(
			"name" => "細部開關",
			"type" => "CheckBox",
			"element" => "data-source{Fix:修改表單,View:檢視表單,Delete:刪除表單,Export:匯出,Import:匯入};",
			"default" => "Fix:Delete",
			"tip" => "",
			"list" => ""
		),
		
		
		"fieldMetadata" => array(
			"name" => "欄位設定",
			"type" => "FieldsDefine",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"reply_bar" => array(
			"name" => "回覆設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"success" => array(
			"name" => "送出成功訊息",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"subject" => array(
			"name" => "回覆信件主旨",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"mail_content" => array(
			"name" => "回覆信件內容",
			"type" => "HtmlEditor",
			"element" => "filter-allow{all};",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"search_bar" => array(
			"name" => "搜尋設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"fieldSearch" => array(
			"name" => "搜尋設定",
			"type" => "FieldsSearch",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"button_bar" => array(
			"name" => "自訂按鈕",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"default_button" => array(
			"name" => "保留預設按鈕",
			"type" => "Switch",
			"element" => "data-source{是,否};",
			"default" => "Y",
			"tip" => "是否保留「儲取」「取消」按鈕？",
			"list" => ""
		),
		
		"fieldButton" => array(
			"name" => "按鈕設定",
			"type" => "FieldsButton",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
	)
);

$master_config["modules"]["link"] = array(
	"title" => "連結管理",
	"type" => "Link",
	"fields" => array(
	
		"title" => array(
			"name" => "模組名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"code" => array(
			"name" => "模組代碼",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"content" => array(
			"name" => "網址",
			"type" => "Text",
			"element" => "required;max-length{255};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"target" => array(
			"name" => "開啟方式",
			"type" => "Select",
			"element" => "data-source{_self:原視窗開啟,_blank:另開視窗};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		
	)
);

$master_config["modules"]["template"] = array(
	"title" => "信件樣版",
	"type" => "Template",
	"fields" => array(
	
		"base_bar" => array(
			"name" => "基本設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
	
		"title" => array(
			"name" => "樣版名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"code" => array(
			"name" => "樣版代碼",
			"type" => "Text",
			"element" => "required;max-length{10};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
				
		"reply_bar" => array(
			"name" => "樣版設定",
			"type" => "Label_Group",
			"element" => "",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"subject" => array(
			"name" => "回覆信件主旨",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => ""
		),
		
		"mail_content" => array(
			"name" => "回覆信件內容",
			"type" => "HtmlEditor",
			"element" => "filter-allow{css,escape};",
			"default" => "",
			"tip" => "",
			"list" => ""
		)
	)
);


$master_config["modules"]["language"] = array(
	"title" => "語系管理",
	"type" => "Language",
	"fields" => array(
	
		"title" => array(
			"name" => "語系名稱",
			"type" => "Text",
			"element" => "required;max-length{50};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		),
		
		"code" => array(
			"name" => "語系代碼",
			"type" => "Text",
			"element" => "required;max-length{10};",
			"default" => "",
			"tip" => "",
			"list" => "Y"
		)
	)
);
?>
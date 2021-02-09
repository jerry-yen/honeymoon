<?php
/**
 * 會員登入/註冊/修改 模組
 */
class Cs_member extends Base_Module {

	/**
	 * 註冊
	 * @param array $data
	 * @param string $key_field 關鍵欄位(不可重複，如信箱/帳號)
	 * @param string $module_code 用戶模組代碼
	 * @return boolean $is_success
	 */
	public function register($data = array(), $key_field = "email", $module_code = "member"){
		
		if(gettype($data) == "object"){
			$data = get_object_vars($data);
		}

		$valid_message = "";
		foreach($data as $key => $value){
			$v = explode("*", $key);
			// 必填驗證
			if(count($v) == 2){
				if(!isset($value) || $value == ""){
					$valid_message .= $v[0] . "为必填栏位\\r\\n";
				}
				else{
					$data[$v[1]] = $value;
				}
			}
			
		}
		if($valid_message != ""){
			$this -> controller -> module_alert -> set_message($valid_message);
			return false;
		}
		
		
		$members = $this -> controller -> get_items($module_code, array("{$key_field}=?"), array($data[$key_field]));
		
		if(count($members) > 0){
			@session_start();
			$_SESSION["CS_USER"] = $members[0] -> id;
			@session_write_close();
			return false;
		}
		
		
		$member = $this -> controller -> get_single_item($module_code, "NONE");
		
		foreach($data as $key => $value){
			if(gettype($member -> {$key}) == "object"){
				$member -> {$key} -> set_value($value);
			}
			else{
				$member -> {$key} = $value;
			}
		}
		
		$id = $member -> insert(false, true);
		
		@session_start();
		$_SESSION["CS_USER"] = $id;
		@session_write_close();
		
		return true;
	}

	/**
	 * 登入
	 * @param string $account 帳號
	 * @param string $password 密碼
	 * @param string $key_field 關鍵欄位(不可重複，如信箱/帳號)
	 * @param string $module_code 用戶模組代碼
	 * @return boolean $is_success
	 */
	public function login($account, $password = "", $key_field = "email", $module_code = "member"){
		
		$where[] = "{$key_field}=?";
		$values[] = $account;
		
		if($password != ""){
			$where[] = "password=?";
			$values[] = $password;
		}
		
		$members = $this -> controller -> get_items($module_code, array("{$key_field}=?"), array($account));
		
		if(count($members) > 0){
			@session_start();
			$_SESSION["CS_USER"] = $members[0] -> id;
			@session_write_close();
			return true;
		}
				
		return false;
	}



	public function is_login(){
		@session_start();
		$is_login = isset($_SESSION["CS_USER"]);
		@session_write_close();
		return $is_login;
	}
	
	public function logout(){
		@session_start();
		unset($_SESSION["CS_USER"]);
		@session_write_close();
	}
	
	public function get_login_member($module_code = "member"){
		
		$member = array();
		
		if($this -> is_login()){
			
			@session_start();
			$member = $this -> controller -> get_single_item($module_code, $_SESSION["CS_USER"]);
			$member = ($member -> is_exists()) ? $member : array();
			@session_write_close();
			
		}
		
		return $member;
	}
	
	/**
	 * 取得購物資訊至IO模組
	 */
	public function get_member_to_io($member){
		$member = $member -> to_array();
		foreach($member as $key => $value){
			if(gettype($value)=="object"){
				 $this -> controller -> module_io -> {$key} = $value -> get_value();
			}
			else{
				$this -> controller -> module_io -> {$key} = $value;
			}
		}
	}
	
	
	/**
	 * 修改會員資訊
	 */
	public function modify($data = array(),$module_code = "member"){
		
		$member = $this -> get_login_member($module_code);
		
		foreach($data as $key => $value){
			if(isset($member -> {$key})){
				 $member -> {$key} -> set_value($value);
			}
		}
		
		$member -> update();
	}

	
}
?>
<?php

/**
 * 信箱模組
 */
class Mailbox extends Base_Module {

	private $mail;
	private $email_config = array();
	private $native_mail_config = array();
	/**
	 * 建構子：初始化所有變數
	 */
	public function __construct() {

	}

	/**
	 * @param $config 設定值
	 * array(
	 *      "publish"       => "Y"          // 是否啟用 SMTP
	 *      "secure"        => "ssl"        // SMTP 安全協定
	 *      "host"          => "host_name"  // SMTP主機位址
	 *      "port"          => "25"         // SMTP Port
	 *      "auth"          => "Y"          // 是否做登入驗證
	 *      "user"          => "xxxx"       // 登入帳號
	 *      "password"      => "xxxxxx"     // 密碼
	 *      "from_address"  => "xx@xx.xx.xx"// 寄件信箱
	 *      "from_name"     => "OOO"        // 寄件名稱
	 *      "reply_address" => "xx@xx.xx.xx"// 回覆信箱
	 * );
	 */
	public function load_config($config = array()) {
		$this -> email_config = (object)$config;

		if ($this -> email_config -> publish == "Y") {
			require_once ("phpmailer/class.phpmailer.php");
			require_once ("phpmailer/PHPMailerAutoload.php");
			$this -> mail = new PHPMailer();
			$this -> mail -> IsSMTP();

			// set mailer to use SMTP
			//$this -> mail -> SMTPDebug = 4;
			$this -> mail -> CharSet = "utf-8";
			$this -> mail -> Encoding = "base64";

			if ($this -> email_config -> secure != "") {
				$this -> mail -> SMTPSecure = $this -> email_config -> secure;
			}

			$this -> mail -> Host = $this -> email_config -> host;
			$this -> mail -> Port = $this -> email_config -> port;
			$this -> mail -> SMTPAuth = ($this -> email_config -> auth == "Y");
			$this -> mail -> Username = $this -> email_config -> user;
			$this -> mail -> Password = $this -> email_config -> password;
			$this -> mail -> WordWrap = 50;
			$this -> mail -> IsHTML(true);
			$this -> mail -> From = $this -> email_config -> from_address;
			$this -> mail -> FromName = $this -> email_config -> from_name;
			$this -> mail -> AddReplyTo($this -> email_config -> reply_address);
		} else {
			$this -> native_mail_config["from_name"] = $this -> email_config -> from_name;
			$this -> native_mail_config["from_address"] = $this -> email_config -> from_address;
		}

	}

	/**
	 * 指定收件者
	 */
	public function set_to($to_emails = array()) {
		
		if ($to_emails == array()) {
			throw new Exception("沒有指定收件者");
		}
		
		if (is_string($to_emails)) {
			$to_emails[] = $to_emails;
		}

		if ($this -> email_config -> publish == "Y") {
			foreach ($to_emails as $mail) {
				if (trim($mail) != "") {
					$this -> mail -> addAddress($mail, $mail);
				}
			}
		} else {
			$this -> native_mail_config["to"] = implode(",", $to_emails);
		}

	}

	/**
	 * 副本
	 */
	public function set_cc($cc_emails = array()) {
		if ($cc_emails == array()){
			return;
		}
		if (is_string($cc_emails)) {
			$cc_emails[] = $cc_emails;
		}

		if ($this -> email_config -> publish == "Y") {
			foreach ($cc_emails as $mail) {
				if (trim($mail) != "") {
					$this -> mail -> AddCC($mail, $mail);
				}
			}
		} else {
		}

	}

	/**
	 * 密件副本
	 */
	public function set_bcc($bcc_emails = array()) {
		if ($bcc_emails == array()){
			return;
		}
		if (is_string($bcc_emails)) {
			$bcc_emails[] = $bcc_emails;
		}
		
		if ($this -> email_config -> publish == "Y") {
			foreach ($bcc_emails as $mail) {
				if (trim($mail) != "") {
					$this -> mail -> AddBCC($mail, $mail);
				}
			}
		} else {
			$this -> native_mail_config["bcc"] = implode(",", $bcc_emails);
		}
	}

	/**
	 * 主旨
	 * @param string $subject 主旨
	 */
	public function set_subject($subject) {
		if ($this -> email_config -> publish == "Y") {
			$this -> mail -> Subject = $subject;
		} else {
			$this -> native_mail_config["subject"] = $subject;
		}
	}

	/**
	 * 信件內容
	 * @param string $content 內容
	 */
	public function set_content($content) {
		if ($this -> email_config -> publish == "Y") {
			$this -> mail -> Body = $content;
		} else {
			$this -> native_mail_config["content"] = $content;
		}
	}

	/**
	 * 發送信件
	 */
	public function send() {
		$this -> controller = Base_Controller::get_instance();
		if ($this -> email_config -> publish == "Y") {

			$email = "=======信件開始=======\r\n";
			$email .= "主旨：" . $this -> mail -> Subject . "\r\n";
			$email .= "內容：" . $this -> mail -> Body . "\r\n";
			$email .= "=======信件結束=======\r\n";

			if (!$this -> mail -> Send()) {
				if ($this -> controller -> module_loader -> is_exists("logger")) {
					$email = "發送失敗 - {$this->mail->ErrorInfo}\r\n" . $email . "\r\n";
					$this -> controller -> module_logger -> log($email, "email");
				}
				return false;
			} else {
				if ($this -> controller -> module_loader -> is_exists("logger")) {
					$email = "發送成功\r\n" . $email . "\r\n";;
					$this -> controller -> module_logger -> log($email, "email");
				}
				return true;
			}
		} else {

			$email = "=======信件開始=======\r\n";
			$email .= "主旨：" . $this -> native_mail_config["subject"] . "\r\n";
			$email .= "內容：" . $this -> native_mail_config["content"] . "\r\n";
			$email .= "=======信件結束=======\r\n";

			//信件內容
			$headers = '';
			$headers  .= 'MIME-Version: 1.0' . "\n";
			$headers .= 'Content-type: text/html; charset=utf-8;' . "\n";
			// Additional headers
			//$headers .= 'To: '.$to_name.'<'.$to.'>' . "\n";
			$headers .= 'From: ' . $this -> native_mail_config["from_address"] . "\n";
			if (isset($this -> native_mail_config["cc"])) {
				$headers .= 'Cc: ' . $this -> native_mail_config["cc"] . " \n";
			}

			if (isset($this -> native_mail_config["bcc"])) {
				$headers .= 'Bcc: ' . $this -> native_mail_config["bcc"] . " \n";
			}

			$to = $this -> native_mail_config["to"];
			$subject = $this -> native_mail_config["subject"];
			$content = $this -> native_mail_config["content"];

			if (mail($to, $subject, $content, $headers)) {
				if ($this -> controller -> module_loader -> is_exists("logger")) {
					$email = "發送成功\r\n" . $email . "\r\n";
					$this -> controller -> module_logger -> log($email, "email");
				}
				return true;

			} else {

				if ($this -> controller -> module_loader -> is_exists("logger")) {
					$email = "發送失敗\r\n" . $email . "\r\n";;

					$this -> controller -> module_logger -> log($email, "email");
				}
				return false;
			}
		}

	}

}
?>
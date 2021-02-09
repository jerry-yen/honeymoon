<?php
/**
 * Code 模組
 */
class Code extends Base_Module {
        public function get_uuid(){
                if (function_exists('com_create_guid')){
                        return preg_replace("/[{}]/","",com_create_guid());
                }else{
                        #mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
                        $charid = md5(uniqid(rand(), true));
                        $hyphen = chr(45);// "-"
                        $uuid = //chr(123)// "{".
                        substr($charid, 0, 8)
                        ."-"
                        .substr($charid, 8, 4)
                        ."-"
                        .substr($charid,12, 4)
                        ."-"
                        .substr($charid,16, 4)
                        ."-"
                        .substr($charid,20,12)
                        //.chr(125)// "}"
                        ;
                        return strtoupper($uuid);
                }
        }
}
?>
<?php

require_once(dirname(__FILE__) . '/PHPExcel.php');
require_once(dirname(__FILE__) . '/PHPExcel/IOFactory.php');

/**
 * Excel模組
 */
class Hmexcel extends Base_Module {

        protected $data = array();

        public function set_header($fields){
                if(count($this -> data) == 0){
                        $this -> data[] = $fields;
                }
                else{
                        $this -> data = array_merge(array($fields), $this -> data);
                }
        }

        public function set_data($data){
                $this -> data = array_merge($this -> data, $data);
        }
        
        public function export($filename){

                $PHPExcel = new PHPExcel();
                $PHPExcel -> setActiveSheetIndex(0);
                $PHPExcel -> getActiveSheet() -> fromArray($this -> data, null, 'A1');

                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=\"$filename.xlsx\"");

                $PHPExcelWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
                $PHPExcelWriter -> save('php://output');
               
               

                
        }
        
}
?>
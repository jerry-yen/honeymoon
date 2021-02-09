<?php
/**
 * 訊息 模組
 */
class Loadingblock extends Base_Module {
        public function block_it($content){
        		ob_start();
                ?>
                <style>#load_block{width:100%;height:10000px;background-color:white;}</style>
                <div id="load_block">&nbsp;</div>
                <?php
                $object = ob_get_contents();
				ob_end_clean();
				
				return preg_replace("/<body(.*?)>/i", "<body$1>" . $object, $content);
				
        }
        
        public function after_load_view(){
                ?>
                <script>
                	$(window).load(function(){
                		$("#load_block").remove();
                	});
                </script>
                <?php
        }       
}
?>
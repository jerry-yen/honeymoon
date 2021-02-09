<?php
class Logout extends MasterController {
        public function main() {
                $this -> loader -> load("ec_master", true);
                $this -> ec_master -> logout();
                $this -> go -> page($this -> config -> relative_master_dir);
        }        
}
?>
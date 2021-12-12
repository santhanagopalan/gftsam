<?php 

Class iActivityGrouping {
    
    private $created_date = "";
    private $prospect_date = "";
    
    private $current_activity = array();
    private $previous_activity = array();
    
    function __construct($cdate,$pdate){
        $this -> created_date = $cdate;
        $this -> prospect_date = $pdate;
    }
    
    public function classify($current_activity, $previous_activity) {
        $this -> current_activity = $current_activity;
        $this -> previous_activity = $previous_activity;
        
        if($this -> isRegressive()){
            return 3;
        }else if($this -> isProgressiveProspect()){
            return 6;
        }else if($this -> isProgressiveLead()){
            return 5;
        }else if($this -> isUnproductive()){
            return 2;
        }else if($this -> isStayingPut()){
            return 4;
        }
        return 1;
    }
    
    public function isProgressiveProspect() {
        $prospect_and_above = array("3", "8", "9");
        if(!in_array($this -> current_activity['status'], $prospect_and_above))
            return false;
        if(!isset($this -> previous_activity['id'])){
            if(($this -> prospect_date) == ($this -> current_activity['date']))
                return true;
        }else{
            if(($this -> previous_activity['status_level']) < ($this -> current_activity['status_level']))
                return true;
            else if($this -> current_activity['status'] == "3" && 
                ($this -> previous_activity['prospect_status_level']) < ($this -> current_activity['prospect_status_level']))
                return true;
        }
        return false;
    }
    
    public function isProgressiveLead() {
        if(!isset($this -> previous_activity['id'])){
            if(($this -> created_date) == ($this -> current_activity['date']))
                return true;
        }else if(($this -> previous_activity['status_level']) < ($this -> current_activity['status_level']))
            return true;
        return false;
    }
    
    public function isStayingPut() {
        if( (isset($this -> previous_activity['status_level']))
                && ($this -> previous_activity['status_level']) == ($this -> current_activity['status_level']))
            return true;
        return false;
    }
    
    public function isRegressive() {
        if(!isset($this -> previous_activity['id']) && ($this -> current_activity['status_level'] == 0)){
            return true;
        }
        if( (isset($this -> previous_activity['status_level']))
            && ($this -> previous_activity['status_level']) > ($this -> current_activity['status_level']))
            return true;
        else if($this -> current_activity['status'] == "3" && isset($this -> previous_activity['prospect_status_level']) 
            && ($this -> previous_activity['prospect_status_level']) > ($this -> current_activity['prospect_status_level']))
            return true;
        return false;
    }
    
    public function isUnproductive(){
        if(isset($this -> current_activity['other_than_call_nature']))
            return false;
        if(isset($this -> current_activity['atleast_one_call_productive']))
            return false;
        $call_nature = array("2", "3", "14");
        if(in_array($this -> current_activity['visit_nature'], $call_nature))
             return true;
        return false;
    }
}

?>

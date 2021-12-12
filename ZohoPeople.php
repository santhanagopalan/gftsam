<?php
/*. require_module 'curl'; .*/
/*. require_module 'json'; .*/
require_once(__DIR__."/log.php");
class ZohoPeople {
    public $emp_id = '';
    public $name = '';
    public $email = '';
    public $mobile = '';
    public $other_email = '';
    public $uid = '';
    public $reporting_mgr = '';
    public $doj = '';
    public $dob = '';
    public $pan = '';
    
    const AUTH_TOKEN = '4358b9f5a02d74fd75c1937900a0f2ea';
    const API_URL = 'https://people.zoho.com/people/api/forms';
    
    public function __construct() {
    }
    
    /**
     * @param int $request_type | 1 - New emp, 2 - Emp edit
     * @param string $people_record_id
     * @return mixed[]
     */
    public function send_emp_dtls($request_type,$people_record_id) {
        global $log;
        $post_arr = array(
            "EmployeeID"=>$this->emp_id,
            "FirstName"=>$this->name,
            "LastName"=>$this->name,
//             "ModifiedBy"=>$this->uid,
//             "Reporting_To"=>$this->reporting_mgr,
            "Dateofjoining"=>date('d-M-y',strtotime($this->doj)),
            "Date_of_birth"=>date('d-M-y',strtotime($this->dob)),
            "PAN"=>$this->pan,
            "Mobile"=>$this->mobile,
            "Other_Email"=>$this->other_email
        );
        $request_url = self::API_URL."/json/employee/insertRecord";
        $post_fields = array('authtoken'=>self::AUTH_TOKEN);
        if($request_type=='1') {
//             $post_arr["AddedBy"] = $this->uid;
            $post_arr["EmailID"] = $this->email;
        } else if($request_type=='2') {
            $request_url = self::API_URL."/json/employee/updateRecord";
            $post_fields['recordId'] = $people_record_id;
        }
        $post_fields['inputData'] = json_encode($post_arr);
        $curl_post_fields = http_build_query($post_fields);
        $curl_options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $curl_post_fields,
            CURLOPT_URL => $request_url
        );
        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $jsonDecode = array();
        if($curl_error = curl_error($ch)) {
            $jsonDecode['status'] = 'fail'; 
        } else {
            $jsonDecode = json_decode($result, true);
        }
        $request_type_string = 'Insert';
        if($request_type=='2') {
            $request_type_string = 'Update';
        }
        $log->logInfo("Zoho People API $request_type_string. Request URL: $request_url, Request Params: $curl_post_fields",array('Response'=>$result),true);
        return $jsonDecode;
    }
    /**
     * @param string $emp_email
     * @return mixed[]
     */
    public function getEmpRecordId($emp_email) {
        global $log;
        $request_url = self::API_URL."/employee/getRecords";
        $post_arr = array('searchField'=>'EmailID','searchOperator'=>'Is','searchText'=>$emp_email);
        $curl_post_fields = http_build_query(array('authtoken'=>self::AUTH_TOKEN,'searchParams'=>json_encode($post_arr)));
        $curl_options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $curl_post_fields,
            CURLOPT_URL => $request_url
        );
        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $jsonDecode = array();
        if($curl_error = curl_error($ch)) {
            $jsonDecode['status'] = 'fail';
        } else {
            $result_arr = json_decode($result,true);
            $emp_record_id = '';
            if(isset($result_arr['response']) and isset($result_arr['response']['result']) and count($result_arr['response']['result'])>0) {
                $record_ids = array_keys($result_arr['response']['result'][0]);
                if(count($record_ids)>0) {
                    $emp_record_id = $record_ids[0];
                }
            }
            $jsonDecode = array($emp_record_id);
        }
        $log->logInfo("Zoho People API Get Emp Details. Request URL: $request_url, Request Params: $curl_post_fields",array('Response'=>$result),true);
        return $jsonDecode;
    }
}

$zoho = new ZohoPeople();
print_r($zoho->getEmpRecordId('build-team@gofrugal.com'));
?>


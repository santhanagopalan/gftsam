<?php
require_once(__DIR__ ."/dbcon.php");
require_once(__DIR__ ."/file_util.php");
require_once(__DIR__ ."/access_util.php");
/**
 * @param string $order_date
 * @param int $lead_code
 * 
 * @return boolean
 */
function check_duplicate_proforma($order_date,$lead_code){
	$resultx=execute_my_query("SELECT GPH_ORDER_NO FROM gft_proforma_hdr WHERE date(GPH_ORDER_DATE)='$order_date' AND GPH_LEAD_CODE=$lead_code and GPH_TYPE=6 ");
	if(mysqli_num_rows($resultx)>0){
		return true;
	}else{
		return false;
	}
}
$dquery=(string)$_REQUEST['query'];
$dheadings=isset($_REQUEST['headings']) ? (string)$_REQUEST['headings']: '';
$export_from_page=isset($_REQUEST['export_from_page']) ? (string)$_REQUEST['export_from_page']: '';
$user_name=isset($_REQUEST['session_user_name']) ? (string)$_REQUEST['session_user_name'] : '';
$user_id=isset($_REQUEST['session_user']) ? (string)$_REQUEST['session_user'] : '';
$type=isset($_REQUEST['export_category']) ? (int)$_REQUEST['export_category'] : 1;
//if($type=='') {$type=1;}
$query=$dquery;
$headings=urldecode($dheadings);
$query=ltrim(stripslashes($query));
$dheadings2=isset($_REQUEST['headings2']) ? (string)$_REQUEST['headings2']: '';
$headings2=urldecode($dheadings2);
$column_fetch=/*. (string[int]) .*/array();
$barcode_gen_url=get_samee_const('BARCODE_PATH');

if(isset($_REQUEST['column_fetch']) and (string)$_REQUEST['column_fetch']!=''){
	$column_fetch=explode(',',urldecode((string)$_REQUEST['column_fetch']));
}
$ERR_MSG="";
if(preg_match('/select/i',$query)){
	$result=execute_my_query($query);
    if(!$result){
	  	$ERR_MSG="While try by Mr/Ms.$user_name  User id: $user_id" .
	  			 "<br>Export From Page: $export_from_page <br>" .
	  			 "Query: ".$query  .
	  			 "<br><b> Query Error :: </b> ". mysqli_error_wrapper();
	  	$subject="ERROR FOUND IN EXPORT ";
	  	mail_error_alert($subject,$msg=$ERR_MSG);
	  	print "Error in Export";
  	}
}else{
	die("<h2> Invalid Operation.. </h2>");
}
$content='';$content2='';$content_hdr='';$print_motgin='';
if($ERR_MSG==''){
	if(isset($_REQUEST['headings']) && (string)$_REQUEST['headings']!=""){
		if($type==1){
			$heading_arr=explode(",",$headings);
			if($heading_arr[0]=="Sl.No"  or  $heading_arr[0]=="S.No"){
				unset($heading_arr[0]);
			}
			$content=implode(",", $heading_arr )."\r\n";
		}
		if($type==2){$content="<tr>".implode("<td>", explode(",",$headings))."</tr>\r\n";}
		if(isset($_REQUEST['headings2']) && (string)$_REQUEST['headings2']!=""){
			if($type==1){$content.=implode(",", explode(",",$headings2))."\r\n";}
			if($type==2){$content.="<tr>".implode("<td>", explode(",",$headings2))."</tr>\r\n";}
		}
	}else{
		for($i=0;$i<mysqli_num_fields($result);$i++){
			if($type==1){$content.=($i!=0?',':'').mysqli_field_name_wrapper($result,$i);}
			if($type==2){$content.=($i!=0?'<td>':'<tr><td>').mysqli_field_name_wrapper($result,$i);}
		}
		if($type==1){$content.="\r\n";}
	}
	$font_size_option = ($type==8) ? "--fontsize 30px" : "";
	if($type==3 or $type==6 or $type==7 or $type==8){
		$content_hdr="<table align=\"center\" border=1 width=\"680px\" cellpadding=5 cellspacing=10>";
		$content.=$content_hdr."<tr>";
	}
	$addr_Sl=0;$addr_row_count=0;
	$data=/*. (string[int]) .*/ array();
	$columan_name=/*. (string[int]) .*/ array();
	for($i=0;$i < mysqli_num_fields($result);$i++){
		$columan_name[$i]=mysqli_field_name_wrapper($result,$i);
	}
	while($query_data=mysqli_fetch_array($result)){
		if($type==1 or $type==2){
			if(is_array($column_fetch) and count($column_fetch)>=1){
				if($column_fetch[0]==""){$array_index=1;}
				else {$array_index=0;}
				for($i=$array_index;$i<count($column_fetch);$i++){
					$column_name=$column_fetch[$i];
					
					if ($column_name == ""){
						$data[$i]="";
					}else{
						$data[$i]=$query_data[$column_name];
					}
					//$data[$i]=str_replace(",", ";" ,$data[$i]);
					$data[$i]=(string)str_replace("\n", ";;" ,$data[$i]);
					$data[$i]=(string)str_replace("\r", "" ,$data[$i]);
					$data[$i]=(string)str_replace("\"", "\'" ,$data[$i]);
					$data[$i]="\"$data[$i]\"";
				}
			}else{
				for($i=0;$i<count($columan_name);$i++){
					$data[$i]=$query_data[$columan_name[$i]];
					if($type==1){
					//$data[$i]=str_replace(",", ";" ,$data[$i]);
					$data[$i]=(string)str_replace("\n", ";;" ,$data[$i]);
					$data[$i]=(string)str_replace("\r", "" ,$data[$i]);
					$data[$i]=(string)str_replace("\"", "\'" ,$data[$i]);
					$data[$i]="\"$data[$i]\"";
					}
				}
			}
			if($type==1){	$content.= implode(",",$data)."\r\n"; }
			if($type==2){	$content.= "<tr><td>".implode("<td>",$data)."</tr>"; }
		}else if($type==3  or $type==6){
			if(isset($query_data['GLH_ADDRESS_VERIFIED']) and $query_data['GLH_ADDRESS_VERIFIED']=='I') continue;
			$print_motgin="--top 0 --bottom 0 --left 0 --right 0";
			$addr_Sl++;
			if(($addr_Sl-1)%3==0){
			    if((($type==6 and $addr_row_count%4==0 ) or ($type==3 and $addr_row_count%5==0)) and $addr_row_count!=0) {
			        if($type==6){
			    		$content.="</tr><tr>$content2";
			    		$content2='';
			    	}
			        $content.='</tr></table>';
				    $content.='<!-- NEW PAGE -->';
				    $content.=$content_hdr."<tr>";
			    }else{
			    	$content.="</tr><tr>";
			    	if($type==6){
			    		$content.="$content2</tr><tr>";
			    		$content2='';
			    	}
			    }
			    $addr_row_count++;
			}
			$content.="<td valign=\"top\" width=\"220px\">";
			$barcode_gen_on=$query_data['Customer_Id'];
			$content.="To<br><b>".$query_data['Customer_Name']."</b>";
			$content.="<br>".((isset($query_data['Authority']) && $query_data['Authority']!='')?$query_data['Authority']:'Propritor');
			$content.=(isset($query_data['Door_No']) && $query_data['Door_No']!=''?"<br>".$query_data['Door_No']:'');
			$content.=(trim($query_data['Block_Society'])!=''?(isset($query_data['Door_No']) && $query_data['Door_No']!='' ?"&nbsp;":'').trim($query_data['Block_Society']):'');
			$content.=($query_data['Street_No']!=''?"<br>".$query_data['Street_No']:'');
			$content.=($query_data['Street_Name']!=''?($query_data['Street_Name']!=$query_data['Street_No']?"<br>".$query_data['Street_Name']:''):'');
			$content.=($query_data['Location']!=''?"<br>".$query_data['Location']:'');
			$content.=(($query_data['Location']!=$query_data['City'] and $query_data['City']!='')?"<br>".$query_data['City']:'');
			$content.="-<b>".$query_data['Pincode'].'</b>';
			$content.="<br>".$query_data['State'];
			$content.=(isset($query_data['MOBILE']) && $query_data['MOBILE']!=''?"<br>".str_replace(' ',', ',trim(preg_replace('!\s+!', ' ',$query_data['MOBILE']))):"");
			$content.=((isset($query_data['MOBILE']) && $query_data['MOBILE']=='' and $query_data['BUSSNO']!='')?"<br>".str_replace('!\s+!', ' ',trim(preg_replace("~[ ]{2,}~"," ",$query_data['BUSSNO']))):"");
			if($export_from_page=="ass_upgrade_oppurtunity_report.php"){
				if($type==3){
					if(strlen($query_data['barcode_gen'])<=7){
						$barcode_gen_on="00000".$query_data['barcode_gen'];
						$barcode_gen_on=substr($barcode_gen_on,-7);
					}else {
						$barcode_gen_on=$query_data['barcode_gen'];
					}
					$product_abr=$query_data['barcode_gen'].'-'.$query_data['Customer_Id'].'-'.$query_data['Product'];
					$content.="<br><IMG SRC=\"$barcode_gen_url?barcode=$barcode_gen_on&width=210&height=25&text=1&additional_info=$product_abr&additional_info_only=1\" alt=\"barcode\" />";
				}
				else if($type==6){
					$bdesc=$query_data['Customer_Id'];
					$content.="<br><IMG SRC=\"$barcode_gen_url?barcode=$bdesc&width=210&height=25&text=1&additional_info=$bdesc&additional_info_only=1\" alt=\"barcode\" />";
					$content2.="<td><IMG SRC=\"$barcode_gen_url?barcode=$bdesc&width=210&height=25&text=1&additional_info=$bdesc&additional_info_only=1\" alt=\"barcode\" /></td>";	
				}
			}else if($export_from_page=="collection_report.php"){
				$barcode_gen_on.="-".$query_data['receiptid_ref'];
				$content.="<br><IMG SRC=\"$barcode_gen_url?barcode=$barcode_gen_on&width=210&height=25&text=1\" alt=\"barcode\" />";
			}else {
				$content.="<br><IMG SRC=\"$barcode_gen_url?barcode=$barcode_gen_on&width=210&height=25&text=1\" alt=\"barcode\" />";
			}
			$content.="</td>";
		}else if($type==7){
				if(isset($query_data['emp_courier_add']) and $query_data['emp_courier_add']=='') continue;
				$print_motgin="--top 0 --bottom 0 --left 20 --right 0";
				$addr_Sl++;
				if(($addr_Sl-1)%2==0){
					if((($type==7 and $addr_row_count%9==0)) and $addr_row_count!=0) {
						if($type==6){
							$content.="</tr><tr>$content2";
							$content2='';
						}
						$content.='</tr></table>';
						$content.='<!-- NEW PAGE -->';
						$content.=$content_hdr."<tr>";
					}else{
						$content.="</tr><tr>";
						if($type==6){
							$content.="$content2</tr><tr>";
							$content2='';
						}
					}
					$addr_row_count++;
				}
				$content.="<td valign=\"top\" width=\"300px\">";
				$content.="<b>".$query_data['GEM_EMP_NAME']."</b>";
				$content.="<br>".$query_data['emp_courier_add'];
				$content.="<br>Mobile: ".$query_data['GEM_MOBILE'].($query_data['GEM_RESIDENCE_NO']!=""?", {$query_data['GEM_RESIDENCE_NO']}":"");
				$content.="</td>";
		}else if($type==8){
		    $print_motgin="--top 10 --bottom 10 --left 20 --right 20";
		    if($addr_row_count%2==0){
		        $content.='<!-- NEW PAGE -->';
		    }
		    $content.='<tr><td valign="top" width="500px" height="240px">';
		    $content.="<b>To, <br>".$query_data['GEM_EMP_NAME']."</b>";
		    $content.="<br>".$query_data['emp_courier_add'];
		    $content.="<br>Mobile: ".$query_data['GEM_MOBILE'].($query_data['GEM_RESIDENCE_NO']!=""?", {$query_data['GEM_RESIDENCE_NO']}":"");
		    $content.="</td></tr><tr height='220px'><td></td></tr>";
		    $addr_row_count++;
		}else if($type==4){
			$print_motgin="--top 0 --bottom 0 --left 0 --right 0";
			if($addr_row_count==0)	$content.=$content_hdr;
			$receipt_id=$query_data['receipt_id'];
			if($addr_row_count!=0){
			    $content.='<!-- NEW PAGE -->';
		    }
			$addr_row_count++;
			$content.=generate_receipt_content($receipt_id,true);
		}else if($type==5){
			if(isset($query_data['Customer_Id'])){
			$print_motgin=" --top 2cm --bottom 2cm --left 1.5cm --right 1.5cm ";
			$GPH_ORDER_DATE=date('Y-m-d');
			if(!check_duplicate_proforma($GPH_ORDER_DATE,$query_data['Customer_Id'])){
				$proforma_no=get_profroma_no($GPH_ORDER_DATE,$user_id);
				$install_dtl="select pfm.gpm_product_name, pm.gpm_skew_desc, pm.gpm_product_code,pm.gpm_product_skew, " .
					" pm.gpm_list_price, pm.gpm_tax_perc,pm.gpm_servise_tax_perc, count(*) qty, " .
					" GID_LIC_PCODE, GID_LIC_PSKEW, GID_LIC_FULLFILLMENT_NO, GID_VALIDITY_DATE, sum(GID_NO_CLIENTS) no_of_clients, GID_EXPIRE_FOR, sum(GID_NO_COMPANYS) no_of_companys" .
					" from gft_install_dtl_new " .
					" join gft_lead_hdr lh on (lh.GLH_LEAD_CODE={$query_data['Customer_Id']}  and GID_LEAD_CODE=lh.GLH_LEAD_CODE and GLH_LEAD_TYPE=1)" .
					" join gft_order_hdr on (GID_ORDER_NO=GOD_ORDER_NO and god_order_status='A')" .
					" join gft_product_master pm on (GID_LIC_PCODE=pm.gpm_product_code and gid_lic_pskew=GPM_REFERER_SKEW and gpm_status='A' and GFT_SKEW_PROPERTY=4) " .
					" join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code)" .
					" where gid_status ='A' and GID_EXPIRE_FOR in (1,3) and GID_VALIDITY_DATE < date(now()) " .
					" group by pm.gpm_product_code, pm.gpm_product_skew order by gpm_product_code ";
				$resultdata=execute_my_query($install_dtl);
				$k=1;$total_amt=0.00;
				$query_order_prod_dtl=/*. (string[int]) .*/ array();
				while($datains=mysqli_fetch_array($resultdata)){
					$GPP_SELL_RATE=round((($datains['qty']*$datains['gpm_list_price'])+($datains['no_of_clients']*1200))/$datains['qty'],2);
					$GPP_TAX_AMT=round($GPP_SELL_RATE*$datains['qty']*$datains['gpm_tax_perc']/100,2);
					$GPP_SER_TAX_AMT=round($GPP_SELL_RATE*$datains['qty']*$datains['gpm_servise_tax_perc']/100,2);
					$GPP_SELL_AMT=round($GPP_SELL_RATE*$datains['qty']*((100+$datains['gpm_servise_tax_perc']+$datains['gpm_tax_perc'])/100),2);
					$query_order_prod_dtl[$k]="insert into gft_proforma_product_dtl (GPP_ORDER_NO,GPP_PRODUCT_CODE,GPP_PRODUCT_SKEW,GPP_SELL_RATE,GPP_TAX_RATE,GPP_SER_TAX_RATE,GPP_QTY,GPP_DISCOUNT_AMT,GPP_SELL_AMT,GPP_TAX_AMT,GPP_SER_TAX_AMT,GPP_LIST_PRICE,GPP_PRINT_ORDER )" .
			    		    	" values('$proforma_no','".$datains['gpm_product_code']."','".$datains['gpm_product_skew']."','$GPP_SELL_RATE','".$datains['gpm_tax_perc']."','".$datains['gpm_servise_tax_perc']."','".$datains['qty']."','0','$GPP_SELL_AMT','$GPP_TAX_AMT','$GPP_SER_TAX_AMT','$GPP_SELL_RATE',$k) ";
					$total_amt+=$GPP_SELL_AMT;
					$k++;
				}
				if((int)$total_amt != 0){
					$query_order_hdr="insert into gft_proforma_hdr (GPH_ORDER_NO,GPH_LEAD_CODE,GPH_EMP_ID,GPH_ORDER_DATE," .
						" GPH_ORDER_AMT,GPH_APPROVEDBY_EMPID,GPH_APPROVAL_CODE,GPH_ORDER_STATUS," .
						" GPH_REMARKS,GPH_REASON_FOR_DISCOUNT,GPH_REASON_FOR_DISCOUNT_DTL,GPH_CREATED_DATE,GPH_VERSION_NO,GPH_CURRENCY_CODE,GPH_TYPE) ".
					    " values('$proforma_no','".$query_data['Customer_Id']."','$user_id','$GPH_ORDER_DATE'," .
						" '$total_amt','$user_id','','A'," .
						" 'Auto generated','','',now(),1,'INR','6')";
					$result_order_hdr=execute_my_query($query_order_hdr);
					if(!$result_order_hdr) {
						die("proforma hdr detail data is not inserted");
					}
					for($k=1;$k<=count($query_order_prod_dtl);$k++){
						$result_insert=execute_my_query($query_order_prod_dtl[$k]);
						if(!$result_insert){
							die ("error find in $query_order_prod_dtl[$k]");
						}
					}
					$content.=($addr_row_count!=0?'<!-- NEW PAGE -->':'').generateproforma_invoice_wothout_order($proforma_no);
					$addr_row_count++;
				}
			}
			}else{
				die("<h2> Invalid Operation.. </h2>");
			}
		}
	}
	if($type==3 or $type==6){
		if($type==6){
			$content.="</tr><tr>$content2";
		}
		$content.="</tr></table>";
	}
	$fileid=time();
    if($type==1){
		header("Content-Disposition: attachment; filename=report_$fileid.csv");
		header('Content-Type: application/octet-stream');
		header("Cache-Control: post-check=0, pre-check=0", false );
		header("Content-Length: ".strlen($content));
		print $content;
    }
    if($type==10) {
        if(strpos($query, " Customer_Id")===false){
            die("Customer_Id is not present in query. Can't export mobile numbers");
        }
        $q1 = " select GCC_CONTACT_NO from gft_customer_contact_dtl ".
              " left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
              " where gcc_contact_type=1 and if(GPU_CONTACT_ID is null,1,GPU_CONTACT_STATUS='A') ".
              " and GCC_LEAD_CODE in (select Customer_Id from ($query) tt) and length(GCC_CONTACT_NO) > 8 ".
              " group by GCC_CONTACT_NO ";
        $r1 = execute_my_query($q1);
        $str = $separator = '';
        while ($d1 = mysqli_fetch_array($r1)){
            $str .= $separator.$d1['GCC_CONTACT_NO'];
            $separator = "\n";
        }
        if($str!='') {
	    	write_to_file("temp_pdf_generator", $str, "cust_mobile_nos.txt");
	    	$f_location_path="$attach_path/temp_pdf_generator/cust_mobile_nos.txt";
	    	$filename = "cust_mobile_nos.txt";
	    	$f_location=realpath($f_location_path);
	    	if ($f_location === FALSE){
	    		error_log("File not found: ".$f_location_path);
	    		die("File not found. Errorcode: 100-200");
	    	}
	    	header('Pragma: public');
	    	header('Expires: 0');
	    	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    	header('Cache-Control: private',false);
	    	header('Content-Description: File Transfer');
	    	header('Content-Disposition: attachment; filename='.basename($filename));
	    	header('Content-Transfer-Encoding: binary');
	    	header('Content-Length: ' . filesize($f_location));
	    	header('Content-Type: text/plain');
	    	readfile($f_location);
    	}
    }
    /*header("Content-Type: text/csv; charset=UTF-8");*/
    if($type==2 or $type==3 or $type==4 or $type==5 or $type==6  or $type==7 or $type==8){
    	$content="<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"><html><head></head><body>".$content."</body></html>";
    	$content=(string)str_replace('<td> </td>','<td>&nbsp;</td>', $content);
    	$content=(string)str_replace('\"','"', $content);
		$content=(string)str_replace('<tbody>','', $content);
		$content=(string)str_replace('</tbody>','', $content);
		$content=(string)str_replace("&apos;","'", $content);
    	$html_fname="report_".$fileid.".html";
    	$folder_name="temp_pdf_generator";
        $t=write_to_file($folder_name,$content,$html_fname,$mode=null);
        $file_path=realpath("../sales_server_support/$folder_name/");
		$f_name=(string)str_replace("html","pdf",$html_fname);
		$fr_name=$file_path.'/'.$f_name;
		$filename=$file_path.'/'.$html_fname;
		echo "<div class='hide'>";
		passthru("htmldoc --quiet --size A4 -t pdf14 --jpeg --footer ... $print_motgin $font_size_option -f $fr_name --webpage $filename ");
		echo "</div>";
		js_location_href_to("file_download.php?file_type=addr_pdf&filename=$f_name");
		close_the_popup();
    }
}
exit;
?>

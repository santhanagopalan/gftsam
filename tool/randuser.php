<?php
require_once (__DIR__.'/../dbcon.php');
global $uid;
if ($uid == ''){
	show_my_alert_msg("Your Session has Expired");
	die("Please do login into SAM <a href=https://sam.gofrugal.com>here</a>");
}
?>
 <head>

<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head> 

<h1>Random User Selection</h1>



<div class="container">
  <div class="row">
    <div class="col-sm-4">
      <h3>Users List</h3>

   	<span id="opener" class="glyphicon glyphicon-cd" data-toggle="modal" data-target="#myModal"></span>

      <br>
		    <select id="sbOne" multiple="multiple">

    		</select>
    		<br>
    		<br>
    		<br><br><br><br><br><br><br><br><br>


    </div>
    <div class="col-sm-4">
      <h3></h3>
    <input type="button" id="left" value="<" />
    <input type="button" id="right" value=">" />
    <input type="button" id="leftall" value="<<" />
    <input type="button" id="rightall" value=">>" />
    <br>
    <br>
    <input type="button" id="randomselect" value="randomselect" />

      <div id=res_selected_index style="display:none"></div>
      <div id=res_selected_value style="display:none"></div>

      <br>
      <div id=res_selected_member></div>
      <input type="button" id="right2" value="Add to Selected Members" style="display:none" />

    </div>
    <div class="col-sm-4">
      <h3>Already selected members</h3>
    <select id="sbTwo" multiple="multiple">
    </select>

    </div>
  </div>
</div>


  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Select User Type</h4>
        </div>
        <div class="modal-body">

			<select name="team" id="modal_team">
    			<option value="">Select team first</option>
			</select>
        </div>
        <div class="modal-footer">
          <button type="button"  class="btn btn-default" id="load_users_button">Load</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>



<script>
(function($) {
    $.rand = function(arg) {
        if ($.isArray(arg)) {
            return arg[$.rand(arg.length)];
        } else if (typeof arg == "number") {
            return Math.floor(Math.random() * arg);
        } else {
            return 4;  // chosen by fair dice roll
        }
    };



})(jQuery);

var items = [523,3452,334,31,57,5346];


$(document).ready(function(){

function selectRandom(origin){

	var length = $(origin).find("option").length;

	var pick=0;

	do{
		var rand = Math.random();

    	pick = Math.floor(rand*length);
    }while (pick <=0 && length>1)

    //var randomValue=$(origin).index(pick);
    var randomValue=$(origin).find("option").eq(pick).val();
    var randomText=$(origin).find("option").eq(pick).text();

    $(origin).val(randomValue);

	$('#res_selected_index').text("length="+length + " rand="+rand);
	$('#res_selected_value').text("selected index="+pick + " value=" + randomValue + " text="+randomText);

	$('#res_selected_member').html("<h2>"+randomText+"<h2>");
	$('#right2').show();
	


    //var randomOption = $(origin).options[pick];
    //alert(randomValue.

	//var length = $('#items option').length;
    //var pick = Math.floor(Math.random()*length);
    //var randomOption = $('#items option')[pick];
}

function moveItems(origin, dest) {
    $(origin).find(':selected').appendTo(dest);
}
 
function moveAllItems(origin, dest) {
    $(origin).children().appendTo(dest);
}

function loadTeam(){

     $.ajax({
            type:'GET',
            url:'getRoleList.php',
            dataType: "json",
            success:function(data){
            	$('#modal_team').empty();
            	$.each(data, function(i,obj){
					$('#modal_team').append($('<option></option>').val(obj['id']).html(obj['value']));
				});
            }
        }); 
}


function loadUsers(){

	var role_id=$('#modal_team').find(":selected").val();
     $.ajax({
            type:'GET',
            url:'getEmpList.php?role_id='+role_id,
            dataType: "json",
            success:function(data){
            	//$('#modal_team').empty();
            	$.each(data, function(i,obj){
            		var option_val=obj['id'];
            		var exists = $("#mySelect").find("option").filter(function() {return this.value === option_val;}).length !=0;
            		if (!exists){
            			$('#sbOne').append($('<option></option>').val(obj['id']).html(obj['value']));
            		}
				});
				$('#myModal').modal('hide')
            }
        }); 
}



$('#left').click(function () {
    moveItems('#sbTwo', '#sbOne');
});
 
$('#right').on('click', function () {
    moveItems('#sbOne', '#sbTwo');
});
 
$('#leftall').on('click', function () {
    moveAllItems('#sbTwo', '#sbOne');
});
 
$('#rightall').on('click', function () {
    moveAllItems('#sbOne', '#sbTwo');
});

$('#randomselect').on('click', function () {
    selectRandom('#sbOne')
});


$('#right2').on('click', function () {
    moveItems('#sbOne', '#sbTwo');
});

loadTeam();

$('#load_users_button').on('click', function () {
    loadUsers();
});

//alert("loaded");

}); 

</script>


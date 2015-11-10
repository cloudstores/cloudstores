$(document).ready(function(){
	
	$('button#signin').click(function(){
		if ( $('#username').val()=='' || $('#passwd').val()=='' ){
			$('div#signin_response').html("Please enter correct username and password");
			return false;
		}
		var person = {
	            username: $("#username").val(),
	            passwd:$("#passwd").val()
	        }
		
		$.ajax({
    		url : "http://127.0.0.1/knitpeer-UX/signin.php",
    		type: "POST",
    		data : person,
    		success: function(data, status, jqXHR)
    		{
    			$("div#signin_response").html(data);
    			data = JSON.parse(data);
    			if(data['error']!=''){
    				window.location.reload();
    			}
    			else{
    				window.location.href="http://127.0.0.1/knitpeer-UX/profile_page.html";
    			}
    		},
    		error: function(jqXHR, status, errorThrown)
    		{
    			$("#signin_response").html(data);
    			alert("Data: " + data );
    		}
		});
		$('#signinForm').submit( function(){
			
		})
			
	});
	
	$('button#signup').click(function(){
		if ( $('#FirstName').val()=='' || $('#LastName').val()=='' || 
				$('#email').val()=='' || $('#phone').val()=='' ){ 
			$('div#signup_response').html("Please fill up all the details");
			return false;
		}
		if ( $('#passwd1').val()=='' || $('#passwd2').val()=='' || $('#passwd1').val()!=$('#passwd2').val()){
			$('div#signup_response').html("Password length should be atleast 8 characters with " +
					"1 special character and atleast 1 digit");
			return false
		}
	});
	  

});
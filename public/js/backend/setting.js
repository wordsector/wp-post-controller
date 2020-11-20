jQuery(document).ready(function($){

         
    function wppcGetParameterByName(name, url) {
        if (!url){
        url = window.location.href;    
        } 
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return "";
        return decodeURIComponent(results[2].replace(/\+/g, " "));
   }

   function wppcIsEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

// Function list ends here


    $(".wppc-tabs a").click(function(e){
        var href = $(this).attr('href');                
        var currentTab = wppcGetParameterByName('tab',href);
        if(!currentTab){
          currentTab = "post_views";
        }                                                                
        $(this).siblings().removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");
        $(".form-wrap").find(".wppc-"+currentTab).siblings().hide();
        $(".form-wrap .wppc-"+currentTab).show();
        window.history.pushState("", "", href);
        return false;
});


$(".wppc-send-support-request").on("click", function(e){

    e.preventDefault();   
    var message     = $("#wppc_query_message").val();  
    var email       = $("#wppc_query_email").val();  

    if($.trim(message) !='' && $.trim(email) !='' && wppcIsEmail(email) == true){
     $.ajax({
                    type: "POST",    
                    url:ajaxurl,                    
                    dataType: "json",
                    data:{action:"wppc_send_query", message:message,email:email, wppc_nonce:wppc_local.wppc_nonce},
                    success:function(response){                       
                      if(response['status'] =='success'){
                        $(".wppc-query-success").removeClass('wppc_hide_element');
                        $(".wppc-query-error").addClass('wppc_hide_element');
                      }else{                                  
                        $(".wppc-query-success").addClass('wppc_hide_element');  
                        $(".wppc-query-error").removeClass('wppc_hide_element');
                      }
                    },
                    error: function(response){                    
                        console.log(response);
                    }
                    });   
    }else{
        
        if($.trim(message) =='' && $.trim(email) ==''){
            alert('Please enter the message and email');
        }else{
        
        
        if($.trim(message) == ''){
            alert('Please enter the message');
        }
        if($.trim(email) == ''){
            alert('Please enter the email');
        }
        if(wppcIsEmail(email) == false){
            alert('Please enter a valid email');
        }
            
        }
        
    }                        

});


});
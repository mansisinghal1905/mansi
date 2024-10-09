
$(document).ready(function() {
    getuserlist();
//  alert($("#receiver_id").val());
    if($("#receiver_id").val()==1){
        
        // getshowmessage($("#receiver_id").val(),false);
    }


$('.selectuser').on('click', function() {
    // alert('hi');
    const id = $(this).data('id'); // Assuming you're getting the ID from a data attribute
    $("#receiver_id").val(id);// console.log(id);

    getshowmessage(id,true);

});

});
function showuser(id){
    // alert(id);
    $("#receiver_id").val(id);// console.log(id);
    getshowmessage(id,true);

}
setInterval(function() {
    getuserlist();
    // getshowmessage();
}, 5000);  // 2000 milliseconds = 2 seconds

function getuserlist(){
   
    $.ajax({
    
            url: 'http://localhost/officeproject/admin/chat/getuserlist', // Use the generated URL
            type: 'post',
            data:
                {id: '0'}, 
                
            dataType : "html",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                // $("#showMessages").empty();
                $("#userlist").html(response);
        
                // Handle success
            },
            
            error: function(xhr, status, error) {
                // console.error('Error:', error);
                // Handle error
            }
        });


}

function getshowmessage(id,is_read){
    // alert(is_read);
    $.ajax({
        url: 'http://localhost/officeproject/admin/showMessage', // Use the generated URL
        type: 'post',
        data:
            {id: id,is_read:is_read}, 
            
        dataType : "html",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
        },
        success: function(response) {
            // $("#showMessages").empty();
            $("#showMessages").html(response);
    
            // Handle success
        },
        error: function(xhr, status, error) {
            // console.error('Error:', error);
            // Handle error
        }
    });
}
function btnclick(){
    const message = $("#message").val().trim();
    const receiver_id = $("#receiver_id").val();
        // alert(receiver_id);
    if (message === '' || receiver_id === '') {
        alert('Message or recipient is empty!');
        return;
    }

    fetch('http://localhost/officeproject/admin/chat/sendMessage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({
            message: message,
            receiver_id: receiver_id
        })
    })
    .then(response => response.json())
    .then(data => {
        $("#message").val('');
        getshowmessage(receiver_id,false);
        console.log('Message sent:', data);
    })
    .catch(error => console.error('Error:', error));
};

window.onbeforeunload = function() {
    navigator.sendBeacon('/logout'); // or whatever route you use to destroy the session
};
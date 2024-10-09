
@extends('admin.layouts.backend.app')
@push('style')
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        #chat {
            border: 1px solid #ccc;
            padding: 10px;
            height: 400px;
            overflow-y: scroll;
            margin-bottom: 10px;
        }
        .message {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .sent {
            text-align: right;
            background-color: #dff0d8;
        }
        .received {
            text-align: left;
            background-color: #f2dede;
        }
    </style>
@endpush
@section('content')
<main class="nxl-container apps-container apps-chat">
        <div class="nxl-content without-header nxl-full-content">
            <!-- [ Main Content ] start -->
            <div class="main-content d-flex">
                    @if(Auth::user()->role != 1)
                        <input type="hidden" value="1" id="receiver_id">
                    @else
                    <input type="hidden" value="" id="receiver_id">
                    @endif
                <!-- [ Content Sidebar ] start -->
               
                <div class="content-sidebar content-sidebar-xl" data-scrollbar-target="#psScrollbarInit">
                    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
                        <h4 class="fw-bolder mb-0">Chat</h4>
                        <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex">
                            <i class="feather-x"></i>
                        </a>
                    </div>
                   
                    <div class="content-sidebar-body">
                        <!-- <div class="py-0 px-4 d-flex align-items-center justify-content-between border-bottom">
                            <form class="sidebar-search">
                                <input type="search" class="py-3 px-0 border-0" id="chattingSearch" placeholder="Search...">
                            </form>
                           
                        </div> -->
                        <div class="content-sidebar-items" id="userlist">

                        </div>
                    </div>
                    <!-- <a href="javascript:void(0);" class="content-sidebar-footer px-4 py-3 fs-11 text-uppercase d-block text-center">Load More</a> -->
                </div>
               
                <!-- [ Content Sidebar  ] end -->
                <!-- [ Main Area  ] start -->
                <div class="content-area" id="showMessages" data-scrollbar-target="#psScrollbarInit">
                </div>
               

              
               
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </main>
@endsection

@push('script')


    <!-- <script>
        const chat = document.getElementById('chat');
        const message = document.getElementById('message');
        const sendButton = document.getElementById('send');
        let receiver_id = 0;
        // Get all elements with the class 'selectuser'
            const selectusers = document.querySelectorAll('.selectuser');

            // Loop through each element and add a click event listener
            selectusers.forEach(function(selectuser) {
            selectuser.addEventListener('click', function(event) {
                // Get the current element that was clicked
                const currentElement = event.target;
                
                // Log the current element
                console.log(currentElement);
                
                // Optionally, you can access the data-id of the clicked element
                const userId = currentElement.getAttribute('data-id');
                 document.getElementById('receiver_id').value = userId;
                receiver_id = userId;

            });
            });


        // Pusher Setup
        // const pusher = new Pusher('9874c74f6e891050bcc9', {
        //     cluster: 'ap2'
        // });

        // const channel = pusher.subscribe('chat');

        // channel.bind('App\\Events\\MessageSent', function(data) {
        //     const chatMessage = document.createElement('div');
        //     chatMessage.className = 'message received';
        //     chatMessage.innerHTML = `<strong>${data.receiver_id}:</strong> ${data.message}`;
        //     chat.appendChild(chatMessage);
        //     chat.scrollTop = chat.scrollHeight; // Auto-scroll to the bottom
        // });

       
    </script> -->


   <script>
        function showInitials(imgElement, initials) {
            // Hide the broken image
            imgElement.style.display = 'none';

            // Create a new div to display initials
            const initialsDiv = document.createElement('div');
            initialsDiv.classList.add('user-initials');
            initialsDiv.textContent = initials;

            // Style the initialsDiv to look like a profile picture
            initialsDiv.style.width = imgElement.width + 'px';
            initialsDiv.style.height = imgElement.height + 'px';
            initialsDiv.style.backgroundColor = '#ccc';
            initialsDiv.style.color = 'rgb(52 84 209)';
            initialsDiv.style.display = 'flex';
            initialsDiv.style.alignItems = 'center';
            initialsDiv.style.justifyContent = 'center';
            initialsDiv.style.fontSize = '20px';
            initialsDiv.style.fontWeight = 'bold';
            initialsDiv.style.borderRadius = '50%';

            // Insert the initials div next to the image
            imgElement.parentNode.insertBefore(initialsDiv, imgElement.nextSibling);
        }
    </script>



@endpush
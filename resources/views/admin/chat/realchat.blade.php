
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
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Chat</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.chat.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Chat</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex d-md-none">
                            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                       
                    </div>
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>

            @if(Auth::user()->role == 1)
            <input type="hidden" value="" id="receiver_id">
                <ul>
                
                @foreach ($userlist as $user)
                    <li>
                        <a href="javascript:void(0)" ><strong data-id ="{{ $user->id }}" class="selectuser">{{ $user->name }}:</strong> </a>
                    </li>
                    @endforeach
                
                </ul>
            @else
             <input type="hidden" value="1" id="receiver_id">
            @endif
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div id="chat">
                    @foreach ($chats as $chat)
                        <div class="message {{ $chat->user_id === auth()->id() ? 'sent' : 'received' }}">
                            <strong>{{ $chat->user->name }}:</strong> {{ $chat->message }}
                        </div>
                    @endforeach
                </div>
                <input type="text" id="message" placeholder="Type a message..." />
                <button id="send">Send</button>
            <!-- [ Main Content ] end -->
            </div>
        <!-- [ Footer ] start -->
        <footer class="footer">
            <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                <span>Copyright ©</span>
                <script>
                    document.write(new Date().getFullYear());
                </script>
            </p>
            <div class="d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Help</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Terms</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Privacy</a>
            </div>
        </footer>
        <!-- [ Footer ] end -->
    </main>
@endsection

@push('script')

<script>
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
        const pusher = new Pusher('9874c74f6e891050bcc9', {
            cluster: 'ap2'
        });

        const channel = pusher.subscribe('chat');

        channel.bind('App\\Events\\MessageSent', function(data) {
            const chatMessage = document.createElement('div');
            chatMessage.className = 'message received';
            chatMessage.innerHTML = `<strong>${data.receiver_id}:</strong> ${data.message}`;
            chat.appendChild(chatMessage);
            chat.scrollTop = chat.scrollHeight; // Auto-scroll to the bottom
        });

        sendButton.addEventListener('click', function() {
            const userInput = message.value.trim();
            if (userInput === '') return; // Don't send empty messages

            fetch('{{ route("admin.chat.sendMessage") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: userInput,
                    receiver_id:document.getElementById('receiver_id').value
                })
            })
            .then(response => response.json())
            .then(data => {
                const chatMessage = document.createElement('div');
                chatMessage.className = 'message sent';
                chatMessage.innerHTML = `<strong>${data.receiver_id}:</strong> ${data.message}`;
                chat.appendChild(chatMessage);
                chat.scrollTop = chat.scrollHeight; // Auto-scroll to the bottom
                message.value = ''; // Clear the input
            });
        });
    </script>

@endpush
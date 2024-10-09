        <div class="content-area-header sticky-top">
            <div class="page-header-left hstack gap-4">
                <a href="javascript:void(0);" class="app-sidebar-open-trigger">
                    <i class="feather-align-left fs-20"></i>
                </a>
                <a href="javascript:void(0);" class="d-flex align-items-center justify-content-center gap-3" data-bs-toggle="offcanvas" data-bs-target="#userProfileDetails">
                    <div class="avatar-image">
                                @if(isset($userName->avatar))
                                
                                    <img src="{{ $userName->avatar }}" class="img-fluid user-avatar" alt="image">
                                @else
                                    <img src="{{ asset('public/assets/images/defulatprofile1.png')}}" alt="default-avatar" class="img-fluid user-avatar" />
                                @endif
                        <!-- <img src="assets/images/avatar/1.png" class="img-fluid" alt="image"> -->
                    </div>
                    <div class="d-none d-sm-block">
                        <div class="fw-bold d-flex align-items-center">{{$userName->name}}</div>
                        <div class="d-flex align-items-center mt-1">
                            <span class="wd-7 ht-7 rounded-circle opacity-75 me-2 @if($userName->chat_status == 'active') bg-success @else bg-danger @endif"></span>
                            @if($userName->chat_status == 'active')
                            <span class="fs-9 text-uppercase fw-bold  text-success">{{$userName->chat_status}} Now</span>
                            @else
                            <span class="fs-9 text-uppercase fw-bold text-danger">{{$userName->chat_status}} </span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            
        </div>
        <div class="content-area-body">
            @foreach($chatMessages as $message)
                <div class="single-chat-item mb-5">
                    @if($message->sender_id === auth()->user()->id)
                        <!-- Display Sent Message -->
                        <div class="d-flex flex-row-reverse align-items-center gap-3 mb-3">
                            <a href="javascript:void(0)" class="avatar-image">
                                <img src="{{ auth()->user()->avatar }}" class="img-fluid rounded-circle" alt="image">
                            </a>
                            <div class="d-flex flex-row-reverse align-items-center gap-2">
                                <a href="javascript:void(0);">{{ auth()->user()->name }}</a>
                                <span class="fs-11 text-muted">{{ $message->created_at->format('h:i A') }}</span>
                            </div>
                        </div>
                        <div class="wd-500 p-3 rounded-5 bg-gray-200 ms-auto">
                            <p class="py-2 px-3 rounded-5 bg-white">{{ $message->message }}</p>
                        </div>
                    @else
                        <!-- Display Received Message -->
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <a href="javascript:void(0)" class="avatar-image">
                                <img src="{{ $userName->avatar }}" class="img-fluid rounded-circle" alt="image">
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                <a href="javascript:void(0);">{{ $userName->name }}</a>
                                <span class="fs-11 text-muted">{{ $message->created_at->format('h:i A') }}</span>
                            </div>
                        </div>
                        <div class="wd-500 p-3 rounded-5 bg-gray-200">
                            <p class="py-2 px-3 rounded-5 bg-white">{{ $message->message }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <!--! BEGIN: Message Editor !-->
        <div class="d-flex align-items-center justify-content-between border-top border-gray-5 bg-white sticky-bottom">
        <input id="message" class="form-control border-0 emoji-picker" placeholder="Type your message here...">
       
            <div class="border-start border-gray-5 send-message">
                <a onclick="btnclick()" href="javascript:void(0)" class="wd-60 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-trigger="hover" id="send1" title="Send Message" style="height: 59px"><i class="feather-send"></i></a>
            </div>
        </div>
        <!--! END: Message Editor !-->

       
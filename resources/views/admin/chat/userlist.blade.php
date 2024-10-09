
    
    @foreach ($userDetails as $user)
        <div class="p-4 d-flex position-relative border-bottom c-pointer single-item">
            <div class="avatar-image">
            @if($user->avatar)
            
                <img src="{{ $user->avatar }}" class="img-fluid user-avatar" alt="image">
            @else
                <img src="{{ asset('public/assets/images/defulatprofile1.png')}}" alt="default-avatar" class="img-fluid user-avatar" />
            @endif
            </div>
            <div class="ms-3 item-desc">
                <div class="w-100 d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0);" class="hstack gap-2 me-2 selectuser" onclick = "showuser({{$user->id}})" data-id ="{{$user->id}}">
                        <span>{{ $user->name }}</span>
                        <div class="wd-5 ht-5 rounded-circle opacity-75 me-1 @if($user->chat_status == 'active') bg-success @else bg-danger @endif"></div>
                        <span class="fs-10 fw-medium text-muted text-uppercase d-none d-sm-block">{{$user->createdDate}} </span>
                        <span class="badge bg-danger">{{$user->chatcount}}</span>
                    </a>
                    
                </div>
                @if($user->is_read==1)
                    <p class="fs-12  fw-semibold text-dark mt-2 mb-0 text-truncate-2-line">{{$user->unreadmessage}}</p>
                @else
                    <p class="fs-12 text-muted mt-2 mb-0 text-truncate-2-line">{{$user->unreadmessage}}</p>
                @endif
                
            </div>
        </div>
    @endforeach
  


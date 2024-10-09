   @foreach($notificationData as $key=>$notification)
   <div class="notifications-item">
        <!-- <img src="{{ asset('public/assets/images/avatar/2.png')}}" alt="" class="rounded me-3 border" /> -->
        <div class="notifications-desc">
        <div class="font-body text-truncate-2-line">
            <span class="fw-semibold text-dark">{{$notification['title']}}</span> {{$notification['message']}}
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="notifications-date text-muted border-bottom border-bottom-dashed">{{$notification['time']}}</div>
            <div class="d-flex align-items-center float-end gap-2">
                <a href="javascript:void(0);" class="d-block wd-8 ht-8 rounded-circle bg-gray-300" data-id="{{$notification['id']}}" data-bs-toggle="tooltip" title="Make as Read" onclick="markAsRead({{$notification['id']}})"></a>
                <a href="javascript:void(0);" class="text-danger" data-bs-toggle="tooltip" title="Remove" onclick="removeNotification({{$notification['id']}})">
                    <i class="feather-x fs-12"></i>
                </a>
            </div>
        </div>
    </div>
    </div>

    @endforeach



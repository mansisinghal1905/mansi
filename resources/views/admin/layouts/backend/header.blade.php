    <!--! [Start] Header !-->
    <!--! ================================================================ !-->
    <header class="nxl-header">
        <div class="header-wrapper">
            <!--! [Start] Header Left !-->
            <div class="header-left d-flex align-items-center gap-4">
                <!--! [Start] nxl-head-mobile-toggler !-->
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
                <!--! [Start] nxl-head-mobile-toggler !-->
                <!--! [Start] nxl-navigation-toggle !-->
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button">
                        <i class="feather-align-left"></i>
                    </a>
                    <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                        <i class="feather-arrow-right"></i>
                    </a>
                </div>
                <!--! [End] nxl-navigation-toggle !-->
                <!--! [Start] nxl-lavel-mega-menu-toggle !-->
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                        <i class="feather-align-left"></i>
                    </a>
                </div>
                <!--! [End] nxl-lavel-mega-menu-toggle !-->
                <!--! [Start] nxl-lavel-mega-menu !-->
                <div class="nxl-drp-link nxl-lavel-mega-menu">
                    <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                        <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    
                </div>
                <!--! [End] nxl-lavel-mega-menu !-->
            </div>
            <!--! [End] Header Left !-->
            <!--! [Start] Header Right !-->
            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">
                  
                    <div class="nxl-h-item d-none d-sm-flex">
                        <div class="full-screen-switcher">
                            <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                                <i class="feather-maximize maximize"></i>
                                <i class="feather-minimize minimize"></i>
                            </a>
                        </div>
                    </div>
                    <div class="nxl-h-item dark-light-theme">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                            <i class="feather-moon"></i>
                        </a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                            <i class="feather-sun"></i>
                        </a>
                    </div>
                    
                    <div class="dropdown nxl-h-item">
                        <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button" data-bs-auto-close="outside">
                            <i class="feather-bell"></i>
                            <span class="badge bg-danger nxl-h-badge" id="notificationcount">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                            <div class="d-flex justify-content-between align-items-center notifications-head">
                                <h6 class="fw-bold text-dark mb-0">Notifications</h6>
                                <a href="javascript:void(0);" onClick ="markAsRead(0)" class="fs-11 text-success text-end ms-auto" data-bs-toggle="tooltip" title="Make as Read">
                                    <i class="feather-check"></i>
                                    <span>Make as Read</span>
                                </a>
                            </div>
                           <div id="shownotificaton"></div>
                           
                            <div class="text-center notifications-footer">
                                <a href="javascript:void(0);" class="fs-13 fw-semibold text-dark">Alls Notifications</a>
                            </div>
                        </div>
                    </div>


                    <div class="dropdown nxl-h-item">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                            <img src="{{ Auth::user()->avatar }}" style="height: 40px;" alt="user-image" class="img-fluid user-avtar me-0" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center">
                                   
                                        @if(Auth::check())
                                            <!-- <img src="{{ Auth::user()->avatar }}" alt="user-image" class="img-fluid user-avatar" /> -->
                                            <img src="{{ Auth::user()->avatar }}" alt="user-image" style="height: 50px; width: 50px;" class="img-fluid user-avatar" />

                                        @else
                                            <img src="{{ asset('public/assets/images/no-image-available.png')}}" alt="default-avatar" class="img-fluid user-avatar" />
                                        @endif

                                    <div>
                                        @if(Auth::check())
                                        <h6 class="text-dark mb-0">{{ ucfirst(Auth::user()->name) }} <span class="badge bg-soft-success text-success ms-1">PRO</span></h6>
                                        @else
                                        <h6 class="text-dark mb-0"> <span class="badge bg-soft-success text-success ms-1">PRO</span></h6>
                                        @endif

                                        <span class="fs-12 fw-medium text-muted">@if(Auth::check()) {{ Auth::user()->email }} @else  @endif</span>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="dropdown">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-success rounded-circle me-2"></i>
                                        <span>Active</span>
                                    </span>
                                    <i class="feather-chevron-right ms-auto me-0"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-10 ht-10 border border-2 border-gray-1 bg-warning rounded-circle me-2"></i>
                                            <span>Always</span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-10 ht-10 border border-2 border-gray-1 bg-success rounded-circle me-2"></i>
                                            <span>Active</span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-10 ht-10 border border-2 border-gray-1 bg-danger rounded-circle me-2"></i>
                                            <span>Bussy</span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-10 ht-10 border border-2 border-gray-1 bg-info rounded-circle me-2"></i>
                                            <span>Inactive</span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-10 ht-10 border border-2 border-gray-1 bg-dark rounded-circle me-2"></i>
                                            <span>Disabled</span>
                                        </span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-10 ht-10 border border-2 border-gray-1 bg-primary rounded-circle me-2"></i>
                                            <span>Cutomization</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="dropdown">
                                    <span class="hstack">
                                        <i class="feather-dollar-sign me-2"></i>
                                        <span>Subscriptions</span>
                                    </span>
                                    <i class="feather-chevron-right ms-auto me-0"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                            <span>Plan</span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                            <span>Billings</span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                            <span>Referrals</span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                            <span>Payments</span>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                            <span>Statements</span>
                                        </span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <span class="hstack">
                                            <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                            <span>Subscriptions</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div> -->
                            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                                <i class="feather-user"></i>
                                <span>Update Profile</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" id="changePasswordLink">
                                <i class="feather-activity"></i>
                                <span>Change Password</span>
                            </a>
                            <!-- <a href="javascript:void(0);" class="dropdown-item">
                                <i class="feather-dollar-sign"></i>
                                <span>Billing Details</span>
                            </a> -->
                            <!-- <a href="javascript:void(0);" class="dropdown-item">
                                <i class="feather-bell"></i>
                                <span>Notifications</span>
                            </a> -->
                            <!-- <a href="javascript:void(0);" class="dropdown-item">
                                <i class="feather-settings"></i>
                                <span>Account Settings</span>
                            </a> -->
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('admin.adminlogout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item" style="border:none; background:none; padding:0; cursor:pointer;">
                                    <i class="feather-log-out"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--! [End] Header Right !-->
        </div>
    </header>
    <!--! ================================================================ !-->
    <!--! [End] Header !-->

    <!-- Change Password Modal Start -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.change.password') }}" method="POST" id="changePasswordForm">
            @csrf
            <!-- @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif -->

          <div class="mb-3">
            <label for="currentPassword" class="form-label">Current Password</label>
            <input type="password" class="form-control @error('old_password') is-invalid @enderror" name="old_password" id="currentPassword">
            @error('old_password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" id="newPassword" >
            @error('new_password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input type="password" name="new_password_confirmation" class="form-control" id="confirmPassword" required>
          </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- <button class="btn btn-primary btn-lg">Submit</button> -->
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Change Password Modal End -->

<!-- Button to trigger the modal -->
<a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
    <i class="feather-user"></i>
    <span>Update Profile</span>
</a>

<!-- Update Profile Modal Start -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProfileForm" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}">
                        @error('phone_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Image</label>
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" accept=".jpg, .jpeg, .png" id="avatar" name="avatar" value="{{ old('avatar', auth()->user()->avatar) }}" autocomplete="avatar">
                        @error('avatar')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <img src="{{ auth()->user()->avatar }}" style="width:80px;margin-top: 10px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Update Profile Modal End-->
<script>
document.getElementById('changePasswordLink').addEventListener('click', function() {
    var myModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
    myModal.show();
});

document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('updateProfileForm');

    form.addEventListener('submit', function (event) {
        // Automatically close the modal after form submission
        form.addEventListener('submit', function () {
            var myModalEl = document.getElementById('updateProfileModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) {
                modal.hide();
            }
        });
    });
});
</script>

<script>
$(document).ready(function(){
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();

        var formData = {
            old_password: $('#currentPassword').val(),
            new_password: $('#newPassword').val(),
            new_password_confirmation: $('#confirmPassword').val(),
            _token: '{{ csrf_token() }}' // Include the CSRF token
        };

        // console.log(formData);

        $.ajax({
            url: "{{ route('admin.change.password') }}",  // Ensure the route is correctly defined in your routes file
            method: 'POST',
            data: formData,
            dataType: "JSON",
            
            success: function (response) {
                        if (response.status) {
                            toastr.success(response.message); // Show success toast
                            $('#changePasswordModal').modal('hide');  // Hide the modal

                        } else {
                            toastr.error(response.message); // Show error toast
                        }
                    },
            error: function(xhr) {
                // Handle error response
                var errors = xhr.responseJSON.errors;

                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');

                if (errors.old_password) {
                    $('#currentPassword').addClass('is-invalid');
                    $('#currentPassword').siblings('.text-danger').text(errors.old_password[0]);
                }
                if (errors.new_password) {
                    $('#newPassword').addClass('is-invalid');
                    $('#newPassword').siblings('.text-danger').text(errors.new_password[0]);
                }
            }
        });
    });
});


$(document).ready(function() {
    // Function to load notifications
    function loadNotifications() {
        $.ajax({
            url: "{{route('admin.fetchNotifications')}}", // Your route to fetch notifications
            type: 'GET',
            dataType: 'html',
            success: function(data) {
                $("#shownotificaton").empty();
                $("#shownotificaton").html(data);
            
             
            }
        });
    }

    // Load notifications when the dropdown is opened
    $('.nxl-head-link').on('click', function() {
        loadNotifications();
    });

    function fetchNotificationCount() {
        $.ajax({
            url: "{{route('admin.getNotificationCount')}}", // Your route to fetch notification count
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Update the badge count with the fetched data
            // console.log(data);
            if(data.status == true){
                $('#notificationcount').text(data.unreadCount);
                loadNotifications();
            }else{
                $('#notificationcount').text(data.unreadCount);
            }
            },
           
        });
    }

    // Call the function when the page loads to update notification count
    fetchNotificationCount();

    setInterval(fetchNotificationCount, 2000); // Check every 60 seconds

    
});
// Mark as read
    function markAsRead(id) {
        // alert('hi');
        $.ajax({
            url: "{{route('admin.markAsRead')}}",
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,  // Pass the dynamic notification ID here
                _token: '{{ csrf_token() }}'  // Include CSRF token
            },
            success: function(response) {
                // fetchNotificationCount();
                // loadNotifications(); // Reload notifications
               
            },
            error: function(xhr, status, error) {
                console.error("Error marking notification as read: " + error);
            }
        });
    }

    // Remove notification
    function removeNotification(id) {
        $.ajax({
            url: "{{ route('admin.deleteNotification') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,  // Pass the dynamic notification ID here
                _token: '{{ csrf_token() }}'  // Include CSRF token
            },
            success: function(response) {
                // loadNotifications(); // Reload notifications
            },
            error: function(xhr, status, error) {
                console.error("Error removing notification: " + error);
            }
        });
    }
</script>


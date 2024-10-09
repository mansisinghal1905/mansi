@extends('admin.auth.layouts.app')
@push('style')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

	<style>
		/ Custom Toastr Styles /
		#toast-container>div {
			background-color: brown;
			/ Dark background color /
			color: #fff;
			/ Light text color /
			box-shadow: none;
			/ Remove shadow /
			border: none;
		}

		#toast-container>div.toast-success {
			background-color: green;
			/ Success messages background color /
			background-color: green;
			transition: background-color 0.3s ease;
		}

		#toast-container>div.toast-error {
			background-color: #d9534f;
			/ Error messages background color /
		}

		#toast-container>div.toast-info {
			background-color: #5bc0de;
			/ Info messages background color /
		}

		#toast-container>div.toast-warning {
			background-color: #f0ad4e;
			/ Warning messages background color /
		}
	
        .toggle-password {
        float: right;
        cursor: pointer;
        margin-right: 10px;
        margin-top: -25px;
        }
    </style>
	<style>
		.toggle-password {
		float: right;
		cursor: pointer;
		margin-right: 10px;
		margin-top: -25px;
	}
	</style>
@endpush

@section('content')
	<main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                    <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                        <img src="{{asset('public/assets/images/logo-icon.png')}}" alt="" class="img-fluid">
                    </div>
                    <div class="card-body p-sm-5">
                        <h2 class="fs-20 fw-bolder mb-4">Resetting</h2>
                        <h4 class="fs-13 fw-bold mb-2">Reset to your password</h4>
                        <p class="fs-12 fw-medium text-muted">Enter your email and a reset link will sent to you, let's access our the best recommendation for you.</p>
                        <form action="{{ route('reset.password.post') }}" method="POST" class="w-100 mt-4 pt-2">
                          @csrf
							<div class="mb-4">
                                <input type="password" class="form-control" name="password" placeholder="New Password">
                                @if($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
								<input type="hidden" name="token"  value="{{$token}}"/>
                            </div>
                            <div class="mb-4">
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Conform Password" required>
                            </div>
                            <div class="mt-5">
                                <button type="submit" class="btn btn-lg btn-primary w-100">Save Change</button>
                            </div>
                        </form>
                        <div class="mt-5 text-muted">
                            <span> Don't have an account?</span>
                            <a href="auth-register-minimal.html" class="fw-bold">Create an Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle the eye slash icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
</script>
<script>
			@if (Session::has('message'))
				var type = "{{ Session::get('alert-type', 'info') }}"
				switch (type) {
					case 'info':
						toastr.options.timeOut = 10000;
						toastr.options =
						{
							"closeButton": true,
							"progressBar": true,
						}
						toastr.info("{{ Session::get('message') }}");
						var audio = new Audio('audio.mp3');
						audio.play();
						break;
					case 'success':

						toastr.options.timeOut = 10000;
						toastr.options =
						{
							"closeButton": true,
							"progressBar": true,
						}
						toastr.success("{{ Session::get('message') }}");
						var audio = new Audio('audio.mp3');
						audio.play();

						break;
					case 'warning':

						toastr.options.timeOut = 10000;
						toastr.options =
						{
							"closeButton": true,
							"progressBar": true,
						}
						toastr.warning("{{ Session::get('message') }}");
						var audio = new Audio('audio.mp3');
						audio.play();

						break;
					case 'error':

						toastr.options.timeOut = 10000;
						toastr.options =
						{
							"closeButton": true,
							"progressBar": true,
						}
						toastr.error("{{ Session::get('message') }}");
						var audio = new Audio('audio.mp3');
						audio.play();
						break;
				}
			@endif
		</script>
@endpush










@extends('admin.auth.layouts.app')

@section('content')
    <main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                    <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                        <img src="{{ asset('public/assets/images/logo-icon.png')}}" alt="" class="img-fluid">
                    </div>
                    <div class="card-body p-sm-5">
                        <h2 class="fs-20 fw-bolder mb-4">Reset</h2>
                        <h4 class="fs-13 fw-bold mb-2">Reset to your username/password</h4>
                        <p class="fs-12 fw-medium text-muted">Enter your email and a reset link will sent to you, let's access our the best recommendation for you.</p>
                        <form action="{{ route('admin.resetPassword') }}" method="post" class="w-100 mt-4 pt-2">
                            @csrf
                            @error('email')
                             <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="mb-4">
                                <input class="form-control" name="email" placeholder="Email or Username" required>
                            </div>
                            <div class="mt-5">
                                <button type="submit" class="btn btn-lg btn-primary w-100">Reset Now</button>
                            </div>
                        </form>
                        <div class="mt-5 text-muted">
                            <!-- <span> Don't have an account?</span> -->
                            <a href="{{ route('login') }}" class="fw-bold">Back To Login Page</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

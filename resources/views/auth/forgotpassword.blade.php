@extends('layouts.auth')
@section('body-class', 'overflow-hidden')

@section('title', 'Login')

@section('content')
<div id="main-wrapper">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 w-100">
        <div class="position-relative z-index-5">
            <div class="row">
                <!-- Left Side -->     
          <div class="col-lg-6 col-xl-7 col-xxl-6 position-relative overflow-hidden bg-dark d-none d-lg-block">
            <div class="circle-top"></div>
            <div>
              <img src="{{ asset('assets/images/logos/logo-icon.svg') }}" class="circle-bottom" alt="Logo-Dark" />
            </div>
            <div class="d-lg-flex align-items-center z-index-5 position-relative h-n80">
              <div class="row justify-content-center w-100">
                <div class="col-lg-7">
                  <h2 class="text-white fs-10 mb-3">Welcome to <br> GREENITCO ITM</h2>
                    <span class="opacity-75 fs-4 text-white d-block mb-3">
                        GreenITCo ITM: Smarter asset management, seamless service desk, maximum efficiency.
                    </span>
                  <a href="https://greenitco.com/products/best-it-asset-management-software" class="btn btn-primary" target="_blank">Learn More</a>
                </div>
              </div>
            </div>
          </div>

                <!-- Right Side -->
             <div class="col-lg-6 col-xl-5 col-xxl-6">
            <div class="min-vh-100 bg-body row justify-content-center justify-content-lg-start align-items-center p-5">
              <div class="col-sm-8 col-md-9 col-xxl-6 auth-card">
                <a href="../index.html" class="text-nowrap logo-img d-block w-100">
                  <img src="{{ asset('assets/images/logos/logo-icon.svg') }}" class="dark-logo" alt="Logo-Dark" />
                </a>
                <h2 class="mb-2 mt-4 fs-7 fw-bolder">Forgot Password</h2>
                <p class="mb-9">Please enter the email address associated with your account and We will email you a link
                  to reset your password.</p>

                <form>
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter Your Email Address" aria-describedby="emailHelp">
                  </div>
                  <a href="javascript:void(0)" class="btn btn-primary w-100 py-8 mb-3">Forgot Password</a>
                  <a href="{{route('login')}}" class="btn bg-primary-subtle text-primary w-100 py-8">Back to Login</a>
                </form>
              </div>
            </div>
          </div>

            </div>
        </div>
    </div>
</div>
@endsection



<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head><base href="../../../../">
		<meta charset="utf-8" />
		<title>Metronic | Login</title>
		<meta name="description" content="Login page example" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Custom Styles(used by this page)-->
		<link href="{{ asset('css/pages/login/classic/login-4.css')}}" rel="stylesheet" type="text/css" />
		<!--end::Page Custom Styles-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="{{ asset('plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles-->
		<!--begin::Layout Themes(used by all pages)-->
		<link href="{{ asset('css/themes/layout/header/base/light.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/themes/layout/header/menu/light.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/themes/layout/brand/dark.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/themes/layout/aside/dark.css')}}" rel="stylesheet" type="text/css" />
		<!--end::Layout Themes-->
		<link rel="shortcut icon" href="{{ asset('media/logos/favicon.ico')}}" />
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
				<div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('{{ asset('media/bg/bg-3.jpg')}}');">
					<div class="login-form text-center p-7 position-relative overflow-hidden">
						<!--begin::Login Header-->
						<div class="d-flex flex-center mb-15">
							<a href="#">
								<img src="{{ asset('/media/logos/logo-letter-13.png')}}" class="max-h-75px" alt="" />
							</a>
						</div>
						<!--end::Login Header-->
						<!--begin::Login Sign in form-->
						<div class="login-signin">
							<div class="mb-20">
								<h3>Sign In To Admin</h3>
								<div class="text-muted font-weight-bold">Enter your details to login to your account:</div>
							</div>
							<form class="form" id="kt_login_signin_form">
								<div class="form-group mb-5">
									<input class="form-control h-auto form-control-solid py-4 px-8" type="text" placeholder="Username" name="username" autocomplete="off" />
								</div>
								<div class="form-group mb-5">
									<input class="form-control h-auto form-control-solid py-4 px-8" type="password" placeholder="Password" name="password" />
								</div>
								<div class="form-group d-flex flex-wrap justify-content-between align-items-center">
									<label class="checkbox m-0 text-muted">
									<input type="checkbox" name="remember" />Remember me
									<span></span></label>
									{{-- <a href="javascript:;" id="kt_login_forgot" class="text-muted text-hover-primary">Forget Password ?</a> --}}
								</div>
								<button id="kt_login_signin_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Sign In</button>
							</form>
							<div class="mt-10">
								{{-- <span class="opacity-70 mr-4">Don't have an account yet?</span>
								<a href="javascript:;" id="kt_login_signup" class="text-muted text-hover-primary font-weight-bold">Sign Up!</a> --}}
							</div>
						</div>
						<!--end::Login Sign in form-->
						<!--begin::Login Sign up form-->
						<div class="login-signup">
							<div class="mb-20">
								<h3>Sign Up</h3>
								<div class="text-muted font-weight-bold">Enter your details to create your account</div>
							</div>
							<form class="form" id="kt_login_signup_form">
								<div class="form-group mb-5">
									<input class="form-control h-auto form-control-solid py-4 px-8" type="text" placeholder="Fullname" name="fullname" />
								</div>
								<div class="form-group mb-5">
									<input class="form-control h-auto form-control-solid py-4 px-8" type="text" placeholder="Email" name="email" autocomplete="off" />
								</div>
								<div class="form-group mb-5">
									<input class="form-control h-auto form-control-solid py-4 px-8" type="password" placeholder="Password" name="password" />
								</div>
								<div class="form-group mb-5">
									<input class="form-control h-auto form-control-solid py-4 px-8" type="password" placeholder="Confirm Password" name="cpassword" />
								</div>
								<div class="form-group mb-5 text-left">
									<label class="checkbox m-0">
									<input type="checkbox" name="agree" />I Agree the
									<a href="#" class="font-weight-bold">terms and conditions</a>.
									<span></span></label>
									<div class="form-text text-muted text-center"></div>
								</div>
								<div class="form-group d-flex flex-wrap flex-center mt-10">
									<button id="kt_login_signup_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Sign Up</button>
									<button id="kt_login_signup_cancel" class="btn btn-light-primary font-weight-bold px-9 py-4 my-3 mx-2">Cancel</button>
								</div>
							</form>
						</div>
						<!--end::Login Sign up form-->
						<!--begin::Login forgot password form-->
						<div class="login-forgot">
							<div class="mb-20">
								<h3>Forgotten Password ?</h3>
								<div class="text-muted font-weight-bold">Enter your email to reset your password</div>
							</div>
							<form class="form" id="kt_login_forgot_form">
								<div class="form-group mb-10">
									<input class="form-control form-control-solid h-auto py-4 px-8" type="text" placeholder="Email" name="email" autocomplete="off" />
								</div>
								<div class="form-group d-flex flex-wrap flex-center mt-10">
									<button id="kt_login_forgot_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Request</button>
									<button id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bold px-9 py-4 my-3 mx-2">Cancel</button>
								</div>
							</form>
						</div>
						<!--end::Login forgot password form-->
					</div>
				</div>
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->
        <script>var HOST_URL = "{{ route('quick-search') }}";</script>
		<!--begin::Global Config(global config for global JS scripts)-->
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<!--end::Global Config-->
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="{{ asset('plugins/global/plugins.bundle.js')}}"></script>
		<script src="{{ asset('plugins/custom/prismjs/prismjs.bundle.js')}}"></script>
		<script src="{{ asset('js/scripts.bundle.js')}}"></script>
		<!--end::Global Theme Bundle-->
		<!--begin::Page Scripts(used by this page)-->
        <script src="{{ asset('plugins/custom/jqueryform/jquery.form.js') }}" type="text/javascript"></script>
        {{-- <script src="{{ asset('js/pages/custom/login/login-general.js')}}"></script> --}}
        <script>
            "use strict";
            // Class Definition

            var myToast = function (type, title, msg = null) {
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            switch (type) {
                case 'success':
                    toastr.success(msg, title);
                    break;
                case 'info':
                    toastr.info(msg, title);
                    break;
                case 'warning':
                    toastr.warning(msg, title);
                    break;
                default:
                    toastr.error(msg, title);
                    break;
            }
        }

            var KTLogin = function () {
            var _login;

            var _showForm = function _showForm(form) {
                var cls = 'login-' + form + '-on';
                var form = 'kt_login_' + form + '_form';

                _login.removeClass('login-forgot-on');

                _login.removeClass('login-signin-on');

                _login.removeClass('login-signup-on');

                _login.addClass(cls);

                KTUtil.animateClass(KTUtil.getById(form), 'animate__animated animate__backInUp');
            };

            var _handleSignInForm = function _handleSignInForm() {
                var validation; // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/

                validation = FormValidation.formValidation(KTUtil.getById('kt_login_signin_form'), {
                fields: {
                    email: {
                    validators: {
                        notEmpty: {
                        message: 'Email is required'
                        }
                    }
                    },
                    password: {
                    validators: {
                        notEmpty: {
                        message: 'Password is required'
                        }
                    }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    //defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
                    bootstrap: new FormValidation.plugins.Bootstrap()
                }
                });
                $('#kt_login_signin_submit').on('click', function (e) {
                e.preventDefault();
                validation.validate().then(function (status) {
                    if (status == 'Valid') {
                        var btn = $('#kt_login_signin_submit');
                        var form = $('#kt_login_signin_form');

                        btn.addClass('spinner spinner-right spinner-light').attr('disabled', true);

                        var additionalData = {
                            _token: "{{ csrf_token() }}"
                        };

                        form.ajaxSubmit({
                            url : "{{url()->current()}}/login",
                            data: additionalData,
                            type: 'POST',
                            success: function(response, status, xhr, $form) {
                                btn.removeClass('spinner spinner-right spinner-light').attr('disabled', false);
                                if(response.status == 1){
                                    setTimeout(function() {
                                        form.trigger('reset');

                                        btn.attr('disabled', false);
                                        myToast('success', 'Berhasil Login');
                                        location.reload();
                                    }, 1000);
                                }else{
                                    btn.attr('disabled', false);
                                    myToast('error', response.message);
                                }
                                
                            },
                            error: function(err){
                                btn.removeClass('spinner spinner-right spinner-light').attr('disabled', false);
                                myToast('error', err.statusText);
                                btn.attr('disabled', false);
                            },
                        });
                    } else {
                    swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                        }
                    }).then(function () {
                        KTUtil.scrollTop();
                    });
                    }
                });
                }); // Handle forgot button

                $('#kt_login_forgot').on('click', function (e) {
                e.preventDefault();

                _showForm('forgot');
                }); // Handle signup

                $('#kt_login_signup').on('click', function (e) {
                e.preventDefault();

                _showForm('signup');
                });
            };

            var _handleSignUpForm = function _handleSignUpForm(e) {
                var validation;
                var form = KTUtil.getById('kt_login_signup_form'); // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/

                validation = FormValidation.formValidation(form, {
                fields: {
                    fullname: {
                    validators: {
                        notEmpty: {
                        message: 'Username is required'
                        }
                    }
                    },
                    email: {
                    validators: {
                        notEmpty: {
                        message: 'Email address is required'
                        },
                        emailAddress: {
                        message: 'The value is not a valid email address'
                        }
                    }
                    },
                    password: {
                    validators: {
                        notEmpty: {
                        message: 'The password is required'
                        }
                    }
                    },
                    cpassword: {
                    validators: {
                        notEmpty: {
                        message: 'The password confirmation is required'
                        },
                        identical: {
                        compare: function compare() {
                            return form.querySelector('[name="password"]').value;
                        },
                        message: 'The password and its confirm are not the same'
                        }
                    }
                    },
                    agree: {
                    validators: {
                        notEmpty: {
                        message: 'You must accept the terms and conditions'
                        }
                    }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap()
                }
                });
                $('#kt_login_signup_submit').on('click', function (e) {
                e.preventDefault();
                validation.validate().then(function (status) {
                    if (status == 'Valid') {
                    swal.fire({
                        text: "All is cool! Now you submit this form",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                        }
                    }).then(function () {
                        KTUtil.scrollTop();
                    });
                    } else {
                    swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                        }
                    }).then(function () {
                        KTUtil.scrollTop();
                    });
                    }
                });
                }); // Handle cancel button

                $('#kt_login_signup_cancel').on('click', function (e) {
                e.preventDefault();

                _showForm('signin');
                });
            };

            var _handleForgotForm = function _handleForgotForm(e) {
                var validation; // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/

                validation = FormValidation.formValidation(KTUtil.getById('kt_login_forgot_form'), {
                fields: {
                    email: {
                    validators: {
                        notEmpty: {
                        message: 'Email address is required'
                        },
                        emailAddress: {
                        message: 'The value is not a valid email address'
                        }
                    }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap()
                }
                }); // Handle submit button

                $('#kt_login_forgot_submit').on('click', function (e) {
                e.preventDefault();
                validation.validate().then(function (status) {
                    if (status == 'Valid') {
                    // Submit form
                    KTUtil.scrollTop();
                    } else {
                    swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                        }
                    }).then(function () {
                        KTUtil.scrollTop();
                    });
                    }
                });
                }); // Handle cancel button

                $('#kt_login_forgot_cancel').on('click', function (e) {
                e.preventDefault();

                _showForm('signin');
                });
            }; // Public Functions


            return {
                // public functions
                init: function init() {
                _login = $('#kt_login');

                _handleSignInForm();

                _handleSignUpForm();

                _handleForgotForm();
                }
            };
            }(); // Class Initialization


            jQuery(document).ready(function () {
            KTLogin.init();
            });
        </script>
		<!--end::Page Scripts-->
	</body>
	<!--end::Body-->
</html>
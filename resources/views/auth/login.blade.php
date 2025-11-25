<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Masuk | Absensi Kehadiran Siswa</title>
    <link href="/sb_admin/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/sb_admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        body {
            background-color: #f8f9fc;
        }

        .bg-login-side {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 2rem;
        }

        .bg-login-side img {
            width: 180px;
            margin-bottom: 1rem;
        }

        .login-title {
            font-weight: 700;
            font-size: 1.2rem;
            line-height: 1.5;
        }

        .brand-title {
            font-weight: 800;
            letter-spacing: .3px;
        }

        .login-subtitle {
            color: #6c757d;
        }

        .btn-primary {
            background-color: #4e73df;
            border: none;
            border-radius: 30px;
        }

        .btn-primary:hover {
            background-color: #3b5bcc;
        }

        .form-control-user {
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="row no-gutters">

                        <!-- Sisi Kiri -->
                        <div class="col-lg-6 bg-login-side d-none d-lg-flex">
                            <div>
                                <img src="/images/logo-sdn1karanganyar.png" alt="Logo Sekolah">
                                <h2 class="login-title mb-0">Absensi Kehadiran Siswa</h2>
                                <p class="mb-0">UPTD SDN 1 Karanganyar</p>
                            </div>
                        </div>

                        <!-- Sisi Kanan -->
                        <div class="col-lg-6 d-flex align-items-center">
                            <div class="p-5 w-100">
                                <div class="text-center mb-4">
                                    <h1 class="h4 text-gray-900 mb-1 brand-title">Masuk ke Akun</h1>
                                    <p class="login-subtitle mb-0">Silakan masuk untuk melanjutkan</p>
                                </div>

                                @if (session('status'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        {{ session('status') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Login gagal.</strong> Periksa kembali data Anda.
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <form class="user" method="POST" action="{{ route('login.attempt') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="email" class="small text-gray-600">Email</label>
                                        <input type="email" id="email" class="form-control form-control-user"
                                            name="email" value="{{ old('email') }}"
                                            placeholder="nama@sekolah.sch.id" required autofocus>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="password" class="small text-gray-600 mb-0">Password</label>
                                        </div>
                                        <input type="password" id="password" class="form-control form-control-user"
                                            name="password" placeholder="••••••••" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="remember"
                                                name="remember" value="1">
                                            <label class="custom-control-label" for="remember">Ingat saya</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <i class="fas fa-sign-in-alt mr-1"></i> Masuk
                                    </button>
                                </form>

                                <hr>
                                <div class="text-center small text-muted">
                                    &copy; {{ date('Y') }} Absensi Kehadiran Siswa
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="/sb_admin/vendor/jquery/jquery.min.js"></script>
    <script src="/sb_admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/sb_admin/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/sb_admin/js/sb-admin-2.min.js"></script>
</body>

</html>

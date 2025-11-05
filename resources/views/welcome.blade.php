<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Login & Register</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        :root {
            --green: #28a745;
            --green-dark: #218838;
            --dark: #1e1e2f;
            --light: #f8f9fa;
            --gray: #6c757d;
            --radius: 12px;
            --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .welcome-container {
            max-width: 1000px;
            width: 100%;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            background: white;
        }

        .welcome-header {
            background: var(--green);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 25px;
            text-align: center;
        }

        .welcome-header .material-symbols-outlined {
            font-size: 4rem;
            margin-bottom: 15px;
        }

        .welcome-header h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .welcome-header p { font-size: 1.1rem; opacity: 0.9; }

        .form-container { padding: 40px; }

        .tab-buttons, .subtab-buttons {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 25px;
        }

        .tab-btn, .sub-tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            border: none;
            background: none;
            font-weight: 600;
            color: var(--gray);
            transition: var(--transition);
            cursor: pointer;
            border-bottom: 3px solid transparent;
        }

        .tab-btn.active, .sub-tab.active {
            color: var(--green);
            border-bottom-color: var(--green);
        }

        .tab-btn:hover, .sub-tab:hover { color: var(--green-dark); }

        .role-buttons .btn {
            background: #fff;
            border: 1px solid #dee2e6;
            color: var(--dark);
            margin-right: 10px;
            transition: var(--transition);
        }

        .role-buttons .btn.active {
            background: var(--green);
            color: white;
            border-color: var(--green);
        }

        .role-buttons .btn:hover { background: #e9ecef; }

        .password-container { position: relative; }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray);
        }

        .btn-primary {
            background: var(--green);
            border: none;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: var(--green-dark);
            transform: translateY(-1px);
        }

        .tab-content, .subtab-content { display: none; }
        .tab-content.active, .subtab-content.active { display: block; }

        footer {
            text-align: center;
            color: var(--gray);
            font-size: 0.9rem;
            padding: 15px;
            background: #f1f3f5;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .welcome-container { box-shadow: none; border-radius: 0; }
            .form-container { padding: 25px; }
            .welcome-header { padding: 40px 20px; }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="row g-0">
            <!-- Left -->
            <div class="col-md-5 welcome-header">
                <span class="material-symbols-outlined">build_circle</span>
                <h1>Welcome</h1>
                <p>Login or Register to Continue</p>
            </div>

            <!-- Right -->
            <div class="col-md-7 form-container">
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="login">Login</button>
                    <button class="tab-btn" data-tab="register">Register</button>
                </div>

                <!-- ==================== LOGIN ==================== -->
                <div class="tab-content active" id="login-tab">
                    <div class="mb-3">
                        <h5 class="fw-bold mb-2">Select Your Role</h5>
                        <div class="d-flex flex-wrap role-buttons">
                            <button type="button" class="btn role-btn active"
                                    data-role="admin" data-action="{{ route('admin.login.submit') }}">
                                <span class="material-symbols-outlined me-1">admin_panel_settings</span> Admin
                            </button>
                            <button type="button" class="btn role-btn"
                                    data-role="staff" data-action="{{ route('staff.login.submit') }}">
                                <span class="material-symbols-outlined me-1">engineering</span> Staff
                            </button>
                            <button type="button" class="btn role-btn"
                                    data-role="owner" data-action="{{ route('owner.login.submit') }}">
                                <span class="material-symbols-outlined me-1">directions_car</span> Vehicle Owner
                            </button>
                        </div>
                    </div>

                    <form id="loginForm" method="POST" action="{{ route('admin.login.submit') }}">
                        @csrf
                        <input type="hidden" id="login-role" name="role" value="admin">

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" required placeholder="Enter your email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 password-container">
                            <label>Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" id="password" required placeholder="Enter your password">
                            <span class="password-toggle" id="passwordToggle">
                                <span class="material-symbols-outlined">visibility</span>
                            </span>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-primary w-100 mt-3">Login</button>
                    </form>
                </div>

                <!-- ==================== REGISTER ==================== -->
                <div class="tab-content" id="register-tab">
                    <div class="subtab-buttons mb-4">
                        <button class="sub-tab active" data-subtab="staff">Staff</button>
                        <button class="sub-tab" data-subtab="owner">Vehicle Owner</button>
                    </div>

                    <!-- Staff Registration -->
                    <div class="subtab-content active" id="staff-reg">
                        <h5 class="fw-bold mb-3">Register New Staff Account</h5>

                        <form id="registerStaffForm" method="POST" action="{{ route('staff.register.submit') }}">
                            @csrf

                            <div class="mb-3">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       required value="{{ old('name') }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       required value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Mobile Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       required value="{{ old('phone') }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                          rows="2" required>{{ old('address') }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Date of Birth</label>
                                    <input type="date" id="reg_date_of_birth_staff" name="date_of_birth"
                                           class="form-control @error('date_of_birth') is-invalid @enderror"
                                           required value="{{ old('date_of_birth') }}">
                                    @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Age</label>
                                    <input type="number" id="reg_age_staff" name="age" class="form-control" readonly
                                           value="{{ old('age') }}">
                                </div>
                            </div>

                            <div class="mb-3 password-container">
                                <label>Password</label>
                                <input type="password" name="password" id="reg_password_staff"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                <span class="password-toggle" id="regPasswordToggleStaff">
                                    <span class="material-symbols-outlined">visibility</span>
                                </span>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 password-container">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" id="reg_password_confirmation_staff"
                                       class="form-control" required>
                                <span class="password-toggle" id="regConfirmPasswordToggleStaff">
                                    <span class="material-symbols-outlined">visibility</span>
                                </span>
                            </div>

                            <button class="btn btn-primary w-100 mt-3">
                                <span class="material-symbols-outlined me-1">person_add</span> Register Staff
                            </button>
                        </form>
                    </div>

                    <!-- Owner Registration (NO DOB / AGE) -->
                    <div class="subtab-content" id="owner-reg">
                        <h5 class="fw-bold mb-3">Register New Vehicle Owner Account</h5>

                        <form id="registerOwnerForm" method="POST" action="{{ route('owner.register.submit') }}">
                            @csrf

                            <div class="mb-3">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       required value="{{ old('name') }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       required value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Mobile Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       required value="{{ old('phone') }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Vehicle Number <small class="text-muted">(e.g. ABC-1234)</small></label>
                                <input type="text" name="vehicle_number"
                                       class="form-control @error('vehicle_number') is-invalid @enderror"
                                       required placeholder="ABC-1234" value="{{ old('vehicle_number') }}">
                                @error('vehicle_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                          rows="2" required>{{ old('address') }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 password-container">
                                <label>Password</label>
                                <input type="password" name="password" id="reg_password_owner"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                <span class="password-toggle" id="regPasswordToggleOwner">
                                    <span class="material-symbols-outlined">visibility</span>
                                </span>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 password-container">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" id="reg_password_confirmation_owner"
                                       class="form-control" required>
                                <span class="password-toggle" id="regConfirmPasswordToggleOwner">
                                    <span class="material-symbols-outlined">visibility</span>
                                </span>
                            </div>

                            <button class="btn btn-primary w-100 mt-3">
                                <span class="material-symbols-outlined me-1">person_add</span> Register Owner
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            /* ---------- Main Tabs (Login / Register) ---------- */
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    btn.classList.add('active');
                    document.getElementById(`${btn.dataset.tab}-tab`).classList.add('active');
                });
            });

            /* ---------- Sub-tabs (Staff / Owner) ---------- */
            document.querySelectorAll('.sub-tab').forEach(st => {
                st.addEventListener('click', () => {
                    document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.subtab-content').forEach(c => c.classList.remove('active'));
                    st.classList.add('active');
                    document.getElementById(`${st.dataset.subtab}-reg`).classList.add('active');
                });
            });

            /* ---------- Role Buttons (Login) ---------- */
            const roleButtons = document.querySelectorAll('.role-btn');
            const loginForm = document.getElementById('loginForm');
            const loginRole = document.getElementById('login-role');

            roleButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    roleButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    loginForm.action = btn.dataset.action;
                    loginRole.value = btn.dataset.role;
                });
            });

            /* ---------- Password Toggles ---------- */
            const toggles = [
                { field: 'password', toggle: 'passwordToggle' },
                { field: 'reg_password_staff', toggle: 'regPasswordToggleStaff' },
                { field: 'reg_password_confirmation_staff', toggle: 'regConfirmPasswordToggleStaff' },
                { field: 'reg_password_owner', toggle: 'regPasswordToggleOwner' },
                { field: 'reg_password_confirmation_owner', toggle: 'regConfirmPasswordToggleOwner' }
            ];

            toggles.forEach(({ field, toggle }) => {
                const input = document.getElementById(field);
                const icon = document.getElementById(toggle);
                if (input && icon) {
                    icon.addEventListener('click', () => {
                        const type = input.type === 'password' ? 'text' : 'password';
                        input.type = type;
                        icon.innerHTML = `<span class="material-symbols-outlined">${type === 'password' ? 'visibility' : 'visibility_off'}</span>`;
                    });
                }
            });

            /* ---------- Age Calculation (Staff Only) ---------- */
            const calcAge = (dobId, ageId) => {
                const dob = document.getElementById(dobId);
                const age = document.getElementById(ageId);
                if (!dob || !age) return;
                dob.addEventListener('change', e => {
                    const birth = new Date(e.target.value);
                    const today = new Date();
                    let years = today.getFullYear() - birth.getFullYear();
                    const m = today.getMonth() - birth.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) years--;
                    age.value = years >= 0 ? years : '';
                });
            };
            calcAge('reg_date_of_birth_staff', 'reg_age_staff');

            /* ---------- Password Validation (Both Forms) ---------- */
            const validatePassword = (pwdId, confirmId) => {
                const pwd = document.getElementById(pwdId);
                const confirm = document.getElementById(confirmId);
                if (!pwd || !confirm) return;

                const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_#^])[A-Za-z\d@$!%*?&_#^]{8,}$/;
                let msgEl = document.getElementById(`${pwdId}_msg`);

                if (!msgEl) {
                    msgEl = document.createElement('small');
                    msgEl.id = `${pwdId}_msg`;
                    msgEl.style.display = 'block';
                    msgEl.style.marginTop = '4px';
                    pwd.parentElement.appendChild(msgEl);
                }

                const check = () => {
                    const p = pwd.value;
                    const c = confirm.value;
                    if (!regex.test(p)) {
                        msgEl.textContent = "8+ chars, uppercase, lowercase, digit, special char.";
                        msgEl.style.color = "red";
                    } else if (c && p !== c) {
                        msgEl.textContent = "Passwords do not match.";
                        msgEl.style.color = "red";
                    } else {
                        msgEl.textContent = "Password is strong!";
                        msgEl.style.color = "green";
                    }
                };

                pwd.addEventListener('input', check);
                confirm.addEventListener('input', check);
            };

            validatePassword('reg_password_staff', 'reg_password_confirmation_staff');
            validatePassword('reg_password_owner', 'reg_password_confirmation_owner');
        });
    </script>

    <!-- Toastr Messages -->
    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach

            // Auto-switch to Register tab on error
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelector('.tab-btn[data-tab="login"]').classList.remove('active');
                document.getElementById('login-tab').classList.remove('active');
                document.querySelector('.tab-btn[data-tab="register"]').classList.add('active');
                document.getElementById('register-tab').classList.add('active');
            });
        @endif
    </script>
</body>
</html>
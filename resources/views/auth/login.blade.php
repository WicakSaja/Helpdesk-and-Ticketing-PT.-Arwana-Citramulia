@extends('layouts.custom-auth')

@section('title', 'Login')

@section('content')

    <div class="login-main" id="loginMain">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo Arwana" style="width: 160px; margin-bottom: 12px;">

            <h3 style="color: #333; font-weight: 700; font-size: 20px;">HELPDESK SYSTEM</h3>
            <p style="color: #777; font-size: 14px;">PT. Arwana Citramulia Tbk</p>
        </div>

        <form onsubmit="handleLogin(event)">

            <div class="form-group">
                <label class="form-label">Email Perusahaan</label>
                <div class="input-wrapper">
                    <span class="icon-box"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" id="email" class="custom-input" placeholder="nama@arwanacitra.com" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <span class="icon-box"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" class="custom-input" placeholder="masukkan password..." required>
                    <span class="toggle-password" onclick="togglePassword('password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-arwana" id="btnLogin">
                MASUK SEKARANG <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>

        </form>

        <div class="auth-footer">
            <p>Belum punya akun? <a href="{{ route('register') }}" class="link-daftar">Daftar di sini</a></p>
        </div>
    </div>

    {{-- ROLE SELECTION MODAL --}}
    <div class="role-modal-overlay" id="roleSelectionModal">
        <div class="role-modal">
            <div class="role-modal-header">
                <h3><i class="fa-solid fa-users-gear"></i> Pilih Role</h3>
                <p>Akun Anda memiliki beberapa role. Silakan pilih role untuk login.</p>
            </div>
            <div class="role-modal-body" id="roleOptionsContainer">
                {{-- Role cards will be injected by JS --}}
            </div>
            <div class="role-modal-footer">
                <button type="button" class="btn-role-cancel" onclick="closeRoleModal()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
            </div>
        </div>
    </div>

    <style>
        .role-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.18s ease;
        }

        .role-modal-overlay.active {
            display: flex;
        }

        /* make modal slightly smaller & tighter */
        .role-modal {
            background: white;
            border-radius: 14px;
            width: 360px;
            max-width: 90vw;
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.16);
            overflow: hidden;
            animation: slideUp 0.22s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .role-modal-header {
            background: linear-gradient(135deg, #d62828, #ff6b6b);
            color: white;
            padding: 24px 24px 18px;
            text-align: center;
        }

        .role-modal-header h3 {
            margin: 0 0 6px;
            font-size: 18px;
            font-weight: 700;
        }

        .role-modal-header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .role-modal-body {
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .role-option-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border: 1.5px solid #f1f1f1;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.16s ease;
            background: #fff;
        }

        .role-option-card:hover {
            border-color: #d62828;
            background: #fff5f5;
            transform: translateX(2px);
        }

        .role-option-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .role-option-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .role-option-info strong {
            font-size: 14px;
            color: #333;
        }

        .role-option-info small {
            font-size: 11px;
            color: #999;
            margin-top: 2px;
        }

        .role-option-arrow {
            color: #ccc;
            font-size: 13px;
        }

        .role-option-card:hover .role-option-arrow {
            color: #d62828;
        }

        .role-modal-footer {
            padding: 10px 14px 14px;
            text-align: center;
        }

        .btn-role-cancel {
            background: none;
            border: 1px solid #ddd;
            padding: 8px 20px;
            border-radius: 8px;
            color: #666;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            transition: 0.16s;
            min-width: 120px;
        }

        .btn-role-cancel:hover {
            background: #f5f5f5;
            border-color: #bbb;
        }

        /* Blur login page content when role modal is open */
        body.role-modal-open .login-main {
            filter: blur(6px) saturate(0.9);
            opacity: 0.7;
            pointer-events: none;
            user-select: none;
            transition: filter 0.18s ease, opacity 0.18s ease;
        }
    </style>

    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/auth-form-handler.js') }}"></script>
    <script>
        // Redirect to dashboard if already logged in, or clear invalid auth data
        document.addEventListener('DOMContentLoaded', function() {
            requireGuestSync();
        });
    </script>
@endsection

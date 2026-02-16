@extends('layouts.custom-auth')

@section('title', 'Login')

@section('content')

    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo Arwana" style="width: 180px; margin-bottom: 15px;">

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
                <input type="password" id="password" class="custom-input" placeholder="Masukkan password..." required>
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
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s ease;
        }
        .role-modal-overlay.active { display: flex; }

        .role-modal {
            background: white;
            border-radius: 16px;
            width: 420px;
            max-width: 92vw;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

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
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .role-option-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border: 2px solid #eee;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .role-option-card:hover {
            border-color: #d62828;
            background: #fff5f5;
            transform: translateX(4px);
        }

        .role-option-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
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
        .role-option-card:hover .role-option-arrow { color: #d62828; }

        .role-modal-footer {
            padding: 12px 20px 18px;
            text-align: center;
        }
        .btn-role-cancel {
            background: none;
            border: 1px solid #ddd;
            padding: 8px 24px;
            border-radius: 8px;
            color: #666;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            transition: 0.2s;
        }
        .btn-role-cancel:hover {
            background: #f5f5f5;
            border-color: #bbb;
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

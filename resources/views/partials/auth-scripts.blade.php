{{-- Page Protection Scripts --}}
<script>
    const API_URL = "{{ env('API_BASE_URL', 'http://127.0.0.1:8000') }}";
</script>
<script src="{{ asset('js/auth-token-manager.js') }}"></script>
<script src="{{ asset('js/role-protection.js') }}"></script>
<script src="{{ asset('js/page-protection.js') }}"></script>

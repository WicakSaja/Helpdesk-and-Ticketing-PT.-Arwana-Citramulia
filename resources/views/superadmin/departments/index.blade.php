@extends('layouts.superadmin')
@section('title', 'Manajemen Departemen')

@section('content')
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h1 class="page-title" style="font-size: 24px; font-weight: 700; color: #333; margin: 0;">Departemen</h1>
        <button class="btn-add" onclick="openModal()"
            style="background: #1565c0; color: white; padding: 12px 25px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> Tambah Baru
        </button>
    </div>

    <div class="table-container"
        style="background: white; padding: 25px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
        <table class="dept-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 15px; border-bottom: 2px solid #f0f0f0; color: #888;">ID</th>
                    <th style="text-align: left; padding: 15px; border-bottom: 2px solid #f0f0f0; color: #888;">Nama
                        Departemen</th>
                    <th style="text-align: right; padding: 15px; border-bottom: 2px solid #f0f0f0; color: #888;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- LOOPING DATA ASLI (TIDAK ADA DUMMY LAGI) --}}
                @forelse($departments as $dept)
                    <tr id="row-{{ $dept['id'] }}">
                        <td style="padding: 15px; border-bottom: 1px solid #f9f9f9;">{{ $dept['id'] }}</td>
                        <td style="padding: 15px; border-bottom: 1px solid #f9f9f9;"><strong>{{ $dept['name'] }}</strong>
                        </td>
                        <td style="padding: 15px; border-bottom: 1px solid #f9f9f9; text-align: right;">
                            <button onclick="editDept({{ $dept['id'] }}, '{{ $dept['name'] }}')"
                                style="background: #fff3e0; color: #f57c00; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; margin-right: 5px;">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="deleteDept({{ $dept['id'] }}, '{{ $dept['name'] }}')"
                                style="background: #ffebee; color: #d62828; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    {{-- TAMPILAN JIKA DATA KOSONG --}}
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px; color: #999;">
                            <i class="fa-solid fa-folder-open" style="font-size: 30px; margin-bottom: 10px;"></i><br>
                            Belum ada data departemen.<br>
                            <small>Silakan tambah data baru.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="deptModal"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000;">
        <div
            style="background: white; width: 450px; padding: 30px; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
            <h3 id="modalTitle" style="margin-top: 0;">Tambah Departemen</h3>

            <form onsubmit="handleSave(event)">
                <input type="hidden" id="deptId">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 5px;">Nama
                        Departemen</label>
                    <input type="text" id="deptName" required placeholder="Contoh: IT"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; outline: none;">
                </div>
                <div style="text-align: right;">
                    <button type="button" onclick="closeModal()"
                        style="background: white; border: 1px solid #ddd; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 10px;">Batal</button>
                    <button type="submit" id="btnSave"
                        style="background: #1565c0; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: 600;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = "Tambah Departemen";
            document.getElementById('deptId').value = "";
            document.getElementById('deptName').value = "";
            document.getElementById('deptModal').style.display = 'flex';
        }

        function editDept(id, name) {
            document.getElementById('modalTitle').innerText = "Edit Departemen";
            document.getElementById('deptId').value = id;
            document.getElementById('deptName').value = name;
            document.getElementById('deptModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('deptModal').style.display = 'none';
        }

        async function handleSave(event) {
            event.preventDefault();
            let btn = document.getElementById('btnSave');
            let originalText = btn.innerText;
            btn.innerText = "Menyimpan...";
            btn.disabled = true;

            let id = document.getElementById('deptId').value;
            let name = document.getElementById('deptName').value;

            try {
                // Tembak Web Laravel (Proxy ke API)
                let response = await fetch("{{ route('superadmin.departments.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        id,
                        name
                    })
                });

                let result = await response.json();

                if (response.ok) {
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data tersimpan!',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    // Tampilkan pesan error dari API (misal: Permission denied)
                    throw new Error(result.message || 'Gagal menyimpan data.');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message
                });
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        function deleteDept(id, name) {
            Swal.fire({
                title: 'Hapus ' + name + '?',
                text: "Tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d62828',
                confirmButtonText: 'Ya, Hapus!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        let response = await fetch(`/superadmin/departments/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        });
                        if (response.ok) {
                            document.getElementById('row-' + id).remove();
                            Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                        } else {
                            let res = await response.json();
                            Swal.fire('Gagal!', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error!', 'Koneksi bermasalah.', 'error');
                    }
                }
            })
        }

        window.onclick = function(event) {
            if (event.target.id === 'deptModal') closeModal();
        }
    </script>
@endsection

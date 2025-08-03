<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Manajemen Surat Masuk</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <div x-data="suratMasukApp()" x-init="suratMasuk = @js($suratMasuk);
    init()" class="p-6">

        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Daftar Surat Masuk</h1>
                    <p class="text-sm text-gray-500">Kelola surat masuk dengan mudah</p>
                </div>
            </div>
            <button @click="downloadCSV()" class="bg-green-600 text-white px-4 py-2 rounded-lg">
                ⬇️ Download Laporan
            </button>
        </div>

        <div class="flex gap-4 mb-4">
            <input type="text" x-model="searchTerm" placeholder="Cari nomor surat, pengirim, atau isi ringkasan..."
                class="w-full p-2 border rounded-lg shadow-sm" />
            <input type="date" x-model="dateFilter" class="p-2 border rounded-lg shadow-sm" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="flex items-center bg-white p-4 rounded-lg shadow-sm gap-4">
                <div class="bg-blue-100 p-2 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Surat</p>
                    <p class="text-xl font-bold" x-text="filteredSurat.length"></p>
                </div>
            </div>
            <div class="flex items-center bg-white p-4 rounded-lg shadow-sm gap-4">
                <div class="bg-purple-100 p-2 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Bulan Ini</p>
                    <p class="text-xl font-bold"
                        x-text="filteredSurat.filter(s => new Date(s.tanggal).getMonth() === new Date().getMonth()).length">
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabel Surat -->
        <div class="overflow-x-auto bg-white p-4 rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Surat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengirim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Isi Ringkasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        <!-- Kolom Aksi -->
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="surat in filteredSurat" :key="surat.id">
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4" x-text="surat.nomor_surat"></td>
                            <td class="px-6 py-4" x-text="surat.pengirim"></td>
                            <td class="px-6 py-4" x-text="formatDate(surat.tanggal)"></td>
                            <td class="px-6 py-4" x-text="surat.isi_ringkasan"></td>
                            <td class="px-6 py-4">
                                <button @click="deleteSurat(surat.id)"
                                    class="text-red-600 hover:text-red-800 font-semibold">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="flex justify-end mt-6 pb-6 pr-4">
            <button @click="showAddForm = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Tambah Surat</span>
            </button>
        </div>

        <!-- Modal Tambah Surat -->
        <div x-show="showAddForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display: none;">
            <div class="bg-white p-6 rounded shadow-md w-full max-w-lg">
                <h2 class="text-lg font-bold mb-4">Tambah Surat Masuk</h2>
                <form @submit.prevent="addSurat()">
                    <input type="text" x-model="formData.nomor_surat" placeholder="Nomor Surat"
                        class="w-full mb-3 border p-2 rounded" required>
                    <input type="text" x-model="formData.pengirim" placeholder="Pengirim"
                        class="w-full mb-3 border p-2 rounded" required>
                    <input type="date" x-model="formData.tanggal" class="w-full mb-3 border p-2 rounded" required>
                    <textarea x-model="formData.isi_ringkasan" placeholder="Isi Ringkasan" class="w-full mb-3 border p-2 rounded"
                        required></textarea>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="closeModal()"
                            class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function suratMasukApp() {
            return {
                suratMasuk: [],
                searchTerm: '',
                dateFilter: '',
                showAddForm: false,
                formData: {
                    nomor_surat: '',
                    pengirim: '',
                    tanggal: '',
                    isi_ringkasan: ''
                },
                get filteredSurat() {
                    let filtered = this.suratMasuk;
                    if (this.searchTerm) {
                        const search = this.searchTerm.toLowerCase();
                        filtered = filtered.filter(surat =>
                            surat.nomor_surat.toLowerCase().includes(search) ||
                            surat.pengirim.toLowerCase().includes(search) ||
                            surat.isi_ringkasan.toLowerCase().includes(search)
                        );
                    }
                    if (this.dateFilter) {
                        filtered = filtered.filter(surat => surat.tanggal === this.dateFilter);
                    }
                    return filtered;
                },
                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                },
                closeModal() {
                    this.showAddForm = false;
                    this.formData = {
                        nomor_surat: '',
                        pengirim: '',
                        tanggal: '',
                        isi_ringkasan: ''
                    };
                },
                addSurat() {
                    fetch('/surat-masuk', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.suratMasuk.unshift(data.data);
                            this.closeModal();
                        })
                        .catch(err => alert('Gagal menambahkan surat.'));
                },
                deleteSurat(id) {
                    if (!confirm('Yakin ingin menghapus surat ini?')) return;
                    fetch(`/surat-masuk/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            }
                        })
                        .then(() => {
                            this.suratMasuk = this.suratMasuk.filter(s => s.id !== id);
                        })
                        .catch(err => alert('Gagal menghapus surat.'));
                },
                downloadCSV() {
                    if (this.filteredSurat.length === 0) {
                        alert('Tidak ada data untuk diunduh.');
                        return;
                    }

                    const headers = ['Nomor Surat', 'Pengirim', 'Tanggal', 'Isi Ringkasan'];
                    const rows = this.filteredSurat.map(surat => [
                        surat.nomor_surat,
                        surat.pengirim,
                        surat.tanggal,
                        surat.isi_ringkasan.replace(/\\n/g, ' ')
                    ]);

                    let csvContent = 'data:text/csv;charset=utf-8,';
                    csvContent += headers.join(',') + '\\n';
                    rows.forEach(rowArray => {
                        csvContent += rowArray.map(item => `"${item}"`).join(',') + '\\n';
                    });

                    const encodedUri = encodeURI(csvContent);
                    const link = document.createElement('a');
                    link.setAttribute('href', encodedUri);
                    link.setAttribute('download', 'laporan_surat_masuk.csv');
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                },
                downloadCSV() {
                    if (this.filteredSurat.length === 0) {
                        alert('Tidak ada data untuk diunduh.');
                        return;
                    }

                    const headers = ['Nomor Surat', 'Pengirim', 'Tanggal', 'Isi Ringkasan'];
                    const rows = this.filteredSurat.map(surat => [
                        surat.nomor_surat,
                        surat.pengirim,
                        surat.tanggal,
                        surat.isi_ringkasan
                        .replace(/(\r\n|\n|\r)/gm, ' ') // hapus newline
                        .replace(/"/g, '""') // escape kutip ganda
                    ]);

                    let csvContent = 'data:text/csv;charset=utf-8,';
                    csvContent += headers.map(h => `"${h}"`).join(',') + '\r\n'; // bungkus header dengan "
                    rows.forEach(rowArray => {
                        const row = rowArray.map(item => `"${item}"`).join(',');
                        csvContent += row + '\r\n';
                    });

                    const encodedUri = encodeURI(csvContent);
                    const link = document.createElement('a');
                    link.setAttribute('href', encodedUri);
                    link.setAttribute('download', 'laporan_surat_masuk.csv');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },

                init() {}
            }
        }
    </script>
</body>

</html>

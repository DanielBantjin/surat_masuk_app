<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;

class SuratMasukController extends Controller
{
    public function index()
    {
        $suratMasuk = SuratMasuk::all();
        return view('view_surat', compact('suratMasuk'));
    }

    public function create()
    {
        return view('input_surat');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nomor_surat' => 'required|string|max:255',
        'pengirim' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'isi_ringkasan' => 'required|string',
    ]);

    $surat = SuratMasuk::create($validated);

    return response()->json([
        'message' => 'Surat berhasil ditambahkan.',
        'data' => $surat,
    ]);
}


    public function show($id)
    {
        $surat = SuratMasuk::findOrFail($id);  // Ambil surat berdasarkan ID
        return response()->json($surat);  // Kirim data surat sebagai response JSON
    }
    public function destroy($id)
{
    $surat = SuratMasuk::findOrFail($id);
    $surat->delete();

    return response()->json(['message' => 'Surat berhasil dihapus']);
}

}

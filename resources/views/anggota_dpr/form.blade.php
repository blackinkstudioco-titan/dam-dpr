<div class="space-y-3">
    <div>
        <label class="block text-sm font-medium">Nama</label>
        <input type="text" name="nama" value="{{ old('nama', $anggota->nama ?? '') }}" class="w-full border-gray-300 rounded-md p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Partai</label>
        <input type="text" name="partai" value="{{ old('partai', $anggota->partai ?? '') }}" class="w-full border-gray-300 rounded-md p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Periode Terpilih</label>
        <input type="text" name="periode_terpilih" value="{{ old('periode_terpilih', $anggota->periode_terpilih ?? '') }}" class="w-full border-gray-300 rounded-md p-2">
    </div>

    <div>
        <label class="block text-sm font-medium">Fraksi</label>
        <select name="fraksi_id" class="w-full border-gray-300 rounded-md p-2">
            <option value="">-- Pilih Fraksi --</option>
            @foreach($fraksi as $f)
                <option value="{{ $f->id }}" {{ old('fraksi_id', $anggota->fraksi_id ?? '') == $f->id ? 'selected' : '' }}>{{ $f->nama_fraksi }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium">Komisi</label>
        <select name="komisi_dpr_id" class="w-full border-gray-300 rounded-md p-2">
            <option value="">-- Pilih Komisi --</option>
            @foreach($komisi as $k)
                <option value="{{ $k->id }}" {{ old('komisi_dpr_id', $anggota->komisi_dpr_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_komisi }}</option>
            @endforeach
        </select>
    </div>
    <?php
    /*
    <div>
        <label class="block text-sm font-medium">Foto</label>
        <input type="file" name="foto" class="w-full border-gray-300 rounded-md p-2">
        @if(!empty($anggota->foto))
            <img src="{{ asset('storage/'.$anggota->foto) }}" class="h-16 mt-2 rounded">
        @endif
    </div>
    */
    ?>
</div>

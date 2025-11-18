<div class="space-y-3">
    <div>
        <label class="block text-sm font-medium">Nama</label>
        <input type="text" name="nama" value="{{ old('nama', $anggota->nama ?? '') }}" class="w-full border-gray-300 rounded-md p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="w-full border-gray-300 rounded-md p-2">
            <option value="Laki-laki" {{ (old('jenis_kelamin', $anggota->jenis_kelamin ?? '') == 'L') ? 'selected' : '' }}>Laki-laki</option>
            <option value="Perempuan" {{ (old('jenis_kelamin', $anggota->jenis_kelamin ?? '') == 'P') ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Fraksi</label>
        <input type="text" name="fraksi" value="{{ old('fraksi', $anggota->fraksi ?? '') }}" class="w-full border-gray-300 rounded-md p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Dapil</label>
        <input type="text" name="dapil" value="{{ old('dapil', $anggota->dapil ?? '') }}" class="w-full border-gray-300 rounded-md p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Periode Terpilih</label>
        <input type="text" name="periode_terpilih" value="{{ old('periode_terpilih', $anggota->periode_terpilih ?? '') }}" class="w-full border-gray-300 rounded-md p-2">
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

// public/js/bulk-upload.js

// Define variables in global scope (window level)
let currentStep = 1;
let currentAlbum = null;
let uploadedFiles = [];
let formData = {};

// Also expose to window for debugging and cross-scope access
window.bulkUploadState = {
    currentStep: 1,
    currentAlbum: null,
    uploadedFiles: [],
    formData: {}
};

// SECURITY FIX (same class of bug as XSS-VULN-08): fileData.file_name is the
// original filename supplied by whoever picked the file in their browser —
// fully attacker-controlled — and gets interpolated into HTML strings below.
function escapeHtmlAttr(value) {
    return String(value ?? '').replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
}

// ===========================
// STEP NAVIGATION (GLOBAL)
// ===========================

function goToStep(step) {
    console.log('🔄 Navigating to step', step);
    
    // Hide all steps
    $('#step1-content, #step2-content, #step3-content').addClass('hidden');
    
    // Update progress indicators
    $('.flex div[id$="-circle"]').removeClass('bg-blue-600').addClass('bg-gray-300');
    $('.flex div[id$="-circle"]').parent().find('p').removeClass('text-gray-700 font-semibold').addClass('text-gray-400');
    $('.flex div[id$="-line"]').removeClass('bg-blue-600').addClass('bg-gray-300');
    
    // Show current step
    $(`#step${step}-content`).removeClass('hidden');
    
    // Update progress
    for(let i = 1; i <= step; i++) {
        $(`#step${i}-circle`).removeClass('bg-gray-300').addClass('bg-blue-600');
        $(`#step${i}-circle`).parent().find('p').removeClass('text-gray-400').addClass('text-gray-700 font-semibold');
        if(i < step) {
            $(`#step${i}-line`).removeClass('bg-gray-300').addClass('bg-blue-600');
        }
    }
    
    currentStep = step;
    window.bulkUploadState.currentStep = step;
}

$(document).ready(function() {
    console.log('🚀 Bulk upload script loaded');
    
    // ⚠️ CRITICAL: Prevent default drag & drop behavior on entire window
    // This prevents browser from opening dropped files as links
    window.addEventListener('dragover', function(e) {
        e.preventDefault();
    }, false);
    
    window.addEventListener('drop', function(e) {
        e.preventDefault();
    }, false);
    
    // Also prevent on document level
    $(document).on('dragover drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    
    // ===========================
    // STEP 1: CREATE ALBUM
    // ===========================
    
    // Prevent default form submission completely
    $('#albumForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('📋 Form submitted, preventing default behavior');
        
        const namaAlbum = $('#nama_album').val().trim();
        const deskripsi = $('#deskripsi').val().trim();
        //add data penugasan, kategori_foto_id and komisi
        const penugasan = $('#event_id').val().trim();
        const akd = $('#komisi_dpr_id').val().trim();
        const kategori_foto_id = $('#kategori_foto_id').val().trim();
        
        console.log('📝 Form data:', { namaAlbum, deskripsi,kategori_foto_id,akd });
        
        if(!namaAlbum) {
            showError('nama_album', 'Nama album wajib diisi');
            return false;
        }
        
        showLoading('Membuat album...', 'Mohon tunggu sebentar');
        
        console.log('🌐 Sending AJAX request...');
        
        $.ajax({
            url: '/foto/bulk-upload/album',
            method: 'POST',
            dataType: 'json',
            data: {
                nama_album: namaAlbum,
                deskripsi: deskripsi,
                event_id: penugasan,
                komisi_dpr_id: akd,
                kategori_foto_id: kategori_foto_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function(xhr) {
                console.log('📤 Request being sent:', {
                    url: '/foto/bulk-upload/album',
                    method: 'POST',
                    data: { 
                        nama_album: namaAlbum,
                        deskripsi: deskripsi,
                        event_id: penugasan,
                        komisi_dpr_id: akd, 
                        kategori_foto_id: kategori_foto_id,
                    }
                });
            },
            success: function(response) {
                hideLoading();
                console.log('✅ Success response:', response);
                
                if(response.success) {
                    //data album disimpan ke variable global
                    currentAlbum = response.data;
                    window.bulkUploadState.currentAlbum = response.data; // Sync to window
                    window.uploadedFilesCount = 0; // Track for beforeunload
                    //exit();
                    $('#album-name-display').text(currentAlbum.nama_album);
                    $('#album-name-display-step3').text(currentAlbum.nama_album);
                    
                    showNotification('success', 'Album berhasil dibuat!');
                    goToStep(2);
                } else {
                    showNotification('error', response.message || 'Gagal membuat album');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error('❌ AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                
                if(xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON?.errors || {};
                    Object.keys(errors).forEach(key => {
                        showError(key, errors[key][0]);
                    });
                    errorMessage = 'Validasi gagal. Periksa input Anda.';
                } else if(xhr.status === 500) {
                    // Server error
                    const response = xhr.responseJSON;
                    errorMessage = response?.message || 'Kesalahan server (500)';
                    
                    // Show detail error jika debug mode
                    if(response?.error_detail) {
                        console.error('Server Error Detail:', response.error_detail);
                        errorMessage += '\n\nCek console untuk detail.';
                    }
                } else if(xhr.status === 419) {
                    // CSRF token mismatch
                    errorMessage = 'Session expired. Refresh halaman dan coba lagi.';
                } else if(xhr.status === 405) {
                    // Method not allowed
                    errorMessage = 'Method not allowed. Ada masalah dengan request method.';
                    console.error('⚠️ Method Not Allowed - pastikan form tidak submit dengan GET');
                }
                
                showNotification('error', errorMessage);
            }
        });
        
        return false; // Extra prevention
    });
    
    // Alternative: Intercept button click
    $('#btnCreateAlbum').on('click', function(e) {
        console.log('🔘 Button clicked');
        // Let the form submit handler handle it
    });
    
    // ===========================
    // STEP 2: UPLOAD FILES
    // ===========================
    
    const dropzone = $('#dropzone');
    const fileInput = $('#fileInput');
    
    // Button click to open file dialog
    $('#btnSelectFiles').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.click();
    });
    
    // Dropzone click (hanya jika klik di area kosong)
    dropzone.on('click', function(e) {
        // Jangan trigger jika klik button
        if ($(e.target).closest('#btnSelectFiles').length > 0) {
            return;
        }
        
        // Jangan trigger jika klik di child elements
        if (e.target !== this && !$(e.target).is('svg, path, p')) {
            return;
        }
        
        e.preventDefault();
        e.stopPropagation();
        fileInput.click();
    });
    
    // File input change
    fileInput.on('change', function(e) {
        e.preventDefault();
        if (this.files && this.files.length > 0) {
            handleFiles(this.files);
        }
        // Reset input value agar bisa upload file yang sama
        this.value = '';
    });
    
    // Drag and drop events
    dropzone.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('border-blue-500 bg-blue-50');
    });
    
    dropzone.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('border-blue-500 bg-blue-50');
    });
    
    dropzone.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('border-blue-500 bg-blue-50');
        
        console.log('📁 Drop event triggered');
        console.log('Event:', e);
        console.log('Original event:', e.originalEvent);
        
        const files = e.originalEvent.dataTransfer.files;
        console.log('Files dropped:', files.length);
        
        if (files && files.length > 0) {
            handleFiles(files);
        } else {
            console.error('No files in drop event');
        }
        
        return false; // Extra prevention
    });
    
    // Handle files upload
    function handleFiles(files) {
        console.log('📂 handleFiles called with', files.length, 'files');
        console.log('currentAlbum:', currentAlbum);
        console.log('window.bulkUploadState.currentAlbum:', window.bulkUploadState.currentAlbum);
        
        // Fallback to window object if local variable undefined
        const album = currentAlbum || window.bulkUploadState.currentAlbum;
        
        if(!album) {
            console.error('❌ No album found!');
            showNotification('error', 'Silakan buat album terlebih dahulu');
            return;
        }
        
        if(files.length === 0) return;
        
        // Validate file types and sizes
        const validFiles = [];
        const maxSize = 15 * 1024 * 1024; // 15MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        for(let i = 0; i < files.length; i++) {
            const file = files[i];
            
            if(!allowedTypes.includes(file.type)) {
                showNotification('error', `File ${file.name} bukan format gambar yang valid`);
                continue;
            }
            
            if(file.size > maxSize) {
                showNotification('error', `File ${file.name} melebihi ukuran maksimal 15MB`);
                continue;
            }
            
            validFiles.push(file);
        }
        
        if(validFiles.length === 0) return;
        
        uploadFiles(validFiles);
    }
    
    // Upload files with progress
    function uploadFiles(files) {
        const total = files.length;
        let uploaded = 0;
        
        // Get album from either local or window scope
        const album = currentAlbum || window.bulkUploadState.currentAlbum;
        
        if (!album) {
            showNotification('error', 'Album tidak ditemukan');
            console.error('Album undefined in uploadFiles');
            return;
        }
        
        $('#uploadProgress').removeClass('hidden');
        $('#totalCount').text(total);
        $('#uploadedCount').text(uploaded);
        
        function uploadNext(index) {
            if(index >= files.length) {
                $('#uploadProgress').addClass('hidden');
                $('#btnToStep3').removeClass('hidden');
                window.uploadedFilesCount = uploadedFiles.length; // Update count
                showNotification('success', `${total} foto berhasil diupload!`);
                return;
            }
            
            const file = files[index];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('album_id', album.id);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            $.ajax({
                url: '/foto/bulk-upload/upload',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(e) {
                        if(e.lengthComputable) {
                            const percentComplete = Math.round((e.loaded / e.total) * 100);
                            const overallPercent = Math.round(((uploaded + (percentComplete / 100)) / total) * 100);
                            updateProgress(overallPercent);
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    if(response.success) {
                        uploadedFiles.push(response.data);
                        addFilePreview(response.data);
                        uploaded++;
                        $('#uploadedCount').text(uploaded);
                        updateProgress(Math.round((uploaded / total) * 100));
                        uploadNext(index + 1);
                    }
                },
                error: function(xhr) {
                    console.error('Upload error:', xhr.responseJSON);
                    showNotification('error', `Gagal upload ${file.name}`);
                    uploadNext(index + 1);
                }
            });
        }
        
        uploadNext(0);
    }
    
    // Update progress bar
    function updateProgress(percent) {
        $('#uploadBar').css('width', percent + '%');
        $('#uploadPercent').text(percent + '%');
    }
    
    // Add file preview
    function addFilePreview(fileData) {
        // SECURITY FIX: escape file_name at every interpolation point.
        const preview = `
            <div class="relative group" data-file-id="${escapeHtmlAttr(fileData.file_id)}">
                <img src="${escapeHtmlAttr(fileData.thumbnail_url)}"
                     alt="${escapeHtmlAttr(fileData.file_name)}"
                     class="w-full h-32 object-cover rounded-lg border-2 border-gray-200">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center">
                    <button type="button"
                            class="btn-remove-file opacity-0 group-hover:opacity-100 bg-red-600 text-white p-2 rounded-full hover:bg-red-700"
                            data-file-id="${escapeHtmlAttr(fileData.file_id)}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-600 mt-1 truncate">${escapeHtmlAttr(fileData.file_name)}</p>
                <p class="text-xs text-gray-400">${formatFileSize(fileData.file_size)}</p>
            </div>
        `;

        $('#uploadedFiles').append(preview);
    }
    
    // Remove file
    $(document).on('click', '.btn-remove-file', function() {
        const fileId = $(this).data('file-id');
        const fileData = uploadedFiles.find(f => f.file_id === fileId);
        
        if(confirm('Hapus foto ini?')) {
            // SECURITY FIX: the server now resolves which files to delete
            // from its own upload receipt (keyed by file_id) instead of
            // trusting a client-supplied paths[] array, so only file_id is
            // sent here now — see BulkUploadController::deleteFile().
            $.ajax({
                url: '/foto/bulk-upload/delete-file',
                method: 'POST',
                data: {
                    file_id: fileId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    uploadedFiles = uploadedFiles.filter(f => f.file_id !== fileId);
                    $(`[data-file-id="${fileId}"]`).remove();
                    
                    if(uploadedFiles.length === 0) {
                        $('#btnToStep3').addClass('hidden');
                    }
                }
            });
        }
    });
    
    // ===========================
    // STEP 3: METADATA
    // ===========================
    
    $('#btnToStep3').on('click', function() {
        if(uploadedFiles.length === 0) {
            showNotification('error', 'Belum ada foto yang diupload');
            return;
        }
        
        loadFormData();
        goToStep(3);
        renderMetadataForms();
    });
    
    // Load form data (kategori, komisi, anggota)
    function loadFormData() {
        $.ajax({
            url: '/foto/bulk-upload/form-data',
            method: 'GET',
            success: function(response) {
                if(response.success) {
                    formData = response.data;
                }
            }
        });
    }
    
    // Render metadata forms
    function renderMetadataForms() {
        const container = $('#metadataContainer');
        container.empty();
        
        $('#totalPhotos').text(uploadedFiles.length);
        
        uploadedFiles.forEach((file, index) => {
            const form = createMetadataForm(file, index);
            container.append(form);
        });
    }
    
    // Create metadata form for each photo
    function createMetadataForm(fileData, index) {
        // SECURITY FIX: escape file_name at every interpolation point.
        return `
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50" data-form-index="${index}">
                <div class="flex gap-4 mb-4">
                    <img src="${escapeHtmlAttr(fileData.thumbnail_url)}"
                         alt="${escapeHtmlAttr(fileData.file_name)}"
                         class="w-24 h-24 object-cover rounded-lg border-2 border-gray-300">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800 mb-1">Foto ${index + 1}</h3>
                        <p class="text-sm text-gray-600">${escapeHtmlAttr(fileData.file_name)}</p>
                        <p class="text-xs text-gray-500">${formatFileSize(fileData.file_size)}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Judul Foto <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="judul_${index}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                               value="${escapeHtmlAttr(fileData.file_name)}"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Foto</label>
                        <input type="text" 
                               name="f_lok_${index}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                               placeholder="Contoh: Ruang Paripurna">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskrp_${index}" 
                                  rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                  placeholder="Deskripsi singkat foto..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kata Kunci</label>
                        <input type="text" 
                               name="k_word_${index}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                               placeholder="pisahkan dengan koma">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fotografer</label>
                        <input type="text" 
                               name="perekam_${index}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                               placeholder="Nama fotografer">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Subyek</label>
                        <input type="text" 
                               name="subyek_${index}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox"  
                                   name="publish_${index}" 
                                   value="1"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm font-semibold text-gray-700">Publish</span>
                        </label>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Apply to all photos
    $('#btnApplyToAll').on('click', function() {
        if(uploadedFiles.length === 0) return;
        
        const firstForm = $('[data-form-index="0"]');
        const values = {};
        
        firstForm.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if(name) {
                const baseName = name.replace('_0', '');
                if($(this).attr('type') === 'checkbox') {
                    values[baseName] = $(this).is(':checked');
                } else {
                    values[baseName] = $(this).val();
                }
            }
        });
        
        // Apply to all other forms except judul (biarkan unique)
        for(let i = 1; i < uploadedFiles.length; i++) {
            const form = $(`[data-form-index="${i}"]`);
            
            Object.keys(values).forEach(key => {
                if(key !== 'judul') { // Jangan copy judul
                    const element = form.find(`[name="${key}_${i}"]`);
                    if(element.attr('type') === 'checkbox') {
                        element.prop('checked', values[key]);
                    } else {
                        element.val(values[key]);
                    }
                }
            });
        }
        
        showNotification('success', 'Data berhasil diterapkan ke semua foto (kecuali judul)');
    });
    
    // Save all metadata
    $('#btnSaveAll').on('click', function(e) {
        e.preventDefault(); // ✅ CRITICAL: Prevent default action
        e.stopPropagation(); // ✅ Stop event bubbling
        
        console.log('💾 Save All clicked');
        const photos = [];
        let isValid = true;
        
        uploadedFiles.forEach((file, index) => {
            const form = $(`[data-form-index="${index}"]`);
            const judul = form.find(`[name="judul_${index}"]`).val().trim();
            
            if(!judul) {
                showNotification('error', `Judul foto ${index + 1} wajib diisi`);
                isValid = false;
                return;
            }
            
            photos.push({
                file_id: file.file_id,
                file_name: file.file_name,
                file_size: file.file_size,
                original_path: file.original_path,
                thumbnail_path: file.thumbnail_path,
                meta_data: file.meta_data,
                album_id: currentAlbum.id,
                //data komisi dan penugasan 
                event_id: currentAlbum.event_id,
                komisi_dpr_id: currentAlbum.komisi_dpr_id,
                //end data komisi dan penugasan
                judul: judul,
                deskrp: form.find(`[name="deskrp_${index}"]`).val(),
                k_word: form.find(`[name="k_word_${index}"]`).val(),
                f_lok: form.find(`[name="f_lok_${index}"]`).val(),
                perekam: form.find(`[name="perekam_${index}"]`).val(),
                subyek: form.find(`[name="subyek_${index}"]`).val(),
                k_name: form.find(`[name="k_name_${index}"]`).val(),
                konseptor: form.find(`[name="konseptor_${index}"]`).val(),
                l_access: form.find(`[name="l_access_${index}"]`).val(),
                kategorisasi_datatempo: currentAlbum.kategori_foto_id,
                //komisi_dpr_id: form.find(`[name="komisi_dpr_id_${index}"]`).val() || null,
                //anggota_dpr_id: form.find(`[name="anggota_dpr_id_${index}"]`).val() || null,
                publish: form.find(`[name="publish_${index}"]`).is(':checked') ? 1 : 0, // Ubah dari boolean ke integer
            });
        });
        
        if(!isValid) return;
        
        showLoading('Menyimpan foto...', `Sedang menyimpan ${photos.length} foto`);
        
        $.ajax({
            url: '/foto/bulk-upload/save-metadata',
            method: 'POST',
            data: {
                photos: photos,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideLoading();
                
                if(response.success) {
                    showNotification('success', response.message);
                    //console.log(response.data);
                    setTimeout(() => {
                        window.location.href = '/data-foto';
                    }, 1000);
                    
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function(xhr) {
                hideLoading();
                console.error('Save error:', xhr.responseJSON);
                showNotification('error', 'Gagal menyimpan foto. Silakan coba lagi.');
            }
        });
    });
    
    // ===========================
    // NAVIGATION BUTTONS
    // ===========================
    
    $('#btnBackToStep1').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if(confirm('Kembali ke step 1? Foto yang sudah diupload akan tetap tersimpan.')) {
            goToStep(1);
        }
    });
    
    $('#btnBackToStep2').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        goToStep(2);
    });
    
    // ===========================
    // HELPER FUNCTIONS
    // ===========================
    
    function showLoading(text, subtext) {
        $('#loadingText').text(text);
        $('#loadingSubtext').text(subtext);
        $('#loadingModal').removeClass('hidden');
    }
    
    function hideLoading() {
        $('#loadingModal').addClass('hidden');
    }
    
    function showNotification(type, message) {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? '✓' : '✕';
        
        const notification = $(`
            <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2">
                <span class="text-xl font-bold">${icon}</span>
                <span>${message}</span>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    function showError(field, message) {
        $(`#error-${field}`).text(message).removeClass('hidden');
        $(`#${field}`).addClass('border-red-500');
        
        setTimeout(() => {
            $(`#error-${field}`).addClass('hidden');
            $(`#${field}`).removeClass('border-red-500');
        }, 3000);
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
});
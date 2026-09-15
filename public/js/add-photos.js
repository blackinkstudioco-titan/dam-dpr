let uploadedFiles = [];
const currentAlbum = window.albumData;

document.addEventListener('DOMContentLoaded', function() {
    initializeDropzone();
    initializeSaveButton();
});

function initializeDropzone() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    dropzone.addEventListener('dragover', () => {
        dropzone.classList.add('border-blue-500', 'bg-blue-50');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-blue-500', 'bg-blue-50');
    });

    dropzone.addEventListener('drop', (e) => {
        dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        handleFiles(e.dataTransfer.files);
    });

    dropzone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
}

function handleFiles(files) {
    Array.from(files).forEach(file => {
        if (validateFile(file)) {
            uploadFile(file);
        }
    });
}

function validateFile(file) {
    const maxSize = 15 * 1024 * 1024; // 15MB
    if (file.size > maxSize) {
        alert(`File ${file.name} terlalu besar. Maksimal 15MB`);
        return false;
    }
    if (!file.type.match('image.*')) {
        alert(`File ${file.name} bukan gambar`);
        return false;
    }
    return true;
}

async function uploadFile(file) {
    showLoading('Mengupload foto...');

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    try {
        const response = await fetch(`/foto/add-photos/upload/${currentAlbum.id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        // Log response status for debugging
        console.log('Response status:', response.status);

        // Get response text first
        const responseText = await response.text();
        console.log('Raw response:', responseText);

        // Try to parse JSON
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            throw new Error('Server returned invalid response');
        }

        if (result.success) {
            uploadedFiles.push(result.data);
            updatePreviewArea();
            hideLoading();
        } else {
            throw new Error(result.message || 'Upload failed');
        }
    } catch (error) {
        hideLoading();
        console.error('Upload failed:', error);
        alert('Gagal upload file: ' + error.message);
    }
}

// SECURITY FIX (XSS-VULN-08): file_name is the ORIGINAL filename supplied by
// whoever picked the file in their browser — fully attacker-controlled — and
// used to be interpolated straight into innerHTML/onclick strings below.
// escapeHtmlAttr() neutralizes it wherever it has to end up inside markup.
function escapeHtmlAttr(value) {
    return String(value ?? '').replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
}

function updatePreviewArea() {
    const previewArea = document.getElementById('previewArea');
    if (!previewArea) return;

    previewArea.innerHTML = '';

    uploadedFiles.forEach(file => {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative group';

        const img = document.createElement('img');
        img.src = file.thumbnail_url;
        img.alt = file.file_name;
        img.className = 'w-full h-48 object-cover rounded-lg';
        wrapper.appendChild(img);

        const overlay = document.createElement('div');
        overlay.className = 'absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all';

        const button = document.createElement('button');
        button.className = 'absolute top-2 right-2 p-2 bg-red-500 rounded-full opacity-0 group-hover:opacity-100';
        button.innerHTML = `
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        `;
        button.addEventListener('click', () => removeFile(file.file_id));
        overlay.appendChild(button);
        wrapper.appendChild(overlay);

        previewArea.appendChild(wrapper);
    });

    updateMetadataForms();
}

function updateMetadataForms() {
    const container = document.getElementById('metadataContainer');
    if (!container) return;

    // SECURITY FIX (XSS-VULN-08): file.file_name is escaped at every
    // interpolation point below — see escapeHtmlAttr() above.
    container.innerHTML = uploadedFiles.map((file, index) => `
        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 mb-4" data-form-index="${index}">
            <div class="flex gap-4 mb-4">
                <img src="${escapeHtmlAttr(file.thumbnail_url)}"
                     alt="${escapeHtmlAttr(file.file_name)}"
                     class="w-24 h-24 object-cover rounded-lg">
                <div>
                    <h3 class="font-medium">Foto ${index + 1}</h3>
                    <p class="text-sm text-gray-500">${escapeHtmlAttr(file.file_name)}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Foto <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="judul_${index}"
                           value="${escapeHtmlAttr(file.file_name)}"
                           class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskrp_${index}" 
                            rows="2"
                            class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                    <input type="text" 
                           name="k_word_${index}"
                           class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <input type="text" 
                           name="f_lok_${index}"
                           class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

    
                <div class="flex items-center space-x-2">
                    <input type="checkbox" 
                           name="publish_${index}"
                           id="publish_${index}"
                           value="1"
                           checked
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="publish_${index}" class="text-sm font-medium text-gray-700">
                        Publish
                    </label>
                </div>
            </div>
        </div>
    `).join('');
}

function initializeSaveButton() {
    const saveButton = document.getElementById('btnSaveAll');
    if (!saveButton) return;

    saveButton.addEventListener('click', savePhotos);
}

async function savePhotos() {
    if (uploadedFiles.length === 0) {
        alert('Belum ada foto yang diupload');
        return;
    }

    showLoading('Menyimpan foto...');

    try {
        const photos = uploadedFiles.map((file, index) => {
            const form = document.querySelector(`[data-form-index="${index}"]`);
            return {
                file_id: file.file_id,
                original_path: file.original_path,
                thumbnail_path: file.thumbnail_path,
                file_size: file.file_size,
                meta_data: file.meta_data,  // Add this line to include EXIF data
                judul: form.querySelector(`[name="judul_${index}"]`).value,
                deskrp: form.querySelector(`[name="deskrp_${index}"]`).value,
                k_word: form.querySelector(`[name="k_word_${index}"]`).value,
                f_lok: form.querySelector(`[name="f_lok_${index}"]`).value,
                publish: form.querySelector(`[name="publish_${index}"]`).checked ? 1 : 0
            };
        });

        // Debug log
        console.log('Saving photos with data:', photos);

        const response = await fetch(`/foto/add-photos/store/${currentAlbum.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ photos })
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = result.redirect;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        hideLoading();
        console.error('Save failed:', error);
        alert('Gagal menyimpan foto: ' + error.message);
    }
}

function showLoading(message) {
    const modal = document.getElementById('loadingModal');
    const text = document.getElementById('loadingText');
    if (modal && text) {
        text.textContent = message;
        modal.classList.remove('hidden');
    }
}

function hideLoading() {
    const modal = document.getElementById('loadingModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function removeFile(fileId) {
    uploadedFiles = uploadedFiles.filter(file => file.file_id !== fileId);
    updatePreviewArea();
}
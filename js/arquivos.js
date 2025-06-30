document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('fileInput');
    const uploadForm = document.getElementById('uploadForm');
    const uploadProgress = document.getElementById('uploadProgress');
    const uploadBar = uploadProgress.querySelector('div');
    const uploadStatus = document.getElementById('uploadStatus');
    const fileGrid = document.getElementById('fileGrid');
    const search = document.getElementById('search');
    const dateFilter = document.getElementById('dateFilter');
    const sortBy = document.getElementById('sortBy');
    const fileModal = document.getElementById('fileModal');
    const modalContent = document.getElementById('modalContent');
    const closeModal = document.getElementById('closeModal');
    const newFolderBtn = document.getElementById('newFolderBtn');

    // Função para buscar arquivos
    function fetchFiles() {
        const params = new URLSearchParams({
            search: search.value,
            date: dateFilter.value,
            sort: sortBy.value
        });
        fetch('listar_arquivos.php?' + params.toString())
            .then(res => res.json())
            .then(renderFiles);
    }

    // Renderiza os arquivos na grid
    function renderFiles(files) {
        fileGrid.innerHTML = '';
        if (!files.length) {
            fileGrid.innerHTML = '<div class="col-span-full text-center text-gray-400">Nenhum arquivo encontrado.</div>';
            return;
        }
        files.forEach(file => {
            const div = document.createElement('div');
            div.className = 'file-thumb bg-white rounded shadow p-2 flex flex-col items-center cursor-pointer hover:ring-2 hover:ring-purple-400 transition';
            div.innerHTML = `<img src="${file.thumb}" alt="${file.name}" class="rounded object-cover" />`;
            div.onclick = () => showFileModal(file);
            fileGrid.appendChild(div);
        });
    }

    // Upload de arquivos
    uploadForm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!fileInput.files.length) return;
        const formData = new FormData();
        for (const file of fileInput.files) {
            formData.append('files[]', file);
        }
        uploadProgress.classList.remove('hidden');
        uploadBar.style.width = '0%';
        uploadStatus.textContent = '';
        fetch('upload_arquivo.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(data => {
                uploadBar.style.width = '100%';
                uploadStatus.textContent = 'Upload concluído!';
                setTimeout(() => {
                    uploadProgress.classList.add('hidden');
                    uploadBar.style.width = '0%';
                    uploadStatus.textContent = '';
                }, 1200);
                fetchFiles();
            });
    });

    // Drag and drop
    fileInput.addEventListener('change', () => uploadForm.dispatchEvent(new Event('submit')));
    fileGrid.addEventListener('dragover', e => { e.preventDefault(); fileGrid.classList.add('ring-2', 'ring-purple-400'); });
    fileGrid.addEventListener('dragleave', e => { fileGrid.classList.remove('ring-2', 'ring-purple-400'); });
    fileGrid.addEventListener('drop', e => {
        e.preventDefault();
        fileGrid.classList.remove('ring-2', 'ring-purple-400');
        const dt = e.dataTransfer;
        if (dt && dt.files.length) {
            fileInput.files = dt.files;
            uploadForm.dispatchEvent(new Event('submit'));
        }
    });

    // Filtros e busca
    search.addEventListener('input', fetchFiles);
    dateFilter.addEventListener('change', fetchFiles);
    sortBy.addEventListener('change', fetchFiles);

    // Modal de detalhes
    function showFileModal(file) {
        fileModal.style.display = 'flex';
        fileModal.classList.remove('hidden');
        modalContent.innerHTML = `
            <img src="${file.url}" alt="${file.name}" />
            <div class="mb-2"><b>URL:</b> <a href="${file.url}" target="_blank" class="text-purple-700 underline">${file.url}</a></div>
            <div class="mb-2"><b>Tamanho:</b> ${file.size}</div>
            <div class="mb-2"><b>Data de envio:</b> ${file.date}</div>
            <div class="popup-actions">
                <button class="bg-red-600 text-white px-3 py-1 rounded" onclick="window.deleteFile('${file.path}')"><i class="fas fa-trash"></i> Excluir Imagem</button>
            </div>
        `;
    }
    closeModal.onclick = () => { fileModal.classList.add('hidden'); fileModal.style.display = 'none'; };
    fileModal.onclick = e => { if (e.target === fileModal) { fileModal.classList.add('hidden'); fileModal.style.display = 'none'; } };

    // Nova pasta
    newFolderBtn.onclick = () => {
        const folder = prompt('Nome da nova pasta:');
        if (folder) {
            fetch('criar_pasta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'folder=' + encodeURIComponent(folder)
            }).then(() => fetchFiles());
        }
    };

    // Inicialização
    fetchFiles();

    // Excluir e renomear (window para acesso inline)
    window.deleteFile = function (path) {
        if (confirm('Tem certeza que deseja excluir este arquivo?')) {
            fetch('excluir_arquivo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'path=' + encodeURIComponent(path)
            }).then(() => {
                fileModal.classList.add('hidden');
                fetchFiles();
            });
        }
    };
    window.renameFile = function (path) {
        const newName = prompt('Novo nome do arquivo:');
        if (newName) {
            fetch('renomear_arquivo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'path=' + encodeURIComponent(path) + '&newName=' + encodeURIComponent(newName)
            }).then(() => {
                fileModal.classList.add('hidden');
                fetchFiles();
            });
        }
    };
}); 
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3 mt-5">
        <div class="container">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> Evangelical Restoration Church Goma - Administration
                <span class="mx-2">|</span>
                <a href="../index.php" target="_blank" class="text-decoration-none">Voir le site</a>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Admin JS -->
    <script src="js/admin.js"></script>

    <script>
        // Initialisation des DataTables
        $(document).ready(function() {
            $('.data-table').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']]
            });
        });

        // Fonction pour afficher les alertes
        function showAlert(type, message, duration = 5000) {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert-' + Date.now();
            
            const alertDiv = document.createElement('div');
            alertDiv.id = alertId;
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            alertContainer.appendChild(alertDiv);
            
            // Auto-remove after duration
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    alert.remove();
                }
            }, duration);
        }

        // Confirmation de suppression
        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }

        // Fonction pour prévisualiser les images
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Fonction pour copier du texte
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showAlert('success', 'Texte copié dans le presse-papiers !');
            }).catch(() => {
                showAlert('error', 'Impossible de copier le texte.');
            });
        }

        // Auto-save pour les formulaires
        function enableAutoSave(formId, interval = 30000) {
            const form = document.getElementById(formId);
            if (!form) return;

            setInterval(() => {
                const formData = new FormData(form);
                formData.append('auto_save', '1');
                
                fetch(form.action || window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Auto-save successful');
                    }
                })
                .catch(error => {
                    console.error('Auto-save failed:', error);
                });
            }, interval);
        }

        // Gestion des uploads de fichiers
        function handleFileUpload(inputId, progressId, callback) {
            const input = document.getElementById(inputId);
            const progress = document.getElementById(progressId);
            
            if (!input || !input.files[0]) return;
            
            const formData = new FormData();
            formData.append('file', input.files[0]);
            
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    if (progress) {
                        progress.style.width = percentComplete + '%';
                        progress.textContent = Math.round(percentComplete) + '%';
                    }
                }
            });
            
            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (callback) callback(response);
                } else {
                    showAlert('error', 'Erreur lors de l\'upload du fichier.');
                }
            });
            
            xhr.addEventListener('error', () => {
                showAlert('error', 'Erreur de connexion lors de l\'upload.');
            });
            
            xhr.open('POST', 'api/upload.php');
            xhr.send(formData);
        }

        // Fonction pour formater les dates
        function formatDate(dateString, format = 'dd/mm/yyyy') {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            switch(format) {
                case 'dd/mm/yyyy':
                    return `${day}/${month}/${year}`;
                case 'dd/mm/yyyy hh:mm':
                    return `${day}/${month}/${year} ${hours}:${minutes}`;
                case 'yyyy-mm-dd':
                    return `${year}-${month}-${day}`;
                default:
                    return date.toLocaleDateString('fr-FR');
            }
        }

        // Validation des formulaires
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        }

        // Recherche en temps réel
        function enableLiveSearch(inputId, targetSelector) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const targets = document.querySelectorAll(targetSelector);
                
                targets.forEach(target => {
                    const text = target.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        target.style.display = '';
                    } else {
                        target.style.display = 'none';
                    }
                });
            });
        }

        // Initialisation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialisation des popovers Bootstrap
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    </script>
</body>
</html>

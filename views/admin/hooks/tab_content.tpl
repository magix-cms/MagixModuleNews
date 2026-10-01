<div class="tab-pane fade" id="magix-modulenews-pane" role="tabpanel" aria-labelledby="magix-modulenews-tab" tabindex="0">
    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h5 class="mb-0 text-primary"><i class="bi bi-link-45deg me-2"></i>Associer des actualités à cet élément</h5>
            </div>

            <input type="hidden" id="modulenews_hashtoken" value="{$hashtoken|default:''}">
            <input type="hidden" id="modulenews_module" value="{$modulenews_module}">
            <input type="hidden" id="modulenews_id_module" value="{$modulenews_id_module}">

            <div class="row">
                {* COLONNE RECHERCHE (TomSelect) *}
                <div class="col-md-5 mb-4 mb-md-0">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-muted mb-3"><i class="bi bi-search me-2"></i>Rechercher une actualité</h6>
                            <select id="tom-select-modulenews" placeholder="Tapez un titre..."></select>
                            <div class="form-text small mt-2">Tapez au moins 2 lettres pour lancer la recherche.</div>
                        </div>
                    </div>
                </div>

                {* COLONNE RÉSULTATS (SortableJS) *}
                <div class="col-md-7">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-list-ol me-2"></i>Actualités sélectionnées</h6>
                        <span class="badge bg-secondary rounded-pill" id="modulenews-count">0</span>
                    </div>

                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-arrows-move me-1"></i> Glissez-déposez les lignes pour modifier l'ordre d'affichage.
                    </div>

                    <ul class="list-group sortable-list" id="modulenews-list">
                        {* Rempli en AJAX au chargement *}
                    </ul>

                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-success" onclick="saveModuleNews()">
                            <i class="bi bi-save me-2"></i> Sauvegarder la liste
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{block name="javascripts" append}
<script src="{$site_url}/{$baseadmin}/templates/js/vendor/tom-select.complete.min.js"></script>
<script>

    {literal}
    document.addEventListener('DOMContentLoaded', function() {
        const moduleName = document.getElementById('modulenews_module').value;
        const idModule = document.getElementById('modulenews_id_module').value;

        // 1. Initialisation de TomSelect
        // 1. Initialisation de TomSelect
        const selectEl = document.getElementById('tom-select-modulenews');
        if (selectEl) {
            new TomSelect(selectEl, {
                valueField: 'id_news',
                labelField: 'name_news', // CORRIGÉ
                searchField: 'name_news', // CORRIGÉ
                placeholder: "Rechercher par titre...",
                load: function(query, callback) {
                    if (query.length < 2) return callback(); // On attend bien 2 lettres minimum
                    fetch('index.php?controller=MagixModuleNews&action=search&q=' + encodeURIComponent(query))
                        .then(response => response.json())
                        .then(json => callback(json))
                        .catch(() => callback());
                },
                render: {
                    option: function(item, escape) {
                        return `<div><strong class="text-dark">${escape(item.name_news)}</strong></div>`; // CORRIGÉ
                    },
                    item: function(item, escape) {
                        return `<div>${escape(item.name_news)}</div>`; // CORRIGÉ
                    }
                },
                onChange: function(value) {
                    if (!value) return;
                    const item = this.options[value];
                    if (item) {
                        addNewsToList(item.id_news, item.name_news); // CORRIGÉ
                        this.clear();
                    }
                }
            });
        }

        // 2. Initialisation du Drag & Drop
        const listEl = document.getElementById('modulenews-list');
        if (listEl && typeof Sortable !== 'undefined') {
            new Sortable(listEl, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'bg-light'
            });
        }

        // 3. Chargement AJAX des actualités existantes
        if (moduleName && idModule > 0) {
            fetch(`index.php?controller=MagixModuleNews&action=load&module=${moduleName}&id_module=${idModule}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        data.forEach(news => addNewsToList(news.id_news, news.name_news)); // CORRIGÉ
                    }
                });
        }
    });

    // Fonction d'ajout visuel
    function addNewsToList(id, title) {
        const ul = document.getElementById('modulenews-list');
        if (ul.querySelector(`input[value="${id}"]`)) {
            if (typeof MagixToast !== 'undefined') MagixToast.warning("Déjà dans la liste.");
            return;
        }

        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center bg-white border-bottom cursor-move';
        li.innerHTML = `
            <input type="hidden" name="linked_news[]" value="${id}">
            <div class="d-flex align-items-center w-100">
                <i class="bi bi-grip-vertical text-muted me-3 fs-5 drag-handle" style="cursor: move;"></i>
                <strong class="d-block text-dark">${title}</strong>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove ms-2" onclick="removeNewsItem(this)">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
        ul.appendChild(li);
        updateNewsCount();
    }

    function removeNewsItem(btn) {
        btn.closest('li').remove();
        updateNewsCount();
    }

    function updateNewsCount() {
        document.getElementById('modulenews-count').textContent = document.querySelectorAll('#modulenews-list li').length;
    }

    // Fonction de sauvegarde AJAX indépendante du formulaire principal
    function saveModuleNews() {
        const formData = new FormData();
        formData.append('hashtoken', document.getElementById('modulenews_hashtoken').value);
        formData.append('modulenews_module', document.getElementById('modulenews_module').value);
        formData.append('modulenews_id_module', document.getElementById('modulenews_id_module').value);

        document.querySelectorAll('input[name="linked_news[]"]').forEach(input => {
            formData.append('linked_news[]', input.value);
        });

        fetch('index.php?controller=MagixModuleNews&action=save', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    if (typeof MagixToast !== 'undefined') MagixToast.success(res.message);
                    else alert(res.message);
                } else {
                    if (typeof MagixToast !== 'undefined') MagixToast.error(res.message);
                    else alert(res.message);
                }
            });
    }
    {/literal}
</script>
{/block}
<?php
declare(strict_types=1);

namespace Plugins\MagixModuleNews\src;

use App\Backend\Controller\BaseController;
use Plugins\MagixModuleNews\db\ModuleNewsAdminDb;
use Magepattern\Component\HTTP\Request;
use Magepattern\Component\Tool\FormTool;
use App\Component\Cache\CacheManager;

class BackendController extends BaseController
{
    public function run(): void
    {
        $action = $_GET['action'] ?? null;

        if ($action && method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->jsonResponse(false, 'Action invalide.');
        }
    }

    public function search(): void
    {
        $db = new ModuleNewsAdminDb();
        $idLang = (int)($this->defaultLang['id_lang'] ?? 1);
        $term = $_GET['q'] ?? '';

        echo json_encode($db->searchActiveNews($term, $idLang));
        exit;
    }

    /**
     * Charge les actualités déjà liées lors de l'ouverture de l'onglet
     */
    public function load(): void
    {
        $db = new ModuleNewsAdminDb();
        $idLang = (int)($this->defaultLang['id_lang'] ?? 1);
        $module = $_GET['module'] ?? '';
        $idModule = (int)($_GET['id_module'] ?? 0);

        echo json_encode($db->getSelectedNews($module, $idModule, $idLang));
        exit;
    }

    public function save(): void
    {
        if (ob_get_length()) ob_clean();

        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $module = FormTool::simpleClean($_POST['modulenews_module'] ?? '');
        $idModule = (int)($_POST['modulenews_id_module'] ?? 0);
        $newsIds = $_POST['linked_news'] ?? [];

        if (!empty($module) && $idModule > 0) {
            $db = new ModuleNewsAdminDb();
            if ($db->saveLinkedNews($module, $idModule, $newsIds)) {
                CacheManager::clearFrontend('magixmodulenews');
                $this->jsonResponse(true, 'Actualités associées sauvegardées.');
            }
        }

        $this->jsonResponse(false, 'Données manquantes.');
    }
}
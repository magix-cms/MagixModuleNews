<?php
declare(strict_types=1);

namespace Plugins\MagixModuleNews;

use App\Component\Hook\HookManager;
use App\Component\Db\PluginDb;
use Magepattern\Component\Tool\SmartyTool;

class Boot
{
    private array $targetModules = [
        'product'  => 'id_product',
        'pages'    => 'id_pages',
        'category' => 'id_cat',
        'about'    => 'id_about'
    ];

    public function register(): void
    {
        $pluginDb = new PluginDb();
        $activeTargets = $pluginDb->getPluginTargets('MagixModuleNews');

        if (empty($activeTargets)) {
            return;
        }

        foreach ($this->targetModules as $module => $idKey) {
            if (isset($activeTargets[$module]) && $activeTargets[$module] == 1) {

                // Injection du bouton d'onglet
                HookManager::register("{$module}_edit_tab", 'MagixModuleNews', function(array $params) use ($module) {
                    $smarty = SmartyTool::getInstance('admin');
                    $smarty->assign('modulenews_module', $module);
                    $file = ROOT_DIR . 'plugins' . DS . 'MagixModuleNews' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_button.tpl';
                    return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
                });

                // Injection du contenu de l'onglet
                HookManager::register("{$module}_edit_content", 'MagixModuleNews', function(array $params) use ($module, $idKey) {
                    $smarty = SmartyTool::getInstance('admin');
                    $idModule = $params[$idKey] ?? 0;
                    $smarty->assign([
                        'modulenews_module' => $module,
                        'modulenews_id_module' => $idModule
                    ]);
                    $file = ROOT_DIR . 'plugins' . DS . 'MagixModuleNews' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_content.tpl';
                    return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
                });
            }
        }
    }
}
<?php
declare(strict_types=1);

namespace Plugins\MagixModuleNews\src;

use App\Frontend\Controller\BaseController;
use Plugins\MagixModuleNews\db\ModuleNewsFrontDb;
use App\Frontend\Db\NewsDb;
use App\Frontend\Db\CompanyDb;
use App\Frontend\Model\NewsPresenter;
use Magepattern\Component\Tool\SmartyTool;
use App\Component\Db\PluginDb;

class FrontendController extends BaseController
{
    private static array $targetsCache = [];

    public static function renderWidget(array $params = []): string
    {
        if (empty(self::$targetsCache)) {
            $pluginDb = new PluginDb();
            self::$targetsCache = $pluginDb->getPluginTargets('MagixModuleNews');
        }

        if (empty(self::$targetsCache)) return '';

        $hookName = $params['name'] ?? '';

        if ($hookName === 'displayPageBottom' && !empty(self::$targetsCache['pages'])) {
            return self::processRender($params, 'pages', 'id_pages');
        }
        if ($hookName === 'displayProductExtraContent' && !empty(self::$targetsCache['product'])) {
            return self::processRender($params, 'product', 'id_product');
        }
        if ($hookName === 'displayCategoryBottom' && !empty(self::$targetsCache['category'])) {
            return self::processRender($params, 'category', 'id_cat');
        }
        if ($hookName === 'displayAboutBottom' && !empty(self::$targetsCache['about'])) {
            return self::processRender($params, 'about', 'id_about');
        }

        return '';
    }

    private static function processRender(array $params, string $module, string $idKey): string
    {
        $id = (int)($params[$idKey] ?? 0);
        if ($id === 0) return '';

        $currentLang = $params['current_lang'] ?? ['id_lang' => 1, 'iso_lang' => 'fr'];
        $idLang = (int)$currentLang['id_lang'];
        $siteUrl = rtrim((string)($params['site_url'] ?? 'http://localhost'), '/');

        $db = new ModuleNewsFrontDb();
        $newsIds = $db->getLinkedNewsIds($module, $id);

        if (empty($newsIds)) return '';

        $newsDb = new NewsDb();
        $companyDb = new CompanyDb();

        // Récupération des infos globales attendues par le Presenter du Core
        $companyInfo = $companyDb->getCompanyInfo() ?: [];
        $skinFolder = $params['mc_settings']['theme']['value'] ?? 'default';

        $formattedNews = [];
        foreach ($newsIds as $idNews) {

            // Appel de la méthode native qui déclenche le hook "extendNewsData"
            $rawNews = $newsDb->getNewsPage((int)$idNews, $idLang);

            if ($rawNews !== false) {
                // Utilisation du formateur natif
                $formatted = NewsPresenter::format($rawNews, $currentLang, $siteUrl, $companyInfo, $skinFolder);

                if ($formatted) {
                    $formattedNews[] = $formatted;
                }
            }
        }

        if (empty($formattedNews)) return '';

        $view = SmartyTool::getInstance('front');

        // NOUVEAU : On assigne le nom du module en plus des actualités
        $view->assign([
            'module_related_news'       => $formattedNews,
            'modulenews_current_module' => $module
        ]);

        $cacheId = 'modulenews_' . $module . '_' . $id . '_' . $idLang;
        $template = ROOT_DIR . 'plugins' . DS . 'MagixModuleNews' . DS . 'views' . DS . 'front' . DS . 'widget.tpl';

        return file_exists($template) ? $view->fetch($template, $cacheId) : '';
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function run(): void
    {
        SmartyTool::addTemplateDir('front', ROOT_DIR . 'plugins' . DS . 'MagixModuleNews' . DS . 'views' . DS . 'front');

        $confFile = ROOT_DIR . 'plugins' . DS . 'MagixModuleNews' . DS . 'i18n' . DS . 'fr.conf';
        if (file_exists($confFile)) {
            $this->view->configLoad($confFile);
        }
    }
}
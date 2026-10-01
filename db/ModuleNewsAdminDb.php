<?php
declare(strict_types=1);

namespace Plugins\MagixModuleNews\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class ModuleNewsAdminDb extends BaseDb
{
    /**
     * Recherche des actualités actives via TomSelect (AJAX)
     */
    public function searchActiveNews(string $term, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select(['n.id_news', 'nc.name_news'])
            ->from('mc_news', 'n')
            ->join('mc_news_content', 'nc', 'n.id_news = nc.id_news')
            ->where('nc.id_lang = :lang', ['lang' => $idLang])
            ->where('nc.published_news = 1')
            ->where('n.date_publish <= NOW()')
            ->where('nc.name_news LIKE :term', ['term' => '%' . $term . '%'])
            ->orderBy('nc.name_news', 'ASC')
            ->limit(15);

        return $this->executeAll($qb) ?: [];
    }

    /**
     * Récupère les actualités déjà liées à ce module
     */
    public function getSelectedNews(string $module, int $idModule, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select(['pmn.id_news', 'nc.name_news'])
            ->from('mc_plug_module_news', 'pmn')
            ->join('mc_news_content', 'nc', 'pmn.id_news = nc.id_news')
            ->where('pmn.module_name = :module', ['module' => $module])
            ->where('pmn.id_module = :id_module', ['id_module' => $idModule])
            ->where('nc.id_lang = :lang', ['lang' => $idLang])
            ->orderBy('pmn.order_news', 'ASC');

        return $this->executeAll($qb) ?: [];
    }

    /**
     * Remplace la sélection complète
     */
    public function saveLinkedNews(string $module, int $idModule, array $newsIds): bool
    {
        $qbDel = new QueryBuilder();
        $qbDel->delete('mc_plug_module_news')
            ->where('module_name = :module', ['module' => $module])
            ->where('id_module = :id_module', ['id_module' => $idModule]);
        $this->executeDelete($qbDel);

        if (!empty($newsIds)) {
            $order = 0;
            foreach ($newsIds as $idNews) {
                $qbIns = new QueryBuilder();
                $qbIns->insert('mc_plug_module_news', [
                    'module_name' => $module,
                    'id_module'   => $idModule,
                    'id_news'     => (int)$idNews,
                    'order_news'  => $order
                ]);
                $this->executeInsert($qbIns);
                $order++;
            }
        }
        return true;
    }
}
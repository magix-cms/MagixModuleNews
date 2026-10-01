<?php
declare(strict_types=1);

namespace Plugins\MagixModuleNews\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class ModuleNewsFrontDb extends BaseDb
{
    public function getLinkedNewsIds(string $module, int $idModule): array
    {
        $cache = $this->getSqlCache();
        $qb = new QueryBuilder();

        // On interroge uniquement notre table pivot
        $qb->select(['id_news'])
            ->from('mc_plug_module_news')
            ->where('module_name = :module', ['module' => $module])
            ->where('id_module = :id_module', ['id_module' => $idModule])
            ->orderBy('order_news', 'ASC');

        $cacheKey = $cache->generateKey($qb->getSql(), $qb->getParams(), 'magixmodulenews');

        $data = $cache->get($cacheKey);
        if ($data !== null) {
            return $data;
        }

        $results = $this->executeAll($qb);
        $formattedIds = $results ? array_column($results, 'id_news') : [];

        $cache->set($cacheKey, $formattedIds, 86400);

        return $formattedIds;
    }
}
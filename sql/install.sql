CREATE TABLE IF NOT EXISTS `mc_plug_module_news` (
    `id_link` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `module_name` varchar(50) NOT NULL,
    `id_module` int(11) unsigned NOT NULL,
    `id_news` int(11) unsigned NOT NULL,
    `order_news` smallint(5) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_link`),
    KEY `idx_module_item` (`module_name`, `id_module`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
<?php
/*
 * Script créant et vérifiant que la table de l'historique d'import existe bien
 * (équivalent standard Dolibarr de l'ancien appel abricot init_db_by_vars)
 */

if(!defined('INC_FROM_DOLIBARR')) {
	define('INC_FROM_CRON_SCRIPT', true);

	require('../config.php');

}


global $db, $conf;

$table = $db->prefix().'bankimport_history';

// Création de la table si elle n'existe pas
$sql = "CREATE TABLE IF NOT EXISTS `".$table."` (
	`rowid` integer AUTO_INCREMENT NOT NULL,
	`date_cre` datetime NULL,
	`date_maj` datetime NULL,
	`num_releve` varchar(50) NULL,
	`fk_bank` integer NOT NULL DEFAULT 0,
	`line_imported_title` text NULL,
	`line_imported_value` text NULL,
	PRIMARY KEY (`rowid`),
	KEY `idx_bankimport_history_date_cre` (`date_cre`),
	KEY `idx_bankimport_history_date_maj` (`date_maj`),
	KEY `idx_bankimport_history_num_releve` (`num_releve`),
	KEY `idx_bankimport_history_fk_bank` (`fk_bank`)
) ENGINE=InnoDB";
if (!empty($conf->db->character_set)) $sql .= ' DEFAULT CHARSET='.$conf->db->character_set;
if (!empty($conf->db->dolibarr_main_db_collation)) $sql .= ' COLLATE='.$conf->db->dolibarr_main_db_collation;

$resql = $db->query($sql);
if (!$resql) {
	dol_print_error($db);
	exit(1);
}

// Ajout des colonnes manquantes sur une table existante (installations historiques)
$TExpectedFields = array(
	'date_cre' => "ALTER TABLE `".$table."` ADD `date_cre` datetime NULL",
	'date_maj' => "ALTER TABLE `".$table."` ADD `date_maj` datetime NULL",
	'num_releve' => "ALTER TABLE `".$table."` ADD `num_releve` varchar(50) NULL, ADD INDEX `idx_bankimport_history_num_releve` (`num_releve`)",
	'fk_bank' => "ALTER TABLE `".$table."` ADD `fk_bank` integer NOT NULL DEFAULT 0, ADD INDEX `idx_bankimport_history_fk_bank` (`fk_bank`)",
	'line_imported_title' => "ALTER TABLE `".$table."` ADD `line_imported_title` text NULL",
	'line_imported_value' => "ALTER TABLE `".$table."` ADD `line_imported_value` text NULL",
);

$resql = $db->query("SHOW COLUMNS FROM `".$table."`");
if ($resql) {
	$TExistingFields = array();
	while ($obj = $db->fetch_object($resql)) {
		$TExistingFields[] = $obj->Field;
	}
	$db->free($resql);

	foreach ($TExpectedFields as $field => $sqlAlter) {
		if (!in_array($field, $TExistingFields)) {
			$resql = $db->query($sqlAlter);
			if (!$resql) {
				dol_print_error($db);
				exit(1);
			}
		}
	}
}

// Les tables créées historiquement par abricot n'ont pas AUTO_INCREMENT sur rowid :
// on le rétablit pour que l'insert standard (last_insert_id) fonctionne
$resql = $db->query("SHOW COLUMNS FROM `".$table."` LIKE 'rowid'");
if ($resql) {
	$obj = $db->fetch_object($resql);
	$db->free($resql);
	if ($obj && stripos($obj->Extra, 'auto_increment') === false) {
		$resql = $db->query("ALTER TABLE `".$table."` MODIFY `rowid` integer AUTO_INCREMENT NOT NULL");
		if (!$resql) {
			dol_print_error($db);
			exit(1);
		}
	}
}

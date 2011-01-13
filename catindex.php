<?php
/* ====================
Copyright (c) 2008-2009, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_SED_EXTPLUGIN]
Code=catindex
Part=index
File=catindex
Hooks=index.tags
Tags=index.tpl:{PLUGIN_CATINDEX}
Order=10
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

// Shortcuts
$root = $cfg['plugin']['catindex']['root'];
$cols = $cfg['plugin']['catindex']['cols'];

// Build 2-level category tree in alphabetical order
$tree = array();
$cat_count = 0;
$subcat_count = 0;
foreach($sed_cat as $code => $val)
{
	$pathcodes = explode('.', $val['path']);
	$root_pos = empty($root) ? -1 : array_search($root, $pathcodes);
	if($root_pos !== false && $code != $root)
	{
		if($pathcodes[$root_pos + 1] == $code)
		{
			$tree[$code] = array();
			$cat_count++;
		}
		else
		{
			$tree[$pathcodes[$root_pos + 1]][] = $code;
			$subcat_count++;
		}
	}
}
ksort($tree);
foreach($tree as $key => $val)
{
	sort($tree[$key]);
}

// Fetch number of items contained in each category
$items_count = array();
$res = sed_sql_query("SELECT COUNT(page_id) AS items, page_cat AS cat FROM $db_pages GROUP BY page_cat");
while($cnt = sed_sql_fetchassoc($res))
{
	$items_count[$cnt['cat']] = (int) $cnt['items'];
}
sed_sql_freeresult($res);

// Split the tree into columns and render the index
$per_col = floor($subcat_count / $cols);
$cur_cats = 0;
$cur_subs = 0;
$t1 = new XTemplate(sed_skinfile('catindex', true));
$t1->assign('CATINDEX_COL_WIDTH', round(100 / $cols));
foreach($tree as $category => $subcats)
{
	foreach($subcats as $subcategory)
	{
		$t1->assign(array(
		'CATINDEX_SUBCATEGORY_URL' => sed_url('list', 'c=' . $subcategory),
		'CATINDEX_SUBCATEGORY_TITLE' => sed_cc($sed_cat[$subcategory]['title']),
		'CATINDEX_SUBCATEGORY_ITEMS' => empty($items_count[$subcategory]) ? 0 : $items_count[$subcategory]
		));
		$t1->parse('CATINDEX.CATINDEX_COLUMN.CATINDEX_CATEGORY.CATINDEX_SUBCATEGORY');
		$cur_subs++;
	}
	$t1->assign(array(
	'CATINDEX_CATEGORY_URL' => sed_url('list', 'c=' . $category),
	'CATINDEX_CATEGORY_TITLE' => sed_cc($sed_cat[$category]['title'])
	));
	$cur_cats++;
	$t1->parse('CATINDEX.CATINDEX_COLUMN.CATINDEX_CATEGORY');
	if($cur_subs >= $per_col || $cur_cats == $cat_count)
	{
		$cur_subs = 0;
		$t1->parse('CATINDEX.CATINDEX_COLUMN');
	}
}
$t1->parse('CATINDEX');
$t->assign('PLUGIN_CATINDEX', $t1->text('CATINDEX'));

?>
<?
if ($jp7_cache) $jp7_cache->endCache();
if ($db_type) $db->Close();
else mysql_close($db);
?>
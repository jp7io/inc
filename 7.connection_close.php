<?php
if ($jp7_cache) $jp7_cache->endCache();
if ($debugger) $debugger->showToolbar(); // Only called when cache is done, avoiding debug from being cached

if ($db_type) $db->Close();
else mysql_close($db);
?>
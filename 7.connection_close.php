<?php

if ($jp7_cache instanceof FileCache) {
    $jp7_cache->endCache();
}
if ($debugger instanceof Jp7_Debugger) {
    $debugger->showToolbar(); // Only called when cache is done, avoiding debug from being cached
}
/*
if ($db instanceof ADOConnection) {
    $db->Close();
}
*/
//asort($GLOBALS['sql_execute_queries']);
//krumo($GLOBALS['sql_execute_count']);
//krumo($GLOBALS['sql_execute_queries']);

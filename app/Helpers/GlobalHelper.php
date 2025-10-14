<?php
function getQuerySql($query)
{
    $builder = $query;
    $sql = $builder->toSql();
    foreach ($builder->getBindings() as $binding) {
        $value = is_numeric($binding) ? $binding : (is_null($binding) ? 'NULL' : "'" . addslashes($binding) . "'");
        $sql = preg_replace('/\?/', $value, $sql, 1);
    }
    $sql = preg_replace('/"([^"]+)"/', '$1', $sql);
    return $sql;
}

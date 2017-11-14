<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 15/11/2017
 * Time: 12:10 AM
 */
if (!function_exists('list_files')) {
    /**
     * list all files within a given path (no recursive)
     * @param $base_path
     * @return array
     */
    function list_files($base_path)
    {
        $directories = \File::files($base_path);
        $results = [];
        foreach ($directories as $directory) {
            $results[] = array_get(pathinfo($directory), 'filename');
        }
        return $results;
    }
}

if (!function_exists('list_directories')) {
    /**
     * list all directories within a given path (no recursive)
     * @param $base_path
     * @return array
     */
    function list_directories($base_path)
    {
        $directories = \File::directories($base_path);
        $results = [];
        foreach ($directories as $directory) {
            $results[] = array_get(pathinfo($directory), 'filename');
        }
        return $results;
    }
}

if (!function_exists('list_files_with_directories')) {
    /**
     * List all files within a given directories with path prepended (can be recursive)
     * @param $basePath
     * @param bool $recursive
     * @return array
     */
    function list_files_with_directories($basePath, $recursive = false)
    {
        $directories = list_directories($basePath);
        $files = list_files($basePath);
        if ($recursive === true && !is_null($directories)) {
            foreach ($directories as $directory) {
                $newBasePath = $basePath . "/{$directory}";
                $newFiles = list_files_with_directories($newBasePath, true);
                if (!is_null($newFiles)) {
                    foreach ($newFiles as $newFile) {
                        $qualifiedFileName = "{$directory}\\{$newFile}";
                        if (!in_array($qualifiedFileName, $files)) {
                            $files[] = $qualifiedFileName;
                        }
                    }
                }
            }
        }
        return $files;
    }
}
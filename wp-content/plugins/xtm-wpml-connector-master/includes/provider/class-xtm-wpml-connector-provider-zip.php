<?php

/**
 * Class XTM_Provider_Zip
 */
class XTM_Provider_Zip
{

    /**
     * @param $pathToZipFile
     * @param $targetPath
     */
    public static function unpack($pathToZipFile, $targetPath ){

        $zip = new ZipArchive;
        $res = $zip->open($pathToZipFile);
        if ($res === true) {
            $zip->extractTo($targetPath);
            $zip->close();
        }
        //unlink($pathToZipFile);
    }

    /**
     * @param $root_path
     * @param $zip_file
     */
    public static function zip_folder($root_path, $zip_file)
    {
        $zip = new ZipArchive();
        $zip->open($root_path . '/../' . $zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root_path),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($root_path) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
    }

    /**
     * @param $dir
     */
    public static function remove_folder($dir)
    {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
               unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }
}

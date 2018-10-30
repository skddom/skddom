<?php

/**
 * Класс управления перехваченными файлами.
 */
class nc_netshop_external_1c_interceptor {
    /** Папка с файлами */
    const FOLDER = '1c_intercept';

    /**
     * Возвращает путь к папке с файлами. Если папка не существует - создает ее.
     *
     * @return string
     */
    public static function get_files_path() {
        $nc_core = nc_core::get_object();
        $path = rtrim($nc_core->TMP_FOLDER, '\/') . '/' . self::FOLDER . '/';
        if (!file_exists($path)) {
            mkdir($path, $nc_core->DIRCHMOD, true);
        }

        return $path;
    }

    /**
     * Возвращает массив файлов в папке.
     *
     * @return array
     */
    public function get_files_list() {
        $files = array();
        $mtimes = array();

        $file_paths = (array)glob(self::get_files_path() . '*.xml');
        foreach ($file_paths as $full_path) {
            $mtime = filemtime($full_path);
            $files[] = array(
                'file' => $full_path,
                'filename' => basename($full_path),
                'created_at' => $mtime,
            );
            // время округляется, чтобы import.xml (загруженный раньше) был выше в списке
            // чем offers.xml при сортировке по времени по убыванию
            $mtimes[] = round(round($mtime / 30) * 30);
        }

        array_multisort($mtimes, SORT_DESC, $files);

        return $files;
    }

    /**
     * Удаляет перехваченный файл.
     *
     * @param string $filename
     */
    public function delete_file($filename) {
        $filename = pathinfo($filename, PATHINFO_BASENAME);
        $path = self::get_files_path() . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Удаляет все перехваченные файлы.
     */
    public function delete_all_files() {
        nc_delete_dir(self::get_files_path());
    }

    /**
     * Копирует файл импорта во временную директорию и возвращает относительный путь к новому файлу.
     *
     * @param string $filename
     * @return string
     */
    public function copy_file_to_temporary_folder($filename) {
        $nc_core = nc_core::get_object();
        $TMP_FOLDER = $nc_core->TMP_FOLDER;

        foreach (scandir($TMP_FOLDER) as $file) {
            $path = $TMP_FOLDER . $file;
            if (strpos($file, self::FOLDER . '_') === 0 && is_dir($path)) {
                nc_delete_dir($path);
            }
        }

        $filename = pathinfo($filename, PATHINFO_BASENAME);
        $directory = self::FOLDER . '_' . uniqid();
        mkdir($TMP_FOLDER . $directory, $nc_core->DIRCHMOD);

        copy(self::get_files_path() . $filename, $TMP_FOLDER . $directory . '/' . $filename);

        return $directory . '/' . $filename;
    }
}
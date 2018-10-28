<?php

/* $Id$ */
if (!class_exists("nc_System")) die("Unable to load file.");

/**
 * Реализация файлового кэша
 */
class nc_cache_io_file extends nc_cache_io {

    protected function __construct() {
        parent::__construct();
        $nc_core = nc_Core::get_object();

        $this->cache_path = isset($nc_core->CACHE_FOLDER) ? $nc_core->CACHE_FOLDER : $nc_core->DOCUMENT_ROOT."/".$nc_core->SUB_FOLDER."netcat_cache/";
    }

    /**
     * Получение экземпляра класса
     * @static
     * @staticvar self $storage
     * @return <type>
     */
    public static function get_object() {
        // call as static
        static $storage;
        // check inited object
        if (!isset($storage)) $storage = new self();

        // return object
        return is_object($storage) ? $storage : false;
    }

    /**
     * Добавлние в кэш данных
     *
     * @param string $key ключ
     * @param string $value значние
     *
     * @return int размер записанных данных, байты
     */
    public function add($key, $value) {
        $nc_core = nc_Core::get_object();
        // директория для записи
        $dir = substr($key, 0, strripos($key, '/'));
        $nc_core->files->create_dir($dir);

        if (!($bytes_writed = @file_put_contents($key, $value))) {
            throw new Exception(str_replace("%FILE", $key, NETCAT_MODULE_CACHE_CLASS_CANNOT_CREATE_FILE));
        }

        return $bytes_writed;
    }

    /**
     * Чтение данных из кэша
     *
     * @param string $key ключ
     *
     * @return mixed данные из кэша или false, если их нет
     */
    public function read($key) {
        if (file_exists($key)) {
            return file_get_contents($key);
        }

        return false;
    }

    /**
     * Удаление определенной кэш-записи
     *
     * @param string $key ключ
     *
     * @return int количество удаленных байт
     */
    public function delete($key) {
        if (file_exists($key) && is_writable(dirname($key))) {
            // data to delete size
            $unlink_data_size = filesize($key);
            // delete file
            unlink($key);
        }

        return $unlink_data_size;
    }

    /**
     * Очистка кэша
     *
     * @param string $dir директория
     * @param <type> $remove_dir удалять саму директорию
     *
     * @return int количество удаленных байт
     */
    public function drop($dir, $remove_dir = false) {
        // end slash
        $dir = rtrim($dir, "/")."/";
        // validate
        if (!is_dir($dir)) {
            return false;
        }
        // deleted file size
        $files_size = 0;
        // delete all files from dir
        if (($dh = @opendir($dir))) {
            // read children
            while (( $file = @readdir($dh) ) !== false) {
                if ($file == "." || $file == ".." || $file == "stat.log")
                        continue;
                // append full path
                $file = $dir.$file;

                // delete dir or file
                switch (true) {
                    case is_file($file):
                        // continue if not accessible
                        if (!is_writable(dirname($file))) continue;
                        // data to delete size
                        $unlink_data_size = filesize($file);
                        // delete
                        if (unlink($file)) {
                            $files_size+= $unlink_data_size;
                        }
                        // unset
                        unset($unlink_data_size);
                        break;
                    case is_dir($file):
                        $files_size+= $this->drop($file, $remove_dir, true);
                        break;
                }
            }
            closedir($dh);
        }
        // remove dir
        if ($remove_dir) @rmdir($dir);
        // return total deleted bytes
        return $files_size ? $files_size : false;
    }

    /**
     * Количество занимаемого места определенной кэш-записи
     *
     * @param string $key ключ
     *
     * @return int размер
     */
    public function get_size($key) {
        return file_exists($key) ? filesize($key) : 0;
    }

    /**
     * Обновить статистику
     *
     * @param string $essence тип кэша
     * @param int  $size количество прибавляемых байтах
     */
    public function update_stat($essence, $size) {
        // return if no permission to write
        if (!is_writable($this->cache_path)) return false;
        $nc_core = nc_Core::get_object();
        $nc_core->files->create_dir($this->cache_path.$essence);
        // stat file path
        $stat_file = $this->cache_path.$essence."/stat.log";
        $content_lenght = file_exists($stat_file) ? file_get_contents($stat_file) : 0;

        $content_lenght += intval($size);

        // return writed bytes count
        if (@is_writable($stat_file)) {
            return file_put_contents($stat_file, $content_lenght);
        }
    }

}
?>
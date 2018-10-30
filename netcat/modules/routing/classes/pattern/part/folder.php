<?php

class nc_routing_pattern_part_folder extends nc_routing_pattern_part {

    public function match(nc_routing_request $request, nc_routing_result $result) {
        $remainder = $result->get_remainder();

        // ensure remainder starts and ends with a slash:
        $starts_with_slash = ($remainder[0] == '/');
        if (!$starts_with_slash) { $remainder = '/' . $remainder; }
        if (substr($remainder, -1) != '/') { $remainder .= '/'; }

        // try to get the folder that corresponds to the unresolved path remainder:
        $folder_settings = $this->get_folder_settings($request->get_site_id(), $remainder);
        if ($folder_settings) {
            $result->set_resource_parameter('folder_id', $folder_settings['Subdivision_ID']);

            // do not remove trailing slash, but remove the slash added above
            $chars_to_remove = strlen($folder_settings['Hidden_URL']) - ($starts_with_slash ? 1 : 2);
            $result->truncate_remainder($chars_to_remove);

            return true;
        }
        else {
            return false;
        }
    }

    public function substitute_values_for(nc_routing_path $path, nc_routing_pattern_parameters $parameters) {
        $folder = $path->get_resource_parameter('folder');

        if (!$folder) {
            $folder_id = $path->get_resource_parameter('folder_id');
            try {
                $folder = nc_core::get_object()->subdivision->get_by_id($folder_id, 'Hidden_URL');
            }
            catch (Exception $e) {}
        }

        if ($folder) {
//            $parameters->folder = $folder;
            return trim($folder, '/');
        }
        else {
            return false;
        }
    }

    /**
     * @param int $site_id
     * @param string $path_remainder
     * @return mixed
     */
    protected function get_folder_settings($site_id, $path_remainder) {
        static $cache = array();

        if (!array_key_exists($path_remainder, $cache)) {
            $cache[$path_remainder] = false;

            $current_remainder = $path_remainder;
            while ($last_slash = strrpos($current_remainder, '/')) {
                $current_remainder = substr($current_remainder, 0, $last_slash);
                $sub = nc_core::get_object()->subdivision->get_by_uri("$current_remainder/", $site_id, null, false, true);
                if ($sub) {
                    $cache[$path_remainder] = $sub;
                    break; // --- exit while() ---
                }
            }
        }

        return $cache[$path_remainder];
    }

}
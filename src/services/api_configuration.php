<?php
class API_configuration
{
    private $connection;
    private $api_token;
    protected $today;
    protected $now;
    public string $token = "";
    public int $user_id = 0;

    function __construct()
    {
        if ($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "192.168.2.20") {
            $server = "localhost";
            $user = "root";
            $password = "";
            $db_name = "u524077001_dmt-cr";
            $api_token = "SECRET_KEY";
            $connection = mysqli_connect($server, $user, $password, $db_name);
            mysqli_set_charset($connection, "utf8");
        } else {
            $server = "localhost";
            $db_name = "";
            $user = "";
            $password = "";
            $api_token = "";
            $connection = mysqli_connect($server, $user, $password, $db_name);
        }

        $this->api_token = $api_token;
        $this->connection = $connection;
        $this->today = date("Y-m-d");
        $this->now = date("Y-m-d H:i:s");
    }

    protected function db_create($sql)
    {
        if (mysqli_query($this->connection, $sql)) {
            return mysqli_insert_id($this->connection);
        } else {
            return false;
        }
    }

    protected function db_update($sql)
    {
        if (mysqli_query($this->connection, $sql)) {
            return true;
        } else {
            return false;
        }
    }

    protected function db_delete($sql)
    {
        if (mysqli_query($this->connection, $sql)) {
            return true;
        } else {
            return false;
        }
    }

    protected function db_read($sql)
    {
        $query = mysqli_query($this->connection, $sql);
        return $query;
    }

    protected function db_set($sql)
    {
        if (mysqli_query($this->connection, $sql)) {
            return true;
        } else {
            return false;
        }
    }

    protected function db_object($query_result)
    {
        return mysqli_fetch_object($query_result);
    }

    protected function db_array($query_result)
    {
        return mysqli_fetch_array($query_result);
    }

    protected function db_assoc($query_result)
    {
        return mysqli_fetch_assoc($query_result);
    }

    protected function db_num_rows($query_result)
    {
        return mysqli_num_rows($query_result);
    }

    protected function slugify($string)
    {
        $string = preg_replace('/[\t\n]/', ' ', $string);
        $string = preg_replace('/\s{2,}/', ' ', $string);
        $list = array(
            'Š' => 'S',
            'š' => 's',
            'Đ' => 'Dj',
            'đ' => 'dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'Č' => 'C',
            'č' => 'c',
            'Ć' => 'C',
            'ć' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            '&' => 'e',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'Ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            '&' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ŕ' => 'R',
            'ŕ' => 'r',
            '/' => '-',
            ' ' => '-',
            '(' => '',
            ')' => '',
            '.' => '',
        );
        $string = strtr($string, $list);
        $string = preg_replace('/-{2,}/', '-', $string);
        $string = strtolower($string);
        return $string;
    }

    protected function upload_image(string $image, string $name)
    {
        $path = "public/images/" . $name;
        $this->base64_to_jpeg($image, $path);
        return $name;
    }

    protected function delete_image(string $name)
    {
        $path = "public/images/" . $name;
        if (file_exists($path)) {
            unlink($path);
            return true;
        } else {
            return false;
        }
    }

    private function base64_to_jpeg($base64_string, $output_file)
    {
        $ifp = fopen($output_file, 'wb');
        $data = explode(',', $base64_string);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return $output_file;
    }

    protected function base64_to_file(string $base64, string $output_file)
    {
        $ifp = fopen($output_file, 'wb');
        $data = explode(',', $base64);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return $output_file;
    }

    protected function real_to_float($value)
    {
        if (str_contains($value, 'R$')) {
            $value = str_replace('R$', '', $value);
            $value = str_replace(' ', '', $value);
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            return floatval($value);
        } else if (str_contains($value, '.') && str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            return floatval($value);
        } else if (str_contains($value, '.') && !str_contains($value, ',')) {
            return floatval($value);
        } else if (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
            return floatval($value);
        } else {
            return floatval($value);
        }
    }

    public function authorization(string $type = "user")
    {
        if ($type == "user") {
            $sql_token = str_replace("Bearer ", "", $this->token);
            $sql = 'SELECT `user_id`, `expires` FROM `api_sessions` WHERE `token` = "' . addslashes($sql_token) . '"';
            $get_user_token_data = $this->db_read($sql);
            if ($this->db_num_rows($get_user_token_data) > 0) {
                $user_token_data = $this->db_object($get_user_token_data);
                if (strtotime($user_token_data->expires) > strtotime($this->now)) {
                    $this->user_id = $user_token_data->user_id;
                    return $user_token_data->user_id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else if ($type == "api") {
            if ($this->token == $this->api_token) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function validate_permissions(string $permissions)
    {
        $sql = 'SELECT `permission` FROM `users_permissions` WHERE `user_id` = ' . $this->user_id . ' AND `permission` = "' . $permissions . '" AND `status` = "true"';
        $get_user_permissions = $this->db_read($sql);
        if ($this->db_num_rows($get_user_permissions) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function generate_user_log(
        int $user_id,
        string $action,
        string $description = null
    ) {
        $sql = 'INSERT INTO `users_logs` (`user_id`, `date`, `action`' . ($description != null ? ', `description`' : '') . ') VALUES ("' . addslashes($user_id) . '", "' . date('Y-m-d H:i:s') . '", "' . addslashes($action) . '"' . ($description != null ? ', "' . addslashes($description) . '"' : '') . ')';
        $this->db_create($sql);
    }
}

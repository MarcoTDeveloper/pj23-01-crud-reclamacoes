<?php

class Users extends Api_configuration
{

    public function create(
        string $name,
        string $email,
        string $password,
        string $position,
        array $permissions
    ) {

        $values = '
        "' . $name . '",
        "' . $email . '",
        "' . password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) . '",
        "' . $position . '"
        ';

        $sql = 'INSERT INTO `users` (`name`, `email`, `password`,`position`) VALUES (' . $values . ')';
        $create_user = $this->db_create($sql);
        if ($create_user) {
            $slug = $this->slugify($create_user . '-' . $name);
            $sql = 'UPDATE `users` SET `slug` = "' . $slug . '" WHERE `id` = ' . $create_user;
            $this->db_update($sql);
            for ($i = 0; $i < count($permissions); $i++) {
                $permission_key = array_keys($permissions);
                $permission_data = (array) $permissions[$permission_key[$i]];

                for ($j = 0; $j < count($permission_data); $j++) {
                    $permission_data_key = array_keys($permission_data);
                    $permission_data_value = $permission_data[$permission_data_key[$j]] ? "true" : "false";

                    $permission = $permission_key[$i] . '.' . $permission_data_key[$j];
                    $sql = 'UPDATE  `users_permissions` SET `status` = "' . $permission_data_value . '" WHERE `user_id` = ' . $create_user . ' AND `permission` = "' . $permission . '"';
                    $this->db_update($sql);
                }
            }
            return [
                'id' => (int) $create_user,
                'name' => $name,
                'email' => $email,
                'position' => $position,
                'slug' => $slug
            ];
        } else {
            return false;
        }
    }

    public function read()
    {
        $sql = 'SELECT `id`, `name`, `email`,`position`, `slug` FROM `users`';
        $get_users = $this->db_read($sql);
        if ($this->db_num_rows($get_users) > 0) {
            $users = [];
            while ($user = $this->db_object($get_users)) {
                $users[] = [
                    'id' => (int) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'position' => $user->position,
                    'slug' => $user->slug
                ];
            }
            return $users;
        } else {
            return [];
        }
    }

    public function read_by_slug(
        string $slug
    ) {
        $sql = 'SELECT `id`, `name`, `email`, `position`, `slug` FROM `users` WHERE `slug` = "' . $slug . '"';
        $get_users = $this->db_read($sql);
        if ($this->db_num_rows($get_users) > 0) {
            $users = $this->db_object($get_users);
            $users->id = (int) $users->id;
            return $users;
        } else {
            return [];
        }
    }

    private function read_by_id(
        int $id
    ) {
        $sql = 'SELECT `id`, `name`, `email`, `position`, `slug` FROM `users` WHERE `id` = "' . $id . '"';
        $get_users = $this->db_read($sql);
        if ($this->db_num_rows($get_users) > 0) {
            $users = $this->db_object($get_users);
            $users->id = (int) $users->id;
            return $users;
        } else {
            return [];
        }
    }

    public function update(
        int $id,
        string $name,
        string $email,
        string $position,
        array $permissions
    ) {
        $old_user = $this->read_by_id($id);
        if ($old_user) {
            $sql = 'UPDATE `users` SET `name` = "' . $name . '" , `email` = "' . $email . '" , `position` = "' . $position . '" , `slug` = "' . $this->slugify($id . '-' . $name) . '"  WHERE `id` = "' . $id .  '"';
            if ($this->db_update($sql)) {
                for ($i = 0; $i < count($permissions); $i++) {
                    $permission_key = array_keys($permissions);
                    $permission_data = (array) $permissions[$permission_key[$i]];

                    for ($j = 0; $j < count($permission_data); $j++) {
                        $permission_data_key = array_keys($permission_data);
                        $permission_data_value = $permission_data[$permission_data_key[$j]] ? "true" : "false";

                        $permission = $permission_key[$i] . '.' . $permission_data_key[$j];
                        $sql = 'UPDATE  `users_permissions` SET `status` = "' . $permission_data_value . '" WHERE `user_id` = ' . $id . ' AND `permission` = "' . $permission . '"';
                        $this->db_update($sql);
                    }
                }
                return [
                    'old_user' => $old_user,
                    'new_user' => [
                        'id' => $id,
                        'name' => $name,
                        'email' => $email,
                        'position' => $position,
                        'slug' => $this->slugify($id . '-' . $name)
                    ]
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete(
        string $slug
    ) {
        $old_user = $this->read_by_slug($slug);
        if ($old_user) {
            $sql = 'DELETE FROM `users` WHERE `slug` = "' . $slug . '"';
            if ($this->db_delete($sql)) {
                return $old_user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

<?php

class Complaints extends API_configuration
{

    public function create(
        int $user_id,
        string $name,
        string $description,
        string $prompt
    ) {

        $values = '
        ' . $user_id . ',
        "' . $name . '",
        "' . $description . '",
        "' . date('Y-m-d H:i:s') . '",
        "' . $prompt . '"
        ';

        $sql = 'INSERT INTO `complaints` (`user_id`, `name`, `description`, `date`, `prompt`) VALUES (' . $values . ')';
        $create_complaint = $this->db_create($sql);
        if ($create_complaint) {
            $slug = $this->slugify($create_complaint . '-' . $name);
            $sql = 'UPDATE `complaints` SET `slug` = "' . $slug . '" WHERE `id` = ' . $create_complaint;
            $this->db_update($sql);
            return [
                'id' => (int) $create_complaint,
                'user_id' => (int) $user_id,
                'name' => $name,
                'description' => $description,
                'prompt' => $prompt,
                'slug' => $slug
            ];
        } else {
            http_response_code(400);
            return ['message' => "Error creating complaint"];
        }
    }

    public function read()
    {
        $sql = 'SELECT `id`, `user_id`, `name`, `description`, `date`, `prompt`, `slug` FROM `complaints`';
        $get_complaints = $this->db_read($sql);
        if ($this->db_num_rows($get_complaints) > 0) {
            $complaints = [];
            while ($complaint = $this->db_object($get_complaints)) {
                $complaints[] = [
                    'id' => (int) $complaint->id,
                    'user_id' => (int) $complaint->user_id,
                    'name' => $complaint->name,
                    'description' => $complaint->description,
                    'date' => $complaint->date,
                    'prompt' => $complaint->prompt,
                    'slug' => $complaint->slug
                ];
            }
            return $complaints;
        } else {
            return [];
        }
    }

    public function read_by_slug(
        string $slug
    ) {
        $sql = 'SELECT `id`, `user_id`, `name`, `description`, `date`, `prompt`, `slug` FROM `complaints` WHERE `slug` = "' . $slug . '"';
        $get_complaints = $this->db_read($sql);
        if ($this->db_num_rows($get_complaints) > 0) {
            $complaints = $this->db_object($get_complaints);
            $complaints->id = (int) $complaints->id;
            $complaints->userId = (int) $complaints->user_id;
            return $complaints;
        } else {
            return [];
        }
    }

    public function read_by_id(
        int $id
    ) {
        $sql = 'SELECT `id`, `user_id`, `name`, `description`, `date`, `prompt`, `slug` FROM `complaints` WHERE `id` = "' . $id . '"';
        $get_complaints = $this->db_read($sql);
        if ($this->db_num_rows($get_complaints) > 0) {
            $complaints = $this->db_object($get_complaints);
            $complaints->id = (int) $complaints->id;
            $complaints->userId = (int) $complaints->userId;
            return $complaints;
        } else {
            return [];
        }
    }

    public function update(
        int $id,
        string $name,
        string $description,
        string $prompt

    ) {
        $old_complaint = $this->read_by_id($id);
        if ($old_complaint) {
            $order = $this->read_by_id($id);
            $sql = 'UPDATE `complaints` SET `name` = "' . $name . '" , `description` = "' . $description . '" , `prompt` = "' . $prompt . '" ,  `slug` = "' . $this->slugify($id . '-' . $name) . '"  WHERE `id` = "' . $id .  '"';
            if ($this->db_update($sql)) {
                return [
                    'old_complaint' => $old_complaint,
                    'new_complaint' => [
                        'id' => (int) $id,
                        'name' => $name,
                        'description' => $description,
                        'prompt' => $prompt,
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
        $old_complaint = $this->read_by_slug($slug);
        if ($old_complaint) {
            $sql = 'DELETE FROM `complaints` WHERE `slug` = "' . $slug . '"';
            if ($this->db_delete($sql)) {
                return [
                    'old_complaint' => $old_complaint
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

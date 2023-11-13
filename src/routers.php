<?php

if (isset($_GET['url'])) {
    $api = new API_configuration();
    $api->token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : "");
    $user = $api->authorization();

    if ($url[0] == 'me') {
        require_once 'src/services/me.php';
        $authorization = $api->authorization("api");
        $me = new Me();

        if ($url[1] == 'login') {
            if (!$authorization) {
                http_response_code(401);
                exit;
            }
            $response = $me->login(
                addslashes($request->email),
                addslashes($request->password)
            );
            if ($response) {
                $api->generate_user_log(
                    $response['user']['id'],
                    'login'
                );
                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(401);
            }
        } else if ($url[1] == 'logout') {
            $response = $me->logout(
                addslashes($headers['Authorization'])
            );
            if ($response) {
                $api->generate_user_log(
                    $api->user_id,
                    'logout'
                );
                http_response_code(200);
                echo json_encode(['message' => 'Logout successfully']);
            } else {
                http_response_code(401);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid url']);
        }
    } else if ($user) {
        if ($url[0] == 'users') {
            require_once 'src/services/users.php';
            $users = new Users();

            if (!isset($url[1])) { //read
                if (!$api->validate_permissions('users.read')) {
                    http_response_code(401);
                }
                $users->user_id = $user;
                $response = $users->read(
                    (isset($_GET['position']) ? ['position' => addslashes($_GET['position'])] : [])
                );
                if ($response || $response == []) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.read'

                    );
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'create') {
                if (!$api->validate_permissions('users.create')) {
                    http_response_code(401);
                    exit;
                }
                $response = $users->create(
                    addslashes($request->name),
                    addslashes($request->email),
                    addslashes($request->password),
                    addslashes($request->position),
                    (array)$request->permissions

                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.create',
                        json_encode($response)
                    );
                    http_response_code(201);
                    echo json_encode([
                        'message' => 'User created'
                    ]);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'update') {
                if (!$api->validate_permissions('users.update')) {
                    http_response_code(401);
                    exit;
                }
                $response = $users->update(
                    addslashes($request->id),
                    addslashes($request->name),
                    addslashes($request->email),
                    addslashes($request->position),
                    (array)$request->permissions
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.update',
                        json_encode($response)
                    );
                    http_response_code(200);
                    echo json_encode([
                        'message' => 'User updated'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['This id does not exist or invalid URL']);
                }
            } else if ($url[1] == 'delete') {
                if (!$api->validate_permissions('users.delete')) {
                    http_response_code(401);
                    exit;
                }
                $response = $users->delete(
                    addslashes($url[2])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.delete',
                        json_encode($response)
                    );
                    http_response_code(204);
                } else {
                    http_response_code(400);
                }
            } else {
                $response = $users->read_by_slug(
                    addslashes($url[1])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.read_by_slug'

                    );
                    http_response_code(200);
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid URL or user not found']);
                }
            }
        } else if ($url[0] == 'complaints') {
            require_once 'src/services/complaints.php';
            $complaints = new Complaints();

            if (!isset($url[1])) {
                if (!$api->validate_permissions('complaints.read')) {
                    http_response_code(401);
                    exit;
                }
                $complaints->user_id = $user;
                $response = $complaints->read(
                    (isset($_GET['position']) ? ['position' => addslashes($_GET['position'])] : [])
                );
                if ($response || $response == []) {
                    $api->generate_user_log(
                        $api->user_id,
                        'complaints.read'

                    );
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'create') {
                if (!$api->validate_permissions('complaints.create')) {
                    http_response_code(401);
                    exit;
                }
                $response = $complaints->create(
                    $user,
                    addslashes($request->name),
                    addslashes($request->description),
                    addslashes($request->prompt)
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'complaint.create',
                        json_encode($response)
                    );
                    http_response_code(201);
                    echo json_encode([
                        'message' => 'Complaint created'
                    ]);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'update') {
                if (!$api->validate_permissions('complaints.update')) {
                    http_response_code(401);
                    exit;
                }
                $response = $complaints->update(
                    addslashes($request->id),
                    addslashes($request->name),
                    addslashes($request->description),
                    addslashes($request->prompt)
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'complaint.update',
                        json_encode($response)
                    );
                    http_response_code(200);
                    echo json_encode([
                        'message' => 'Complaint updated'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['This id does not exist or invalid URL']);
                }
            } else if ($url[1] == 'delete') {
                if (!$api->validate_permissions('complaints.delete')) {
                    http_response_code(401);
                    exit;
                }
                $response = $complaints->delete(
                    addslashes($url[2])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'complaint.delete',
                        json_encode($response)
                    );
                    http_response_code(204);
                } else {
                    http_response_code(400);
                }
            } else {
                $response = $complaints->read_by_slug(
                    addslashes($url[1])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'complaint.read_by_slug'

                    );
                    http_response_code(200);
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid URL or complaint not found']);
                }
            }
        }
    } else {
        http_response_code(401);
    }
} else {
    echo json_encode([
        'message' => 'server running',
        'version' => VERSION,
    ]);
}

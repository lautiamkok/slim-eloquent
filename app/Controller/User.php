<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Illuminate\Database\Capsule\Manager;
use \Monolog\Logger;
use \Ramsey\Uuid\Uuid;

class User
{
    protected $database;

    public function __construct(
        Manager $database,
        Logger $logger
    ) {
        $this->database = $database;

        // Log anything.
        $logger->addInfo("Logged from user controller");
    }

    public function fetchUsers(Request $request)
    {
        // Columns to select.
        $columns = [
            'uuid',
            'name',
            'created_on',
            'updated_on',
        ];

        // Get user(s).
        $collection = $this->database
            ->table('user')
            ->get($columns);

        // Return the result.
        return $collection;
    }

    public function fetchUser(Request $request, array $args)
    {
        // Columns to select.
        $columns = [
            'uuid',
            'name',
            'created_on',
            'updated_on',
        ];

        // Get user.
        // https://laravel.com/docs/5.5/collections#method-first
        $data = $this->database
            ->table('user')
            ->where('name', '=', $args['name'])
            ->first($columns);

        // Throw error if no result found.
        if (!$data) {
            throw new \Exception('No user found', 400);
        }

        // Return the result.
        return $data;
    }

    public function insertUser(Request $request)
    {
        // Get params and validate them here.
        $name = $request->getParam('name');
        $email = $request->getParam('email');

        // Throw if empty.
        if (!$name) {
            throw new \Exception('$name is empty', 400);
        }

        // Throw if empty.
        if (!$email) {
            throw new \Exception('$email is empty', 400);
        }

        // Create a timestamp.
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();
        // Or:
        // $timestamp = time();

        // Generate a version 1 (time-based) UUID object.
        // https://github.com/ramsey/uuid
        $uuid3 = Uuid::uuid1();
        $uuid = $uuid3->toString();

        // Assuming this is a model in a more complex app system.
        $model = new \stdClass;
        $model->uuid = $uuid;
        $model->name = $name;
        $model->email = $email;
        $model->created_on = $timestamp;

        // Insert user.
        // https://laravel.com/docs/5.5/queries#inserts
        $result = $this->database
            ->table('user')
            ->insert([
                'uuid' => $model->uuid,
                'name' => $model->name,
                'email' => $model->email,
                'created_on' => $model->created_on
            ]);

        // Throw if it fails.
        if (!$result) {
            throw new \Exception('Insert row failed', 400);
        }

        // Return the model if it is OK.
        return $model;
    }

    public function updateUser(Request $request)
    {
        // Get params and validate them here.
        $uuid = $request->getParam('uuid');
        $name = $request->getParam('name');
        $email = $request->getParam('email');

        // Throw if empty.
        if (!$uuid) {
            throw new \Exception('$uuid is empty', 400);
        }

        // Throw if empty.
        if (!$name) {
            throw new \Exception('$name is empty', 400);
        }

        // Throw if empty.
        if (!$email) {
            throw new \Exception('$email is empty', 400);
        }

        // Create a timestamp.
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();

        // Assuming this is a model in a more complex app system.
        $model = new \stdClass;
        $model->uuid = $uuid;
        $model->name = $name;
        $model->email = $email;
        $model->updated_on = $timestamp;

        // Update user(s).
        // https://laravel.com/docs/5.5/queries#updates
        $result = $this->database
            ->table('user')
            ->where("uuid", $model->uuid)
            ->update([
                'name' => $model->name,
                'email' => $model->email,
                'updated_on' => $model->updated_on,
            ]);

        // Throw if it fails.
        if ($result === 0) {
            throw new \Exception('Update row failed', 400);
        }

        // Return the model if it is OK.
        return $model;
    }

    public function deleteUser(Request $request)
    {
        // Get params and validate them here.
        $uuid = $request->getParam('uuid');

        // Throw if empty.
        if (!$uuid) {
            throw new \Exception('$uuid is empty', 400);
        }

        // Assuming this is a model in a more complex app system.
        $model = new \stdClass;
        $model->uuid = $uuid;

        // Delete user.
        // https://laravel.com/docs/5.5/queries#deletes
        $result = $this->database
            ->table('user')
            ->where('uuid', $model->uuid)
            ->delete();

        // Throw if it fails.
        if ($result === 0) {
            throw new \Exception('Delete row failed', 400);
        }

        // Return the model if it is OK.
        return $model;
    }
}

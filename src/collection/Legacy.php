<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 27-03-18
 * Time: 11:49
 */

namespace edwrodrig\contento\collection;


class Legacy
{
    protected $end_point;
    protected $session = '';

    public function __construct(string $end_point) {
        $this->end_point = $end_point;
    }

    public function login(string $user, string $password) {
        $result = file_get_contents($this->end_point, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode([
                    'action' => 'user_login',
                    'username' => $user,
                    'password' => $password
                ])
            ]
        ]));

        $this->session = json_decode($result, true)['data']['session'];
    }

    public function get_data($collection, $class) {
        $result = file_get_contents($this->end_point, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode([
                    'action' => 'contento_data_by_collection',
                    'collection' => $collection,
                    'short' => false,
                    'session' => $this->session
                ])
            ]
        ]));

        $result = json_decode($result, true)['data'];

        $elements = [];

        foreach ( $result as $data) {
            $elements[] = $class::create_from_array($data['data']);
        }
        return $elements;
    }
}
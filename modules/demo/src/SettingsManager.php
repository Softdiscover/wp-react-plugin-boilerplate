<?php

declare(strict_types=1);

namespace Me\Plugin\Demo;

use WP_REST_Response;
use WP_Error;
use WP_REST_Request;

class SettingsManager
{
    protected SettingsRepositoryInterface $repository;

    public function __construct(SettingsRepositoryInterface $repository)
    {
        $this->repository = $repository;
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
    }
    public function registerRestRoutes(): void
    {
        register_rest_route('custom/v1', '/option', [
            'methods' => 'GET',
            'callback' => [$this, 'getSettings'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);

        register_rest_route('custom/v1', '/option', [
            'methods' => 'PUT',
            'callback' => [$this, 'updateSettings'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
            'args' => [
                'data' => [
                    'required' => true,
                    'type' => 'string',
                ]
            ],
        ]);
    }

    public function getSettings(): WP_REST_Response
    {
        return new WP_REST_Response(['data' => $this->repository->read()]);
    }


    public function updateSettings(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $data = sanitize_text_field((string)$request->get_param('data'));
        if (empty($data)) {
            return new WP_Error('empty_data', 'No data provided', ['status' => 400]);
        }
        $updated = $this->repository->update($data);
        return new WP_REST_Response(['success' => $updated]);
    }
}

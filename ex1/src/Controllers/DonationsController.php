<?php

class DonationsController {
    private $service;

    public function __construct() {
        $this->service = new DonationsService();
    }

    /**
     * @return array<string,mixed>|array<string,string>
     */
    public function index(): array {
        try {
            $donations = $this->service->getAll();
            return [
                'status' => 'success',
                'data' => $donations
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param string $id
     * @return array<string,string>|array<string,mixed>
     */
    public function show($id): array {
        try {
            $donation = $this->service->getById($id);
            if (!$donation) {
                return [
                    'status' => 'error',
                    'message' => 'Donation not found'
                ];
            }
            return [
                'status' => 'success',
                'data' => $donation
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param array $request
     * @return array<string,string>|array<string,mixed>
     */
    public function store($request): array {
        try {
            $data = [
                'name' => $request['name'] ?? '',
                'description' => $request['description'] ?? ''
            ];

            $result = $this->service->create($data);

            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Donation created successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to create donation'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param string $id
     * @param array $request
     * @return array<string,string>|array<string,mixed>
     */
    public function update($id, $request): array {
        try {
            $data = [
                'name' => $request['name'] ?? '',
                'description' => $request['description'] ?? ''
            ];

            $result = $this->service->update($id, $data);

            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Donation updated successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to update donation'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param string $id
     * @return array<string,string>|array<string,mixed>
     */
    public function destroy($id): array {
        try {
            $result = $this->service->delete($id);

            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Donation deleted successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to delete donation'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}

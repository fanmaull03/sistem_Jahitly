<?php

namespace App\Services;

use App\Models\Fabric;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;

/**
 * OrderMaterialService - Mengelola material/bahan untuk order
 * 
 * Service ini menangani logika terkait bahan order:
 * - Set sumber bahan (customer vs penjahit)
 * - Pilih fabric yang digunakan
 * - Manage status material (ready vs PO)
 * - Set durasi PO
 * - Mark material as ready
 * - Automatically transition order jika material ready
 * 
 * Principle:
 * - Centralize material logic
 * - Handle dependent updates (when material ready, trigger transition)
 * - Notify customer about material status
 */
class OrderMaterialService
{
    // ──────────────────────────────────────────────────────────
    // Dependencies
    // ──────────────────────────────────────────────────────────

    private OrderBusinessRulesService $businessRules;
    private OrderStatusTransitionService $transition;

    public function __construct()
    {
        $this->businessRules = app(OrderBusinessRulesService::class);
        $this->transition = app(OrderStatusTransitionService::class);
    }

    // ──────────────────────────────────────────────────────────
    // Set Material Source
    // ──────────────────────────────────────────────────────────

    /**
     * Update sumber & status material order
     * 
     * Rules:
     * - material_source: 'customer' atau 'jasa'
     * - Jika 'customer': material_status harus 'ready' (customer bawa sendiri)
     * - Jika 'jasa': pilih fabric, status otomatis sesuai fabric availability
     * 
     * @param Order $order Order yang diupdate
     * @param string $source Sumber material (customer|jasa)
     * @param int|null $fabricId Fabric ID jika sumber 'jasa'
     * @param int|null $poDays Durasi PO jika fabric PO
     * @param int $adminId Admin yang melakukan action
     * @return array{success: bool, message: string, errors?: list<string>}
     */
    public function setMaterialSource(
        Order $order,
        string $source,
        ?int $fabricId = null,
        ?int $poDays = null,
        int $adminId = 0
    ): array
    {
        // Validasi input
        $validation = $this->validateMaterialInput($source, $fabricId, $poDays);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => 'Material input tidak valid.',
                'errors' => $validation['errors'],
            ];
        }

        // Prepare update data
        $updateData = [
            'material_source' => $source,
        ];

        // Jika customer bawa sendiri
        if ($source === 'customer') {
            $updateData['material_status'] = 'ready';
            $updateData['fabric_id'] = null;
            $updateData['po_days'] = null;
        }
        // Jika dari penjahit
        else {
            $fabric = Fabric::find($fabricId);
            if (!$fabric) {
                return [
                    'success' => false,
                    'message' => 'Fabric tidak ditemukan.',
                ];
            }

            $updateData['fabric_id'] = $fabric->id;

            // Tentukan status berdasarkan ketersediaan fabric
            if ($fabric->stock_status === 'tersedia') {
                $updateData['material_status'] = 'ready';
                $updateData['po_days'] = null;
            } else {
                $updateData['material_status'] = 'po';
                $updateData['po_days'] = $poDays ?? $fabric->po_days ?? 7;
            }
        }

        // Update order
        $order->update($updateData);

        // Recalculate price karena material berpengaruh ke harga
        $estimation = $this->businessRules->calculateEstimation($order);
        $order->update([
            'estimated_price' => $estimation['estimated_price'],
            'estimated_finish_date' => $estimation['estimated_finish_date'],
        ]);

        // Notify customer
        $this->notifyCustomerAboutMaterial($order);

        // Check: jika material ready & order dalam status menunggu_bahan, auto-transition
        if ($updateData['material_status'] === 'ready' && $order->status === 'menunggu_bahan') {
            $this->transition->moveToQueue($order, $adminId);
        }

        return [
            'success' => true,
            'message' => 'Material berhasil diupdate.',
        ];
    }

    /**
     * Validasi input material
     * 
     * @param string $source Sumber material
     * @param int|null $fabricId Fabric ID (required jika source='jasa')
     * @param int|null $poDays PO days
     * @return array{valid: bool, errors?: list<string>}
     */
    private function validateMaterialInput(
        string $source,
        ?int $fabricId = null,
        ?int $poDays = null
    ): array
    {
        $errors = [];

        // Validasi source
        if (!in_array($source, ['customer', 'jasa'], true)) {
            $errors[] = 'Sumber material harus "customer" atau "jasa".';
        }

        // Jika jasa, harus ada fabric
        if ($source === 'jasa') {
            if (!$fabricId) {
                $errors[] = 'Pilih fabric untuk material dari penjahit.';
            } else {
                $fabric = Fabric::find($fabricId);
                if (!$fabric) {
                    $errors[] = 'Fabric tidak ditemukan.';
                }
            }
        }

        // Validasi PO days jika ada
        if ($poDays !== null) {
            if ($poDays < 3 || $poDays > 30) {
                $errors[] = 'Durasi PO harus antara 3-30 hari.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Mark Material as Ready
    // ──────────────────────────────────────────────────────────

    /**
     * Tandai material sudah ready (jika sebelumnya PO)
     * 
     * Pre-condition: material_status harus 'po'
     * 
     * Jika order dalam status menunggu_bahan, auto-transition ke queue
     * 
     * @param Order $order Order yang di-update
     * @param int $adminId Admin yang melakukan action
     * @return array{success: bool, message: string}
     */
    public function markMaterialReady(Order $order, int $adminId): array
    {
        // Validasi status material
        if ($order->material_status !== 'po') {
            return [
                'success' => false,
                'message' => 'Material ini tidak dalam status PO, sudah ready.',
            ];
        }

        // Update material status
        $order->update(['material_status' => 'ready']);

        // Notify
        $this->notifyCustomerAboutMaterial($order);

        // Auto-transition jika dalam menunggu_bahan
        if ($order->status === 'menunggu_bahan') {
            $this->transition->moveToQueue($order, $adminId);
        }

        return [
            'success' => true,
            'message' => 'Material ditandai ready. Order siap masuk antrian produksi.',
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Get Material Information
    // ──────────────────────────────────────────────────────────

    /**
     * Cek apakah material sudah ready
     * 
     * @param Order $order
     * @return bool
     */
    public function isMaterialReady(Order $order): bool
    {
        return $order->material_status === 'ready';
    }

    /**
     * Cek apakah material dalam status PO
     * 
     * @param Order $order
     * @return bool
     */
    public function isMaterialPo(Order $order): bool
    {
        return $order->material_status === 'po';
    }

    /**
     * Dapatkan informasi fabric yang digunakan
     * 
     * @param Order $order
     * @return array{source: string, fabric?: array, po_days?: int}
     */
    public function getMaterialInfo(Order $order): array
    {
        $order->loadMissing('fabric');

        $info = [
            'source' => $order->material_source ?? 'unknown',
            'status' => $order->material_status ?? 'unknown',
        ];

        if ($order->fabric) {
            $info['fabric'] = [
                'name' => $order->fabric->name,
                'type' => $order->fabric->type,
                'stock_status' => $order->fabric->stock_status,
                'price_per_meter' => $order->fabric->price_per_meter,
            ];
        }

        if ($order->po_days) {
            $info['po_days'] = $order->po_days;
        }

        return $info;
    }

    /**
     * Get fabric options untuk dropdown
     * 
     * Hanya ambil fabric yang active
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableFabrics()
    {
        return Fabric::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // ──────────────────────────────────────────────────────────
    // Notification
    // ──────────────────────────────────────────────────────────

    /**
     * Kirim notifikasi ke customer tentang status material
     * 
     * @param Order $order
     * @return void
     */
    private function notifyCustomerAboutMaterial(Order $order): void
    {
        $customer = $order->customer;
        if (!$customer) {
            return;
        }

        $source = $order->material_source === 'customer' ? 'customer' : 'penjahit';
        $status = $order->material_status === 'ready' ? 'sudah siap' : 'sedang di-PO';

        $message = 'Bahan untuk pesanan #' . $order->order_number . 
                   ' (dari ' . $source . ') ' . $status . '.';

        $customer->notify(new OrderStatusUpdated($order, $message));
    }
}

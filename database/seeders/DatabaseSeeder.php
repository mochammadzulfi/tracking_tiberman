<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Group, Fleet, Shipment, TrackingPoint, DeliveryProof, AuditLog};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat beberapa grup
        $groups = Group::factory(3)->create();

        // Buat user sesuai role
        $creator = User::factory()->create([
            'name' => 'Creator User',
            'email' => 'creator@test.com',
            'role' => 'creator',
            'group_id' => $groups->random()->id,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'group_id' => $groups->random()->id,
        ]);

        $superuser = User::factory()->create([
            'name' => 'Super User',
            'email' => 'superuser@test.com',
            'role' => 'superuser',
            'group_id' => $groups->random()->id,
        ]);

        // Tambahkan armada
        $fleets = Fleet::factory(5)->create();

        // Tambahkan shipment
        $shipments = Shipment::factory(10)->create([
            'assigned_fleet_id' => $fleets->random()->id,
            'assigned_driver_id' => $admin->id,
            'created_by_user_id' => $creator->id,
        ]);

        // Tracking point (simulasi update lokasi)
        foreach ($shipments as $shipment) {
            $shipment->trackingPoints()->createMany([
                [
                    'fleet_id' => $shipment->assigned_fleet_id,
                    'driver_id' => $shipment->assigned_driver_id,
                    'lat' => -7.2575 + fake()->randomFloat(4, 0.001, 0.01),
                    'lng' => 112.7521 + fake()->randomFloat(4, 0.001, 0.01),
                    'source' => 'GPS',
                    'ip_address' => fake()->ipv4(),
                    'ip_geo' => ['country' => 'ID', 'city' => 'Surabaya'],
                    'is_ip_mismatch' => false,
                ]
            ]);
        }

        // Bukti pengiriman
        foreach ($shipments as $shipment) {
            if ($shipment->status === 'delivered') {
                $shipment->deliveryProofs()->create([
                    'photo_path' => 'proofs/example.jpg',
                    'receiver_name' => fake()->name(),
                    'received_at' => now(),
                    'uploaded_by_user_id' => $admin->id,
                ]);
            }
        }

        // Audit logs
        foreach ($shipments as $shipment) {
            $shipment->auditLogs()->create([
                'user_id' => $creator->id,
                'action' => 'CREATE_SHIPMENT',
                'meta' => ['code' => $shipment->code],
                'ip_address' => fake()->ipv4(),
            ]);
        }
    }
}

<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;

class NotificationService
{
    /**
     * Kirim notifikasi in-app ke semua pengguna berdasarkan nama role.
     */
    public static function sendToRole(string $roleName, string $title, string $message, ?string $linkTarget = null, ?string $laporanId = null): void
    {
        $users = User::whereHas('role', function ($q) use ($roleName) {
            $q->where('nama_role', $roleName);
        })->get();

        foreach ($users as $user) {
            Notifikasi::create([
                'id_user' => $user->id_user,
                'id_laporan' => $laporanId,
                'judul_notifikasi' => $title,
                'pesan_notifikasi' => $message,
                'link_target' => $linkTarget,
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Kirim notifikasi in-app ke satu pengguna tertentu.
     */
    public static function sendToUser(string $userId, string $title, string $message, ?string $linkTarget = null, ?string $laporanId = null): void
    {
        Notifikasi::create([
            'id_user' => $userId,
            'id_laporan' => $laporanId,
            'judul_notifikasi' => $title,
            'pesan_notifikasi' => $message,
            'link_target' => $linkTarget,
            'is_read' => 0,
            'created_at' => now(),
        ]);
    }
}

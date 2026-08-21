<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Ambil data notifikasi pengguna yang sedang login (JSON).
     */
    public function getNotifications()
    {
        $userId = auth()->id();

        $unreadCount = Notifikasi::where('id_user', $userId)
            ->where('is_read', 0)
            ->count();

        $notifications = Notifikasi::where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id_notifikasi' => $n->id_notifikasi,
                    'judul_notifikasi' => $n->judul_notifikasi,
                    'pesan_notifikasi' => $n->pesan_notifikasi,
                    'is_read' => (int) $n->is_read,
                    'link_target' => $n->link_target ?: ($n->id_laporan ? route('laporan.show', $n->id_laporan) : '#'),
                    'time_ago' => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca dan buka link target.
     */
    public function read($id)
    {
        $userId = auth()->id();

        $notif = Notifikasi::where('id_notifikasi', $id)
            ->where('id_user', $userId)
            ->firstOrFail();

        $notif->update(['is_read' => 1]);

        $targetUrl = $notif->link_target;
        if (!$targetUrl && $notif->id_laporan) {
            $targetUrl = route('laporan.show', $notif->id_laporan);
        }

        return redirect()->to($targetUrl ?: route('home'));
    }

    /**
     * Tandai semua notifikasi pengguna sebagai dibaca.
     */
    public function markAllAsRead(Request $request)
    {
        $userId = auth()->id();

        Notifikasi::where('id_user', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}

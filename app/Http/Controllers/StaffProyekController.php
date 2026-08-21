<?php

namespace App\Http\Controllers;

/**
 * @deprecated Proyek Perbaikan telah dihapus dari tahapan bisnis SI-SARPRAS.
 */
class StaffProyekController extends Controller
{
    public function index()
    {
        return redirect()->route('staff.rab.index');
    }
}

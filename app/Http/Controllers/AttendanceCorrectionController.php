<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceCorrectionController extends Controller
{
    public function index()
    {
        $requests = AttendanceCorrectionRequest::where('user_id', Auth::user()->id)
            ->latest()
            ->paginate(10);

        return view('attendances.correction', ['requests' => $requests]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        if (! $request->time_in && ! $request->time_out) {
            return redirect()->back()->withInput()
                ->withErrors(['time_in' => 'Isi minimal salah satu jam masuk atau jam keluar.']);
        }

        $exists = AttendanceCorrectionRequest::where('user_id', Auth::user()->id)
            ->whereDate('date', $request->date)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()
                ->withErrors(['date' => 'Sudah ada pengajuan koreksi yang menunggu untuk tanggal ini.']);
        }

        AttendanceCorrectionRequest::create([
            'user_id' => Auth::user()->id,
            'date' => Carbon::parse($request->date)->toDateString(),
            'time_in' => $request->time_in ? $request->time_in.':00' : null,
            'time_out' => $request->time_out ? $request->time_out.':00' : null,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('attendance-correction')
            ->with('flash.banner', 'Pengajuan koreksi dikirim. Menunggu persetujuan HRD.');
    }
}

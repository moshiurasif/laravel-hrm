<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function markAttendance(Request $request)
    {
        $user = auth()->user(); // Assuming you're using authentication

        $currentDate = Carbon::now()->toDateString();
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $currentDate)
            ->first();

        if ($existingAttendance) {
            // Attendance already marked for the day
            return response()->json(['message' => 'Attendance already marked for today.'], 400);
        }

        $attendance = new Attendance([
            'user_id' => $user->id,
            'date' => $currentDate,
            'clock_in' => Carbon::now()->toTimeString(),
        ]);

        $attendance->save();

        return response()->json(['message' => 'Attendance marked successfully.']);
    }

    public function markClockOut(Request $request)
    {
        $user = auth()->user();

        $currentDate = Carbon::now()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $currentDate)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found for today.'], 404);
        }

        $attendance->clock_out = Carbon::now()->toTimeString();
        $attendance->save();

        return response()->json(['message' => 'Clock out marked successfully.']);
    }
}

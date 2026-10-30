<?php

namespace App\Livewire;

use App\ExtendedCarbon;
use App\Models\Attendance;
use App\Models\Barcode;
use App\Models\LeaveQuota;
use App\Models\Shift;
use App\Services\AttendanceCalculator;
use App\Services\ShiftSchedule;
use Ballen\Distical\Calculator as DistanceCalculator;
use Ballen\Distical\Entities\LatLong;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ScanComponent extends Component
{
    public ?Attendance $attendance = null;

    public $shift_id = null;

    public $shifts = null;

    public ?array $currentLiveCoords = null;

    public string $successMsg = '';

    public bool $isAbsence = false;

    public function scan(string $barcodeValue)
    {
        if (is_null($this->currentLiveCoords)) {
            return __('Invalid location');
        }

        if (is_null($this->shift_id)) {
            return __('Invalid shift');
        }

        /** @var Barcode|null $barcode */
        $barcode = Barcode::firstWhere('value', $barcodeValue);
        if (! Auth::check() || ! $barcode) {
            return 'Invalid barcode';
        }

        $user = Auth::user();

        $rateKey = 'scan-'.$user->id;
        if (RateLimiter::tooManyAttempts($rateKey, 20)) {
            return __('Too many scan attempts, please try again in a minute');
        }
        RateLimiter::hit($rateKey, 60);

        if ($barcode->branch_id && $user->branch_id && $barcode->branch_id !== $user->branch_id) {
            return __('Barcode is not for your branch');
        }

        $barcodeLocation = new LatLong($barcode->latLng['lat'], $barcode->latLng['lng']);
        $userLocation = new LatLong($this->currentLiveCoords[0], $this->currentLiveCoords[1]);

        if (($distance = $this->calculateDistance($userLocation, $barcodeLocation)) > $barcode->radius) {
            return __('Location out of range').": {$distance}"."m. Max: {$barcode->radius}"."m";
        }

        $openAttendance = $this->findOpenAttendance($user->id);

        if ($openAttendance) {
            $attendance = $this->checkOut($openAttendance);
            $this->successMsg = __('Attendance Out Successful');
        } else {
            $recentCheckout = Attendance::where('user_id', $user->id)
                ->whereNotNull('time_out')
                ->latest('updated_at')
                ->first();

            if ($recentCheckout && $recentCheckout->updated_at?->diffInMinutes(now()) < 10) {
                return __('You have already checked out');
            }

            $attendance = $this->createAttendance($barcode);
            $this->successMsg = __('Attendance In Successful');
        }

        if ($attendance) {
            $this->setAttendance($attendance->fresh());
            Attendance::clearUserAttendanceCache(Auth::user(), Carbon::parse($attendance->date));

            return true;
        }
    }

    public function calculateDistance(LatLong $a, LatLong $b)
    {
        $distanceCalculator = new DistanceCalculator($a, $b);
        $distanceInMeter = floor($distanceCalculator->get()->asKilometres() * 1000); // convert to meters

        return $distanceInMeter;
    }

    /** @return Attendance */
    public function createAttendance(Barcode $barcode)
    {
        $now = Carbon::now();
        $date = $now->copy();

        /** @var Shift $shift */
        $shift = Shift::findOrFail($this->shift_id);

        if (ShiftSchedule::isCrossMidnight($shift) && $now->format('H:i:s') <= substr((string) $shift->end_time, 0, 8)) {
            $date->subDay();
        }

        $start = ShiftSchedule::startAt($shift, $date);
        $status = $now->greaterThan($start->copy()->addMinutes((int) $shift->late_tolerance_minutes)) ? 'late' : 'present';

        $attendance = Attendance::create([
            'user_id' => Auth::user()->id,
            'barcode_id' => $barcode->id,
            'date' => $date->toDateString(),
            'time_in' => $now->format('H:i:s'),
            'time_out' => null,
            'shift_id' => $shift->id,
            'latitude' => doubleval($this->currentLiveCoords[0]),
            'longitude' => doubleval($this->currentLiveCoords[1]),
            'status' => $status,
            'note' => null,
            'attachment' => null,
        ]);

        return app(AttendanceCalculator::class)->apply($attendance);
    }

    /** @return Attendance */
    protected function checkOut(Attendance $attendance)
    {
        $now = Carbon::now();
        $shift = $attendance->shift;
        $status = $attendance->status;

        if ($shift) {
            $end = ShiftSchedule::endAt($shift, $attendance->date);
            $status = $now->lessThan($end)
                ? 'incomplete'
                : (app(AttendanceCalculator::class)->lateMinutes($attendance) > 0 ? 'late' : 'present');
        }

        $attendance->update([
            'time_out' => $now->format('H:i:s'),
            'status' => $status,
        ]);

        return app(AttendanceCalculator::class)->apply($attendance);
    }

    protected function findOpenAttendance(string $userId): ?Attendance
    {
        return Attendance::where('user_id', $userId)
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->whereIn('status', ['present', 'late', 'incomplete'])
            ->whereDate('date', '>=', now()->subDay()->toDateString())
            ->latest('date')
            ->latest('id')
            ->first();
    }

    protected function setAttendance(Attendance $attendance)
    {
        $this->attendance = $attendance;
        $this->shift_id = $attendance->shift_id;
        $this->isAbsence = ! in_array($attendance->status, ['present', 'late', 'incomplete']);
    }

    public function getAttendance()
    {
        if (is_null($this->attendance)) {
            return null;
        }

        return [
            'time_in' => $this->attendance?->time_in,
            'time_out' => $this->attendance?->time_out,
        ];
    }

    public function mount()
    {
        $this->shifts = Shift::all();

        $attendance = $this->findOpenAttendance(Auth::user()->id)
            ?? Attendance::where('user_id', Auth::user()->id)
                ->where('date', date('Y-m-d'))
                ->first();

        if ($attendance) {
            $this->setAttendance($attendance);

            return;
        }

        if ($this->shifts->isEmpty()) {
            return;
        }

        // get closest shift from current time
        $closest = ExtendedCarbon::now()
            ->closestFromDateArray($this->shifts->pluck('start_time')->toArray());

        $this->shift_id = $this->shifts
            ->where(fn (Shift $shift) => $shift->start_time == $closest->format('H:i:s'))
            ->first()->id;
    }

    public function render()
    {
        $user = Auth::user();
        $branchId = $user?->branch_id;

        $scanBarcodes = Barcode::query()
            ->when($branchId, fn ($query) => $query->where(
                fn ($query) => $query->where('branch_id', $branchId)->orWhereNull('branch_id')
            ))
            ->get(['id', 'name', 'value', 'latitude', 'longitude', 'radius']);

        $quota = LeaveQuota::firstOrCreate(
            ['user_id' => $user?->id, 'year' => now()->year],
            ['quota_days' => 12]
        );

        return view('livewire.scan', [
            'scanBarcodes' => $scanBarcodes,
            'quotaRemaining' => $quota->remaining_days,
        ]);
    }
}

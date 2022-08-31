<?php

namespace App\Services;

use App\Event;
use App\Room;
use Carbon\Carbon;

class EventService
{
    public function createRecurringEvents($requestData)
    {
        $recurringUntil            = Carbon::parse($requestData['recurring_until'])->setTime(23, 59, 59);
        $requestData['start_time'] = Carbon::parse($requestData['start_time'])->addWeek();
        $requestData['end_time']   = Carbon::parse($requestData['end_time'])->addWeek();

        while ($requestData['end_time']->lte($recurringUntil)) {
            $this->createEvent($requestData);
            $requestData['start_time']->addWeek();
            $requestData['end_time']->addWeek();
        }
    }

    public function createEvent($requestData)
    {
        $requestData['start_time'] = $requestData['start_time']->format('Y-m-d H:i');
        $requestData['end_time']   = $requestData['end_time']->format('Y-m-d H:i');

        return Event::create($requestData);
    }

    public function isRoomTaken($requestData)
    {
        $recurringUntil = Carbon::parse($requestData['recurring_until'])->setTime(23, 59, 59);
        $start_time     = Carbon::parse($requestData['start_time']);
        $end_time       = Carbon::parse($requestData['end_time']);
        $events         = Event::where('room_id', $requestData['room_id'])
            ->where(function($query) use ($start_time, $end_time) {
                $query->where(function($query1) use ($start_time) {
                        $query1->where('start_time', '<', $start_time)->where('end_time', '>', $start_time);
                    })->orWhere(function($query2) use ($end_time) {
                        $query2->where('start_time', '<', $end_time)->where('end_time', '>', $end_time);
                    })->orWhere(function($query3) use ($start_time, $end_time) {
                        $query3->where('start_time', '<', $start_time)->where('end_time', '>', $end_time);
                    });
            })->get();

        if ($events->isEmpty()) {
            return true;
        }
        // dd($events->toSql(), $events->getBindings());

        return false;
    }

    public function chargeHourlyRate($requestData, Room $room)
    {
        if (!$room->hourly_rate) {
            return true;
        }

        $recurringUntil = Carbon::parse($requestData['recurring_until'])->setTime(23, 59, 59);
        $start_time     = Carbon::parse($requestData['start_time']);
        $end_time       = Carbon::parse($requestData['end_time']);
        $hours          = (int) ceil($end_time->diffInMinutes($start_time) / 60);
        $totalHours     = 0;

        do {
            $totalHours += $hours;

            $start_time->addWeek();
            $end_time->addWeek();
        } while ($end_time->lte($recurringUntil));

        return auth()->user()->chargeCredits($totalHours, $room);
    }
}

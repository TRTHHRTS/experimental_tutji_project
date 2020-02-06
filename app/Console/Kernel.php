<?php
namespace App\Console;

use App\Http\NotifyController;
use App\Mailable\ReserveMail;
use App\Models\Lesson;
use App\Models\ReservedLesson;
use App\Models\ReservedTime;
use App\NotifyEnum;
use App\StatusEnum;
use App\ReserveStatusEnum;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Log;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule){
        Log::info("Инициализация запланированных задач");
        //return;
        $schedule->call(function () {
            Log::info("Начало задачи актуализации статусов занятий");
            $lessons = Lesson::whereStatus(StatusEnum::ACTIVE)->get();
            Log::info("Количество активных занятий: ". count($lessons));
            foreach ($lessons as $lesson) {
                $isFinished = true;
                foreach ($lesson->reservedTimes as $time) {
                    $lessonDateTime = Carbon::parse($time->lesson_date . ' ' . $time->lesson_time . ':00');
                    $durationHours = intval(substr($time->duration, 0, 2));
                    $durationMinutes = intval(substr($time->duration, 2, 4));
                    $lessonDateTime->addHours($durationHours);
                    $lessonDateTime->addMinutes($durationMinutes);
                    if ($lessonDateTime->isFuture()) {
                        $isFinished = false;
                        break;
                    }
                }
                if ($isFinished) {
                    Log::info("Занятия с id=$lesson->id завершено!");
                    $lesson->status = StatusEnum::FINISHED;
                    $lesson->save();
                }
            }
            Log::info("Конец задачи актуализации статусов занятий");
        })->everyMinute()->evenInMaintenanceMode();

        $schedule->call(function () {
            Log::info("Начало задачи актуализации времени резервирования");
            $times = ReservedTime::where('closed', false)->get();
            foreach ($times as $time) {
                $lessonDateTime = Carbon::parse($time->lesson_date . ' ' . $time->lesson_time . ':00');
                $durationHours = intval(substr($time->duration, 0, 2));
                $durationMinutes = intval(substr($time->duration, 2, 4));
                $lessonDateTime->addHours($durationHours);
                $lessonDateTime->addMinutes($durationMinutes);
                if ($lessonDateTime->isPast()) {
                    Log::info("Время резервирования с id=$time->id закрывается.");
                    $time->closed = true;
                    $time->save();
                }
            }
            Log::info("Конец задачи актуализации времени резервирования");
        })->everyMinute()->evenInMaintenanceMode();

        $schedule->call(function () {
            Log::info("Начало задачи актуализации статусов резервирования");
            $today = Carbon::now();
            $openedReserves = ReservedLesson::whereClosed(false)->get();
            Log::info('Всего открытых записей: ' . count($openedReserves));
            foreach($openedReserves as $res) {
                $time = $res->reserveTime;
                $lessonDateTime = $time->getCarbonResTime();
                $durationHours = intval(substr($time->duration, 0, 2));
                $durationMinutes = intval(substr($time->duration, 2, 4));
                $lessonDateTime->addHours($durationHours);
                $lessonDateTime->addMinutes($durationMinutes);
                if ($lessonDateTime->isPast()) {
                    switch ($res->reserve_status) {
                        case ReserveStatusEnum::NEW_RESERVE:
                            $res->reserve_status = ReserveStatusEnum::COMPLETED;
                            $res->save();
                            Log::info("Запись с id=$res->id завершена, запись помечена как <завершена>");
                            break;
                        case ReserveStatusEnum::TEACHER_NOT_COME:
                        case ReserveStatusEnum::OTHER_REASONS:
                        case ReserveStatusEnum::COMPLETED:
                            if ($lessonDateTime->diffInDays($today, false) >= 7) {
                                $res->reason = ReserveStatusEnum::COMPLETED;
                                $res->closed = true;
                                $res->save();
                                Log::info("Записи с id=$res->id больше 7 дней, запись закрывается");
                            }
                            break;
                    }
                } else {
                    // Вышлем письмо тем пользователям, у которых занятие менее, чем через сутки начинается
                    if ($lessonDateTime->diffInDays($today, false) == 0 && $res->user->settings->notify_scheduled_lessons) {
                        $hasReserve = NotifyController::hasReserveNotify(NotifyEnum::NOTIFY_RESERVED, $res->user_id, $res->id) > 0;
                        if (!$hasReserve) {
                            NotifyController::addNotifyRecord($res->user_id, NotifyEnum::NOTIFY_RESERVED, $res->id);
                        }
                    }
                }
            }
            Log::info('Конец задачи по актуализации статусов резервирования');
        })->everyMinute()->evenInMaintenanceMode();

        $schedule->call(function () {
            Log::info("Начало задачи отправки уведомлений!");
            $records = DB::table('notify_data')->where('sent', 0)->get()->toArray();
            foreach ($records as $record) {
                if ($record->type == NotifyEnum::NOTIFY_RESERVED) {
                    Log::info("Отправка уведомления о скором занятии пользователю с ID=" . $record->user_id);
                    $res = ReservedLesson::find($record->param);
                    if (!is_null($res->user->email) && $res->user->email_verified) {
                        $reserveMail = new ReserveMail(
                            $res->lesson_name,
                            $res->reserveTime->lesson_date,
                            $res->reserveTime->lesson_time,
                            $res->lesson->user->name,
                            $res->teacher_phone);
                        Mail::to($res->user->email)->send($reserveMail);
                        Log::info("Отправлено пользователю с ID=".$res->user->id.", занятие: ".$res->lesson_name);
                    }
                }
                DB::table('notify_data')->where('id', $record->id)->update(['sent' => 1]);
            }
            Log::info("Конец задачи отправки уведомлений!");
        })->everyMinute()->evenInMaintenanceMode();

        Log::info("Конец инициализации запланированных задач");
    }

    protected function commands(){
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

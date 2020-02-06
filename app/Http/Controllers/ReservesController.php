<?php
namespace App\Http\Controllers;

use App\Models\ReservedLesson;
use App\Models\LessonMessages;
use App\ReserveStatusEnum;
use App\StatusEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservesController extends Controller
{
    public function getHistory(Request $request) {
        if (!$request->has("reserveId")) {
            return response(__('errors.incorrectRequest'), 500);
        }
        $reserveId = intval($request->get("reserveId"));
        $result = LessonMessages::where('reserve_id', $reserveId)
            ->with(['user:id,name', 'user.rights:user_id,moder_rights,admin_rights'])
            ->orderBy("updated_at")->get();
        return response($result);
    }

    public function adminAnswer(Request $request) {
        if (!$request->has("reserveId")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        if (!$request->has("newStatus")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        if (!$request->has("message")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        $reserve = ReservedLesson::find($request->get("reserveId"));
        $reserve->reserve_status = intval($request->get("newStatus"));
        $reserve->save();
        $mes = new LessonMessages();
        $mes->reserve_id = $reserve->id;
        $mes->user_id = Auth::id();
        $mes->message = $request->get("message");
        $mes->save();
        return response(["status" => 0]);
    }

    public function closeReserve(Request $request) {
        if (!$request->has("reserveId")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        if (!$request->has("userId")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        if (!$request->has("message")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        if (empty($request->get("message"))) {
            return response(__('errors.emptyReason'), 422);
        }
        $reserve = ReservedLesson::find($request->get("reserveId"));
        $reserve->closed = true;
        $reserve->reason = $request->get("message");
        $reserve->reserve_status = ReserveStatusEnum::OTHER_REASONS;
        $reserve->save();
        return response(["status" => 0]);
    }

    public function getLastResConv(Request $request) {
        if (!$request->has("reserveId")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        $reserve = ReservedLesson::find($request->get("reserveId"));
        $status = $reserve->reserve_status;
        if ($status == ReserveStatusEnum::AWAITING_PUPIL) {
            if ($reserve->user_id !== Auth::id()) {
                return response(__('errors.wrongReserve'), 422);
            }
        } else if($status == ReserveStatusEnum::AWAITING_TEACHER) {
            if ($reserve->lesson->user_id !== Auth::id()) {
                return response(__('errors.wrongReserve'), 422);
            }
        } else {
            return response(__('errors.incorrectRequest'), 422);
        }
        $lessonMessage = LessonMessages::whereReserveId($request->get("reserveId"))->latest()->limit(1)->first();
        return $lessonMessage->message;
    }

    public function initUserNotCome(Request $request) {
        if (!$request->has("reserveId")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        $resLesson = ReservedLesson::find($request->get("reserveId"));
        if ($resLesson->user_id !== Auth::id()) {
            return response(__('errors.notYourReserve'), 422);
        }
        if ($resLesson->reserve_status !== ReserveStatusEnum::COMPLETED || $resLesson->closed === true) {
            return response(__('errors.incorrectLessonStatus'), 422);
        }
        $resLesson->reserve_status = ReserveStatusEnum::TEACHER_NOT_COME;
        $resLesson->save();
        return response(['status'=>0]);
    }

    /** Поставить статус о несостоявшемся занятии, закрыть при необходимости */
    public function reserveNotPassed() {
        $data = $_POST;
        $reason = isset($_POST['reason']) && !empty($_POST['reason']) ? $_POST['reason'] : __('common.autoClosed');
        $reserveId = intval($data['reserveId']);
        $isClosed = boolval($data['isClosed']);
        $resLes = ReservedLesson::find($reserveId);
        $resLes->reserve_status = ReserveStatusEnum::OTHER_REASONS;
        $resLes->reason = $reason;
        $resLes->closed = $isClosed;
        $resLes->save();
        return response(['status'=>0]);
    }

    public function confirmNotCome(Request $request) {
        if (!$request->has("reserveId")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        $resLesson = ReservedLesson::find($request->get("reserveId"));
        if ($resLesson->lesson->user_id !== Auth::id()) {
            return response(__('errors.notYourLesson'), 422);
        }
        if($resLesson->reserve_status !== ReserveStatusEnum::TEACHER_NOT_COME || $resLesson->closed === true) {
            return response(__('errors.incorrectLessonStatus'), 422);
        }
        $resLesson->closed = true;
        $resLesson->save();
        return response(['status'=>0]);
    }

    /**
     * Общий метод для ученика и преподавателя
     * Отметить, что пользователь не согласен с тем, что противоположная сторона указала, что он не пришел на занятие
     */
    public function userNotAgree(Request $request) {
        if (!$request->has("reserveId")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        if (!$request->has("message")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        if (empty($request->get("message"))) {
            return response(__('errors.emptyMessage'), 422);
        }
        $resLesson = ReservedLesson::find($request->get("reserveId"));
        if ($resLesson->lesson->user_id !== Auth::id()) {
            return response(__('errors.notYourLesson'), 422);
        }
        if ($resLesson->reserve_status !== ReserveStatusEnum::TEACHER_NOT_COME) {
            return response(__('errors.incorrectLessonStatus'), 422);
        }
        $resLesson->reserve_status = ReserveStatusEnum::AWAITING_ADMINISTRATION;
        $resLesson->save();

        $mes = new LessonMessages();
        $mes->reserve_id = $resLesson->id;
        $mes->user_id = Auth::id();
        $mes->message = $request->get("message");
        $mes->save();
        return response(['status'=>0]);
    }

    /**
     * Отменить запись:
     * для ученика - отменить запись на занятие
     * для препода - отменить запись ученика или отменить запись всех учеников на это время и закрыть время
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function cancelReserve() {
        $data = $_POST;
        $isTeacher = 'true' === $data['isTeacher'];
        $reserveId = intval($data['reserveId']);
        $resLesson = ReservedLesson::find($reserveId);
        if ($resLesson->reserve_status !== ReserveStatusEnum::NEW_RESERVE) {
            return response(__('errors.incorrectLessonStatus'), 500);
        }
        $daysBeforeLesson = Carbon::createFromFormat('Y-m-d', $resLesson->reserveTime->lesson_date)->diffInDays(Carbon::now());
        if ($daysBeforeLesson <= 0) {
            return response(["status"=>4, "message"=>__("errors.cantCancelReserve")]);
        }
        if ($isTeacher) {
            $lesson = $resLesson->lesson;
            if ($lesson->user_id !== Auth::id()) {
                return response(__('errors.notYourLesson'), 500);
            }
            $cancelAll = 'true' === $data['cancelAll'];
            $newStatus = $cancelAll ? ReserveStatusEnum::TEACHER_CANCEL_LESSON : ReserveStatusEnum::TEACHER_CANCEL_RECORD;
            if ($cancelAll) {
                $resTime = $resLesson->reserveTime;
                foreach ($resTime->reserves as $reserve) {
                    $reserve->reserve_status = $newStatus;
                    $reserve->closed = true;
                    $reserve->save();
                }
                $resTime->closed = true;
                $resTime->save();
            } else {
                $resLesson->reserve_status = $newStatus;
                $resLesson->save();
            }
            // меняем статус занятия на Завершенное, так как все записи мы отменили.
            $resLesson->lesson->status = StatusEnum::FINISHED;
            $resLesson->lesson->save();
        } else {
            if ($resLesson->user_id !== Auth::id()) {
                return response(__('errors.notYourReserve'), 500);
            }
            $resLesson->reserve_status = ReserveStatusEnum::PUPIL_CANCEL_RECORD;
        }
        // сразу закрываем запись, нечего тут обсуждать.
        $resLesson->closed = true;
        $resLesson->save();
        return response(['status'=>0]);
    }

    public function answerToAdministration(Request $request): \Illuminate\Http\Response {
        $checkResponse = $this->commonCheckAnswerReserveParams($request);
        if ($checkResponse != null) {
            return $checkResponse;
        }
        $resLesson = ReservedLesson::find($request->get("reserveId"));
        if ($request->has("contact")) {
            $checkResponse = $this->checkForContact($resLesson);
        } else {
            $checkResponse = $this->checkReserveForAnswer($resLesson);
        }
        if ($checkResponse != null) {
            return $checkResponse;
        }
        $lessonMessage = new LessonMessages();
        $lessonMessage->user_id = Auth::id();
        $lessonMessage->message = $request->get("message");
        $lessonMessage->reserve_id = $resLesson->id;
        $lessonMessage->save();
        $resLesson->reserve_status = ReserveStatusEnum::AWAITING_ADMINISTRATION;
        $resLesson->save();
        return response(['status'=>0]);
    }

    private function commonCheckAnswerReserveParams(Request $request) {
        if (!$request->has("reserveId")) {
            return response(__('errors.incorrectRequest'), 422);
        }
        if (!$request->has("message") || empty($request->get("message"))) {
            return response("Сообщение не должно быть пустое", 422);
        }
        return null;
    }

    private function checkForContact(ReservedLesson $resLesson) {
        if ($resLesson->reserve_status == ReserveStatusEnum::AWAITING_ADMINISTRATION || $resLesson->reserve_status == ReserveStatusEnum::COMPLETED) {
            if ($resLesson->user_id !== Auth::id() && $resLesson->lesson->user_id !== Auth::id()) {
                return response(__('errors.wrongReserve'), 422);
            }
        } else {
            return response(__('errors.incorrectRequest'), 422);
        }
        return null;
    }

    private function checkReserveForAnswer(ReservedLesson $resLesson) {
        if ($resLesson->reserve_status == ReserveStatusEnum::AWAITING_PUPIL) {
            if ($resLesson->user_id !== Auth::id()) {
                return response(__('errors.wrongReserve'), 422);
            }
        } else if($resLesson->reserve_status == ReserveStatusEnum::AWAITING_TEACHER) {
            if ($resLesson->lesson->user_id !== Auth::id()) {
                return response(__('errors.wrongReserve'), 422);
            }
        } else {
            return response(__('errors.incorrectRequest'), 422);
        }
        return null;
    }
}

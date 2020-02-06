<?php
namespace App\Http\Controllers;

use App\Models\ReservedLesson;
use App\Models\User;
use App\Models\Lesson;
use App\Models\UserRole;
use App\Models\Withdrawal;
use App\WithdrawalStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    public function index() {
        $data = $_GET;
        $clientPage =  isset($data['clientPage']) ? $data['clientPage'] : 1;
        $clientSize =  isset($data['clientSize']) ? $data['clientSize'] : 10;
        $lessonPage =  isset($data['lessonPage']) ? $data['lessonPage'] : 1;
        $lessonSize =  isset($data['lessonSize']) ? $data['lessonSize'] : 10;
        $res = [];
        $res['totalUsers'] = User::count();
        $res['totalLessons'] = Lesson::count();
        $res['users'] = User::with('rights')->where("id", "<>", Auth::id())->take($clientSize)->offset(($clientPage-1)*$clientSize)->get();
        $lessons = Lesson::take($lessonSize)->offset(($lessonPage-1)*$lessonSize)->get();
        foreach ($lessons as $lesson) {
            $lesson['username'] = User::find($lesson->user_id)->name;
        }
        $res['lessons'] = $lessons;
        return response($res);
    }

    public function loadUsers() {
        $params = $this->getPaginatorParams($_GET);
        $res['data'] = User::with('rights')
            ->where("id", "<>", Auth::id())
            ->take($params->pageSize)->offset(($params->curPage - 1) * $params->pageSize)->get();
        $res['total'] = User::count();
        return response($res);
    }

    public function loadLessons() {
        $params = $this->getPaginatorParams($_GET);
        $res['data'] = Lesson::with(['user' => function($query) { $query->select('id', 'name as teacher_name'); }])
            ->select(['lessons.id', 'lessons.user_id', 'lessons.name as lesson_name', 'is_unique', 'created_at'])
            ->take($params->pageSize)->offset(($params->curPage - 1) * $params->pageSize)->get();
        $res['total'] = Lesson::count();
        return response($res);
    }

    public function loadReserves() {
        $data = $_GET;
        $params = $this->getPaginatorParams($data);
        $showClosed = isset($data['showClosed']) ? $data['showClosed'] === "true" : false;

        $q = ReservedLesson::where("reserved_lessons.closed", $showClosed);
        $res['data'] = $q->with([
            'reserveTime:id,lesson_date,lesson_time,duration',
            'user:id,name,phone',
            'lesson:id,user_id,name',
            'lesson.user:id,name'
        ])
            ->take($params->pageSize)->offset(($params->curPage - 1) * $params->pageSize)
            ->get();
        $res['total'] = $q->count();
        return response($res);
    }

    private function getPaginatorParams($data) {
        if (!isset($data['currentPage']) || !isset($data['pageSize'])) {
            abort(500, __('errors.incorrectRequest'));
        }
        return (object) ['curPage' => intval($data['currentPage']), 'pageSize' => intval($data['pageSize'])];
    }

    public function grantRights() {
        $data = $_POST;
        $userId = $data['USER_ID'];
        $isModerRights = $data['RIGHTS_TYPE'] == 'MODERATOR';
        $isAdminRights = $data['RIGHTS_TYPE'] == 'ADMIN';
        $userRole = UserRole::where('user_id', $userId)->first();
        if (!$userRole) {
            $userRole = new UserRole();
        }
        $userRole['moder_rights'] = $userRole['moder_rights'] || $isModerRights;
        $userRole['admin_rights'] = $userRole['admin_rights'] || $isAdminRights;
        $userRole['user_id'] = $userId;
        return response(json_encode($userRole->save()));
    }

    public function revokeRights() {
        $data = $_POST;
        $userId = $data['USER_ID'];
        $userRole = UserRole::where('user_id', $userId)->first();
        return response(json_encode($userRole->delete()));
    }

    public function setUnique() {
        $data = $_POST;
        $lessonId = $data['LESSON_ID'];
        $isUnique = $data['IS_UNIQUE'] === 'true';
        $lesson = Lesson::find($lessonId);
        $lesson->is_unique = $isUnique;
        $result = $lesson->save();
        return response(['status' => $result ? 0 : 4]);
    }
}

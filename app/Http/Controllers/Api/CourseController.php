<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    //
    // return semua list course
    public function courseList()
    {
        $result = Course::select('name', 'thumbnail', 'lesson_num', 'id')->get();
        return response()->json([
            'code' => 200,
            'msg' => 'my course list here',
            'data' => $result
        ], 200);
    }
}

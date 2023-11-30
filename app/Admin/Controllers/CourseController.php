<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\CourseType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Tree;

class CourseController extends AdminController
{
    //
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Course';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Course());

        $grid->column('id', __('Id'));
        $grid->column('user_token', __('Instructor'))->display(function ($token) {
            // get username by token dari tabel user
            return User::where('token', '=', $token)->value('name');
        });
        $grid->column('name', __('Name'));
        $grid->column('thumbnail', __('Thumbnail'))->image('', 70, 50);
        $grid->column('description', __('Description'));
        $grid->column('type_id', __('Type id'));
        $grid->column('lesson_num', __('Lesson num'));
        $grid->column('video_length', __('Video length'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Course::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('description', __('Description'));
        $show->field('lesson_num', __('Jumlah pelajaran'));
        $show->field('video_length', __('Durasi video'));
        $show->field('follow', __('Follow'));
        $show->field('score', __('Score'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    // form create/edit
    protected function form()
    {
        $form = new Form(new Course());

        $form->text('name', __('Nama'));
        // get kategori dalam bentuk key & value pair, parameter terakhir adalah key
        $result = CourseType::pluck('title', 'id');
        // select form
        $form->select('type_id', __('Kategori'))->options($result);
        // buat thumbnail
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->text('video', __('Link video'));
        $form->text('description', __('Deskripsi'));
        // $form->decimal('price', __('Harga'));
        $form->number('lesson_num', __('Jumlah pelajaran'));
        $form->number('video_length', __('Durasi video'));

        // user yang posting
        $result = User::pluck('name', 'token');
        $form->select('user_token', __('Instructor'))->options($result);
        $form->display('created_at', __('Created at'));
        $form->display('updated_at', __('Updated at'));

        // $form->text('title', __('Title'));
        // $form->textarea('description', __('Deskripsi'));
        // $form->number('order', __('Order'));

        return $form;
    }
}

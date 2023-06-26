<?php

namespace App\Controller;

use App\Model\Post;
use App\Model\User;
use App\Model\Profile;
use Core\BaseController;
use Core\View;

class Apples extends BaseController
{
    public function indexAction()
    {
        // echo 'apple index method';
        $name = 'hi';
        try {
            $user = User::find(2);
            var_dump($user);
            // $user->ratedPosts()->attach([4]);
            // var_dump($user);
            // exit;
            // $user = User::find(1);
            $user->ratedPosts()->detach([4]);
            $user2 = User::find(2);
            // $users = User::with(['ratedPosts'])->get();
            // foreach ($users as $user) {
            //     echo $user->name;
            //     foreach ($user->ratedPosts as $post) {
            //         echo $post->title;
            //     }
            // }
            // $user = User::find(2);
            // $user->profile()->create(['counotry' => 'ca', 'city' => 'abc']);
            // exit;
            // $user = User::find(1);
            // $user->posts()->create(['title' => '123', 'userLevel' => 1000]);
            // exit;

            // $profile = Profile::get();
            // var_dump($profile);

            // $profile = Profile::with(['user'])->first();
            // echo $profile->user->name;
            // $user = User::with(['profile'])->first();
            // var_dump($user->profile->counotry);
            // exit;

            // $user = User::with(['posts'])->find(1);
            // foreach ($user->posts as $post) {
            //     echo $post->title . '<br />';
            // }
            // exit;

            // $user  = User::find(1);
            // var_dump($user->posts()->where('title', '=', 'second title')->toSql());
            // exit;
            // $post = $user->posts()->where('title', '=', 'second title')->update([
            //     'title' => '666'
            // ]);
            // foreach ($post as $key => $val) {
            //     var_dump($post[$key]->title);
            // }

            // $user = User::find(1)->profile()->get();
            // foreach ($user as $key => $val) {
            //     var_dump($user[$key]->city);
            // }
            // $user->name = 'realreal';
            // $user->save();
            exit;
            // $user = User::find(1);
            // if ($user) {
            //     $user->remove();
            // }

            // var_dump($user->phone);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        // $output = View::renderTemplate('Home/index.html', ['name' => $name]);
        // $this->response->setoutput($output);
        return View::renderTemplate('Home/index.html', ['name' => $name]);
    }
    public function makeJuiceAction()
    {
        echo htmlspecialchars(print_r($this->route_params, true));
        echo 'made a juice';
    }

    public function testAction()
    {
        echo '123';
    }
}

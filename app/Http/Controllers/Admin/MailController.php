<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MailSend;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;

class MailController extends Controller
{
    public function send(MailSend $request)
    {
        $userService = new UserService();
        $users = [];
        switch ($request->input('type')) {
            case 1: $users = $userService->getAllUsers();
            break;
            case 2: $users = $userService->getUsersByIds($request->input('receiver'));
            break;
            // available users
            case 3: $users = $userService->getAvailableUsers();
            break;
            // un available users
            case 4: $users = $userService->getUnAvailbaleUsers();
            break;
        }

        foreach ($users as $user) {
            SendEmailJob::dispatch([
                'email' => $user->email,
                'subject' => $request->input('subject'),
                'template_name' => 'notify',
                'template_value' => [
                    'name' => config('v2board.app_name', 'V2Board'),
                    'url' => config('v2board.app_url'),
                    'content' => $request->input('content')
                ]
            ]);
        }

        return response([
            'data' => true
        ]);
    }
}

<?php

namespace App\Controllers\Auth;

use App\Models\UsersModel;
use Exception;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Github;

use stdClass;

class Login extends AuthController
{
    public function index()
    {
        
        $data['title'] = ('login');

        return view('Auth/Login', $data);
    }
    public function admin_login_post()
    {
        var_dump($this->request->getVar('email'));

        $userModel = new UsersModel();

        $rules = [
            'email' => [
                'label'  => trans('email'),
                'rules'  => 'required|min_length[4]|max_length[100]|valid_email',
                'errors' => [
                    'required' => trans('form_validation_required'),
                    'min_length' => trans('form_validation_min_length'),
                    'max_length' => trans('form_validation_max_length'),
                    'valid_email' => 'Please check the Email field. It does not appear to be valid.',
                ],
            ],
            'password' => [
                'label'  => trans('password'),
                'rules'  => 'required',
                'errors' => [
                    'required' => trans('form_validation_required'),
                ],
            ],

        ];
       
        if ($this->validate($rules)) {

            $user = $userModel->get_user_by_email($this->request->getVar('email'));
            if (!empty($user) && $user->role != 1 && get_general_settings()->maintenance_mode_status == 1) {
                $this->session->setFlashData('errors_form', "Site under construction! Please try again later.");
                return redirect()->back();
            }

            if ($userModel->login()) {
                //remember user
                $remember_me = $this->request->getVar('remember_me');
                if ($remember_me == 1) {
                    $this->response->setCookie('_remember_user_id', user()->id, time() + 86400);
                }
                return redirect()->to(admin_url())->withCookies();
            } else {

                return redirect()->back()->withInput();
            }
        } else {

            $this->session->setFlashData('errors_form', $this->validator->listErrors());
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }
    }


}

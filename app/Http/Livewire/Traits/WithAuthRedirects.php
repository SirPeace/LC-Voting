<?php

namespace App\Http\Livewire\Traits;

trait WithAuthRedirects
{
    public function redirectToLogin()
    {
        $this->setIntendedUrl();

        return redirect()->route('login');
    }

    public function redirectToRegister()
    {
        $this->setIntendedUrl();

        return redirect()->route('register');
    }

    protected function setIntendedUrl(): void
    {
        redirect()->setIntendedUrl(url()->previous());
    }
}

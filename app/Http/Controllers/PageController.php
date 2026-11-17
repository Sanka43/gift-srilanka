<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function blog(): View
    {
        return view('pages.blog');
    }

    public function meetTheTeam(): View
    {
        return view('pages.meet-the-team');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function shipping(): View
    {
        return view('pages.shipping');
    }

    public function returnPolicy(): View
    {
        return view('pages.return');
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function dashboard(): View
    {
        return view('dashboard');
    }

    public function robots(): Response
    {
        $url = rtrim(config('app.url'), '/');

        return response(
            "User-agent: *\nAllow: /\nDisallow: /cart\nDisallow: /checkout\nDisallow: /profile\nDisallow: /customer\nDisallow: /dashboard\nDisallow: /admin\nDisallow: /wishlist\nDisallow: /login\nDisallow: /register\nDisallow: /forgot-password\nDisallow: /reset-password\nDisallow: /email/verify\nDisallow: /confirm-password\n\nSitemap: {$url}/sitemap.xml\n",
            200,
            ['Content-Type' => 'text/plain']
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Page;

class PageController extends Controller
{
    public function page($slug): View
    {
        \Log::info('PageController::page called', ['slug' => $slug, 'locale' => app()->getLocale()]);
        
        $page = Page::where('slug', '=', $slug)
            ->where('status', 'ACTIVE')
            ->firstOrFail();

        // El modelo Page ya maneja la traducción automáticamente
        // gracias a los accessors que retornan el contenido según app()->getLocale()
        
        $seo = [
            'seo_title' => $page->title,
            'seo_description' => $page->meta_description,
        ];

        return view('theme::page', compact('page', 'seo'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Change user's language preference
     */
    public function change(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,id',
        ]);

        // Update user's language preference
        if (auth()->check()) {
            auth()->user()->update([
                'language' => $request->language,
            ]);

            app()->setLocale($request->language);
        }

        return redirect()->back()->with('success', __('messages.language_changed'));
    }

    /**
     * Get available languages
     */
    public function getAvailable()
    {
        return response()->json([
            'languages' => [
                ['code' => 'en', 'name' => 'English'],
                ['code' => 'id', 'name' => 'Bahasa Indonesia'],
            ],
        ]);
    }
}

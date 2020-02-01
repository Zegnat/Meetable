<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controller as BaseController;
use App\Setting;
use DateTime, DateTimeZone, Exception;

class SettingsController extends BaseController
{
    // Access control to this is managed in the route definition

    public function get() {
        return view('settings');
    }

    public function post() {
        $properties = ['add_an_event', 'logo_url', 'logo_width', 'logo_height', 'favicon_url',
            'ga_id', 'home_meta_description', 'home_social_image_url'];
        foreach($properties as $id) {
            Setting::set($id, request($id));
        }

        $checkboxes = ['enable_webmention_responses', 'enable_ticket_url', 'show_rsvps_in_ics',
            'auth_hide_login', 'auth_hide_logout'];
        foreach($checkboxes as $id) {
            Setting::set($id, request($id) ? 1 : 0);
        }

        $passwords = ['googlemaps_api_key', 'twitter_consumer_key', 'twitter_consumer_secret',
            'twitter_access_token', 'twitter_access_token_secret'];
        foreach($passwords as $id) {
            if(request($id) != '********') {
                Setting::set($id, request($id));
            }
        }

        session()->flash('settings-saved', 'The settings have been saved');
        return redirect(route('settings'));
    }

}

<?php
namespace App\Services;

use App\User, App\Response, App\ResponsePhoto;
use Log, Storage;
use App\Events\ResizeImages;

class ExternalResponse {

    public static function setResponsePropertiesFromXRayData(&$response, $data, $sourceURL, $targetURL) {
        if(isset($data['published'])) {
            $response->published = date('Y-m-d H:i:s', strtotime($data['published']));
        }

        foreach(['url', 'name'] as $prop) {
            if(isset($data[$prop])) {
                $response->{$prop} = $data[$prop];
            } else {
                $response->{$prop} = null;
            }
        }

        if(isset($data['rsvp'])) {
            $response->rsvp = strtolower($data['rsvp']);
        } else {
            $response->rsvp = null;
        }

        if(isset($data['like-of']) && in_array($targetURL, $data['like-of'])) {
            $response->is_like = true;
        }

        if(isset($data['content']['text'])) {
            $response->content_text = $data['content']['text'];
        } else {
            $response->content_text = null;
        }

        if(isset($data['content']['html'])) {
            $response->content_html = $data['content']['html'];
        } else {
            $response->content_html = null;
        }

        foreach(['name', 'photo', 'url'] as $prop) {
            if(isset($data['author'][$prop])) {
                $response->{'author_'.$prop} = $data['author'][$prop];
            } else {
                $response->{'author_'.$prop} = null;
            }
        }

        if(isset($data['rsvp']) && isset($data['author']['url'])) {
            // Set the rsvp_user_id if source URL domain matches the author URL domain
            if(\p3k\url\host_matches($sourceURL, $data['author']['url'])) {
                // Check if there is a user with this URL
                $rsvpUser = User::where('url', $data['author']['url'])->first();
                if($rsvpUser) {
                    $response->rsvp_user_id = $rsvpUser->id;
                }
            }
        }

        if(!empty($data['post-type']))
            $response->post_type = $data['post-type'];

        $response->data = json_encode($data);
    }

    public static function setPhotoRecords(&$response, $photos) {

        if(!$photos) {
            // Delete all existing photos
            ResponsePhoto::where('response_id', $response->id)->delete();
            return;
        }

        foreach($photos as $url) {
            // Check if this photo already exists
            $exists = ResponsePhoto::where('response_id', $response->id)
              ->where('source_url', $url)
              ->first();
            if(!$exists) {
                $photo = ResponsePhoto::create($response, [
                    'source_url' => $url,
                    'alt' => null,
                ]);
            } else {
                $photo = $exists;
            }
            // Queue a job to resize the images, including re-running this for updates
            event(new ResizeImages($photo));
        }

        // Check if there are any records for photos that no longer exist in the post
        $existing = ResponsePhoto::where('response_id', $response->id)->get();
        foreach($existing as $photo) {
            if(!in_array($photo->source_url, $photos)) {
                $photo->delete();
            }
        }
    }

}

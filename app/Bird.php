<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Bird extends Model
{
    protected $fillable = [
    	'name',
	    'imagepath',
    ];

    public function saveImageFromURL(String $url, String $birdname)
    {
    	$this->fresh();
    	$img = $this->id.'_'.trim(strtolower(str_replace(' ', '_', $birdname)));
    	$this->imagepath = $img;
    	$this->save();

	    $ch = curl_init ($url);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
	    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $rawdata=curl_exec($ch);
	    curl_close ($ch);

	    Storage::disk('local')->put($img, $rawdata);
    }
}

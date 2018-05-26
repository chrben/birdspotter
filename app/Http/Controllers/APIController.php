<?php

namespace App\Http\Controllers;

use App\Bird;
use BigV\ImageCompare;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function createBird(Request $request)
    {
		$bird = Bird::create(['name' => $request->birdname, 'imagepath' => '']);
		$bird->saveImageFromURL($request->birdurl, $request->birdname);
    	return response('OK', 200);
    }

    public function spotBird(Request $request)
    {
		$this->storeImageToCompare($request->birdurl);

		$results = [];

		$comparer = new ImageCompare();
		foreach (Bird::all() as $bird) {
			$res = $comparer->compare(base_path('storage/app/'.$bird->imagepath), base_path('public/img_to_compare'));
			$results[] = ['birdname' => $bird->name, 'result' => $res];
			if ($res === 0) break;
		}
	    usort($results, function($a, $b) {
		    return $a['result'] <=> $b['result'];
	    });
	    if ($request->debug == 1)
	    {
		    return response()->json($results);
	    }
		return response()->json($results[0]);
    }

    private function storeImageToCompare(String $url)
    {
    	$local_file = 'img_to_compare';
	    $ch = curl_init ($url);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
	    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $rawdata=curl_exec($ch);
	    curl_close ($ch);

	    $fp = fopen($local_file,'w');
	    fwrite($fp, $rawdata);
	    fclose($fp);
    }
}

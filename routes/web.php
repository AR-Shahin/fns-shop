<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get('/test', function () {
    // return getBrowser();
    $platform = getPlatForm();
    if($platform == "Mac"){
      return shell_exec("ifconfig en0 | awk '/ether/{print $2}'");
    }elseif($platform == "Linux"){
      return shell_exec("ifconfig en0 | awk '/ether/{print $2}'");
    }
    return shell_exec("ifconfig en0 | awk '/ether/{print $2}'"); // Mac
    //return $mac = shell_exec("ip link | awk '{print $2}'"); // Linux
        // preg_match_all('/([a-z0-9]+):\s+((?:[0-9a-f]{2}:){5}[0-9a-f]{2})/i', $mac, $matches);
        // $output = array_combine($matches[1], $matches[2]);
        // $mac_address_values =  json_encode($output, JSON_PRETTY_PRINT);
        // $mac = json_decode($mac_address_values);

        // dd($mac_address_values);
        // return $mac;

        // function getOSInformation()
        // {
            // if (false == function_exists("shell_exec") || false == is_readable("/etc/os-release")) {
            //     return null;
            // }

            $os         = shell_exec('cat /etc/os-release');
            $listIds    = preg_match_all('/.*=/', $os, $matchListIds);
            $listIds    = $matchListIds[0];

            $listVal    = preg_match_all('/=.*/', $os, $matchListVal);
            $listVal    = $matchListVal[0];

            array_walk($listIds, function(&$v, $k){
                $v = strtolower(str_replace('=', '', $v));
            });

            array_walk($listVal, function(&$v, $k){
                $v = preg_replace('/=|"/', '', $v);
            });

            return array_combine($listIds, $listVal);
        // }


});

 function getPlatForm()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        if (preg_match('/linux/i', $u_agent)) {
          $platform = 'linux';
        }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
          $platform = 'mac';
        }elseif (preg_match('/windows|win32/i', $u_agent)) {
          $platform = 'windows';
        }

        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
          $bname = 'Internet Explorer';
          $ub = "MSIE";
        }elseif(preg_match('/Firefox/i',$u_agent)){
          $bname = 'Mozilla Firefox';
          $ub = "Firefox";
        }elseif(preg_match('/OPR/i',$u_agent)){
          $bname = 'Opera';
          $ub = "Opera";
        }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
          $bname = 'Google Chrome';
          $ub = "Chrome";
        }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
          $bname = 'Apple Safari';
          $ub = "Safari";
        }elseif(preg_match('/Netscape/i',$u_agent)){
          $bname = 'Netscape';
          $ub = "Netscape";
        }elseif(preg_match('/Edge/i',$u_agent)){
          $bname = 'Edge';
          $ub = "Edge";
        }elseif(preg_match('/Trident/i',$u_agent)){
          $bname = 'Internet Explorer';
          $ub = "MSIE";
        }

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
      ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {

        }

        $i = count($matches['browser']);
        if ($i != 1) {

          if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
              $version= $matches['version'][0];
          }else {
              $version= $matches['version'][1];
          }
        }else {
          $version= $matches['version'][0];
        }

        if ($version==null || $version=="") {$version="?";}

        $data = [
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
          ];

        return ucfirst($data['platform']);
    }

    /**
       * Get browsing user browser information
       *
       * @param null
       * @return browser @platform
       */

     function getBrowser()
    {
        $arr_browsers = ["Opera", "Edg", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];

        $agent = $_SERVER['HTTP_USER_AGENT'];

        $user_browser = '';
        foreach ($arr_browsers as $browser) {
            if (strpos($agent, $browser) !== false) {
                $user_browser = $browser;
                break;
            }
        }

        switch ($user_browser) {
            case 'MSIE':
                $user_browser = 'Internet Explorer';
                break;

            case 'Trident':
                $user_browser = 'Internet Explorer';
                break;

            case 'Edg':
                $user_browser = 'Microsoft Edge';
                break;
        }

        return $user_browser;
    }

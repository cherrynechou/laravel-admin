<?php

namespace CherryneChou\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Locale
{
    /**
     *  孟加拉语 bn-BD          bn
     *  美式英语 en_US          en
     *  波斯语  fa-IR           fa
     *  印尼语  id-ID           id
     *  日语    ja-JP           ja
     *  葡萄牙语的巴西版本 pt-BR   pt_BR
     *  简体中文 zh-CN           zh-CN
     *  繁体中亠 zh-TW           zh-TW
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
	public function handle(Request $request, Closure $next)
    {
  		$lang = $request->header('Accept-Language');

        if($lang == 'bn-BD'){
            $locale = 'bn';
        }else if($lang == 'en_US'){
            $locale = 'en';
        }else if($lang == 'fa-IR'){
            $locale = 'fa';
        }else if($lang == 'id-ID'){
            $locale = 'id';
        }else if($lang == 'ja-JP'){
            $locale = 'ja';
        }else if($lang == 'pt_BR'){
            $locale = 'pt_BR';
        }else if($lang == 'zh-CN'){
            $locale = 'zh-CN';
        }else if($lang == 'zh-TW'){
            $locale = 'zh-TW';
        }else{
            $locale =  config('app.locale');
        }

	    app()->setLocale($locale);

        return $next($request);
    }
}
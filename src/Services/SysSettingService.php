<?php
namespace CherryneChou\Admin\Services;

use CherryneChou\Admin\Models\Config;
use CherryneChou\Admin\Models\ConfigGroup;
use CherryneChou\Admin\Contracts\SysSettingServiceInterface;
use Illuminate\Support\Facades\Log;

class SysSettingService implements SysSettingServiceInterface
{
	 /**
     * 缓存过期时间（秒），默认30天
     */
    private const CACHE_TTL = 60 * 60 * 24 * 30;


	public function refreshCache()
	{
		try{
			$groups =  ConfigGroup::with('configs')->get();
			$configs = [];

			foreach ($groups as $group) {
                $configs[$group->key] = [];
                foreach ($group->configs as $config) {
                    // 根据类型自动转换值
                    $configs[$group->key][$config->key] = $config->value;
                }
            }

            // 使用带过期时间的缓存，而非永久缓存
            Cache::put(config('admin.config_cache_key'), $configs, self::CACHE_TTL);

		}catch(\Throwalbe $e){
			Log::error('刷新系统配置缓存失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
		}
	}



	public function getChangedConfig(string $groupKey, array $updated)
	{
		//数据库中的数据
        $oldConfigDatas = Config::query()->where('group_key', $groupKey)->get();

        //更改的值
        $updatedValues = [];
        foreach ($updates as $key => $value) {
            $found = $oldConfigDatas->where('key', $key)->first();
            if($found->value != $value){
                $changed['id'] = $found->id;
                $changed['value'] = $value;
                $updatedValues[] = $changed;
            }
        }

        return $updatedValues;
	}
}
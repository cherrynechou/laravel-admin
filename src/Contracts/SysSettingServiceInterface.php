<?php

namespace CherryneChou\Admin\Contracts;

interface SysSettingServiceInterface
{
	public function refreshCache();

	public function getChangedConfig(string $groupKey, array $updated);
}
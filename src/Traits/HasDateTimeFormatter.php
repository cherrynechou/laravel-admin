<?php
namespace CherryneChou\Admin\Traits;

trait HasDateTimeFormatter
{
	protected function serializeDate(\DateTimeInterface $date)
    {
        if (version_compare(app()->version(), '7.0.0') < 0) {
            return parent::serializeDate($date);
        }
        
        return $date->format($this->getDateFormat());
    }
}
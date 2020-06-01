<?php

namespace Signa\Helpers;


class Version
{
    public static function addVersion($asset)
    {
        $output = '';

        foreach ($asset->getResources() as $resource) {
            if ($resource->getType() == 'js') {
                $output .= "\n" . sprintf('<script type="text/javascript" src="/%s?v=%s"></script>', $resource->getPath(), SCRIPT_VERSION);
            } else if ($resource->getType() == 'css') {
                $output .= "\n" . sprintf('<link rel="stylesheet" type="text/css" href="/%s?v=%s">', $resource->getPath(), SCRIPT_VERSION);
            }
        }

//        \dump($asset->getResources());
        return $output;
    }
}
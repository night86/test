<?php

namespace Signa\Helpers;

class Models
{
	static public function serializeBeforeSave($array)
	{
	    if (is_object($array)) {
            $array = (array)$array;
        }
		$old = $array;
		if (is_array($array))
		{
		    try {
                return serialize($array);
            } catch (\Exception $e) {
		        // ??
            }
		}
		else if (is_array(unserialize($array)))
		{
			return $old;
		}
		else
		{
			return serialize(array());
		}
	}

	static public function reunserialize($array)
	{
		if (is_array($array) && empty($array))
		{
			return null;
		}
		else if (is_array($array))
		{
			return $array;
		}
		else if (is_array(unserialize($array)))
		{
			return unserialize($array);
		}
		else
		{
			return null;//$this->reunserialize($array);
		}
	}

	static public function serializeObjBeforeSave($array)
	{
		$old = $array;
		if (is_object($array))
		{
			return serialize($array);
		}
		else if (is_object(unserialize($array)))
		{
			return $old;
		}
		else
		{
			return serialize(array());
		}
	}

	static public function reunserializeObj($array)
	{
	    if (is_array($array)) {
	        return ModelsHelper::reunserialize($array);
        }

		if (is_object($array) && empty($array))
		{
			return null;
		}
		else if (is_object($array))
		{
			return $array;
		}
		else if (is_object(unserialize($array)))
		{
			return unserialize($array);
		}
		else
		{
			return null;//$this->reunserialize($array);
		}
	}
       
}
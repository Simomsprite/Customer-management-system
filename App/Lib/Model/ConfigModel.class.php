<?php 
class ConfigModel extends Model
{

    /**
     * 读取配置
     */
    public function getValue($name = '')
    {
        return $this->where(array('name' => $name))->getField('value');
    }


} 

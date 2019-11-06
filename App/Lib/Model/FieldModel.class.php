<?php 
    /*自定义字段类
    *许浩光
    *add 添加字段
    *delete 删除字段
    *edit 修改字段
    */
    class FieldModel extends Model{
        //设置默认表名为空
        protected $tableName = ''; 
        protected $trueTableName = ''; 
        protected $queryStr = ''; 
        protected $_validate;
        public function _initialize(){
            $validate[0] = 'field';
            $validate[1] = '/^[a-z]([a-z]|_)+[a-z]$/i';
            $validate[2] = L('FIELD NAME FORMAT IS INCORRECT');
            $validate[3] = 1;
            $validate[4] = 'regex';
            $validate[5] = 3;
            $this->_validate[] = $validate;
        }
        //修改字段
        public function add($data = false){
            if(!$this->autoValidation($data)) return false;
            $this->tableName = $data['is_main']?$data['model']:$data['model'].'_data';
            $maxlength = (intval($data['max_length']) != 0)? intval($data['max_length']): 255;
            switch($data['form_type']) {
				case 'address':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
                
                case 'box':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` VARCHAR($maxlength ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$data[default_value]' COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
				
                case 'textarea':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
                
                case 'editor':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
                
                // case 'number':
                //     $defaultvalue = abs(intval($data['default_value'])) > 2147483647 ? 2147483647 : intval($data['default_value']);
                //     $maxlength = intval($maxlength) > 11 ? 11:intval($maxlength);
                //     $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` int ($maxlength) NOT NULL DEFAULT '$defaultvalue' COMMENT '".$data['name']."'";
                //     return $this->db->execute($this->queryStr);
                // break;
                case 'number':
                    $defaultvalue = abs(intval($data['default_value'])) > 2147483647 ? 2147483647 : intval($data['default_value']);
                    $maxlength = 11;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` int ($maxlength) NOT NULL DEFAULT '$defaultvalue' COMMENT '".$data['name']."'";
                    return $this->db->execute($this->queryStr);
                break;
				
				// case 'floatnumber':
    //                 $defaultvalue = abs(intval($data['default_value'])) > 2147483647 ? 2147483647 : intval($data['default_value']);
    //                 $maxlength = intval($maxlength) > 11 ? 9:(intval($maxlength)-2);
    //                 $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` float ($maxlength,2) NOT NULL DEFAULT '$defaultvalue' COMMENT '".$data['name']."'";
    //                 return $this->db->execute($this->queryStr);
    //             break;
                case 'floatnumber':
                    $defaultvalue = abs(intval($data['default_value'])) > 999999999999999999.99 ? 999999999999999999.99 : intval($data['default_value']);
                    $maxlength = 18;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` decimal ($maxlength,2) NOT NULL DEFAULT '$defaultvalue' COMMENT '".$data['name']."'";
                    return $this->db->execute($this->queryStr);
                break;
                
                case 'datetime':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` int (10) NOT NULL COMMENT '".$data['name']."'";
                    return $this->db->execute($this->queryStr);
                break;
                default:
                    $maxlength = $maxlength < 20774 ? $maxlength : 333;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` ADD `$data[field]` VARCHAR($maxlength ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$data[default_value]' COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
            }
        }
        
        public function save($data = false){
            if(!$this->autoValidation($data)) return false;
            $this->tableName = $data['is_main']?$data['model']:$data['model'].'_data';
            $maxlength = ($data['max_length'] && intval($data['max_length']) != 0)? intval($data['max_length']): 255;
            switch($data['form_type']) {
				case 'address':
					$this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
				
                case 'box':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` VARCHAR( $maxlength ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$data[default_value]' COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
                
                case 'textarea':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
                
                case 'editor':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
                
    //             case 'number':
    //                 $defaultvalue = abs(intval($data['default_value'])) > 2147483647 ? 2147483647 : intval($data['default_value']);
    //                 $maxlength = intval($maxlength) > 11 ? 9:(intval($maxlength)-2);
    //                 $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` int ($maxlength) NOT NULL DEFAULT '$defaultvalue' COMMENT '".$data['name']."'";
    //                 return $this->db->execute($this->queryStr);
    //             break;
				
				// case 'floatnumber':
    //                 $defaultvalue = abs(intval($data['default_value'])) > 32767.99 ? 32767.99 : intval($data['default_value']);
    //                 $maxlength = $maxlength > 7 ? 7:$maxlength;
    //                 $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` float ($maxlength) NOT NULL DEFAULT '$defaultvalue' COMMENT '".$data['name']."'";
    //                 return $this->db->execute($this->queryStr);
    //             break;
                case 'number':
                    $defaultvalue = abs(intval($data['default_value'])) > 2147483647 ? 2147483647 : intval($data['default_value']);
                    $maxlength = 11;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` int ($maxlength) NOT NULL DEFAULT '$defaultvalue' COMMENT '".$data['name']."'";
                    return $this->db->execute($this->queryStr);
                break;
                
                case 'floatnumber':
                    $defaultvalue = abs(intval($data['default_value'])) > 999999999999999999.99 ? 999999999999999999.99 : intval($data['default_value']);
                    $maxlength = 18;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` decimal ($maxlength,2) NOT NULL DEFAULT '$defaultvalue' COMMENT '".$data['name']."'";
                    return $this->db->execute($this->queryStr);
                break;
                
                case 'datetime':
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` int (10) NOT NULL COMMENT '".$data['name']."'";
                    return $this->db->execute($this->queryStr);
                break;
				
				default:
                    $maxlength = $maxlength < 20774 ? $maxlength : 333;
                    $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` CHANGE `$data[field_old]` `$data[field]` VARCHAR( $maxlength ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '$data[default_value]' COMMENT '".$data['name']."'";
                    return $this->execute($this->queryStr);
                break;
            }
        }
        public function getLastSql() {
            return $this->queryStr;
        }
        // 鉴于getLastSql比较常用 增加_sql 别名
        public function _sql(){
            return $this->getLastSql();
        }
        
        public function delete($data){
            $this->tableName = $data['is_main']?$data['model']:$data['model'].'_data';
            $this->queryStr = "ALTER TABLE `" . $this->tablePrefix . $this->tableName . "` DROP `$data[field]`;";
            return $this->execute($this->queryStr);
        }

        /**
         * 获取添加时主表附表字段
         * @param $model String 模块名称
         * @return $arr Array 主表字段  附表字段
         */
        public function get_add_field($model = '')
        {
            if ($model == '') return false;
            $fields = M('fields')->field('field,is_main,form_type')->where('model="" || model="%s"', $model)->select();
            $main_fields = $data_fields = array();
            foreach ($fields as $key => $val) {
                if ($val['is_main']) {
                    $main_fields[$val['field']] = $val;
                } else {
                    $data_fields[$val['field']] = $val;
                }
            }
            return array('main_fields' => $main_fields, 'data_fields' => $data_fields);
        }

        /**
         * 获取在列表展示的字段
         * @param $model String 模块名称
         * @return $str String
         */
        public function get_list_show_field($model = '')
        {
            if ($model == '') return false;
            return M('fields')->field('field,name,form_type')->where(array('model' => $model, 'in_index' => 1))->order('order_id')->select();
        }

        /**
         * 获取在详情展示的字段 （本模块所有字段）
         * @param $model String 模块名称
         * @return $str String
         */
        public function get_view_field($model = null) 
        {
            $where = array('model' => $model);
            return M('fields')->field('field,name,form_type')->where($where)->order('order_id')->select();
        }

        /**
         * 去获取字段名称
         */
        public function getFieldName($model)
        {
            $fields = M('fields')->field('name,field')->where(array('model' => $model))->select();
            $data = array();
            foreach ($fields as $key => $val) {
                $data[$val['field']] = $val['name'];
            }
            return $data;
        }

    }
/*CRM数据库更新-20190912
*用于更新数据库。
*格式如下
*/
/*2019-09-12 王学松 -新增*/
/**
修改系统默认职位为岗位
 */
update pd_fields set name='岗位',in_index=0,in_add=0  where field_id=42;
/**
添加职位
 */
insert into pd_fields
(model,is_main,field,`name`,form_type,default_value,color,
max_length,is_unique,is_recheck,is_null,
is_validate,in_index,in_add,input_tips,setting,
order_id,operating,is_show,status)
values ('leads','1','crm_gzcqgt','职位','box','','333333',
'0','0','0','0',
'0','0','1','','array(''type''=>''select'',''data''=>array(1=>''普通职员'',2=>''部门主管'',3=>''部门经理'',4=>''总经理''))',
'0','0','0','0');

/**
线索表中添加职位字段
 */
alter table pd_leads add crm_gzcqgt varchar(255) NOT NULL DEFAULT '' COMMENT'职位';

/**
2019/09/27
手机必填
 */
update pd_fields set is_null=1 where field_id=40;
/**
客户信息来源非必填
 */
update pd_fields set is_null=0 where field_id=9;
/**
联系人地址非必填不显示
 */
update pd_fields set in_add=0,is_null=0 where field_id=32;
/**
联系人添加邮编不显示
 */
update pd_fields set in_add=0,is_null=0 where field_id=34;
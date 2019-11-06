SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO" ;

CREATE TABLE IF NOT EXISTS `pdcrm_account_money` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL COMMENT '银行账户ID',
  `money` decimal(10,2) NOT NULL COMMENT '账户初始化余额',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '初始化时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='财务账户初始化余额' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_action_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `param_name` varchar(100) DEFAULT NULL,
  `action_id` int(10) NOT NULL,
  `action_delete` int(1) NOT NULL COMMENT '查动态时使用  0：不是删除操作  1：为删除操作',
  `content` varchar(500) NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='操作日志表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_announcement` (
  `announcement_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `order_id` int(10) NOT NULL,
  `role_id` int(10) NOT NULL COMMENT '发表人岗位',
  `title` varchar(200) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(10) NOT NULL COMMENT '发表时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `color` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL COMMENT '通知部门id',
  `status` int(1) NOT NULL COMMENT '是否发布1发布2停用',
  `isshow` int(1) NOT NULL DEFAULT '0' COMMENT '是否公开1是0否',
  PRIMARY KEY (`announcement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='存放知识文章信息' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_announcement_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NOT NULL COMMENT '公告ID',
  `role_id` int(11) NOT NULL COMMENT '阅读人',
  `read_time` int(10) NOT NULL COMMENT '阅读时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='公告阅读表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_bank_account` (
  `account_id` int(10) NOT NULL AUTO_INCREMENT,
  `bank_account` varchar(50) NOT NULL COMMENT '银行账号',
  `company` varchar(50) NOT NULL COMMENT '收款单位',
  `open_bank` varchar(50) NOT NULL COMMENT '开户行',
  `description` varchar(50) NOT NULL COMMENT '备注',
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='银行账户表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_business` (
  `business_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '商机id',
  `name` varchar(255) NOT NULL DEFAULT '..',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '商机编号',
  `prefixion` varchar(30) NOT NULL,
  `customer_id` int(10) NOT NULL COMMENT '客户id',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者岗位',
  `owner_role_id` int(10) NOT NULL COMMENT '所有者岗位',
  `total_amount` int(10) unsigned NOT NULL COMMENT '产品总数',
  `total_subtotal_val` decimal(16,2) NOT NULL COMMENT '共计价格(折前)',
  `final_discount_rate` decimal(5,2) NOT NULL COMMENT '整单折扣',
  `final_price` decimal(16,2) NOT NULL COMMENT '最终价格',
  `create_time` int(10) NOT NULL COMMENT '商机创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `status_id` int(10) NOT NULL COMMENT '商机状态id',
  `nextstep_time` int(10) NOT NULL,
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL,
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `contacts_id` int(10) NOT NULL COMMENT '商机联系人',
  `possibility` varchar(500) NOT NULL COMMENT '可能性',
  PRIMARY KEY (`business_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本表存放商机相关信息' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_business_status` (
  `status_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '商机状态',
  `name` varchar(20) DEFAULT NULL COMMENT '商机状态名',
  `order_id` int(10) DEFAULT NULL COMMENT '顺序号',
  `is_end` int(1) NOT NULL,
  `description` varchar(200) DEFAULT NULL COMMENT '商机状态描述',
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='本表存放商机状态信息' AUTO_INCREMENT=101 ;

INSERT INTO `pdcrm_business_status` (`status_id`, `name`, `order_id`, `is_end`, `description`) VALUES
(1, '初步洽谈', 1, 0, '初步洽谈'),
(2, '深入沟通', 2, 0, '深入沟通'),
(3, '销售定价', 3, 0, '定价'),
(98, '合同发票', 6, 1, '合同发票'),
(99, '项目失败', 99, 1, '项目失败'),
(100, '完成收款', 100, 1, '完成收款');

CREATE TABLE IF NOT EXISTS `pdcrm_comment` (
  `comment_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `content` varchar(1000) NOT NULL COMMENT '评论内容',
  `creator_role_id` int(10) NOT NULL COMMENT '评论人',
  `to_role_id` int(10) NOT NULL COMMENT '被评论人',
  `module` varchar(50) NOT NULL COMMENT '模块',
  `module_id` int(10) NOT NULL COMMENT '模块id',
  `create_time` int(10) NOT NULL COMMENT '添加时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

INSERT INTO `pdcrm_config` (`id`, `name`, `value`, `description`) VALUES
(1, 'defaultinfo', 'a:10:{s:4:"logo";N;s:8:"logo_min";N;s:4:"name";s:9:"PDCRM";s:11:"description";s:24:"客户关系管理系统";s:5:"state";s:9:"北京市";s:4:"city";s:9:"市辖区";s:15:"allow_file_type";s:55:"pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,txt,zip";s:19:"contract_alert_time";i:30;s:10:"task_model";s:0:"";s:10:"is_invoice";s:0:"";}', ''),
(2, 'customer_outdays', '7', '客户设置放入客户池天数'),
(3, 'customer_limit_condition', 'month', '客户池领取条件限制 day：今日 week： 本周 month：本月'),
(4, 'customer_limit_counts', '30', '客户池领取次数限制'),
(5, 'leads_outdays', '7', '线索超出天数放入客户池'),
(6, 'contract_custom', 'C_', ''),
(7, 'num_id', '', ''),
(8, 'is_invoice', '', '是否添加开发票选项'),
(9, 'receivables_custom', 'M_', '应收款前缀'),
(10, 'sms', 'a:5:{s:3:"uid";s:17:"";s:6:"";s:6:"";s:9:"sign_name";s:6:"";s:12:"sign_sysname";s:0:"";s:4:"name";s:3:"sms";}', ''),
(11, 'smtp', 'a:9:{s:12:"MAIL_ADDRESS";s:16:"";s:9:"MAIL_SMTP";s:11:"";s:9:"MAIL_PORT";s:2:"";s:14:"MAIL_LOGINNAME";s:16:"";s:13:"MAIL_PASSWORD";s:16:"";s:11:"MAIL_SECURE";N;s:12:"MAIL_CHARSET";s:5:"UTF-8";s:9:"MAIL_AUTH";b:1;s:9:"MAIL_HTML";b:1;}', ''),
(12, 'business_custom', 'M_', '商机编码前缀'),
(13, 'openrecycle', '2', ''),
(14, 'business_code', '0', '商机编号数'),
(15, 'contract_outdays', '30', ''),
(16, 'cc_check', '', ''),
(17, 'bc_check', '', ''),
(18, 'fc_check', '', ''),
(19, 'user_custom', 'K', '员工编号前缀'),
(20, 'uc_check', '', '员工编号前缀是否替换');

CREATE TABLE IF NOT EXISTS `pdcrm_contacts` (
  `contacts_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '联系人id',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者岗位id',
  `name` varchar(50) NOT NULL COMMENT '联系人姓名',
  `post` varchar(20) NOT NULL COMMENT '客户联系人岗位',
  `department` varchar(20) NOT NULL COMMENT '客户联系人部门',
  `sex` varchar(50) NOT NULL COMMENT '联系人性别',
  `saltname` varchar(20) NOT NULL DEFAULT '',
  `telephone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `qq_no` varchar(50) NOT NULL DEFAULT '',
  `contacts_address` varchar(500) NOT NULL COMMENT '地址',
  `zip_code` varchar(20) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '信息更新时间',
  `is_deleted` int(1) NOT NULL COMMENT '是否被删除',
  `delete_role_id` int(10) NOT NULL,
  `delete_time` int(10) NOT NULL,
  `gender` varchar(20) NOT NULL COMMENT '性别',
  `role` varchar(255) NOT NULL DEFAULT '' COMMENT '角色',
  PRIMARY KEY (`contacts_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本表存放客户联系人对应关系信息' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_contacts_data` (
  `contacts_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  PRIMARY KEY (`contacts_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pdcrm_contract` (
  `contract_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `prefixion` varchar(50) NOT NULL COMMENT '表前缀',
  `number` varchar(50) NOT NULL COMMENT '编号',
  `business_id` int(10) NOT NULL COMMENT '商机',
  `supplier_id` int(10) NOT NULL COMMENT '供应商id',
  `customer_id` int(10) NOT NULL COMMENT '客户id',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '合同类型',
  `price` decimal(10,2) NOT NULL COMMENT '总价',
  `count_nums` int(11) NOT NULL COMMENT '产品数量',
  `due_time` int(10) NOT NULL COMMENT '签约日期',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者',
  `content` text NOT NULL COMMENT '合同内容',
  `description` text NOT NULL COMMENT '描述',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `start_date` int(10) NOT NULL COMMENT '生效时间',
  `end_date` int(10) NOT NULL COMMENT '到期时间',
  `status` varchar(20) NOT NULL COMMENT '合同状态',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `contract_name` varchar(100) NOT NULL COMMENT '合同名称',
  `is_checked` int(10) NOT NULL COMMENT '是否审核0未审核1通过2未通过',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  `check_des` int(10) NOT NULL COMMENT '审核备注',
  `examine_role_id` int(11) NOT NULL COMMENT '审核人',
  PRIMARY KEY (`contract_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_contract_check` (
  `check_id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) NOT NULL COMMENT '合同ID',
  `role_id` int(11) NOT NULL COMMENT '负责人ID',
  `is_checked` int(11) NOT NULL COMMENT '审核状态',
  `content` varchar(200) DEFAULT NULL COMMENT '审核内容',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`check_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_control` (
  `control_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '操作id',
  `module_id` int(10) NOT NULL COMMENT '模块id',
  `name` varchar(20) NOT NULL COMMENT '操作名',
  `m` varchar(20) NOT NULL COMMENT '对应Action',
  `a` varchar(20) NOT NULL COMMENT '行为',
  `parameter` varchar(50) NOT NULL COMMENT '参数',
  `description` varchar(200) NOT NULL COMMENT '操作描述',
  PRIMARY KEY (`control_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='本表存放操作信息' AUTO_INCREMENT=79 ;

INSERT INTO `pdcrm_control` (`control_id`, `module_id`, `name`, `m`, `a`, `parameter`, `description`) VALUES
(1, 1, 'crm面板操作', 'index', 'index', '', 'CRM系统面板'),
(2, 7, '修改个人信息', 'User', 'edit', '', '是的法士大夫地方'),
(4, 7, '添加用户', 'User', 'add', '', ''),
(78, 7, '删除员工', 'User', 'delete', '', ''),
(6, 7, '添加部门', 'User', 'department_add', '', ''),
(7, 7, '修改部门', 'User', 'department_edit', '', ''),
(8, 7, '删除部门', 'User', 'department_delete', '', ''),
(9, 7, '添加岗位', 'User', 'role_add', '', ''),
(10, 7, '修改岗位', 'User', 'role_edit', '', ''),
(11, 7, '删除岗位', 'User', 'role_delete', '', ''),
(12, 2, '添加商机', 'Business', 'add', '', ''),
(34, 2, '完整商机信息', 'Business', 'view', '', ''),
(13, 2, '修改商机', 'Business', 'edit', '', ''),
(14, 2, '删除商机', 'Business', 'delete', '', ''),
(15, 2, '添加商机日志', 'Business', 'addLogging', '', ''),
(16, 2, '修改商机日志', 'Business', 'eidtLogging', '', ''),
(17, 2, '删除商机日志', 'Business', 'deleteLogging', '', ''),
(18, 1, '用户登录', 'User', 'login', '', ''),
(19, 1, '用户注册', 'User', 'register', '', ''),
(20, 1, '退出', 'User', 'logout', '', ''),
(21, 7, '查看部门信息', 'User', 'department', '', ''),
(22, 1, '找回密码', 'User', 'lostPW', '', ''),
(23, 1, '重置密码', 'User', 'lostpw_reset', '', ''),
(24, 7, '查看员工信息', 'User', 'index', '', ''),
(25, 7, '查看岗位信息', 'User', 'role', '', ''),
(26, 7, '岗位分配', 'User', 'user_role_relation', '', ''),
(27, 7, '员工资料修改', 'User', 'editUsers', '', ''),
(28, 1, '查看我的日志', 'User', 'mylog', '', ''),
(60, 6, '岗位授权', 'Permission', 'authorize', '', ''),
(30, 7, '个人日志详情', 'User', 'mylog_view', '', ''),
(31, 7, '删除个人日志', 'User', 'mylog_delete', '', ''),
(32, 2, '查看商机信息', 'Business', 'index', '', ''),
(33, 2, '查看商机日志', 'Business', 'logging', '', ''),
(35, 3, '产品列表', 'product', 'index', '', ''),
(36, 3, '添加产品', 'Product', 'add', '', ''),
(37, 3, '修改产品信息', 'product', 'edit', '', ''),
(38, 3, '删除产品', 'Product', 'delete', '', ''),
(39, 3, '查看产品分类信息', 'Product', 'category', '', ''),
(40, 3, '添加产品分类', 'Product', 'category_add', '', ''),
(41, 3, '删除产品分类', 'Product', 'deleteCategory', '', ''),
(42, 3, '修改产品分类', 'Product', 'editcategory', '', ''),
(43, 3, '产品销量统计', 'Product', 'count', '', ''),
(44, 5, '查看客户信息', 'Customer', 'customerView', '', ''),
(45, 5, '添加客户', 'Customer', 'add', '', ''),
(46, 5, '修改客户信息', 'Customer', 'edit', '', ''),
(47, 5, '删除客户', 'Customer', 'delete', '', ''),
(48, 5, '添加客户联系人', 'Contacts', 'add', '', ''),
(49, 5, '查看客户联系人', 'Contacts', 'view', '', ''),
(50, 5, '删除客户联系人', 'Contacts', 'delete', '', ''),
(51, 5, '修改客户联系人', 'Contacts', 'edit', '', ''),
(52, 6, '查看操作模块', 'Permission', 'module', '', ''),
(53, 6, '修改操作模块', 'Permission', 'module_edit', '', ''),
(54, 6, '添加操作模块信息', 'Permission', 'module_add', '', ''),
(55, 6, '删除操作模块', 'Permission', 'module_delete', '', ''),
(56, 6, '查看操作信息', 'Permission', 'index', '', ''),
(57, 6, '修改操作', 'Permission', 'control_edit', '', ''),
(58, 6, '删除模块', 'Permission', 'control_delete', '', ''),
(59, 6, '添加操作', 'Permission', 'control_add', '', ''),
(61, 9, 'smtp设置', 'Config', 'smtpConfig', '', ''),
(62, 9, '删除状态', 'Config', 'deleteStatus', '', ''),
(63, 9, '修改状态', 'Config', 'editStatus', '', ''),
(64, 9, '添加状态', 'Config', 'addStatus', '', ''),
(65, 9, '查看状态', 'Config', 'statusList', '', ''),
(66, 9, '查看状态流', 'Config', 'flowList', '', ''),
(67, 9, '添加状态流', 'Config', 'addStatusflow', '', ''),
(68, 9, '删除状态流', 'Config', 'deleteStatusFlow', '', ''),
(69, 9, '修改状态流信息', 'Config', 'editStatusFlow', '', '');

CREATE TABLE IF NOT EXISTS `pdcrm_customer` (
  `customer_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '客户id',
  `owner_role_id` int(10) NOT NULL COMMENT '所有者岗位',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者id',
  `contacts_id` int(10) NOT NULL DEFAULT '0' COMMENT '首要联系人',
  `name` varchar(333) NOT NULL DEFAULT '' COMMENT '客户名称',
  `origin` varchar(150) NOT NULL DEFAULT '' COMMENT '客户信息来源',
  `industry` varchar(150) NOT NULL DEFAULT '' COMMENT '客户行业',
  `create_time` int(10) NOT NULL COMMENT '建立时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `get_time` int(10) NOT NULL COMMENT '领取或分配时间',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `is_locked` int(1) NOT NULL COMMENT '是否锁定',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `grade` varchar(255) NOT NULL DEFAULT '1' COMMENT '客户等级',
  `customer_code` varchar(255) NOT NULL DEFAULT '' COMMENT '客户编号',
  `address` varchar(500) NOT NULL COMMENT '客户地址',
  `customer_owner_id` varchar(50) NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本表存放客户的相关信息' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_customer_data` (
  `customer_id` int(10) unsigned NOT NULL COMMENT '客户id',
  `no_of_employees` varchar(150) NOT NULL DEFAULT '' COMMENT '员工数',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户附表信息';

CREATE TABLE IF NOT EXISTS `pdcrm_customer_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL COMMENT '客户',
  `user_id` int(10) NOT NULL COMMENT '用户',
  `start_time` int(10) NOT NULL COMMENT '时间',
  `type` int(10) NOT NULL COMMENT '1：领取 2：分配',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COMMENT='客户记录表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_email_template` (
  `template_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `subject` varchar(200) NOT NULL COMMENT '主题',
  `title` varchar(100) NOT NULL,
  `content` varchar(500) NOT NULL COMMENT '内容',
  `order_id` int(4) NOT NULL COMMENT '顺序id',
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短信模板' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_examine` (
  `examine_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `creator_role_id` int(10) NOT NULL COMMENT '创建人',
  `owner_role_id` varchar(200) NOT NULL COMMENT '出差人',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `type` int(2) NOT NULL COMMENT '审批类型',
  `content` text NOT NULL COMMENT '审批内容',
  `examine_role_id` int(10) NOT NULL COMMENT '审批人',
  `cate` varchar(200) DEFAULT NULL COMMENT '审批事项',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `duration` float(10,1) NOT NULL COMMENT '时长：天',
  `money` float(10,2) NOT NULL COMMENT '报销金额/借款金额',
  `budget` int(10) NOT NULL COMMENT '预算金额',
  `advance` int(10) NOT NULL COMMENT '预支金额',
  `start_address` varchar(200) NOT NULL COMMENT '出发地',
  `vehicle` varchar(30) NOT NULL COMMENT '交通工具',
  `end_address` varchar(200) NOT NULL COMMENT '目的地 出差地',
  `examine_status` int(1) NOT NULL COMMENT '状态',
  `order_id` int(2) NOT NULL COMMENT '审核排序ID',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`examine_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='审批表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_examine_check` (
  `check_id` int(11) NOT NULL AUTO_INCREMENT,
  `examine_id` int(11) NOT NULL COMMENT '审批ID',
  `role_id` int(11) NOT NULL COMMENT '负责人ID',
  `is_checked` int(11) NOT NULL COMMENT '审核状态',
  `content` varchar(200) DEFAULT NULL COMMENT '审核内容',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`check_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_examine_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `examine_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件和审批对应关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_examine_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL COMMENT '审批类型',
  `name` varchar(30) NOT NULL COMMENT '审批名',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `type` int(11) NOT NULL COMMENT '0启用1停用',
  `option` int(11) NOT NULL COMMENT '0自选1设置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `pdcrm_examine_status` (`id`, `status`, `name`, `update_time`, `type`, `option`) VALUES
(1, 1, '普通审批', 1486694160, 0, 0),
(2, 2, '请假审批', 1486694160, 0, 0),
(3, 3, '普通报销', 1486694160, 0, 0),
(4, 4, '差旅报销', 1486694160, 0, 0),
(5, 5, '出差申请', 1486694160, 0, 0),
(6, 6, '借款申请', 1486694160, 0, 0);

CREATE TABLE IF NOT EXISTS `pdcrm_examine_step` (
  `step_id` int(10) NOT NULL AUTO_INCREMENT,
  `department_id` int(10) NOT NULL COMMENT '部门ID',
  `process_id` int(10) NOT NULL COMMENT '所属流程',
  `name` varchar(50) NOT NULL COMMENT '步骤名称',
  `position_id` int(10) NOT NULL COMMENT '岗位',
  `role_id` int(10) NOT NULL COMMENT '审批人',
  `order_id` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`step_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='审批流程 - 步骤' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_examine_travel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `examine_id` int(10) NOT NULL COMMENT '审批ID',
  `start_address` varchar(150) NOT NULL COMMENT '出发地',
  `start_time` int(10) NOT NULL COMMENT '出发时间',
  `end_address` varchar(150) NOT NULL COMMENT '目的地',
  `end_time` int(10) NOT NULL COMMENT '到达时间',
  `vehicle` varchar(40) NOT NULL COMMENT '交通工具',
  `duration` varchar(10) NOT NULL COMMENT '住宿(天)',
  `money` decimal(9,2) NOT NULL COMMENT '金额',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_fields` (
  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `model` varchar(20) NOT NULL COMMENT '对应模块小写，如business',
  `is_main` int(1) NOT NULL COMMENT '是否是主表字段1是0否',
  `field` varchar(50) NOT NULL COMMENT '数据库字段名',
  `name` varchar(100) NOT NULL COMMENT '显示标识',
  `form_type` varchar(20) NOT NULL COMMENT '数据类型 text 单行文本 textarea 多行文本 editor 编辑器 box 选项 datetime 日期 number 数字 user员工email邮箱phone手机号mobile电话phone',
  `default_value` varchar(100) NOT NULL COMMENT '默认值',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `max_length` int(4) NOT NULL COMMENT '字段长度',
  `is_unique` int(1) NOT NULL COMMENT '是否唯一1是0否',
  `is_null` int(1) NOT NULL COMMENT '是否允许为空',
  `is_validate` int(1) NOT NULL COMMENT '是否验证',
  `in_index` int(1) NOT NULL COMMENT '是否列表页显示1是0否',
  `in_add` int(1) NOT NULL DEFAULT '1' COMMENT '是否添加时显示1是0否',
  `input_tips` varchar(500) NOT NULL COMMENT '输入提示',
  `setting` text NOT NULL COMMENT '设置',
  `order_id` int(5) NOT NULL COMMENT '同一模块内的顺序id',
  `operating` int(1) NOT NULL COMMENT '0改删、1改、2无、3删',
  `is_show` int(1) NOT NULL COMMENT '是否在客户页显示',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='字段表' AUTO_INCREMENT=47 ;

INSERT INTO `pdcrm_fields` (`field_id`, `model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`) VALUES
(1, '', 1, 'owner_role_id', '负责人', 'user', '', '', 10, 0, 0, 0, 1, 1, '', '', 0, 2, 0),
(2, '', 1, 'creator_role_id', '创建人', 'user', '', '', 10, 0, 0, 0, 1, 1, '', '', 0, 2, 0),
(3, '', 1, 'delete_role_id', '删除人', 'user', '', '', 10, 0, 0, 0, 1, 1, '', '', 0, 2, 0),
(4, '', 1, 'is_deleted', '是否删除', 'deleted', '', '', 1, 0, 0, 0, 1, 1, '', '', 0, 2, 0),
(5, '', 1, 'create_time', '创建时间', 'datetime', '', '', 10, 0, 0, 0, 1, 1, '', '', 0, 2, 0),
(6, '', 1, 'update_time', '更新时间', 'datetime', '', '', 10, 0, 0, 0, 1, 1, '', '', 0, 2, 0),
(7, '', 1, 'delete_time', '删除时间', 'datetime', '', '', 10, 0, 0, 0, 1, 1, '', '', 0, 2, 0),
(8, 'customer', 1, 'name', '客户名称', 'text', '', '5521FF', 333, 1, 1, 1, 1, 1, '测试客户', '', 0, 1, 0),
(9, 'customer', 1, 'origin', '客户信息来源', 'box', '', '333333', 150, 0, 1, 1, 1, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''电话营销'',2=>''网络营销'',3=>''上门推销''))', 4, 1, 0),
(10, 'customer', 1, 'industry', '客户行业', 'box', '', '050505', 150, 0, 0, 0, 1, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''IT/教育'',2=>''电子/商务'',3=>''对外贸易'',4=>''酒店、旅游'',5=>''金融、保险'',6=>''房产行业'',7=>''医疗/保健'',8=>''政府、机关''))', 2, 1, 0),
(11, 'customer', 0, 'no_of_employees', '员工数', 'box', '', '0A0A0A', 150, 0, 0, 0, 1, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''10人以下'',2=>''10--20人'',3=>''20-50人'',4=>''50人以上''))', 5, 1, 0),
(12, 'customer', 0, 'description', '备注', 'textarea', '', '333333', 0, 0, 0, 1, 0, 1, '', '', 7, 1, 0),
(13, 'customer', 1, 'customer_code', '客户编号', 'text', '', '333333', 0, 1, 1, 1, 1, 1, '', '', 1, 1, 0),
(14, 'customer', 1, 'grade', '客户等级', 'box', '1', '333333', 0, 0, 1, 0, 1, 1, '', 'array(''type''=>''radio'',''data''=>array(1=>''1'',2=>''2'',3=>''3'',4=>''4'',5=>''5''))', 6, 2, 0),
(15, 'customer', 1, 'address', '客户地址', 'address', '', '333333', 0, 0, 1, 0, 1, 1, '', '', 3, 1, 0),
(16, 'customer', 1, 'customer_owner_id', '客户负责人', 'text', '', '333333', 0, 0, 0, 0, 1, 1, '', '', 0, 2, 0),
(17, 'product', 1, 'sketch', '商品描述', 'text', '', '333333', 0, 0, 1, 0, 0, 1, '', '', 0, 2, 0),
(18, 'product', 1, 'product_num', '产品编号', 'text', '', '333333', 0, 0, 0, 0, 1, 1, '', '', 3, 1, 0),
(19, 'product', 1, 'standard', '规格', 'box', '', '333333', 200, 0, 1, 1, 1, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''个'',2=>''箱'',3=>''套'',4=>''盒'',5=>''瓶'',6=>''块'',7=>''只'',8=>''把'',9=>''枚'',10=>''条''))', 2, 1, 0),
(20, 'product', 0, 'description', '备注', 'textarea', '', '', 0, 0, 0, 0, 0, 1, '', '', 6, 1, 0),
(21, 'product', 1, 'name', '产品名称', 'text', '', '021012', 200, 0, 1, 1, 1, 1, '', '', 0, 1, 0),
(22, 'product', 1, 'cost_price', '成本价', 'floatnumber', '', '1F1F1F', 10, 0, 0, 0, 1, 1, '', '', 4, 2, 0),
(23, 'product', 1, 'suggested_price', '建议售价', 'floatnumber', '', '', 0, 0, 0, 0, 1, 1, '', '', 5, 2, 0),
(24, 'product', 1, 'category_id', '产品类别', 'p_box', '', '', 0, 0, 0, 0, 1, 1, '', '', 1, 2, 0),
(25, 'contacts', 1, 'role', '角色', 'box', '', '333333', 0, 0, 1, 1, 1, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''普通人'',2=>''决策人'',3=>''分项决策人'',4=>''商务决策'',5=>''技术决策'',6=>''财务决策'',7=>''使用人'',8=>''意见影响人''))', 2, 1, 0),
(26, 'contacts', 1, 'saltname', '尊称', 'box', '', '333333', 50, 0, 1, 0, 1, 1, '', 'array(''type''=>''radio'',''data''=>array(1=>''先生'',2=>''女士''))', 3, 1, 0),
(27, 'contacts', 1, 'customer_id', '所属客户', 'customer', '', '333333', 50, 0, 0, 1, 1, 1, '', '', 0, 2, 0),
(28, 'contacts', 1, 'post', '职位', 'text', '', '333333', 20, 0, 1, 0, 1, 1, '', '', 4, 2, 0),
(29, 'contacts', 1, 'telephone', '手机', 'mobile', '', '333333', 50, 0, 1, 0, 1, 1, '', '', 5, 2, 1),
(30, 'contacts', 1, 'email', '邮箱', 'email', '', '333333', 50, 0, 1, 0, 1, 1, '', '', 7, 2, 0),
(31, 'contacts', 1, 'qq_no', 'QQ', 'text', '', '333333', 50, 0, 1, 0, 1, 1, '', '', 6, 2, 0),
(32, 'contacts', 1, 'contacts_address', '地址', 'address', '', '333333', 100, 0, 1, 1, 0, 1, '', '', 8, 1, 0),
(33, 'contacts', 1, 'name', '联系人姓名', 'text', '', '333333', 20, 0, 1, 1, 1, 1, '', '', 1, 2, 1),
(34, 'contacts', 1, 'zip_code', '邮编', 'text', '', '333333', 20, 0, 1, 0, 0, 1, '', '', 9, 1, 0),
(35, 'contacts', 1, 'description', '备注', 'textarea', '', '333333', 500, 0, 1, 0, 0, 1, '', '', 10, 1, 0),
(36, 'leads', 1, 'nextstep_time', '下次联系时间', 'datetime', '', '', 0, 0, 0, 0, 1, 1, '', '', 9, 2, 0),
(37, 'leads', 1, 'nextstep', '下次联系内容', 'text', '', '', 0, 0, 0, 0, 1, 1, '', '', 10, 2, 0),
(38, 'leads', 1, 'contacts_name', '联系人姓名', 'text', '', '333333', 0, 0, 1, 1, 1, 1, '', '', 2, 1, 0),
(39, 'leads', 1, 'saltname', '尊称', 'box', '', '333333', 0, 0, 0, 0, 1, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''女士'',2=>''先生''))', 5, 1, 0),
(40, 'leads', 1, 'mobile', '手机', 'mobile', '', '333333', 0, 0, 0, 1, 1, 1, '', '', 6, 1, 0),
(41, 'leads', 1, 'email', '邮箱', 'email', '', '', 0, 0, 0, 1, 0, 1, '', '', 7, 1, 0),
(42, 'leads', 1, 'position', '职位', 'text', '', '', 0, 0, 0, 0, 0, 1, '', '', 4, 1, 0),
(43, 'leads', 1, 'address', '地址', 'address', '', '333333', 0, 0, 0, 0, 0, 1, '', '', 8, 0, 0),
(44, 'leads', 0, 'description', '备注', 'textarea', '', '', 0, 0, 0, 0, 0, 1, '', '', 11, 1, 0),
(45, 'leads', 1, 'name', '公司名', 'text', '', '05330E', 0, 0, 1, 0, 1, 1, '', '', 3, 1, 0),
(46, 'leads', 1, 'source', '来源', 'box', '', '333333', 0, 0, 1, 0, 0, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''网络营销'',2=>''公开媒体'',3=>''合作伙伴'',4=>''员工介绍'',5=>''广告'',6=>''推销电话'',7=>''其他''))', 1, 1, 0);

CREATE TABLE IF NOT EXISTS `pdcrm_file` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附件主键',
  `name` varchar(50) NOT NULL COMMENT '附件名',
  `role_id` int(10) NOT NULL COMMENT '创建者岗位',
  `size` int(10) NOT NULL COMMENT '文件大小字节',
  `create_date` int(10) NOT NULL COMMENT '创建时间',
  `file_path` varchar(200) NOT NULL COMMENT '文件路径',
  `file_path_thumb` varchar(200) NOT NULL COMMENT '图片缩略图',
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_finance_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL COMMENT '活动编号',
  `name` varchar(255) NOT NULL COMMENT '活动名称',
  `type` tinyint(1) NOT NULL,
  `account_ids` varchar(500) NOT NULL COMMENT '科目ID',
  `remark` varchar(1000) NOT NULL COMMENT '备注',
  `is_pause` tinyint(1) NOT NULL COMMENT '1停用',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='财务活动表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_knowledge` (
  `knowledge_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `category_id` int(10) NOT NULL COMMENT '文章类别',
  `role_id` int(10) NOT NULL COMMENT '发表人岗位',
  `title` varchar(200) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(10) NOT NULL COMMENT '发表时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `hits` int(10) NOT NULL COMMENT '点击次数',
  `color` varchar(50) NOT NULL,
  PRIMARY KEY (`knowledge_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='存放知识文章信息' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_knowledge_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章类别id',
  `parent_id` int(11) NOT NULL COMMENT '父类别id',
  `name` varchar(30) NOT NULL COMMENT '类别名称',
  `description` varchar(100) NOT NULL COMMENT '备注',
  `to_department` varchar(200) NOT NULL COMMENT '权限部门id',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='知识文章分类信息表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_leads` (
  `leads_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '线索主键',
  `owner_role_id` int(10) NOT NULL COMMENT '拥有者岗位',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者岗位',
  `name` varchar(255) NOT NULL,
  `position` varchar(20) NOT NULL COMMENT '职位',
  `contacts_name` varchar(255) NOT NULL,
  `saltname` varchar(255) NOT NULL DEFAULT '',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL COMMENT '电子邮箱',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人的岗位id',
  `delete_time` int(10) NOT NULL,
  `is_transformed` int(1) NOT NULL COMMENT '是否转换',
  `transform_role_id` int(10) NOT NULL COMMENT '转换者',
  `contacts_id` int(10) NOT NULL COMMENT '转换成联系人',
  `customer_id` int(10) NOT NULL COMMENT '转换成的客户',
  `business_id` int(10) NOT NULL COMMENT '转换成的商机',
  `nextstep` varchar(50) NOT NULL COMMENT '下一次联系',
  `nextstep_time` int(10) NOT NULL COMMENT '联系时间',
  `have_time` int(10) NOT NULL COMMENT '最后一次领取时间',
  `first_time` int(10) NOT NULL COMMENT '第一次跟进时间',
  `address` varchar(500) NOT NULL,
  `source` varchar(500) NOT NULL COMMENT '线索来源',
  PRIMARY KEY (`leads_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线索表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_leads_data` (
  `leads_id` int(10) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`leads_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_leads_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `leads_id` int(10) NOT NULL,
  `owner_role_id` int(10) NOT NULL,
  `start_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_log` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `role_id` int(11) NOT NULL COMMENT '创建者岗位',
  `category_id` int(10) NOT NULL,
  `sign` int(1) NOT NULL DEFAULT '0' COMMENT '1关联签到',
  `create_date` int(10) NOT NULL COMMENT '创建时间',
  `update_date` int(10) NOT NULL COMMENT '更新时间',
  `subject` varchar(200) NOT NULL COMMENT '主题',
  `content` text NOT NULL COMMENT '内容',
  `comment_id` int(10) NOT NULL COMMENT '评论id',
  `about_roles` varchar(200) NOT NULL COMMENT '新增相关人',
  `about_roles_name` varchar(500) NOT NULL COMMENT '新增相关人姓名',
  `status` tinyint(1) NOT NULL COMMENT '0未阅1已阅2已点评',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='日志表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_log_category` (
  `category_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(200) NOT NULL COMMENT '分类名',
  `order_id` int(10) NOT NULL COMMENT '顺序id',
  `description` varchar(500) NOT NULL COMMENT '描述',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='日志类型表' AUTO_INCREMENT=5 ;

INSERT INTO `pdcrm_log_category` (`category_id`, `name`, `order_id`, `description`) VALUES
(1, '模块日志', 4, '其他模块的相关日志'),
(2, '月报', 3, '每月工作总结'),
(3, '周报', 2, '每周工作总结'),
(4, '日报', 1, '每日工作总结');

CREATE TABLE IF NOT EXISTS `pdcrm_log_talk` (
  `talk_id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL COMMENT '组内分标示',
  `log_id` int(10) NOT NULL,
  `send_role_id` int(10) NOT NULL COMMENT '发送者id',
  `receive_role_id` int(10) NOT NULL COMMENT '接收者id',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(10) NOT NULL,
  `g_mark` varchar(50) NOT NULL COMMENT '标示',
  PRIMARY KEY (`talk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='日志评论回复表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_member` (
  `member_id` int(10) NOT NULL AUTO_INCREMENT,
  `telephone` varchar(20) NOT NULL COMMENT '手机号',
  `name` varchar(200) NOT NULL COMMENT '客户名',
  `password` varchar(200) NOT NULL COMMENT '密码',
  `salt` varchar(4) NOT NULL COMMENT '安全符',
  `honorific` varchar(50) NOT NULL COMMENT '尊称',
  `birth` date NOT NULL COMMENT '出生日期',
  `create_time` int(10) NOT NULL COMMENT '创建（注册）日期',
  `update_time` int(10) NOT NULL COMMENT '修改日期',
  `lostpw_time` int(10) NOT NULL COMMENT '密码找回时间',
  `reg_ip` varchar(50) NOT NULL COMMENT '注册IP',
  `address_id` int(10) NOT NULL COMMENT '上次（默认）送货地址',
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线上客户表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_member_address` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '线上客户ID',
  `address` varchar(2000) NOT NULL COMMENT '地址',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线上客户地址表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_message` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `to_role_id` int(11) unsigned NOT NULL,
  `from_role_id` int(11) unsigned NOT NULL,
  `content` text NOT NULL,
  `read_time` int(11) unsigned NOT NULL,
  `send_time` int(11) unsigned NOT NULL,
  `status` int(1) NOT NULL,
  `file_id` int(10) NOT NULL COMMENT '附件ID',
  `is_mark` TINYINT(1) NOT NULL COMMENT '1已标记',
  PRIMARY KEY (`message_id`),
  KEY `to_role_id` (`to_role_id`,`from_role_id`,`read_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_note` (
  `note_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL,
  `content` varchar(1000) NOT NULL COMMENT '内容',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='便笺表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_order` (
  `order_id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT '编号',
  `prefixion` varchar(50) NOT NULL COMMENT '前缀',
  `price` decimal(10,2) NOT NULL COMMENT '金额',
  `payment_state` tinyint(1) NOT NULL COMMENT '0未支付1已支付（线上）2已支付（线下）3已退还',
  `check_state` tinyint(1) NOT NULL COMMENT '0未接单1已接单2已取消',
  `status` int(10) NOT NULL COMMENT '订单阶段',
  `member_id` int(10) NOT NULL COMMENT '客户ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `check_role_id` int(10) NOT NULL COMMENT '审核人ID',
  `remark` varchar(1000) NOT NULL COMMENT '备注',
  `address` varchar(1000) NOT NULL COMMENT '收货地址',
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商城订单表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_payables` (
  `payables_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '应付款id',
  `customer_id` int(10) NOT NULL COMMENT '客户id',
  `receiver` varchar(100) NOT NULL COMMENT '收款单位',
  `contract_id` int(10) DEFAULT NULL COMMENT '合同id',
  `purchase_id` int(10) DEFAULT NULL COMMENT '采购单id',
  `sales_id` int(10) DEFAULT NULL COMMENT '销售单',
  `sales_code` varchar(20) NOT NULL COMMENT '销售单序列号',
  `purchase_code` varchar(20) NOT NULL COMMENT '采购单序列号',
  `type` int(1) NOT NULL COMMENT '0：普通  1：采购  2：销售退货',
  `name` varchar(500) NOT NULL COMMENT '应付款名',
  `price` decimal(10,2) NOT NULL COMMENT '应付金额',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者id',
  `owner_role_id` int(10) NOT NULL,
  `description` text NOT NULL COMMENT '描述',
  `pay_time` int(10) NOT NULL COMMENT '付款时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(2) NOT NULL COMMENT '状态0未收1部分收2已收',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `is_deleted` int(1) NOT NULL DEFAULT '0' COMMENT ' 是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  PRIMARY KEY (`payables_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应付款表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_paymentorder` (
  `paymentorder_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '付款单id',
  `name` varchar(500) NOT NULL COMMENT '付款单主题',
  `money` decimal(10,2) NOT NULL COMMENT '付款金额',
  `payables_id` int(10) NOT NULL COMMENT '应付款id',
  `type` int(10) NOT NULL COMMENT '应付款，付款单类别',
  `description` text NOT NULL COMMENT '描述',
  `pay_time` int(10) NOT NULL COMMENT '付款时间',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者id',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态0待审1审核通过2审核失败',
  `update_time` int(10) NOT NULL COMMENT '审核时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `is_deleted` int(1) NOT NULL DEFAULT '0' COMMENT ' 是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `receipt_account` varchar(50) DEFAULT NULL COMMENT '付款账户',
  `examine_role_id` int(11) NOT NULL COMMENT '审核人',
  `check_des` varchar(200) NOT NULL COMMENT '审核备注',
  `bank_account_id` int(10) NOT NULL,
  `bank_name` int(100) NOT NULL,
  `bank_acount` int(100) NOT NULL,
  `company` int(100) NOT NULL,
  PRIMARY KEY (`paymentorder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='付款单' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_permission` (
  `permission_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `role_id` int(10) NOT NULL COMMENT '岗位id',
  `position_id` int(10) NOT NULL COMMENT '岗位组id',
  `url` varchar(50) NOT NULL COMMENT '对应模块操作',
  `description` varchar(200) NOT NULL COMMENT '权限备注',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1自己和下属2所有人3自己4部门所有人',
  PRIMARY KEY (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本表用来存放权限信息' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_position` (
  `position_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '岗位id',
  `parent_id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `department_id` int(10) NOT NULL,
  `description` varchar(200) NOT NULL COMMENT '描述',
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='岗位表控制权限' AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_position` (`position_id`, `parent_id`, `name`, `department_id`, `description`) VALUES
(1, 0, 'CEO', 1, '');

CREATE TABLE IF NOT EXISTS `pdcrm_praise` (
  `praise_id` int(10) NOT NULL AUTO_INCREMENT,
  `log_id` int(10) NOT NULL COMMENT '日志id',
  `role_id` int(10) NOT NULL COMMENT '赞的人role_id',
  PRIMARY KEY (`praise_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_product` (
  `product_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '产品id',
  `category_id` int(11) NOT NULL COMMENT '产品类别的id',
  `name` varchar(200) NOT NULL DEFAULT '',
  `creator_role_id` int(10) NOT NULL COMMENT '产品信息添加者',
  `cost_price` int(10) NOT NULL DEFAULT '0' COMMENT '成本价',
  `suggested_price` float(10,2) NOT NULL COMMENT '建议售价',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `standard` varchar(200) NOT NULL DEFAULT '' COMMENT '规格',
  `points` int(10) NOT NULL COMMENT '产品积分',
  `description` varchar(2000) NOT NULL DEFAULT '' COMMENT '备注',
  `is_deleted` tinyint(1) NOT NULL COMMENT '1删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除人ID',
  `product_num` varchar(255) NOT NULL DEFAULT '' COMMENT '产品编号',
  `is_shop` tinyint(1) NOT NULL COMMENT '1商城展示',
  `sketch` varchar(500) NOT NULL COMMENT '商品描述',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_product_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '产品类别id',
  `parent_id` int(11) NOT NULL COMMENT '父类别id',
  `name` varchar(30) NOT NULL COMMENT '类别名称',
  `description` varchar(100) NOT NULL COMMENT '备注',
  `is_shop` tinyint(1) NOT NULL COMMENT '1展示',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_product_category` (`category_id`, `parent_id`, `name`, `description`) VALUES
(1, 0, '默认', '');

CREATE TABLE IF NOT EXISTS `pdcrm_product_data` (
  `product_id` int(10) NOT NULL COMMENT '主键',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品信息附表';

CREATE TABLE IF NOT EXISTS `pdcrm_product_images` (
  `images_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` int(10) NOT NULL COMMENT '关联产品id',
  `is_main` int(1) NOT NULL COMMENT '0：副图  1：主图',
  `name` varchar(500) NOT NULL COMMENT '源文件名',
  `save_name` varchar(500) NOT NULL COMMENT '保存至服务器的文件名',
  `size` varchar(500) NOT NULL COMMENT 'KB',
  `path` varchar(500) NOT NULL COMMENT '路径',
  `thumb_path` varchar(255) NOT NULL COMMENT '缩略图',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `listorder` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`images_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品图库' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_receivables` (
  `receivables_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '应收款id',
  `customer_id` int(10) NOT NULL COMMENT '客户id',
  `payer` varchar(500) NOT NULL COMMENT '付款单位',
  `contract_id` int(10) DEFAULT NULL COMMENT '合同id',
  `sales_id` int(10) DEFAULT NULL COMMENT '销售单',
  `purchase_id` int(10) DEFAULT NULL COMMENT '采购单id',
  `sales_code` varchar(20) NOT NULL COMMENT '销售单序列号',
  `purchase_code` varchar(20) NOT NULL COMMENT '采购单序列号',
  `type` int(10) NOT NULL COMMENT '0：普通  1：销售  2：采购退货',
  `name` varchar(500) NOT NULL COMMENT '应收款名',
  `price` decimal(10,2) NOT NULL COMMENT '应收金额',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者id',
  `owner_role_id` int(10) NOT NULL,
  `description` text NOT NULL COMMENT '描述',
  `pay_time` int(10) NOT NULL COMMENT '收款时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(2) NOT NULL COMMENT '状态0未收1部分收2已收',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `is_deleted` int(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `prefixion` varchar(50) NOT NULL COMMENT '表前缀',
  PRIMARY KEY (`receivables_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应收款表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_receivingorder` (
  `receivingorder_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '收款单id',
  `name` varchar(500) NOT NULL COMMENT '收款单主题',
  `money` decimal(10,2) NOT NULL COMMENT '收款金额',
  `receivables_id` int(10) NOT NULL COMMENT '应收款id',
  `type` tinyint(1) NOT NULL COMMENT '应收款类别',
  `description` text NOT NULL COMMENT '描述',
  `pay_time` int(10) NOT NULL COMMENT '付款时间',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者id',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态0待审1审核通过2审核失败',
  `update_time` int(10) NOT NULL COMMENT '审核时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `is_deleted` int(1) NOT NULL DEFAULT '0' COMMENT ' 是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `contract_id` int(10) NOT NULL COMMENT '合同ID',
  `invoice` int(1) NOT NULL COMMENT '1开发票 2不开发票',
  `invoice_num` varchar(100) NOT NULL COMMENT '发票号',
  `invoice_money` decimal(9,2) NOT NULL COMMENT '发票金额',
  `check_des` varchar(200) NOT NULL COMMENT '审核备注',
  `bank_account_id` int(10) NOT NULL COMMENT '账户id',
  `receipt_account` varchar(500) DEFAULT NULL COMMENT '收款账户',
  `examine_role_id` int(11) NOT NULL COMMENT '审核人',
  `receipt_bank` varchar(500) NOT NULL COMMENT '开户行',
  `company` varchar(100) NOT NULL,
  PRIMARY KEY (`receivingorder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收款单' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_remind` (
  `remind_id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT '相关模块',
  `module_id` int(10) NOT NULL COMMENT '相关模块ID',
  `remind_time` int(10) NOT NULL COMMENT '提醒时间',
  `content` varchar(500) NOT NULL COMMENT '提醒内容',
  `create_role_id` int(10) NOT NULL COMMENT '提醒人ID',
  `is_remind` tinyint(1) NOT NULL COMMENT '1已提醒',
  PRIMARY KEY (`remind_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='相关提醒表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_role` (
  `role_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '岗位id',
  `position_id` int(10) NOT NULL COMMENT '岗位组名',
  `user_id` int(10) NOT NULL COMMENT '员工id',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本表存放用户岗位信息' AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_role` (`role_id`, `position_id`,`user_id`) VALUES
(1, 1, 1);

CREATE TABLE IF NOT EXISTS `pdcrm_role_department` (
  `department_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `parent_id` int(10) NOT NULL COMMENT '父类部门id',
  `name` varchar(50) NOT NULL COMMENT '部门名',
  `charge_position` int(10) NOT NULL COMMENT '部门最高级别岗位',
  `description` varchar(200) NOT NULL COMMENT '部门描述',
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本表存放部门信息' AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_role_department` (`department_id`, `parent_id`, `name`, `description`) VALUES
(1, 0, '办公室', '公司文档管理、财务管理');

CREATE TABLE IF NOT EXISTS `pdcrm_r_business_contacts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `contacts_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_business_contract` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL COMMENT '商机id',
  `contract_id` int(10) NOT NULL COMMENT '合同id',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商机合同关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_business_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_business_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `file_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_business_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商机和日志id对应关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_business_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `ori_price` decimal(12,2) NOT NULL COMMENT '原价',
  `discount_rate` decimal(5,2) NOT NULL COMMENT '单个折扣',
  `unit_price` decimal(12,2) NOT NULL COMMENT '销售价格',
  `sales_price` float(10,2) NOT NULL COMMENT '成交价',
  `estimate_price` float(10,2) NOT NULL COMMENT '报价',
  `amount` int(10) NOT NULL COMMENT '产品交易数量',
  `unit` varchar(50) NOT NULL DEFAULT '' COMMENT '单位',
  `subtotal` decimal(14,2) NOT NULL COMMENT '小计',
  `description` varchar(200) NOT NULL COMMENT '产品交易备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_business_status` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '关系主键',
  `business_id` int(10) NOT NULL COMMENT '商机id',
  `gain_rate` int(3) NOT NULL,
  `status_id` int(10) NOT NULL COMMENT '状态id',
  `description` text NOT NULL COMMENT '阶段描述',
  `due_date` int(10) NOT NULL COMMENT '预计成交日期',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人id',
  `update_time` int(10) NOT NULL COMMENT '推进时间',
  `update_role_id` int(10) NOT NULL COMMENT '推进人',
  `total_price` float(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商机状态阶段表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_contacts_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contacts_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_contract_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL COMMENT '合同id',
  `file_id` int(10) NOT NULL COMMENT '文件id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同文件关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_contract_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `sales_price` float(10,2) NOT NULL,
  `estimate_price` float(10,2) NOT NULL,
  `amount` int(10) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_contract_sales` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL COMMENT '合同id',
  `sales_id` int(10) NOT NULL COMMENT '销售单id',
  `distribution_id` int(10) NOT NULL COMMENT '待配货ID',
  `sales_type` int(10) NOT NULL COMMENT '0销售1退货2待配货',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同与销售单关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_customer_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL,
  `file_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_customer_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_file_finance` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `finance_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_file_leads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `leads_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件和日志对应关系' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_file_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件和日志对应关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_file_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_finance_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `finance_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_leads_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `leads_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_log_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `log_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_member_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '客户ID',
  `file_id` int(10) NOT NULL COMMENT '附件ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线上客户附件关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_member_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '线上客户ID',
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线上客户日志表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_order_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `log_id` int(10) NOT NULL COMMENT '日志ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单进度日志表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_order_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `product_id` int(10) NOT NULL COMMENT '产品ID',
  `unit_price` decimal(10,2) NOT NULL COMMENT '单价',
  `ori_price` decimal(10,2) NOT NULL COMMENT '建议售价',
  `amount` int(10) NOT NULL COMMENT '数量',
  `unit` varchar(50) NOT NULL COMMENT '单位',
  `subtotal` decimal(10,2) NOT NULL COMMENT '小计',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单产品关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_sales` (
  `sales_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '销售单id',
  `customer_id` int(10) NOT NULL COMMENT '客户id',
  `creator_role_id` int(10) NOT NULL COMMENT '制单人',
  `sn_code` varchar(20) NOT NULL COMMENT '销售单序列号',
  `subject` varchar(100) NOT NULL COMMENT '主题',
  `prime_price` decimal(9,2) NOT NULL COMMENT '销售单整体价格未减折扣额时价格',
  `final_discount_rate` decimal(10,2) NOT NULL COMMENT '折扣率',
  `sales_price` decimal(9,2) NOT NULL COMMENT '折扣后销售单实际应付金额',
  `total_amount` int(10) NOT NULL COMMENT '总数量',
  `type` int(1) NOT NULL COMMENT '0：销售   1：退货',
  `status` int(10) NOT NULL COMMENT '97：未出库 98： 已出库 99：未入库   100：已入库',
  `is_checked` int(1) NOT NULL COMMENT '0：未审核   1：审核',
  `shipping_address` varchar(300) DEFAULT NULL COMMENT '发货地址',
  `discount_price` decimal(9,2) NOT NULL COMMENT '折扣额',
  `description` varchar(500) NOT NULL,
  `create_time` int(10) NOT NULL,
  `sales_time` int(10) NOT NULL COMMENT '销售日期',
  `outof_time` int(10) NOT NULL COMMENT '出库时间',
  `logistics_number` varchar(100) NOT NULL COMMENT '物流单号',
  `receiving_people` varchar(50) NOT NULL COMMENT '收件人',
  `receiving_phone` varchar(20) NOT NULL COMMENT '收件电话',
  `check_role_id` int(11) NOT NULL COMMENT '审核人',
  PRIMARY KEY (`sales_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='销售表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_sales_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) NOT NULL COMMENT '销售单ID',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `content` varchar(200) NOT NULL COMMENT '物流详情',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_sales_product` (
  `sales_product_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '销售单商品id',
  `sales_id` int(10) NOT NULL COMMENT '销售单id',
  `product_id` int(10) NOT NULL COMMENT '商品id',
  `warehouse_id` int(10) NOT NULL COMMENT '仓库id',
  `amount` int(10) NOT NULL COMMENT '数量',
  `ori_price` decimal(10,2) NOT NULL COMMENT '建议售价',
  `unit_price` decimal(9,2) NOT NULL COMMENT '销售时商品单价',
  `unit` varchar(50) NOT NULL COMMENT '商品单位',
  `cost_price` decimal(10,2) NOT NULL COMMENT '销售时成本价',
  `discount_rate` decimal(10,2) NOT NULL COMMENT '折扣率',
  `tax_rate` int(10) NOT NULL COMMENT '税率',
  `description` varchar(500) NOT NULL COMMENT '描述',
  `subtotal` decimal(10,2) NOT NULL COMMENT '小计',
  PRIMARY KEY (`sales_product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='销售单商品表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_sign` (
  `sign_id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL,
  `role_id` int(10) NOT NULL,
  `x` float(10,6) NOT NULL COMMENT 'x坐标',
  `y` float(10,6) NOT NULL COMMENT 'y坐标',
  `address` varchar(50) NOT NULL,
  `log` varchar(100) NOT NULL,
  `create_time` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `log_id` int(10) NOT NULL COMMENT '关联沟通日志',
  PRIMARY KEY (`sign_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_sign_img` (
  `img_id` int(10) NOT NULL AUTO_INCREMENT,
  `sign_id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '图片上传时名字',
  `save_name` varchar(100) NOT NULL COMMENT '图片保存名',
  `path` varchar(200) NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`img_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_sms_record` (
  `sms_record_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL COMMENT '发件人',
  `telephone` text NOT NULL COMMENT '发送号码',
  `content` text NOT NULL COMMENT '发送内容',
  `sendtime` int(10) NOT NULL COMMENT '发送时间',
  `phone_counts` int(11) NOT NULL COMMENT '短信数',
  PRIMARY KEY (`sms_record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短信发送记录表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_sms_template` (
  `template_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `subject` varchar(200) NOT NULL COMMENT '主题',
  `content` varchar(500) NOT NULL COMMENT '内容',
  `order_id` int(4) NOT NULL COMMENT '顺序id',
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短信模板' AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_sms_template` (`template_id`, `subject`, `content`, `order_id`) VALUES
(1, '默认模板', '有一个特别的日子，鲜花都为你展现；有一个特殊的日期，阳光都为你温暖；有一个美好的时刻，百灵都为你欢颜；有一个难忘的今天，亲朋都为你祝愿；那就是今天是你的生日，祝你幸福安康顺意连年！', 1);

CREATE TABLE IF NOT EXISTS `pdcrm_stock` (
  `stock_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` int(10) NOT NULL COMMENT '商品id',
  `warehouse_id` int(10) NOT NULL COMMENT '仓库id',
  `amounts` int(10) NOT NULL COMMENT '库存数量',
  `last_change_time` int(10) NOT NULL COMMENT '库存上次变动时间',
  PRIMARY KEY (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='库存' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_top` (
  `top_id` int(10) NOT NULL AUTO_INCREMENT,
  `module_id` int(10) NOT NULL COMMENT '相关模块ID',
  `set_top` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1置顶',
  `top_time` int(10) NOT NULL COMMENT '置顶时间',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `module` varchar(50) NOT NULL DEFAULT 'business' COMMENT '置顶模块',
  PRIMARY KEY (`top_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='置顶表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `role_id` int(10) NOT NULL COMMENT '当前使用岗位',
  `category_id` int(11) NOT NULL COMMENT '用户类别',
  `status` int(1) NOT NULL COMMENT '1启用2停用3未激活',
  `type` int(4) NOT NULL COMMENT '员工类型',
  `name` varchar(20) NOT NULL COMMENT '用户名',
  `prefixion` varchar(200) NOT NULL COMMENT '前缀',
  `number` varchar(200) NOT NULL COMMENT '编号',
  `full_name` varchar(255) NOT NULL COMMENT '用户姓名',
  `img` varchar(100) NOT NULL COMMENT '头像',
  `thumb_path` varchar(255) NOT NULL COMMENT '头像缩略图',
  `password` varchar(32) NOT NULL COMMENT '用户密码',
  `salt` varchar(4) NOT NULL COMMENT '安全符',
  `sex` int(1) NOT NULL COMMENT '用户性别1男2女',
  `email` varchar(30) NOT NULL COMMENT '用户邮箱',
  `telephone` varchar(20) NOT NULL COMMENT '用户的电话',
  `address` varchar(100) NOT NULL COMMENT '用户的联系地址',
  `hometown` varchar(255) NOT NULL COMMENT '家乡',
  `birthday` date NOT NULL COMMENT '出生日期',
  `entry` date NOT NULL COMMENT '入职日期',
  `introduce` varchar(1000) NOT NULL COMMENT '自我介绍',
  `office_tel` varchar(30) NOT NULL COMMENT '办公电话',
  `qq` varchar(255) NOT NULL COMMENT 'QQ/MSN',
  `navigation` varchar(1000) NOT NULL COMMENT '用户自定义导航菜单',
  `simple_menu` varchar(1000) NOT NULL COMMENT '自定义快捷添加菜单',
  `dashboard` text NOT NULL COMMENT '个人面板',
  `reg_ip` varchar(15) NOT NULL COMMENT '注册时的ip',
  `reg_time` int(10) NOT NULL COMMENT '用户的注册时间',
  `last_login_time` int(10) NOT NULL COMMENT '用户最后一次登录的时间',
  `lostpw_time` int(10) NOT NULL COMMENT '用户申请找回密码的时间',
  `weixinid` varchar(150) NOT NULL,
  `last_read_time` varchar(500) NOT NULL COMMENT '手机端客户，商机等最后阅读时间',
  `token` varchar(32) NOT NULL COMMENT '会话机制',
  `token_time` int(11) NOT NULL COMMENT '会话时间',
  `developer_token` varchar(100) NOT NULL COMMENT '推送token',
  `is_receivables` int(1) NOT NULL COMMENT '合同审核是否生成应收款',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本表用来存放用户的相关基本信息' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_user_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '类别id',
  `name` varchar(20) NOT NULL COMMENT '类别的名字',
  `description` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='本表存放用户类别信息' AUTO_INCREMENT=3 ;

INSERT INTO `pdcrm_user_category` (`category_id`, `name`, `description`) VALUES
(1, '管理员', ''),
(2, '员工', '');

CREATE TABLE IF NOT EXISTS `pdcrm_user_smtp` (
  `smtp_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '发件箱名称',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `settinginfo` text NOT NULL COMMENT 'smtp设置',
  PRIMARY KEY (`smtp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='smtp设置表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_voucher_account` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL COMMENT '科目编码',
  `name` varchar(255) NOT NULL COMMENT '科目名称',
  `category` int(10) NOT NULL COMMENT '科目类别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '借贷方向（1借2贷）',
  `parent_id` int(10) NOT NULL COMMENT '父类ID',
  `is_pause` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用1启用',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `create_role_id` int(10) NOT NULL COMMENT '创建人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='财务科目' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_warehouse` (
  `warehouse_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '仓库id',
  `name` varchar(200) NOT NULL COMMENT '仓库名',
  `description` varchar(500) NOT NULL COMMENT '描述',
  PRIMARY KEY (`warehouse_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='仓库表' AUTO_INCREMENT= 3;

INSERT INTO `pdcrm_warehouse` (`warehouse_id`, `name`, `description`) VALUES
(1, '1号仓库', '存储固体货物'),
(2, '2号仓库', '存储液体货物');

CREATE TABLE IF NOT EXISTS `pdcrm_workrule` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL COMMENT '年份',
  `sdate` int(10) NOT NULL COMMENT '开始时间',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1休息2工作日',
  `status` tinyint(1) NOT NULL COMMENT '1自定义时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='工作日配置' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_workrule_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL COMMENT '年份',
  `value` varchar(50) NOT NULL COMMENT '配置工作日',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='工作日配置表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_event` (
  `event_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '活动id',
  `owner_role_id` int(10) NOT NULL COMMENT '所有人岗位',
  `subject` varchar(50) NOT NULL COMMENT '主题',
  `start_date` int(10) NOT NULL COMMENT '开始时间',
  `end_date` int(10) NOT NULL COMMENT '结束时间',
  `venue` varchar(100) NOT NULL COMMENT '活动地点',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者id',
  `create_date` int(10) NOT NULL COMMENT '创建时间',
  `update_date` int(10) NOT NULL COMMENT '修改时间',
  `send_email` INT( 1 ) NOT NULL COMMENT  '发送通知邮件1不发送0',
  `recurring` int(1) NOT NULL COMMENT '重复1 不重复0',
  `description` text NOT NULL COMMENT '描述',
  `isclose` int(1) NOT NULL COMMENT '是否关闭0开启1关闭',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `color` VARCHAR( 50 ) NOT NULL COMMENT  '颜色',
  `module` VARCHAR( 50 ) NOT NULL COMMENT  '相关模块',
  `module_id` INT( 10 ) NOT NULL COMMENT  '相关模块ID',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='活动信息表' AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `pdcrm_task` (
  `task_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '任务id',
  `type_id` int(10) NOT NULL COMMENT '分类id',
  `owner_role_id` varchar(200) NOT NULL COMMENT '任务所有者岗位',
  `about_roles` varchar(200) NOT NULL COMMENT '任务相关人',
  `subject` varchar(100) NOT NULL COMMENT '任务主题',
  `due_date` int(10) NOT NULL COMMENT '任务结束时间',
  `status` varchar(20) NOT NULL COMMENT '任务状态',
  `priority` varchar(10) NOT NULL COMMENT '优先级',
  `send_email` varchar(50) NOT NULL COMMENT '是否发送通知邮件  1发送0不发送',
  `description` text NOT NULL COMMENT '描述',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者岗位',
  `create_date` int(10) NOT NULL COMMENT '创建时间',
  `update_date` int(10) NOT NULL COMMENT '修改时间',
  `isclose` int(1) NOT NULL COMMENT '是否关闭',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `finish_date` int(10) NOT NULL COMMENT '完成时间',
  `order_id` int(10) NOT NULL COMMENT '排序ID',
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务信息表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_task_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL COMMENT '分类名',
  `role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `order_id` int(10) NOT NULL COMMENT '排序ID',
  `is_deleted` TINYINT( 1 ) NOT NULL COMMENT  '1删除',
  `del_role_id` INT( 10 ) NOT NULL COMMENT  '删除人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务列表分类' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_task_sub` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) NOT NULL COMMENT '主任务ID',
  `content` varchar(1000) NOT NULL COMMENT '内容',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int( 10 ) NOT NULL COMMENT '修改时间',
  `is_done` tinyint(1) NOT NULL COMMENT '1完成',
  `done_role_id` int(10) NOT NULL COMMENT '完成人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='子任务表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_task_talk` (
  `talk_id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL COMMENT '组内分标示',
  `task_id` int(10) NOT NULL,
  `send_role_id` int(10) NOT NULL COMMENT '发送者id',
  `receive_role_id` int(10) NOT NULL COMMENT '接收者id',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(10) NOT NULL,
  `g_mark` varchar(50) NOT NULL COMMENT '标示',
  PRIMARY KEY (`talk_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='任务评论回复表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_task_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) NOT NULL COMMENT '任务ID',
  `file_id` int(10) NOT NULL COMMENT '附件ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务附件关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_task_action` (
  `action_id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) NOT NULL COMMENT '任务ID',
  `role_id` int(10) NOT NULL COMMENT '操作人ID',
  `create_date` date NOT NULL COMMENT '操作时间',
  `create_time` int(10) NOT NULL COMMENT '操作时间',
  `type` int(10) NOT NULL COMMENT '操作类型',
  `content` varchar(1000) NOT NULL COMMENT '操作内容',
  `about_role_id` VARCHAR( 500 ) NOT NULL COMMENT '分配人ID',
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务活动表' AUTO_INCREMENT=1 ;

ALTER TABLE  `pdcrm_message` ADD  `is_notifi` TINYINT( 1 ) NOT NULL COMMENT  '1已提醒（桌面）';

CREATE TABLE IF NOT EXISTS `pdcrm_scene` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL COMMENT '模块',
  `name` varchar(255) NOT NULL COMMENT '场景名称',
  `role_id` int(10) NOT NULL,
  `data` text NOT NULL COMMENT '属性值',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `is_hide` INT( 1 ) NOT NULL COMMENT  '1隐藏',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='自定义场景' AUTO_INCREMENT=1 ;


ALTER TABLE  `pdcrm_examine` CHANGE  `examine_status`  `examine_status` INT( 1 ) NOT NULL COMMENT  '状态（0待审、1审批中、2通过、3失败）';

CREATE TABLE IF NOT EXISTS `pdcrm_log_status` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='沟通日志类型' AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_log_status` (`id`, `name`, `create_role_id`, `create_time`, `update_time`) VALUES
(1, '电话', 1, 1502792328, 1502792328),
(2, '发邮件', 1, 1502852220, 1502852220),
(3, '发短信', 1, 1502852228, 1502852228),
(4, '见面拜访', 1, 1502852239, 1502852239);

CREATE TABLE IF NOT EXISTS `pdcrm_log_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `content` VARCHAR( 1000 ) NOT NULL COMMENT  '内容',
  `status_id` int(10) NOT NULL COMMENT '类型ID',
  `type` tinyint(1) NOT NULL COMMENT '1系统2个人',
  `role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='沟通日志自定义回复表' AUTO_INCREMENT=1 ;

ALTER TABLE  `pdcrm_product` CHANGE  `cost_price`  `cost_price` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0' COMMENT  '成本价';

ALTER TABLE  `pdcrm_product` CHANGE  `suggested_price`  `suggested_price` DECIMAL( 10, 2 ) NOT NULL COMMENT  '建议售价';

ALTER TABLE  `pdcrm_customer` ADD  `nextstep_time` INT( 10 ) NOT NULL COMMENT  '下次联系时间' AFTER  `get_time`;

ALTER TABLE  `pdcrm_log` ADD  `nextstep_time` INT( 10 ) NOT NULL COMMENT  '下次联系时间' AFTER  `update_date`;

ALTER TABLE  `pdcrm_log` ADD  `status_id` INT( 10 ) NOT NULL COMMENT  '跟进类型' AFTER  `category_id`;

ALTER TABLE  `pdcrm_scene` ADD  `order_id` INT( 10 ) NOT NULL COMMENT  '排序ID' AFTER  `role_id`;

ALTER TABLE  `pdcrm_scene` ADD  `type` TINYINT( 1 ) NOT NULL COMMENT  '1系统0自定义',
ADD  `by` VARCHAR( 50 ) NOT NULL COMMENT  '系统参数';

INSERT INTO `pdcrm_scene` (`id`, `module`, `name`, `role_id`, `order_id`, `data`, `create_time`, `update_time`, `is_hide`, `type`, `by`) VALUES
(1, 'customer', '我的客户', 0, 0, '', 0, 0, 0, 1, 'me'),
(2, 'customer', '下属客户', 0, 1, '', 0, 0, 0, 1, 'sub'),
(3, 'customer', '全部客户', 0, 3, '', 0, 0, 0, 1, 'all');

CREATE TABLE IF NOT EXISTS `pdcrm_scene_default` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT '模块',
  `role_id` int(10) NOT NULL,
  `scene_id` int(10) NOT NULL COMMENT '场景ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工默认场景关系表' AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_fields` (`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`) VALUES
('customer', 1, 'nextstep_time', '下次联系时间', 'datetime', '', '333333', 200, 0, 1, 0, 0, 1, '', '', 0, 2);

ALTER TABLE  `pdcrm_fields` ADD  `is_recheck` INT( 1 ) NOT NULL COMMENT  '是否查重1是0否' AFTER  `is_unique`;

INSERT INTO `pdcrm_fields` (`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`) VALUES('customer', 1, 'customer_status', '客户状态', 'box', '意向客户', '333333', 0, 0, 0, 1, 0, 1, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''意向客户'',2=>''已成交客户'',3=>''失败客户''))', 1, 2, 0);

ALTER TABLE  `pdcrm_customer` ADD  `customer_status` VARCHAR( 255 ) NOT NULL DEFAULT '意向客户' COMMENT '客户状态';

ALTER TABLE  `pdcrm_user` ADD  `customer_num` INT NOT NULL COMMENT  '拥有客户数' AFTER  `number`;

ALTER TABLE  `pdcrm_contract` ADD  `contract_status` INT( 1 ) NOT NULL COMMENT  '1已续签2已忽略' AFTER  `type`;
ALTER TABLE  `pdcrm_contract` ADD  `renew_contract_id` INT NOT NULL COMMENT  '续签合同id' AFTER  `customer_id`;

CREATE TABLE IF NOT EXISTS `pdcrm_customer_share` (
  `share_id` int(10) NOT NULL AUTO_INCREMENT,
  `share_role_id` int(10) NOT NULL COMMENT '分享人ID',
  `by_sharing_id` int(10) NOT NULL COMMENT '被分享人ID',
  `customer_id` int(10) NOT NULL COMMENT '客户ID',
  `share_time` int(10) NOT NULL COMMENT '分享时间',
  PRIMARY KEY (`share_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_scene` (`id`, `module`, `name`, `role_id`, `order_id`, `data`, `create_time`, `update_time`, `is_hide`, `type`, `by`) VALUES (NULL, 'customer', '共享给我的', '', '5', '', '', '', '', '1', 'share');

INSERT INTO `pdcrm_scene` (`id`, `module`, `name`, `role_id`, `order_id`, `data`, `create_time`, `update_time`, `is_hide`, `type`, `by`) VALUES (NULL, 'customer', '我共享的', '', '6', '', '', '', '', '1', 'myshare');

CREATE TABLE IF NOT EXISTS `pdcrm_action_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_role_id` int(11) NOT NULL,
  `create_time` int(10) NOT NULL,
  `model_name` varchar(50) NOT NULL COMMENT '模块名',
  `action_id` int(11) NOT NULL COMMENT '模块信息ID',
  `type` varchar(30) NOT NULL,
  `duixiang` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_config` (`id`, `name`, `value`, `description`) VALUES (NULL, 'receivables_time', '30', '应收款提前几天提醒');

ALTER TABLE  `pdcrm_user` ADD  `crm_version` VARCHAR( 20 ) NOT NULL COMMENT  '版本信息';

CREATE TABLE IF NOT EXISTS `pdcrm_r_customer_invoice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL COMMENT '客户ID',
  `invoice_header` varchar(255) NOT NULL COMMENT '开票抬头',
  `taxes_num` varchar(255) NOT NULL COMMENT '纳税识别号',
  `opening_bank` varchar(255) NOT NULL COMMENT '开户行',
  `account_number` varchar(255) NOT NULL COMMENT '开户账号',
  `billing_address` varchar(500) NOT NULL COMMENT '开票地址',
  `telephone` varchar(50) NOT NULL COMMENT '电话',
  `create_time` INT( 10 ) NOT NULL COMMENT  '创建时间',
  `update_time` INT( 10 ) NOT NULL COMMENT  '修改时间',
  `create_role_id` INT( 10 ) NOT NULL COMMENT  '创建人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户发票信息表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_invoice` (
  `invoice_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR( 255 ) NOT NULL COMMENT '发票编号',
  `customer_id` int(10) NOT NULL COMMENT '客户ID',
  `contract_id` int(10) NOT NULL COMMENT '合同ID',
  `price` decimal(18,2) NOT NULL COMMENT '开票金额',
  `billing_type` tinyint(1) NOT NULL COMMENT '开票类型',
  `number` varchar(255) NOT NULL COMMENT '发票号码',
  `description` varchar(1000) NOT NULL COMMENT '备注',
  `invoice_header` varchar(255) NOT NULL COMMENT '开票抬头',
  `taxes_num` varchar(255) NOT NULL COMMENT '纳税识别号',
  `opening_bank` varchar(255) NOT NULL COMMENT '开户行',
  `account_number` varchar(255) NOT NULL COMMENT '开户账号',
  `billing_address` varchar(255) NOT NULL COMMENT '开票地址',
  `telephone` varchar(50) NOT NULL COMMENT '电话',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `is_checked` TINYINT( 1 ) NOT NULL COMMENT  '0待审1通过2失败',
  `check_role_id` INT( 10 ) NOT NULL COMMENT  '审核人ID',
  `check_time` INT( 10 ) NOT NULL COMMENT  '审核时间',
  PRIMARY KEY (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同发票表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_file_invoice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) NOT NULL COMMENT '发票ID',
  `file_id` int(10) NOT NULL COMMENT '附件ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='发票附件关系表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_target` (
  `target_id` int(10) NOT NULL AUTO_INCREMENT,
  `target_type` tinyint(1) NOT NULL COMMENT '目标类型 1销售额 2回款金额',
  `id_type` tinyint(1) NOT NULL COMMENT '1 Id为部门Id  2Id为role_id',
  `id` int(10) NOT NULL,
  `year` int(4) NOT NULL COMMENT '年份',
  `month1` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month2` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month3` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month4` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month5` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month6` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month7` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month8` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month9` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month10` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month11` decimal(10,2) NOT NULL COMMENT '月份目标',
  `month12` decimal(10,2) NOT NULL COMMENT '月份目标',
  `total` decimal(10,2) NOT NULL COMMENT '年度总目标',
  PRIMARY KEY (`target_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='业绩目标设置表' AUTO_INCREMENT=1 ;

ALTER TABLE  `pdcrm_examine` CHANGE  `budget`  `budget` DECIMAL( 18, 2 ) NOT NULL COMMENT  '预算金额';

ALTER TABLE  `pdcrm_examine` CHANGE  `money`  `money` DECIMAL( 18, 2 ) NOT NULL COMMENT  '报销金额/借款金额';

ALTER TABLE  `pdcrm_examine` CHANGE  `advance`  `advance` DECIMAL( 18, 2 ) NOT NULL COMMENT  '预支金额';

ALTER TABLE  `pdcrm_examine_travel` CHANGE  `money`  `money` DECIMAL( 18, 2 ) NOT NULL COMMENT  '金额';

ALTER TABLE  `pdcrm_contract` CHANGE  `price`  `price` DECIMAL( 18, 2 ) NOT NULL COMMENT  '总价';

ALTER TABLE  `pdcrm_payables` CHANGE  `price`  `price` DECIMAL( 18, 2 ) NOT NULL COMMENT  '应付金额';

ALTER TABLE  `pdcrm_paymentorder` CHANGE  `money`  `money` DECIMAL( 18, 2 ) NOT NULL COMMENT  '付款金额';

ALTER TABLE  `pdcrm_sales` CHANGE  `prime_price`  `prime_price` DECIMAL( 18, 2 ) NOT NULL COMMENT  '销售单整体价格未减折扣额时价格';

ALTER TABLE  `pdcrm_sales` CHANGE  `sales_price`  `sales_price` DECIMAL( 18, 2 ) NOT NULL COMMENT  '折扣后销售单实际应付金额';

ALTER TABLE  `pdcrm_sales` CHANGE  `discount_price`  `discount_price` DECIMAL( 18, 2 ) NOT NULL COMMENT  '折扣额';

ALTER TABLE  `pdcrm_user` ADD  `extid` INT( 4 ) NOT NULL COMMENT  '坐席号' AFTER  `user_id`;

CREATE TABLE IF NOT EXISTS `pdcrm_invoice_check` (
  `check_id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) NOT NULL COMMENT '发票ID',
  `role_id` int(10) NOT NULL COMMENT '负责人ID',
  `is_checked` tinyint(1) NOT NULL COMMENT '1通过2驳回',
  `content` varchar(500) NOT NULL COMMENT '审核内容',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`check_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='发票审核记录表' AUTO_INCREMENT=1 ;

UPDATE  `pdcrm_fields` SET  `operating` =  '1' WHERE `model` = 'contacts' AND `field` = 'name';

UPDATE  `pdcrm_fields` SET  `operating` =  '1' WHERE `model` = 'contacts' AND `field` = 'telephone';

UPDATE  `pdcrm_fields` SET  `operating` =  '1' WHERE `model` = 'contacts' AND `field` = 'qq_no';

UPDATE  `pdcrm_fields` SET  `operating` =  '1' WHERE `model` = 'contacts' AND `field` = 'email';

ALTER TABLE  `pdcrm_task` ADD  `module` VARCHAR( 50 ) NOT NULL COMMENT  '相关模块',
ADD  `module_id` INT( 10 ) NOT NULL COMMENT  '相关模块ID';

ALTER TABLE  `pdcrm_examine` CHANGE  `budget`  `budget` DECIMAL( 18, 2 ) NOT NULL COMMENT  '普通报销、差旅、出差、借款（金额）';

ALTER TABLE  `pdcrm_invoice` ADD  `invoice_time` INT( 10 ) NOT NULL COMMENT  '开票时间';

ALTER TABLE  `pdcrm_task` CHANGE  `about_roles`  `about_roles` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '任务分配人';

ALTER TABLE  `pdcrm_task` CHANGE  `owner_role_id`  `owner_role_id` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '任务关注人';

CREATE TABLE IF NOT EXISTS `pdcrm_business_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '组名',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商机状态组表' AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_business_type` (`id` ,`name` ,`create_role_id` ,`create_time` ,`update_time`) VALUES (1, '默认分组', '1', '1511768134', '1511768134');

ALTER TABLE  `pdcrm_business_status` ADD  `type_id` INT( 10 ) NOT NULL DEFAULT  '1' COMMENT  '状态组ID';

ALTER TABLE  `pdcrm_business` ADD  `status_type_id` INT( 10 ) NOT NULL DEFAULT  '1' COMMENT  '状态组ID';

ALTER TABLE `pdcrm_payables`
  DROP `receiver`,
  DROP `purchase_id`,
  DROP `sales_id`,
  DROP `sales_code`,
  DROP `purchase_code`;

ALTER TABLE  `pdcrm_payables` CHANGE  `contract_id`  `contract_id` INT( 10 ) NULL COMMENT  '合同id';

ALTER TABLE  `pdcrm_payables` CHANGE  `type`  `type_id` INT( 10 ) NOT NULL COMMENT  '应付款类型';

CREATE TABLE IF NOT EXISTS `pdcrm_finance_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `field` varchar(50) NOT NULL COMMENT '字段',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='财务相关类型' AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_finance_type` (`id`, `field`, `name`, `create_role_id`, `create_time`, `update_time`) VALUES
(1, 'payables', '客户支出', 1, 1512113079, 1512113079),
(2, 'payables', '报销', 1, 1512113270, 1512113270),
(3, 'payables', '工资', 1, 1512113279, 1512113279);

ALTER TABLE  `pdcrm_payables` CHANGE  `contract_id`  `contract_id` INT( 10 ) NOT NULL COMMENT  '合同id';

ALTER TABLE  `pdcrm_paymentorder` CHANGE  `update_time`  `update_time` INT( 10 ) NOT NULL COMMENT  '修改时间';

ALTER TABLE  `pdcrm_paymentorder` ADD  `check_time` INT( 10 ) NOT NULL COMMENT  '审核时间' AFTER  `examine_role_id`;

ALTER TABLE  `pdcrm_paymentorder` CHANGE  `type`  `type` INT( 10 ) NOT NULL COMMENT  '类别（暂不用）';

ALTER TABLE `pdcrm_receivables`
  DROP `sales_id`,
  DROP `purchase_id`,
  DROP `sales_code`,
  DROP `purchase_code`;

ALTER TABLE  `pdcrm_bank_account` CHANGE  `bank_account`  `bank_account` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '银行账号';

ALTER TABLE  `pdcrm_bank_account` CHANGE  `company`  `company` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '收款单位';

ALTER TABLE  `pdcrm_bank_account` CHANGE  `open_bank`  `open_bank` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '开户行';

ALTER TABLE  `pdcrm_bank_account` CHANGE  `description`  `description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '备注';

ALTER TABLE `pdcrm_paymentorder`
  DROP `bank_name`,
  DROP `bank_acount`;

ALTER TABLE  `pdcrm_paymentorder` CHANGE  `receipt_account`  `receipt_account` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  '银行账户';

ALTER TABLE  `pdcrm_paymentorder` CHANGE  `company`  `company` VARCHAR( 255 ) NOT NULL COMMENT  '付款单位';

ALTER TABLE  `pdcrm_paymentorder` CHANGE  `bank_account_id`  `bank_account_id` INT( 10 ) NOT NULL COMMENT  '付款账户';

INSERT INTO `pdcrm_fields` ( `model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`) VALUES
( 'business', 1, 'name', '商机名', 'text', '', '090D08', 0, 1, 0, 1, 1, 1, 1, '', '', 0, 2),
( 'business', 1, 'customer_id', '客户', 'customer', '', '', 0, 0, 0, 0, 1, 1, 1, '', '', 1, 2),
( 'business', 1, 'contacts_id', '联系人', 'contacts', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 2, 2),
( 'business', 1, 'status_id', '商机进度', 'b_box', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 3, 2),
( 'business', 1, 'possibility', '成交几率', 'p_box', '', '', 0, 0, 0, 0, 1, 1, 1, '', '', 4, 2),
( 'business', 1, 'nextstep_time', '下次联系时间', 'datetime', '', '', 0, 0, 0, 0, 1, 1, 1, '', '', 5, 2),
( 'business', 0, 'description', '备注', 'textarea', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 6, 1);

CREATE TABLE IF NOT EXISTS `pdcrm_business_data` (
  `business_id` int(10) NOT NULL COMMENT '主键',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`business_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商机数据表';

ALTER TABLE  `pdcrm_receivingorder` ADD  `check_time` INT( 10 ) NOT NULL COMMENT  '审核时间';

ALTER TABLE  `pdcrm_business_status` CHANGE  `is_end`  `is_end` INT( 1 ) NOT NULL COMMENT  '0正常2失败3成功';

UPDATE `pdcrm_business_status` SET  `is_end` =  '2' WHERE  `pdcrm_business_status`.`status_id` =99;

UPDATE `pdcrm_business_status` SET  `is_end` =  '3' WHERE  `pdcrm_business_status`.`status_id` =100;

ALTER TABLE  `pdcrm_business_status` CHANGE  `name`  `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  '商机状态名';

ALTER TABLE pdcrm_business_status DROP INDEX name;

ALTER TABLE pdcrm_business_status DROP INDEX name_2;

CREATE TABLE IF NOT EXISTS `pdcrm_kaoqin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `daka_time` int(10) NOT NULL COMMENT '打卡时间',
  `x` varchar(255) NOT NULL,
  `y` varchar(255) NOT NULL,
  `address` varchar(500) NOT NULL,
  `status` int(2) NOT NULL COMMENT '状态  （1 正常签到）（2 迟到） （3 早退） （4 正常签退）',
  `config_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '规则类型（1wifi,2地理位置）',
  `remark` varchar(500) NOT NULL,
  `shangban_time` varchar(30) NOT NULL COMMENT '上班时间',
  `xiaban_time` varchar(30) NOT NULL COMMENT '下班时间',
  `token_id` varchar(100) NOT NULL COMMENT '设备ID',
  `wifi_name` varchar(255) NOT NULL COMMENT 'wifi名称',
  `mac_address` varchar(255) NOT NULL COMMENT 'MAC地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_route` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `wifi_name` varchar(255) NOT NULL COMMENT 'wifi名称',
  `mac_address` varchar(255) NOT NULL COMMENT 'mac地址',
  `create_role_id` int(10) NOT NULL COMMENT '创建人',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='考勤路由' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_kaoqin_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `shangban_time` TIME NOT NULL COMMENT '上班时间',
  `xiaban_time` TIME NOT NULL COMMENT '下班时间',
  `x` varchar(30) NOT NULL COMMENT 'x坐标',
  `y` varchar(30) NOT NULL COMMENT 'y坐标',
  `radius` int(10) NOT NULL COMMENT '半径（米）',
  `create_role_id` int(10) NOT NULL COMMENT '创建人',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='考勤规则表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_r_contract_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL COMMENT '合同ID',
  `log_id` int(10) NOT NULL COMMENT '日志ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同日志表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pdcrm_cycel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT '模块',
  `module_id` int(10) NOT NULL COMMENT '模块ID',
  `num` varchar(50) NOT NULL COMMENT '数量',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '类型（1周2月3年4仅一次）',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='自定义循环周期表' AUTO_INCREMENT=1 ;

ALTER TABLE  `pdcrm_contract` ADD  `renew_parent_id` INT( 10 ) NOT NULL COMMENT  '续约组父类ID';

CREATE TABLE IF NOT EXISTS `pdcrm_contract_examine` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL COMMENT '岗位ID',
  `order_id` int(10) NOT NULL COMMENT '排序ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同审批流' AUTO_INCREMENT=1 ;

ALTER TABLE  `pdcrm_contract_check` CHANGE  `is_checked`  `is_checked` TINYINT( 1 ) NOT NULL COMMENT  '审核状态(1同意2驳回)';

ALTER TABLE  `pdcrm_contract` ADD  `order_id` INT( 10 ) NOT NULL COMMENT  '审批流程ID';

ALTER TABLE  `pdcrm_contract` CHANGE  `is_checked`  `is_checked` INT( 10 ) NOT NULL COMMENT  '是否审核0未审核1通过2未通过3审批中';

ALTER TABLE  `pdcrm_contract` ADD  `examine_type_id` INT( 10 ) NOT NULL COMMENT  '审批流类型ID（0自选1默认流程1）';

ALTER TABLE  `pdcrm_kaoqin_config` ADD  `reg_address` VARCHAR( 1000 ) NOT NULL COMMENT  '地理位置';

ALTER TABLE  `pdcrm_invoice` ADD  `express` VARCHAR( 255 ) NOT NULL COMMENT  '快递单号';

ALTER TABLE  `pdcrm_user` CHANGE  `name`  `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '用户名';

ALTER TABLE  `pdcrm_user` CHANGE  `email`  `email` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '用户邮箱';

INSERT INTO `pdcrm_fields` ( `model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`) VALUES
( 'contract', 1, 'contract_name', '合同名称', 'text', '', '090D08', 0, 1, 0, 1, 1, 1, 1, '', '', 0, 2),
( 'contract', 1, 'customer_id', '客户', 'customer', '', '', 0, 0, 0, 0, 1, 1, 1, '', '', 1, 2),
( 'contract', 1, 'business_id', '商机', 'business', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 2, 2),
( 'contract', 1, 'price', '合同金额(元)', 'text', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 3, 2),
( 'contract', 1, 'due_time', '签约时间', 'datetime', '', '', 0, 0, 0, 1, 1, 0, 1, '', '', 4, 2),
( 'contract', 1, 'start_date', '合同生效时间', 'datetime', '', '', 0, 0, 0, 0, 1, 1, 1, '', '', 5, 2),
( 'contract', 1, 'end_date', '合同到期时间', 'datetime', '', '', 0, 0, 0, 0, 1, 1, 1, '', '', 6, 2),
( 'contract', 1, 'description', '合同描述', 'textarea', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 7, 1);

CREATE TABLE IF NOT EXISTS `pdcrm_contract_data` (
  `contract_id` int(10) NOT NULL COMMENT '主键',
  PRIMARY KEY (`contract_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同附表';

INSERT INTO `pdcrm_scene` (`module`, `name`, `role_id`, `order_id`, `data`, `create_time`, `update_time`, `is_hide`, `type`, `by`) VALUES
( 'contract', '我的合同', 0, 0, '', 0, 0, 0, 1, 'me'),
( 'contract', '下属合同', 0, 1, '', 0, 0, 0, 1, 'sub'),
( 'contract', '全部合同', 0, 3, '', 0, 0, 0, 1, 'all'),
( 'leads', '我的线索', 0, 0, '', 0, 0, 0, 1, 'me'),
( 'leads', '下属线索', 0, 1, '', 0, 0, 0, 1, 'sub'),
( 'leads', '全部线索', 0, 3, '', 0, 0, 0, 1, 'all'),
( 'business', '我的商机', 0, 0, '', 0, 0, 0, 1, 'me'),
( 'business', '下属商机', 0, 1, '', 0, 0, 0, 1, 'sub'),
( 'business', '全部商机', 0, 3, '', 0, 0, 0, 1, 'all');

CREATE TABLE IF NOT EXISTS `pdcrm_call_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL COMMENT '通话人role_id',
  `telephone` varchar(30) NOT NULL COMMENT '电话号码',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `answer_time` int(10) NOT NULL COMMENT '摘机时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `call_status` tinyint(1) NOT NULL COMMENT '1来电、2呼出、3挂断、4接听',
  `talk_time` int(10) NOT NULL COMMENT '通话时长（秒）',
  `dial_time` int(10) NOT NULL COMMENT '摘机时长',
  `call_type` tinyint(1) NOT NULL COMMENT '1呼入2呼出',
  `hungup_type` tinyint(1) NOT NULL COMMENT '1话机挂断2客户挂断',
  `session_id` varchar(20) NOT NULL COMMENT '通话session_id',
  `file_path` varchar(500) NOT NULL COMMENT '录音文件路径',
  `size` int(10) NOT NULL COMMENT '录音文件大小',
  `file_name` varchar(500) NOT NULL COMMENT '文件名称',
  `call_upload` tinyint(1) NOT NULL COMMENT '1上传至阿里云，0CRM服务器',
  `model` varchar(20) NOT NULL COMMENT '模块',
  `model_id` int(10) NOT NULL COMMENT '模块ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='通话记录表' AUTO_INCREMENT=1 ;

ALTER TABLE  `pdcrm_user` ADD  `call_status` TINYINT( 1 ) NOT NULL COMMENT  '1启用';

INSERT INTO `pdcrm_fields` ( `model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`) VALUES
( 'business', 1, 'final_price', '商机金额', 'text', '', '090D08', 0, 0, 0, 1, 0, 0, 1, '', '', 7, 2);

INSERT INTO  `pdcrm_config` (
`id` ,
`name` ,
`value` ,
`description`
)
VALUES (
NULL ,  'call_record',  '1',  '1开启录音0关闭录音'
);

ALTER TABLE  `pdcrm_examine_status` ADD  `description` VARCHAR( 500 ) NOT NULL COMMENT  '审批类型描述';

ALTER TABLE  `pdcrm_fields` ADD  `status` INT( 10 ) NOT NULL COMMENT  '模块类型（审批）';

CREATE TABLE IF NOT EXISTS `pdcrm_examine_data` (
  `examine_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='审批附表';

ALTER TABLE  `pdcrm_examine_step` CHANGE  `role_id`  `role_id` VARCHAR( 200 ) NOT NULL COMMENT  '审批人';

ALTER TABLE  `pdcrm_examine_step` ADD  `relation` TINYINT( 1 ) NOT NULL COMMENT  '审批流程关系（1并2或）';

ALTER TABLE  `pdcrm_examine_check` ADD  `order_id` INT( 10 ) NOT NULL COMMENT  '审批流程步骤ID';

ALTER TABLE  `pdcrm_examine_check` ADD  `is_end` TINYINT( 1 ) NOT NULL COMMENT  '1无效审批2为了区别新老逻辑';

ALTER TABLE  `pdcrm_examine` CHANGE  `examine_role_id`  `examine_role_id` VARCHAR( 255 ) NOT NULL COMMENT  '审批人';

CREATE TABLE IF NOT EXISTS `pdcrm_template` (
  `template_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键，模板ID',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模板名称',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模板内容',
  `object_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模板所属对象',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `is_default` tinyint(1) NOT NULL COMMENT '是否采用默认模板。0否，1是',
  `default` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '默认模板内容',
  `system` tinyint(1) NOT NULL DEFAULT 0 COMMENT '系统默认模板，不可删除，1是，0否',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '模板状态：1启用；0禁用',
  PRIMARY KEY (`template_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

CREATE TABLE IF NOT EXISTS `pdcrm_template_object` (
  `id` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '对象名称',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '对象名称',
  `models` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '字段模块  @fields-model',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

INSERT INTO `pdcrm_template_object` VALUES ('contract', '合同订单', 'contacts,contract,customer');

CREATE TABLE IF NOT EXISTS `pdcrm_template_field_block` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

INSERT INTO `pdcrm_template_field_block` (`id`, `name`, `content`) VALUES
(1, '产品信息', '<table align="center" border="2"><tbody><tr class="firstRow"><td width="84" valign="middle" style="word-break: break-all;" align="center">产品名称</td><td width="84" valign="middle" style="word-break: break-all;" align="center"><span style="text-align: -webkit-center;">价格(元)</span></td><td width="84" valign="middle" style="word-break: break-all;" align="center">折扣(%)</td><td width="84" valign="middle" style="word-break: break-all;" align="center">销售单价(元)</td><td width="84" valign="middle" style="word-break: break-all;" align="center">数量</td><td width="84" valign="middle" style="word-break: break-all;" align="center">单位</td><td width="84" valign="middle" style="word-break: break-all;" align="center">小计</td></tr><tr php="each"><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="产品名称" data-original="product.product_name">{{product.product_name}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="价格(元)" data-original="product.ori_price">{{product.ori_price}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="折扣(%)" data-original="product.discount_rate">{{product.discount_rate}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="销售单价(元)" data-original="product.unit_price">{{product.unit_price}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="数量" data-original="product.amount">{{product.amount}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="单位" data-original="product.unit">{{product.unit}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="小计" data-original="product.subtotal">{{product.subtotal}}</span></td></tr></tbody></table>');

ALTER TABLE  `pdcrm_customer` ADD  `lng` DOUBLE( 14, 11 ) NOT NULL COMMENT  '客户地理位置经度' AFTER  `address` ,
ADD  `lat` DOUBLE( 14, 11 ) NOT NULL COMMENT  '客户地理位置纬度' AFTER  `lng`;

CREATE TABLE IF NOT EXISTS `pdcrm_import_error_data`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `error_data` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '错误数据 json  行数：原因',
  `excel` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '导入文件名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

INSERT INTO `pdcrm_config` (`name`,`value`,`description`) VALUES ('search_disable_user',0,'可搜索已停用用户: 1是；0否');

CREATE TABLE `pdcrm_market`  (
  `market_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '活动名称',
  `start_time` int(10) NOT NULL COMMENT '活动开始时间',
  `end_time` int(10) NOT NULL COMMENT '活动结束时间',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '活动类型',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '活动地址',
  `expected_cost` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '预计成本',
  `expected_income` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '预计收入',
  `real_cost` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '实际成本',
  `real_income` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '实际收入',
  `status` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '活动状态',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者',
  `owner_role_id` int(10) NOT NULL COMMENT '所有者',
  `executor_role_ids` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '参与的人',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `create_time` int(10) NOT NULL COMMENT '添加时间',
  `is_lock` tinyint(1) NOT NULL DEFAULT 0 COMMENT '锁定 1是；0否',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '作废 1是；0否',
  PRIMARY KEY (`market_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

CREATE TABLE `pdcrm_market_data`  (
  `market_id` int(10) NOT NULL COMMENT '主键',
  `plan` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '活动计划',
  `execution_description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '执行描述',
  `summary` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '总结',
  `effect` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '效果',
  `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '描述',
  PRIMARY KEY (`market_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

CREATE TABLE `pdcrm_r_file_market`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `market_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

CREATE TABLE `pdcrm_r_market_customer`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `market_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

CREATE TABLE `pdcrm_r_market_leads`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `market_id` int(10) NOT NULL,
  `leads_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

CREATE TABLE `pdcrm_r_market_log`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `market_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'name', '活动名称', 'text', '', '333333', 255, 0, 0, 1, 1, 1, 1, '', '', 0, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'start_time', '活动开始时间', 'datetime', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 2, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'end_time', '活动结束时间', 'datetime', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 3, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'type', '活动类型', 'box', '', '333333', 64, 0, 0, 1, 1, 1, 1, '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'促销活动\',2=>\'品牌活动\',3=>\'会议销售\',4=>\'搜索引擎\',5=>\'互联网广告\',6=>\'平面媒体广告\',7=>\'电视媒体广告\',8=>\'关系公关\',9=>\'电话营销\',10=>\'短信营销\',11=>\'邮件营销\'))', 1, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'address', '活动地址', 'text', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 4, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'expected_cost', '预计成本', 'floatnumber', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 6, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'expected_income', '预计收入', 'floatnumber', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 7, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'real_cost', '实际成本', 'floatnumber', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 8, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'real_income', '实际收入', 'floatnumber', '', '', 0, 0, 0, 0, 0, 1, 1, '', '', 9, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 0, 'plan', '活动计划', 'textarea', '', '333333', 0, 0, 0, 0, 0, 0, 1, '', '', 10, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 0, 'execution_description', '执行描述', 'textarea', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 11, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 0, 'summary', '总结', 'textarea', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 13, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 0, 'effect', '效果', 'textarea', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 12, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 0, 'description', '描述', 'textarea', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 14, 1, 0, 0);
INSERT INTO `pdcrm_fields`(`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES ('market', 1, 'status', '活动状态', 'box', '已计划', '333333', 0, 0, 0, 1, 1, 1, 1, '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'已计划\',2=>\'进行中\',3=>\'已结束\',4=>\'终止\'))', 5, 2, 0, 0);

ALTER TABLE `pdcrm_contract` ADD COLUMN `market_id` int(10) NOT NULL DEFAULT 0 COMMENT '活动ID';

ALTER TABLE  `pdcrm_contract_examine` CHANGE  `role_id`  `role_id` VARCHAR( 500 ) NOT NULL COMMENT  '岗位ID';

ALTER TABLE  `pdcrm_contract_examine` ADD  `relation` TINYINT( 1 ) NOT NULL COMMENT  '审批流程关系（1并2或）';

ALTER TABLE  `pdcrm_contract_check` ADD  `is_end` TINYINT( 1 ) NOT NULL COMMENT  '1无效审批2为了区别新老逻辑';

ALTER TABLE  `pdcrm_contract_check` ADD  `order_id` INT( 10 ) NOT NULL COMMENT  '审批流程步骤ID';

ALTER TABLE  `pdcrm_contract` CHANGE  `examine_role_id`  `examine_role_id` VARCHAR( 500 ) NOT NULL COMMENT  '审核人';

-- 2.5.0版本进销存升级节点
-- 新产品表 pdcrm_product_info

DROP TABLE IF EXISTS `pdcrm_product_info`;
CREATE TABLE IF NOT EXISTS `pdcrm_product_info` (
  `product_info_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` int(10) NOT NULL COMMENT '产品基本信息id',
  `number` varchar(255) NOT NULL COMMENT '商品编号',
  `bar_code` varchar(255) NOT NULL COMMENT '条形码',
  `price` decimal(16,2) NOT NULL COMMENT '建议售价',
  `price_cost` decimal(16,2) NOT NULL COMMENT '建议采购价',
  `price_cost_avg` decimal(16,2) NOT NULL COMMENT '进销存动态平均成本单价',
  `state` tinyint(1) NOT NULL COMMENT '0 正常  1下架 3不可用',
  PRIMARY KEY (`product_info_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='产品表' AUTO_INCREMENT=1 ;


-- 产品规格分类表 pdcrm_product_spec_value

DROP TABLE IF EXISTS `pdcrm_product_spec_value`;
CREATE TABLE IF NOT EXISTS `pdcrm_product_spec_value` (
  `value_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_info_id` int(10) NOT NULL COMMENT '产品id',
  `spec_name` varchar(64) NOT NULL COMMENT '规格名称',
  `spec_value` varchar(64) NOT NULL COMMENT '规格值',
  PRIMARY KEY (`value_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='产品多规格值记录表' AUTO_INCREMENT=1 ;


-- 产品规格分类设置表 pdcrm_product_spec

DROP TABLE IF EXISTS `pdcrm_product_spec`;
CREATE TABLE IF NOT EXISTS `pdcrm_product_spec` (
  `spec_id` int(10) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) NOT NULL COMMENT '产品类别id',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `spec_val` varchar(255) NOT NULL COMMENT '规格值',
  PRIMARY KEY (`spec_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='产品规格分类表' AUTO_INCREMENT=1 ;

-- 供应商表 pdcrm_supplier

DROP TABLE IF EXISTS `pdcrm_supplier`;
CREATE TABLE IF NOT EXISTS `pdcrm_supplier` (
  `supplier_id` int(10) NOT NULL AUTO_INCREMENT,
  `number` varchar(50) NOT NULL COMMENT '供应商编号',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1启用 2禁用',
  `create_time` int(10) NOT NULL,
  `creator_role_id` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL COMMENT '0未删除 1已删除',
  `crm_rkxjgu` varchar(255) NOT NULL DEFAULT '' COMMENT '服务类型',
  `category` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商类别',
  `crm_gbzegv` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商等级',
  `crm_fekgue` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商性质',
  PRIMARY KEY (`supplier_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='进销存供应商主表' AUTO_INCREMENT=1 ;

--
-- 供应商副表 表的结构 `pdcrm_supplier_data`
--

DROP TABLE IF EXISTS `pdcrm_supplier_data`;
CREATE TABLE IF NOT EXISTS `pdcrm_supplier_data` (
  `supplier_id` int(10) NOT NULL,
  `crm_pexwas` varchar(100) NOT NULL DEFAULT '' COMMENT '备注'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='供应商附表';

--
-- 供应商联系人表 表的结构 `pdcrm_supplier_contacts`
--

DROP TABLE IF EXISTS `pdcrm_supplier_contacts`;
CREATE TABLE IF NOT EXISTS `pdcrm_supplier_contacts` (
  `contacts_id` int(10) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '联系人',
  `telephone` varchar(20) NOT NULL COMMENT '联系方式',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `address` varchar(100) NOT NULL COMMENT '地址',
  `remark` varchar(200) NOT NULL COMMENT '备注',
  PRIMARY KEY (`contacts_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_purchase` 采购单或销售退货单表
--

DROP TABLE IF EXISTS `pdcrm_purchase`;
CREATE TABLE IF NOT EXISTS `pdcrm_purchase` (
  `purchase_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `number` varchar(64) NOT NULL COMMENT '采购单编号',
  `name` varchar(64) NOT NULL COMMENT '标题',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '采购类型: 1采购; 2销售退货',
  `type_id` int(10) NOT NULL COMMENT 'type为1时supplier_id; 2时sales_id',
  `purchase_amount` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '采购金额',
  `other_amount` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '其他金额',
  `discount` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '整单折扣',
  `creator_role_id` int(10) NOT NULL COMMENT '创建人',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态: 0待入库; 1部分入库; 2已入库',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `purchase_time` int(10) NOT NULL COMMENT '采购时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`purchase_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_purchase_product` 采购单或销售退货单表关联产品表
--

DROP TABLE IF EXISTS `pdcrm_purchase_product`;
CREATE TABLE IF NOT EXISTS `pdcrm_purchase_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) NOT NULL COMMENT '采购单id',
  `product_info_id` int(10) NOT NULL COMMENT '产品id',
  `price_cost` decimal(16,2) NOT NULL COMMENT '采购单价',
  `price` decimal(16,2) NOT NULL COMMENT '建议售价',
  `price_discount` decimal(16,2) NOT NULL COMMENT '采购折扣单价',
  `count` int(10) NOT NULL COMMENT '产品数量',
  `discount` tinyint(2) NOT NULL COMMENT '折扣百分比',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='采购单关联产品信息表' AUTO_INCREMENT=1 ;

-- 仓库表
DROP TABLE IF EXISTS `pdcrm_warehouse`;
CREATE TABLE IF NOT EXISTS `pdcrm_warehouse` (
  `warehouse_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '仓库id',
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '仓库名',
  `description` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '描述',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '地址',
  `number` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '编号',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 2禁用',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '软删除',
  `owner_role_id` varchar(128) NOT NULL COMMENT '仓库负责人，可多个',
  PRIMARY KEY (`warehouse_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '仓库表' ROW_FORMAT = Dynamic;

--
-- 表的结构 `pdcrm_sn` sn码表
--
DROP TABLE IF EXISTS `pdcrm_sn`;
CREATE TABLE IF NOT EXISTS `pdcrm_sn` (
  `sn_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT ' 主键',
  `stock_in_id` int(10) NOT NULL DEFAULT '0' COMMENT '入库单id[不可修改]',
  `stock_out_id` int(10) NOT NULL DEFAULT '0' COMMENT '出库id',
  `warehouse_id` int(10) NOT NULL DEFAULT '0' COMMENT '仓库id',
  `product_info_id` int(10) NOT NULL DEFAULT '0' COMMENT '产品id',
  `sn` varchar(255) NOT NULL COMMENT '  sn码',
  `price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `price_cost` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '  采购价格',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0入库在途 1在库 2出库 3退货在途',
  `creator_role_id` int(10) NOT NULL COMMENT '创建人',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`sn_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_sn_log` sn码日志表
--

DROP TABLE IF EXISTS `pdcrm_sn_log`;
CREATE TABLE IF NOT EXISTS `pdcrm_sn_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sn_id` int(10) NOT NULL COMMENT 'SN_ID',
  `type` tinyint(1) NOT NULL COMMENT '1：入库；2：出库',
  `type_id` int(10) NOT NULL COMMENT '关联ID 出库stock_out_id 入库 stock_in_id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_stock` 库存信息表
--
DROP TABLE IF EXISTS `pdcrm_stock`;
CREATE TABLE IF NOT EXISTS `pdcrm_stock` (
  `stock_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_info_id` int(10) NOT NULL COMMENT '商品id',
  `warehouse_id` int(10) NOT NULL COMMENT '仓库id',
  `lower_limit` int(10) NOT NULL COMMENT '库存下限',
  `upper_limit` int(10) NOT NULL COMMENT '库存上限',
  `purchase_count` int(10) NOT NULL COMMENT '在途量：采购数量，未入库',
  `order_count` int(10) NOT NULL COMMENT '订单量：订单数量，未出库',
  `count` int(10) NOT NULL COMMENT '库存数量',
  `last_change_time` int(10) NOT NULL COMMENT '库存上次变动时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`stock_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='库存' AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_stock_in` 入库记录表
--

DROP TABLE IF EXISTS `pdcrm_stock_in`;
CREATE TABLE IF NOT EXISTS `pdcrm_stock_in` (
  `stock_in_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `number` varchar(32) NOT NULL COMMENT '入库编号',
  `type` tinyint(255) NOT NULL COMMENT '入库类型：1采购；2销售退货；3；调拨；4其他',
  `type_id` int(10) NOT NULL COMMENT '关联ID',
  `warehouse_id` int(10) NOT NULL DEFAULT '0' COMMENT '仓库ID ，不启用多仓库为0',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`stock_in_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_stock_in_productinfo` 入库产品信息表
--

DROP TABLE IF EXISTS `pdcrm_stock_in_productinfo`;
CREATE TABLE IF NOT EXISTS `pdcrm_stock_in_productinfo` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `stock_in_id` int(10) NOT NULL COMMENT '入库ID',
  `product_info_id` int(10) NOT NULL COMMENT '产品ID',
  `count` int(10) NOT NULL COMMENT '数量',
  `remark` varchar(512) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_stock_out` 出库记录表
--

DROP TABLE IF EXISTS `pdcrm_stock_out`;
CREATE TABLE IF NOT EXISTS `pdcrm_stock_out` (
  `stock_out_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `number` varchar(32) NOT NULL COMMENT '出库编号',
  `type` tinyint(255) NOT NULL COMMENT '出库类型：1销售；2采购退货；3；调拨；4其他',
  `type_id` int(10) NOT NULL COMMENT '关联ID',
  `warehouse_id` int(10) NOT NULL DEFAULT '0' COMMENT '仓库ID ，不启用多仓库为0',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `express` varchar(32) NOT NULL DEFAULT '' COMMENT '物流单号',
  PRIMARY KEY (`stock_out_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_stock_out_productinfo` 出库产品信息表
--

DROP TABLE IF EXISTS `pdcrm_stock_out_productinfo`;
CREATE TABLE IF NOT EXISTS `pdcrm_stock_out_productinfo` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `stock_out_id` int(10) NOT NULL COMMENT '出库ID',
  `product_info_id` int(10) NOT NULL COMMENT '产品ID',
  `count` int(10) NOT NULL COMMENT '数量',
  `remark` varchar(512) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_transfer` 调拨单表
--

DROP TABLE IF EXISTS `pdcrm_transfer`;
CREATE TABLE IF NOT EXISTS `pdcrm_transfer` (
  `transfer_id` int(10) NOT NULL AUTO_INCREMENT,
  `out_warehouse_id` int(10) NOT NULL COMMENT '调出仓',
  `in_warehouse_id` int(10) NOT NULL COMMENT '调入仓',
  `number` varchar(50) NOT NULL COMMENT '调拨单号',
  `transfer_out_date` int(10) NOT NULL COMMENT '调出日期',
  `transfer_in_date` int(10) NOT NULL COMMENT '调入日期',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人role_id',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  `creator_role_id` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '默认0未出库 1已出库（在途） 2已入库',
  PRIMARY KEY (`transfer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='库存调拨记录表' AUTO_INCREMENT=1 ;

--
-- 表的结构 `pdcrm_transfer_product` 调拨单关联产品表
--

DROP TABLE IF EXISTS `pdcrm_transfer_product`;
CREATE TABLE IF NOT EXISTS `pdcrm_transfer_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_id` int(10) NOT NULL COMMENT '调拨单id',
  `product_info_id` int(10) NOT NULL COMMENT '产品id',
  `count` int(10) NOT NULL COMMENT '产品数量',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='采购单关联产品信息表' AUTO_INCREMENT=1 ;

-- 自定义审批日志表

DROP TABLE IF EXISTS `pdcrm_exam_log`;
CREATE TABLE `pdcrm_exam_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) NOT NULL COMMENT '模块ID: pdcrm_exam_type',
  `order_id` int(10) NOT NULL COMMENT '对应订单id',
  `step_id` int(10) NOT NULL COMMENT '审批步骤id',
  `role_id` int(10) NOT NULL COMMENT '审批人',
  `result` tinyint(1) NOT NULL COMMENT '结果。0：驳回，1：通过，2结束 3撤销',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- 自定义审批流程表

DROP TABLE IF EXISTS `pdcrm_exam_process`;
CREATE TABLE `pdcrm_exam_process` (
  `process_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) NOT NULL COMMENT '模块ID: pdcrm_exam_type',
  `name` varchar(64) NOT NULL COMMENT '模块名称',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '启用与否：1是，0否',
  PRIMARY KEY (`process_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- 自定义审批步骤表

DROP TABLE IF EXISTS `pdcrm_exam_step`;
CREATE TABLE `pdcrm_exam_step` (
  `step_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `process_id` int(10) NOT NULL COMMENT '审批流ID: pdcrm_exam_process',
  `role_ids` varchar(128) NOT NULL COMMENT '审批人，逗号分隔两端加逗号',
  `relation` varchar(255) NOT NULL COMMENT '步骤逻辑，1：并且；2或者',
  `step` int(10) NOT NULL COMMENT '步骤',
  PRIMARY KEY (`step_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- 自定义审批类型表

DROP TABLE IF EXISTS `pdcrm_exam_type`;
CREATE TABLE `pdcrm_exam_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '模块名称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态默认0，0：不启用（自定义审批），1启用：审批流；',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`type_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- 自定义审批订单关系表

CREATE TABLE `pdcrm_r_exam` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) NOT NULL COMMENT '模块ID: pdcrm_exam_type',
  `order_id` int(10) NOT NULL COMMENT '对应订单id',
  `exam_status` tinyint(1) NOT NULL COMMENT '审批结果：0：待审；1审批中；2通过；3驳回',
  `role_ids` varchar(255) NOT NULL COMMENT '审批人role_id',
  `step_id` int(10) NOT NULL COMMENT '审批步骤表id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- 销售关联产品表 pdcrm_sales_product 把 product_id 改名为 product_info_id
ALTER TABLE  `pdcrm_sales_product` CHANGE  `product_id`  `product_info_id` INT( 10 ) NOT NULL;

-- 销售表 pdcrm_sales 新增字段
ALTER TABLE  `pdcrm_sales` ADD  `owner_role_id` INT( 10 ) NOT NULL COMMENT  '负责人ID';
ALTER TABLE  `pdcrm_sales` ADD  `update_time` INT( 10 ) NOT NULL COMMENT  '更新时间';

-- 商机产品关系表 pdcrm_r_business_product
-- 描述 字段product_id 改为 product_info_id 

ALTER TABLE  `pdcrm_r_business_product` CHANGE  `product_id`  `product_info_id` INT( 10 ) NOT NULL;

-- 旧产品表 pdcrm_product
-- 描述 新增字段

ALTER TABLE  `pdcrm_product` ADD  `has_sn` TINYINT( 1 ) NOT NULL COMMENT  '是否有sn码';
ALTER TABLE  `pdcrm_product` ADD  `enable_spec` TINYINT( 1 ) NOT NULL COMMENT  '是否启用产品多规格 0否 1是';

-- 应收款表
ALTER TABLE `pdcrm_receivables` MODIFY COLUMN `customer_id` int(10) NOT NULL DEFAULT 0 COMMENT '客户id / 供应商id';
ALTER TABLE `pdcrm_receivables` MODIFY COLUMN `contract_id` int(10) NOT NULL DEFAULT 0 COMMENT '合同id / 采购单id';
ALTER TABLE `pdcrm_receivables` MODIFY COLUMN `type` int(10) NOT NULL DEFAULT 0 COMMENT '0：普通  1：销售  2：采购退货（0、1是之前有的，0没找到使用场景）';

-- 应付款表
ALTER TABLE  `pdcrm_payables` CHANGE  `customer_id`  `customer_id` INT( 10 ) NOT NULL COMMENT  '客户id，当type_id等于-1时，值为供应商id的值';
ALTER TABLE  `pdcrm_payables` CHANGE  `contract_id`  `contract_id` INT( 10 ) NOT NULL COMMENT  '当type_id等于-1时，值为采购单id的值，-2时值为销售退货单id，即purchase_id,其他情况还为contract_id';
ALTER TABLE  `pdcrm_payables` CHANGE  `type_id`  `type_id` INT( 10 ) NOT NULL COMMENT  '应付款类型 -1采购退款 -2销售退货退款 其他数字代表其他类型';

-- 供应商相关自定义字段
INSERT INTO `pdcrm_fields` (`model`, `is_main`, `field`, `name`, `form_type`, `default_value`, `color`, `max_length`, `is_unique`, `is_recheck`, `is_null`, `is_validate`, `in_index`, `in_add`, `input_tips`, `setting`, `order_id`, `operating`, `is_show`, `status`) VALUES
('supplier', 1, 'crm_rkxjgu', '服务类型', 'text', '', '333333', 40, 0, 0, 0, 0, 1, 1, '', '', 3, 0, 0, 0),
('supplier', 1, 'category', '供应商类别', 'box', '', '333333', 0, 0, 0, 0, 0, 1, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''默认类别''))', 0, 1, 0, 0),
('supplier', 1, 'name', '供应商名称', 'text', '', '333333', 50, 0, 0, 0, 0, 1, 1, '', '', 1, 1, 0, 0),
('supplier', 0, 'crm_pexwas', '备注', 'text', '', '333333', 100, 0, 0, 0, 0, 0, 1, '', '', 4, 0, 0, 0),
('supplier', 1, 'crm_gbzegv', '供应商等级', 'box', '', '333333', 0, 0, 0, 0, 0, 0, 1, '', 'array(''type''=>''select'',''data''=>array(1=>''A级'',2=>''B级'',3=>''C级''))', 2, 0, 0, 0),
('supplier', 1, 'crm_fekgue', '供应商性质', 'text', '', '333333', 0, 0, 0, 0, 0, 0, 1, '', '', 0, 0, 0, 0);

-- config 配置
INSERT INTO `pdcrm_config` (`name`, `value`, `description`) VALUES ('purchase_prefix', 'CGD_', '采购单号前缀');
INSERT INTO `pdcrm_config` (`name`, `value`, `description`) VALUES ('purchase_return_prefix', 'CTD_', '采购退货单号前缀');
INSERT INTO `pdcrm_config` (`name`, `value`, `description`) VALUES ('sales_return_prefix', 'XTD_', '销售退货单号前缀');
INSERT INTO `pdcrm_config` (`name`, `value`, `description`) VALUES ('stock_in_prefix', 'RKD_', '入库单号前缀');
INSERT INTO `pdcrm_config` (`name`, `value`, `description`) VALUES ('stock_out_prefix', 'CKD_', '出库单号前缀');
-- 超库存销售
INSERT INTO `pdcrm_config` (`name`, `value`, `description`) VALUES ('over_stock_sales', '0', '是否允许超库存销售');

-- pdcrm_user 表增加 kaopin 字段
ALTER TABLE  `pdcrm_user` ADD  `kaoqin` TINYINT( 1 ) NOT NULL DEFAULT  '1' COMMENT  '是否考勤，1是，0否';

-- 审批模块设置
INSERT INTO `pdcrm_exam_type`(`type_id`, `name`, `status`, `remark`, `update_time`) VALUES (1, '采购', 0, '采购模块的审批', 0);
INSERT INTO `pdcrm_exam_type`(`type_id`, `name`, `status`, `remark`, `update_time`) VALUES (2, '采购退货', 0, '采购退货模块的审批', 0);
INSERT INTO `pdcrm_exam_type`(`type_id`, `name`, `status`, `remark`, `update_time`) VALUES (3, '销售退货', 0, '销售退货模块的审批', 0);
INSERT INTO `pdcrm_exam_type`(`type_id`, `name`, `status`, `remark`, `update_time`) VALUES (4, '库存调拨', 0, '库存调拨模块的审批', 0);

-- 产品原“规格”名称改为“单位”
UPDATE `pdcrm_fields` SET  `name` =  '单位' WHERE (`model` = 'product' and `field` = 'standard');
UPDATE `pdcrm_template_field_block` SET `content` = '<table align="center" border="2"><tbody><tr class="firstRow">
<td width="84" valign="middle" style="word-break: break-all;" align="center">产品名称</td><td width="84" valign="middle" style="word-break: break-all;" align="center">规格</td><td width="84" valign="middle" style="word-break: break-all;" align="center"><span style="text-align: -webkit-center;">价格(元)</span></td><td width="84" valign="middle" style="word-break: break-all;" align="center">折扣(%)</td><td width="84" valign="middle" style="word-break: break-all;" align="center">销售单价(元)</td><td width="84" valign="middle" style="word-break: break-all;" align="center">数量</td><td width="84" valign="middle" style="word-break: break-all;" align="center">单位</td><td width="84" valign="middle" style="word-break: break-all;" align="center">小计</td></tr><tr php="each"><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="产品名称" data-original="product.product_name">{{product.product_name}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="规格" data-original="product.spec">{{product.spec}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="价格(元)" data-original="product.ori_price">{{product.ori_price}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="折扣(%)" data-original="product.discount_rate">{{product.discount_rate}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="销售单价(元)" data-original="product.unit_price">{{product.unit_price}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="数量" data-original="product.amount">{{product.amount}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="单位" data-original="product.unit">{{product.unit}}</span></td><td valign="middle" align="center" colspan="1" rowspan="1" style="word-break: break-all;"><span class="variable-wrapper" contenteditable="false" title="小计" data-original="product.subtotal">{{product.subtotal}}</span></td></tr></tbody></table>' WHERE `id` = 1;
ALTER TABLE  `pdcrm_file` ADD  `oss` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '是否上传至OSS服务器 默认0否 1是' AFTER  `file_path_thumb`;

-- 2.5.1版本
CREATE TABLE `pdcrm_visitor_plan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL COMMENT '外键ID',
  `plan_time` int(11) NOT NULL COMMENT '初次计划时间',
  `delay_time` int(11) NOT NULL DEFAULT '0' COMMENT '延期时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0 未完成；1 推迟；2 过期；3 放弃；4 完成；',
  `content` varchar(255) NOT NULL COMMENT '提醒内容',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '完成模块',
  `module_id` int(11) NOT NULL DEFAULT '0' COMMENT '完成模块ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE  `pdcrm_r_customer_log` ADD  `contacts_id` INT( 10 ) NOT NULL COMMENT  '客户联系人ID';
ALTER TABLE `pdcrm_r_business_log` ADD COLUMN `contacts_id` int(10) NOT NULL COMMENT '客户联系人ID';

-- 2.5.2版本
-- 2.5.3版本
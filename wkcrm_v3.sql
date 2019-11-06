/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50519
Source Host           : localhost:3306
Source Database       : wkcrm_v3

Target Server Type    : MYSQL
Target Server Version : 50519
File Encoding         : 65001

Date: 2019-04-18 15:43:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `pd_account_money`
-- ----------------------------
DROP TABLE IF EXISTS `pd_account_money`;
CREATE TABLE `pd_account_money` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL COMMENT '银行账户ID',
  `money` decimal(10,2) NOT NULL COMMENT '账户初始化余额',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '初始化时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='财务账户初始化余额';

-- ----------------------------
-- Records of pd_account_money
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_action_log`;
CREATE TABLE `pd_action_log` (
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
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COMMENT='操作日志表';

-- ----------------------------
-- Records of pd_action_log
-- ----------------------------
INSERT INTO `pd_action_log` VALUES ('1', '1', 'leads', 'add', null, '1', '0', '管理员admins在2019-03-27 09:54:20添加了id为1的线索。', '1553651660');
INSERT INTO `pd_action_log` VALUES ('2', '1', 'leads', 'edit', null, '1', '0', '管理员admins在2019-03-27 09:54:39修改了id为1的线索。', '1553651679');
INSERT INTO `pd_action_log` VALUES ('3', '1', 'product', 'add', null, '1', '0', '管理员admins在2019-04-09 20:05:39添加了id为1的产品。', '1554811539');
INSERT INTO `pd_action_log` VALUES ('4', '1', 'customer', 'add', null, '1', '0', '管理员admins在2019-04-09 20:29:18添加了id为1的客户。', '1554812958');
INSERT INTO `pd_action_log` VALUES ('5', '1', 'contract', 'add', null, '1', '0', '管理员admins在2019-04-09 20:30:03添加了id为1的合同。', '1554813003');
INSERT INTO `pd_action_log` VALUES ('6', '1', 'contract', 'check', null, '1', '0', '管理员admins在2019-04-09 20:30:46审核了id为1的合同。', '1554813046');
INSERT INTO `pd_action_log` VALUES ('7', '1', 'supplier', 'add', null, '1', '0', '管理员admins在2019-04-09 20:32:18添加了id为1的供应商。', '1554813138');
INSERT INTO `pd_action_log` VALUES ('8', '1', 'finance', 'add', 't=paymentorder', '1', '0', '管理员admins在2019-04-09 20:36:02添加了id为1的财务。', '1554813362');
INSERT INTO `pd_action_log` VALUES ('9', '1', 'finance', 'add', 't=paymentorder', '2', '0', '管理员admins在2019-04-09 20:36:15添加了id为2的财务。', '1554813375');
INSERT INTO `pd_action_log` VALUES ('10', '1', 'contract', 'add', null, '2', '0', '管理员admins在2019-04-10 12:05:49添加了id为2的合同。', '1554869149');
INSERT INTO `pd_action_log` VALUES ('11', '1', 'contract', 'check', null, '2', '0', '管理员admins在2019-04-10 12:07:03审核了id为2的合同。', '1554869223');
INSERT INTO `pd_action_log` VALUES ('12', '1', 'contract', 'check', null, '2', '0', '管理员admins在2019-04-10 12:08:39审核了id为2的合同。', '1554869319');
INSERT INTO `pd_action_log` VALUES ('13', '1', 'contract', 'check', null, '2', '0', '管理员admins在2019-04-10 12:09:57审核了id为2的合同。', '1554869397');
INSERT INTO `pd_action_log` VALUES ('14', '1', 'customer', 'add', null, '2', '0', '管理员admins在2019-04-10 12:36:19添加了id为2的客户。', '1554870979');
INSERT INTO `pd_action_log` VALUES ('15', '1', 'product', 'add', null, '2', '0', '管理员admins在2019-04-10 12:40:25添加了id为2的产品。', '1554871225');
INSERT INTO `pd_action_log` VALUES ('16', '1', 'finance', 'add', 't=paymentorder', '3', '0', '管理员admins在2019-04-10 12:56:16添加了id为3的财务。', '1554872176');
INSERT INTO `pd_action_log` VALUES ('17', '1', 'finance', 'add', 't=receivingorder', '1', '0', '管理员admins在2019-04-10 12:56:57添加了id为1的财务。', '1554872217');
INSERT INTO `pd_action_log` VALUES ('18', '1', 'finance', 'add', 't=receivingorder', '2', '0', '管理员admins在2019-04-10 12:58:29添加了id为2的财务。', '1554872309');
INSERT INTO `pd_action_log` VALUES ('19', '1', 'contract', 'add', null, '3', '0', '管理员admins在2019-04-10 13:02:37添加了id为3的合同。', '1554872557');
INSERT INTO `pd_action_log` VALUES ('20', '1', 'contract', 'check', null, '3', '0', '管理员admins在2019-04-10 13:03:03审核了id为3的合同。', '1554872583');
INSERT INTO `pd_action_log` VALUES ('21', '1', 'finance', 'add', 't=receivingorder', '3', '0', '管理员admins在2019-04-10 13:04:48添加了id为3的财务。', '1554872688');
INSERT INTO `pd_action_log` VALUES ('22', '1', 'user', 'edit', null, '2', '0', '管理员admins在2019-04-10 13:40:20编辑了id为2的用户。', '1554874820');
INSERT INTO `pd_action_log` VALUES ('23', '1', 'user', 'edit', null, '3', '0', '管理员admins在2019-04-10 13:40:43编辑了id为3的用户。', '1554874843');
INSERT INTO `pd_action_log` VALUES ('24', '2', 'sign', 'add', null, '1', '0', '员工金先生在2019-04-10 13:43:48添加了id为1的签到。', '1554875028');
INSERT INTO `pd_action_log` VALUES ('25', '1', 'leads', 'add', null, '2', '0', '管理员admins在2019-04-11 09:02:19添加了id为2的线索。', '1554944539');
INSERT INTO `pd_action_log` VALUES ('26', '1', 'contract', 'add', null, '4', '0', '管理员admins在2019-04-11 09:06:28添加了id为4的合同。', '1554944788');
INSERT INTO `pd_action_log` VALUES ('27', '1', 'business', 'add', null, '1', '0', '管理员admins在2019-04-11 09:12:21添加了id为1的商机。', '1554945141');
INSERT INTO `pd_action_log` VALUES ('28', '1', 'business', 'advance', null, '1', '0', '管理员admins在2019-04-11 09:12:38推进了id为1的商机。', '1554945158');
INSERT INTO `pd_action_log` VALUES ('29', '1', 'contract', 'check', null, '4', '0', '管理员admins在2019-04-11 09:14:35审核了id为4的合同。', '1554945275');
INSERT INTO `pd_action_log` VALUES ('30', '2', 'product', 'edit', null, '2', '0', '员工金先生在2019-04-11 11:11:16修改了id为2的产品。', '1554952276');
INSERT INTO `pd_action_log` VALUES ('31', '2', 'product', 'edit', null, '2', '0', '员工金先生在2019-04-11 11:13:49修改了id为2的产品。', '1554952429');
INSERT INTO `pd_action_log` VALUES ('32', '2', 'product', 'add', null, '3', '0', '员工金先生在2019-04-11 11:26:24添加了id为3的产品。', '1554953184');
INSERT INTO `pd_action_log` VALUES ('33', '2', 'product', 'add', null, '4', '0', '员工金先生在2019-04-11 11:28:16添加了id为4的产品。', '1554953296');
INSERT INTO `pd_action_log` VALUES ('34', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 11:29:45修改了id为4的产品。', '1554953385');
INSERT INTO `pd_action_log` VALUES ('35', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:13:54修改了id为4的产品。', '1554956034');
INSERT INTO `pd_action_log` VALUES ('36', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:16:12修改了id为4的产品。', '1554956172');
INSERT INTO `pd_action_log` VALUES ('37', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:16:35修改了id为4的产品。', '1554956195');
INSERT INTO `pd_action_log` VALUES ('38', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:17:18修改了id为4的产品。', '1554956238');
INSERT INTO `pd_action_log` VALUES ('39', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:22:54修改了id为4的产品。', '1554956574');
INSERT INTO `pd_action_log` VALUES ('40', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:34:41修改了id为4的产品。', '1554957281');
INSERT INTO `pd_action_log` VALUES ('41', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:35:33修改了id为4的产品。', '1554957333');
INSERT INTO `pd_action_log` VALUES ('42', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:36:16修改了id为4的产品。', '1554957376');
INSERT INTO `pd_action_log` VALUES ('43', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:37:09修改了id为4的产品。', '1554957429');
INSERT INTO `pd_action_log` VALUES ('44', '2', 'product', 'edit', null, '4', '0', '员工金先生在2019-04-11 12:40:55修改了id为4的产品。', '1554957655');
INSERT INTO `pd_action_log` VALUES ('45', '2', 'product', 'edit', null, '3', '0', '员工金先生在2019-04-11 12:42:24修改了id为3的产品。', '1554957744');
INSERT INTO `pd_action_log` VALUES ('46', '1', 'leads', 'add', null, '3', '0', '管理员admins在2019-04-16 10:04:17添加了id为3的线索。', '1555380257');
INSERT INTO `pd_action_log` VALUES ('47', '2', 'sign', 'add', null, '2', '0', '员工金先生在2019-04-16 21:43:11添加了id为2的签到。', '1555422191');
INSERT INTO `pd_action_log` VALUES ('48', '1', 'business', 'advance', null, '1', '0', '管理员admins在2019-04-17 10:54:12推进了id为1的商机。', '1555469652');
INSERT INTO `pd_action_log` VALUES ('49', '1', 'business', 'advance', null, '1', '0', '管理员admins在2019-04-17 10:54:34推进了id为1的商机。', '1555469674');
INSERT INTO `pd_action_log` VALUES ('50', '1', 'contract', 'add', null, '5', '0', '管理员admins在2019-04-17 10:57:30添加了id为5的合同。', '1555469850');
INSERT INTO `pd_action_log` VALUES ('51', '1', 'business', 'advance', null, '1', '0', '管理员admins在2019-04-17 10:58:52推进了id为1的商机。', '1555469932');
INSERT INTO `pd_action_log` VALUES ('52', '1', 'contract', 'add', null, '6', '0', '管理员admins在2019-04-18 10:02:21添加了id为6的合同。', '1555552941');
INSERT INTO `pd_action_log` VALUES ('53', '1', 'contract', 'add', null, '7', '0', '管理员admins在2019-04-18 10:04:35添加了id为7的合同。', '1555553075');
INSERT INTO `pd_action_log` VALUES ('54', '1', 'contract', 'add', null, '8', '0', '管理员admins在2019-04-18 10:11:59添加了id为8的合同。', '1555553519');
INSERT INTO `pd_action_log` VALUES ('55', '1', 'finance', 'add', 't=receivingorder', '4', '0', '管理员admins在2019-04-18 11:04:30添加了id为4的财务。', '1555556670');
INSERT INTO `pd_action_log` VALUES ('56', '1', 'leads', 'add', null, '4', '0', '管理员admins在2019-04-18 13:29:09添加了id为4的线索。', '1555565349');
INSERT INTO `pd_action_log` VALUES ('57', '1', 'product', 'add', null, '5', '0', '管理员admins在2019-04-18 13:39:50添加了id为5的产品。', '1555565990');
INSERT INTO `pd_action_log` VALUES ('58', '1', 'product', 'add', null, '6', '0', '管理员admins在2019-04-18 13:47:18添加了id为6的产品。', '1555566438');
INSERT INTO `pd_action_log` VALUES ('59', '1', 'contract', 'add', null, '9', '0', '管理员admins在2019-04-18 13:58:39添加了id为9的合同。', '1555567119');
INSERT INTO `pd_action_log` VALUES ('60', '1', 'leads', 'edit', null, '4', '0', '管理员admins在2019-04-18 14:26:31修改了id为4的线索。', '1555568791');
INSERT INTO `pd_action_log` VALUES ('61', '1', 'customer', 'add', null, '4', '0', '管理员admins在2019-04-18 14:28:59添加了id为4的客户。', '1555568939');

-- ----------------------------
-- Table structure for `pd_action_record`
-- ----------------------------
DROP TABLE IF EXISTS `pd_action_record`;
CREATE TABLE `pd_action_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_role_id` int(11) NOT NULL,
  `create_time` int(10) NOT NULL,
  `model_name` varchar(50) NOT NULL COMMENT '模块名',
  `action_id` int(11) NOT NULL COMMENT '模块信息ID',
  `type` varchar(30) NOT NULL,
  `duixiang` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_action_record
-- ----------------------------
INSERT INTO `pd_action_record` VALUES ('1', '1', '1555248831', 'customer', '3', '修改', '将线索 事实上 转化为客户');
INSERT INTO `pd_action_record` VALUES ('2', '1', '1555468756', 'business', '1', '修改', '<div>将 商机金额 由 <span style=\"color:#77B0E9\">\"35.00\"</span> 修改为 <span style=\"color:#E7AE6F\">\"25.00\"</span> </div><div>将 下次联系时间 由 <span style=\"color:#77B0E9\">\"2019-04-17\"</span> 修改为 <span style=\"color:#E7AE6F\">\"1970-01-01\"</span> </div>');
INSERT INTO `pd_action_record` VALUES ('3', '1', '1555468775', 'business', '1', '修改', '<div>将 下次联系时间 由 <span style=\"color:#77B0E9\">\"1970-01-01\"</span> 修改为 <span style=\"color:#E7AE6F\">\"1970-01-01\"</span> </div>');

-- ----------------------------
-- Table structure for `pd_announcement`
-- ----------------------------
DROP TABLE IF EXISTS `pd_announcement`;
CREATE TABLE `pd_announcement` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='存放知识文章信息';

-- ----------------------------
-- Records of pd_announcement
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_announcement_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_announcement_data`;
CREATE TABLE `pd_announcement_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NOT NULL COMMENT '公告ID',
  `role_id` int(11) NOT NULL COMMENT '阅读人',
  `read_time` int(10) NOT NULL COMMENT '阅读时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='公告阅读表';

-- ----------------------------
-- Records of pd_announcement_data
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_bank_account`
-- ----------------------------
DROP TABLE IF EXISTS `pd_bank_account`;
CREATE TABLE `pd_bank_account` (
  `account_id` int(10) NOT NULL AUTO_INCREMENT,
  `bank_account` varchar(255) NOT NULL COMMENT '银行账号',
  `company` varchar(255) NOT NULL COMMENT '收款单位',
  `open_bank` varchar(255) NOT NULL COMMENT '开户行',
  `description` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='银行账户表';

-- ----------------------------
-- Records of pd_bank_account
-- ----------------------------
INSERT INTO `pd_bank_account` VALUES ('1', '62222002222', '111', '对公账户', '');

-- ----------------------------
-- Table structure for `pd_business`
-- ----------------------------
DROP TABLE IF EXISTS `pd_business`;
CREATE TABLE `pd_business` (
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
  `status_type_id` int(10) NOT NULL DEFAULT '1' COMMENT '状态组ID',
  PRIMARY KEY (`business_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='本表存放商机相关信息';

-- ----------------------------
-- Records of pd_business
-- ----------------------------
INSERT INTO `pd_business` VALUES ('1', 'M_20190411-0001', '20190411-0001', 'M_', '1', '1', '1', '2', '25.00', '0.00', '25.00', '1554945141', '1555469932', '100', '0', '0', '0', '0', '0', '10%', '1');

-- ----------------------------
-- Table structure for `pd_business_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_business_data`;
CREATE TABLE `pd_business_data` (
  `business_id` int(10) NOT NULL COMMENT '主键',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`business_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商机数据表';

-- ----------------------------
-- Records of pd_business_data
-- ----------------------------
INSERT INTO `pd_business_data` VALUES ('1', '435345345345');

-- ----------------------------
-- Table structure for `pd_business_status`
-- ----------------------------
DROP TABLE IF EXISTS `pd_business_status`;
CREATE TABLE `pd_business_status` (
  `status_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '商机状态',
  `name` varchar(100) DEFAULT NULL COMMENT '商机状态名',
  `order_id` int(10) DEFAULT NULL COMMENT '顺序号',
  `is_end` int(1) NOT NULL COMMENT '0正常2失败3成功',
  `description` varchar(200) DEFAULT NULL COMMENT '商机状态描述',
  `type_id` int(10) NOT NULL DEFAULT '1' COMMENT '状态组ID',
  PRIMARY KEY (`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COMMENT='本表存放商机状态信息';

-- ----------------------------
-- Records of pd_business_status
-- ----------------------------
INSERT INTO `pd_business_status` VALUES ('1', '初步洽谈', '1', '0', '初步洽谈', '1');
INSERT INTO `pd_business_status` VALUES ('2', '深入沟通', '2', '0', '深入沟通', '1');
INSERT INTO `pd_business_status` VALUES ('3', '销售定价', '3', '0', '定价', '1');
INSERT INTO `pd_business_status` VALUES ('98', '合同发票', '6', '1', '合同发票', '1');
INSERT INTO `pd_business_status` VALUES ('99', '项目失败', '99', '2', '项目失败', '1');
INSERT INTO `pd_business_status` VALUES ('100', '完成收款', '100', '3', '完成收款', '1');

-- ----------------------------
-- Table structure for `pd_business_type`
-- ----------------------------
DROP TABLE IF EXISTS `pd_business_type`;
CREATE TABLE `pd_business_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '组名',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='商机状态组表';

-- ----------------------------
-- Records of pd_business_type
-- ----------------------------
INSERT INTO `pd_business_type` VALUES ('1', '默认分组', '1', '1511768134', '1511768134');

-- ----------------------------
-- Table structure for `pd_call_record`
-- ----------------------------
DROP TABLE IF EXISTS `pd_call_record`;
CREATE TABLE `pd_call_record` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通话记录表';

-- ----------------------------
-- Records of pd_call_record
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_comment`
-- ----------------------------
DROP TABLE IF EXISTS `pd_comment`;
CREATE TABLE `pd_comment` (
  `comment_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `content` varchar(1000) NOT NULL COMMENT '评论内容',
  `creator_role_id` int(10) NOT NULL COMMENT '评论人',
  `to_role_id` int(10) NOT NULL COMMENT '被评论人',
  `module` varchar(50) NOT NULL COMMENT '模块',
  `module_id` int(10) NOT NULL COMMENT '模块id',
  `create_time` int(10) NOT NULL COMMENT '添加时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表';

-- ----------------------------
-- Records of pd_comment
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_config`
-- ----------------------------
DROP TABLE IF EXISTS `pd_config`;
CREATE TABLE `pd_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_config
-- ----------------------------
INSERT INTO `pd_config` VALUES ('1', 'defaultinfo', 'a:10:{s:4:\"logo\";N;s:8:\"logo_min\";N;s:4:\"name\";s:9:\"PDCRM\";s:11:\"description\";s:24:\"客户关系管理系统\";s:5:\"state\";s:9:\"北京市\";s:4:\"city\";s:9:\"市辖区\";s:15:\"allow_file_type\";s:55:\"pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,txt,zip\";s:19:\"contract_alert_time\";i:30;s:10:\"task_model\";s:0:\"\";s:10:\"is_invoice\";s:0:\"\";}', '');
INSERT INTO `pd_config` VALUES ('2', 'customer_outdays', '7', '客户设置放入客户池天数');
INSERT INTO `pd_config` VALUES ('3', 'customer_limit_condition', 'month', '客户池领取条件限制 day：今日 week： 本周 month：本月');
INSERT INTO `pd_config` VALUES ('4', 'customer_limit_counts', '30', '客户池领取次数限制');
INSERT INTO `pd_config` VALUES ('5', 'leads_outdays', '7', '线索超出天数放入客户池');
INSERT INTO `pd_config` VALUES ('6', 'contract_custom', 'C_', '');
INSERT INTO `pd_config` VALUES ('7', 'num_id', '', '');
INSERT INTO `pd_config` VALUES ('8', 'is_invoice', '', '是否添加开发票选项');
INSERT INTO `pd_config` VALUES ('9', 'receivables_custom', 'M_', '应收款前缀');
INSERT INTO `pd_config` VALUES ('10', 'sms', 'a:5:{s:3:\"uid\";s:17:\"\";s:6:\"\";s:6:\"\";s:9:\"sign_name\";s:6:\"\";s:12:\"sign_sysname\";s:0:\"\";s:4:\"name\";s:3:\"sms\";}', '');
INSERT INTO `pd_config` VALUES ('11', 'smtp', 'a:9:{s:12:\"MAIL_ADDRESS\";s:16:\"\";s:9:\"MAIL_SMTP\";s:11:\"\";s:9:\"MAIL_PORT\";s:2:\"\";s:14:\"MAIL_LOGINNAME\";s:16:\"\";s:13:\"MAIL_PASSWORD\";s:16:\"\";s:11:\"MAIL_SECURE\";N;s:12:\"MAIL_CHARSET\";s:5:\"UTF-8\";s:9:\"MAIL_AUTH\";b:1;s:9:\"MAIL_HTML\";b:1;}', '');
INSERT INTO `pd_config` VALUES ('12', 'business_custom', 'M_', '商机编码前缀');
INSERT INTO `pd_config` VALUES ('13', 'openrecycle', '2', '');
INSERT INTO `pd_config` VALUES ('14', 'business_code', '0', '商机编号数');
INSERT INTO `pd_config` VALUES ('15', 'contract_outdays', '30', '');
INSERT INTO `pd_config` VALUES ('16', 'cc_check', '', '');
INSERT INTO `pd_config` VALUES ('17', 'bc_check', '', '');
INSERT INTO `pd_config` VALUES ('18', 'fc_check', '', '');
INSERT INTO `pd_config` VALUES ('19', 'user_custom', 'K', '员工编号前缀');
INSERT INTO `pd_config` VALUES ('20', 'uc_check', '', '员工编号前缀是否替换');
INSERT INTO `pd_config` VALUES ('22', 'receivables_time', '30', '应收款提前几天提醒');
INSERT INTO `pd_config` VALUES ('23', 'call_record', '1', '1开启录音0关闭录音');
INSERT INTO `pd_config` VALUES ('24', 'search_disable_user', '0', '可搜索已停用用户: 1是；0否');
INSERT INTO `pd_config` VALUES ('25', 'purchase_prefix', 'CGD_', '采购单号前缀');
INSERT INTO `pd_config` VALUES ('26', 'purchase_return_prefix', 'CTD_', '采购退货单号前缀');
INSERT INTO `pd_config` VALUES ('27', 'sales_return_prefix', 'XTD_', '销售退货单号前缀');
INSERT INTO `pd_config` VALUES ('28', 'stock_in_prefix', 'RKD_', '入库单号前缀');
INSERT INTO `pd_config` VALUES ('29', 'stock_out_prefix', 'CKD_', '出库单号前缀');
INSERT INTO `pd_config` VALUES ('30', 'over_stock_sales', '0', '是否允许超库存销售');

-- ----------------------------
-- Table structure for `pd_contacts`
-- ----------------------------
DROP TABLE IF EXISTS `pd_contacts`;
CREATE TABLE `pd_contacts` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='本表存放客户联系人对应关系信息';

-- ----------------------------
-- Records of pd_contacts
-- ----------------------------
INSERT INTO `pd_contacts` VALUES ('1', '0', '事实上', '', '', '', '女士', '18888888888', '', '事实上', '', '', '', '1555248831', '0', '0', '0', '0', '', '');

-- ----------------------------
-- Table structure for `pd_contacts_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_contacts_data`;
CREATE TABLE `pd_contacts_data` (
  `contacts_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  PRIMARY KEY (`contacts_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_contacts_data
-- ----------------------------
INSERT INTO `pd_contacts_data` VALUES ('1');

-- ----------------------------
-- Table structure for `pd_contract`
-- ----------------------------
DROP TABLE IF EXISTS `pd_contract`;
CREATE TABLE `pd_contract` (
  `contract_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `prefixion` varchar(50) NOT NULL COMMENT '表前缀',
  `number` varchar(50) NOT NULL COMMENT '编号',
  `business_id` int(10) NOT NULL COMMENT '商机',
  `supplier_id` int(10) NOT NULL COMMENT '供应商id',
  `customer_id` int(10) NOT NULL COMMENT '客户id',
  `renew_contract_id` int(11) NOT NULL COMMENT '续签合同id',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '合同类型',
  `contract_status` int(1) NOT NULL COMMENT '1已续签2已忽略',
  `price` decimal(18,2) NOT NULL COMMENT '总价',
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
  `is_checked` int(10) NOT NULL COMMENT '是否审核0未审核1通过2未通过3审批中',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  `check_des` int(10) NOT NULL COMMENT '审核备注',
  `examine_role_id` varchar(500) NOT NULL COMMENT '审核人',
  `renew_parent_id` int(10) NOT NULL COMMENT '续约组父类ID',
  `order_id` int(10) NOT NULL COMMENT '审批流程ID',
  `examine_type_id` int(10) NOT NULL COMMENT '审批流类型ID（0自选1默认流程1）',
  `market_id` int(10) NOT NULL DEFAULT '0' COMMENT '活动ID',
  PRIMARY KEY (`contract_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='合同表';

-- ----------------------------
-- Records of pd_contract
-- ----------------------------
INSERT INTO `pd_contract` VALUES ('1', 'C_', 'C_20190409-0001', '0', '0', '1', '0', '1', '0', '50.00', '2', '1554825600', '1', '1', '', '', '1554813003', '1554813046', '1554739200', '1588348800', '已创建', '0', '0', '0', '销售合同1', '1', '1554813046', '0', '', '0', '0', '0', '0');
INSERT INTO `pd_contract` VALUES ('2', 'C_', 'C_20190410-0002', '0', '0', '1', '0', '1', '0', '25.00', '1', '1554825600', '1', '1', '', '', '1554869149', '1554869397', '1554825600', '1556553600', '已创建', '0', '0', '0', '123', '1', '1554869397', '0', '', '0', '0', '0', '0');
INSERT INTO `pd_contract` VALUES ('3', 'C_', 'C_20190410-0003', '0', '0', '2', '0', '1', '0', '40000.00', '10', '1554825600', '1', '1', '', '', '1554872557', '1554872583', '1554825600', '1556553600', '已创建', '0', '0', '0', '手机出售', '1', '1554872583', '0', '', '0', '0', '0', '0');
INSERT INTO `pd_contract` VALUES ('4', 'C_', 'C_20190411-0004', '0', '0', '1', '0', '1', '0', '435.00', '0', '1555516800', '1', '1', '', '', '1554944787', '1554945275', '1554912000', '1556553600', '已创建', '0', '0', '0', '多少', '1', '1554945275', '0', '', '0', '0', '0', '0');
INSERT INTO `pd_contract` VALUES ('5', 'C_', 'C_20190417-0005', '1', '0', '1', '0', '1', '0', '25.00', '0', '1555430400', '1', '1', '', '', '1555469849', '1555469849', '1555430400', '1555430400', '已创建', '0', '0', '0', '000', '0', '0', '0', '1', '0', '0', '0', '0');
INSERT INTO `pd_contract` VALUES ('6', 'C_', 'C_20190418-0006', '0', '0', '3', '0', '1', '0', '200.00', '0', '1555516800', '1', '1', '', '', '1555552941', '1555552941', '1555516800', '1555516800', '已创建', '0', '0', '0', '12300000', '0', '0', '0', '1', '0', '0', '0', '0');
INSERT INTO `pd_contract` VALUES ('7', 'C_', 'C_20190418-0007', '0', '0', '1', '0', '1', '0', '200.00', '0', '1555516800', '1', '1', '', '', '1555553074', '1555553074', '1555516800', '1555516800', '已创建', '0', '0', '0', '聂风雨', '0', '0', '0', '2', '0', '0', '0', '0');
INSERT INTO `pd_contract` VALUES ('8', 'C_', 'C_20190418-0008', '0', '0', '3', '0', '1', '0', '0.00', '2', '1555516800', '1', '1', '', '', '1555553519', '1555553519', '1555516800', '1555516800', '已创建', '0', '0', '0', '123878769', '0', '0', '0', '1', '0', '0', '0', '0');
INSERT INTO `pd_contract` VALUES ('9', 'C_', 'C_20190418-0009', '0', '0', '3', '0', '1', '0', '220.00', '2', '1555516800', '1', '1', '', 'asdasdasdasd', '1555567119', '1555567119', '1555516800', '1555603200', '已创建', '0', '0', '0', '123123123123123', '0', '0', '0', '2', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `pd_contract_check`
-- ----------------------------
DROP TABLE IF EXISTS `pd_contract_check`;
CREATE TABLE `pd_contract_check` (
  `check_id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) NOT NULL COMMENT '合同ID',
  `role_id` int(11) NOT NULL COMMENT '负责人ID',
  `is_checked` tinyint(1) NOT NULL COMMENT '审核状态(1同意2驳回)',
  `content` varchar(200) DEFAULT NULL COMMENT '审核内容',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  `is_end` tinyint(1) NOT NULL COMMENT '1无效审批2为了区别新老逻辑',
  `order_id` int(10) NOT NULL COMMENT '审批流程步骤ID',
  PRIMARY KEY (`check_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_contract_check
-- ----------------------------
INSERT INTO `pd_contract_check` VALUES ('1', '1', '1', '1', '', '1554813046', '0', '0');
INSERT INTO `pd_contract_check` VALUES ('2', '2', '1', '1', '', '1554869223', '0', '0');
INSERT INTO `pd_contract_check` VALUES ('3', '2', '1', '1', '', '1554869319', '0', '0');
INSERT INTO `pd_contract_check` VALUES ('4', '2', '1', '1', '', '1554869397', '0', '0');
INSERT INTO `pd_contract_check` VALUES ('5', '3', '1', '1', '', '1554872583', '0', '0');
INSERT INTO `pd_contract_check` VALUES ('6', '4', '1', '1', '', '1554945275', '0', '0');

-- ----------------------------
-- Table structure for `pd_contract_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_contract_data`;
CREATE TABLE `pd_contract_data` (
  `contract_id` int(10) NOT NULL COMMENT '主键',
  PRIMARY KEY (`contract_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同附表';

-- ----------------------------
-- Records of pd_contract_data
-- ----------------------------
INSERT INTO `pd_contract_data` VALUES ('1');
INSERT INTO `pd_contract_data` VALUES ('2');
INSERT INTO `pd_contract_data` VALUES ('3');
INSERT INTO `pd_contract_data` VALUES ('4');
INSERT INTO `pd_contract_data` VALUES ('5');
INSERT INTO `pd_contract_data` VALUES ('6');
INSERT INTO `pd_contract_data` VALUES ('7');
INSERT INTO `pd_contract_data` VALUES ('8');
INSERT INTO `pd_contract_data` VALUES ('9');

-- ----------------------------
-- Table structure for `pd_contract_examine`
-- ----------------------------
DROP TABLE IF EXISTS `pd_contract_examine`;
CREATE TABLE `pd_contract_examine` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` varchar(500) NOT NULL COMMENT '岗位ID',
  `order_id` int(10) NOT NULL COMMENT '排序ID',
  `relation` tinyint(1) NOT NULL COMMENT '审批流程关系（1并2或）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同审批流';

-- ----------------------------
-- Records of pd_contract_examine
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_control`
-- ----------------------------
DROP TABLE IF EXISTS `pd_control`;
CREATE TABLE `pd_control` (
  `control_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '操作id',
  `module_id` int(10) NOT NULL COMMENT '模块id',
  `name` varchar(20) NOT NULL COMMENT '操作名',
  `m` varchar(20) NOT NULL COMMENT '对应Action',
  `a` varchar(20) NOT NULL COMMENT '行为',
  `parameter` varchar(50) NOT NULL COMMENT '参数',
  `description` varchar(200) NOT NULL COMMENT '操作描述',
  PRIMARY KEY (`control_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='本表存放操作信息';

-- ----------------------------
-- Records of pd_control
-- ----------------------------
INSERT INTO `pd_control` VALUES ('1', '1', 'crm面板操作', 'index', 'index', '', 'CRM系统面板');
INSERT INTO `pd_control` VALUES ('2', '7', '修改个人信息', 'User', 'edit', '', '是的法士大夫地方');
INSERT INTO `pd_control` VALUES ('4', '7', '添加用户', 'User', 'add', '', '');
INSERT INTO `pd_control` VALUES ('78', '7', '删除员工', 'User', 'delete', '', '');
INSERT INTO `pd_control` VALUES ('6', '7', '添加部门', 'User', 'department_add', '', '');
INSERT INTO `pd_control` VALUES ('7', '7', '修改部门', 'User', 'department_edit', '', '');
INSERT INTO `pd_control` VALUES ('8', '7', '删除部门', 'User', 'department_delete', '', '');
INSERT INTO `pd_control` VALUES ('9', '7', '添加岗位', 'User', 'role_add', '', '');
INSERT INTO `pd_control` VALUES ('10', '7', '修改岗位', 'User', 'role_edit', '', '');
INSERT INTO `pd_control` VALUES ('11', '7', '删除岗位', 'User', 'role_delete', '', '');
INSERT INTO `pd_control` VALUES ('12', '2', '添加商机', 'Business', 'add', '', '');
INSERT INTO `pd_control` VALUES ('34', '2', '完整商机信息', 'Business', 'view', '', '');
INSERT INTO `pd_control` VALUES ('13', '2', '修改商机', 'Business', 'edit', '', '');
INSERT INTO `pd_control` VALUES ('14', '2', '删除商机', 'Business', 'delete', '', '');
INSERT INTO `pd_control` VALUES ('15', '2', '添加商机日志', 'Business', 'addLogging', '', '');
INSERT INTO `pd_control` VALUES ('16', '2', '修改商机日志', 'Business', 'eidtLogging', '', '');
INSERT INTO `pd_control` VALUES ('17', '2', '删除商机日志', 'Business', 'deleteLogging', '', '');
INSERT INTO `pd_control` VALUES ('18', '1', '用户登录', 'User', 'login', '', '');
INSERT INTO `pd_control` VALUES ('19', '1', '用户注册', 'User', 'register', '', '');
INSERT INTO `pd_control` VALUES ('20', '1', '退出', 'User', 'logout', '', '');
INSERT INTO `pd_control` VALUES ('21', '7', '查看部门信息', 'User', 'department', '', '');
INSERT INTO `pd_control` VALUES ('22', '1', '找回密码', 'User', 'lostPW', '', '');
INSERT INTO `pd_control` VALUES ('23', '1', '重置密码', 'User', 'lostpw_reset', '', '');
INSERT INTO `pd_control` VALUES ('24', '7', '查看员工信息', 'User', 'index', '', '');
INSERT INTO `pd_control` VALUES ('25', '7', '查看岗位信息', 'User', 'role', '', '');
INSERT INTO `pd_control` VALUES ('26', '7', '岗位分配', 'User', 'user_role_relation', '', '');
INSERT INTO `pd_control` VALUES ('27', '7', '员工资料修改', 'User', 'editUsers', '', '');
INSERT INTO `pd_control` VALUES ('28', '1', '查看我的日志', 'User', 'mylog', '', '');
INSERT INTO `pd_control` VALUES ('60', '6', '岗位授权', 'Permission', 'authorize', '', '');
INSERT INTO `pd_control` VALUES ('30', '7', '个人日志详情', 'User', 'mylog_view', '', '');
INSERT INTO `pd_control` VALUES ('31', '7', '删除个人日志', 'User', 'mylog_delete', '', '');
INSERT INTO `pd_control` VALUES ('32', '2', '查看商机信息', 'Business', 'index', '', '');
INSERT INTO `pd_control` VALUES ('33', '2', '查看商机日志', 'Business', 'logging', '', '');
INSERT INTO `pd_control` VALUES ('35', '3', '产品列表', 'product', 'index', '', '');
INSERT INTO `pd_control` VALUES ('36', '3', '添加产品', 'Product', 'add', '', '');
INSERT INTO `pd_control` VALUES ('37', '3', '修改产品信息', 'product', 'edit', '', '');
INSERT INTO `pd_control` VALUES ('38', '3', '删除产品', 'Product', 'delete', '', '');
INSERT INTO `pd_control` VALUES ('39', '3', '查看产品分类信息', 'Product', 'category', '', '');
INSERT INTO `pd_control` VALUES ('40', '3', '添加产品分类', 'Product', 'category_add', '', '');
INSERT INTO `pd_control` VALUES ('41', '3', '删除产品分类', 'Product', 'deleteCategory', '', '');
INSERT INTO `pd_control` VALUES ('42', '3', '修改产品分类', 'Product', 'editcategory', '', '');
INSERT INTO `pd_control` VALUES ('43', '3', '产品销量统计', 'Product', 'count', '', '');
INSERT INTO `pd_control` VALUES ('44', '5', '查看客户信息', 'Customer', 'customerView', '', '');
INSERT INTO `pd_control` VALUES ('45', '5', '添加客户', 'Customer', 'add', '', '');
INSERT INTO `pd_control` VALUES ('46', '5', '修改客户信息', 'Customer', 'edit', '', '');
INSERT INTO `pd_control` VALUES ('47', '5', '删除客户', 'Customer', 'delete', '', '');
INSERT INTO `pd_control` VALUES ('48', '5', '添加客户联系人', 'Contacts', 'add', '', '');
INSERT INTO `pd_control` VALUES ('49', '5', '查看客户联系人', 'Contacts', 'view', '', '');
INSERT INTO `pd_control` VALUES ('50', '5', '删除客户联系人', 'Contacts', 'delete', '', '');
INSERT INTO `pd_control` VALUES ('51', '5', '修改客户联系人', 'Contacts', 'edit', '', '');
INSERT INTO `pd_control` VALUES ('52', '6', '查看操作模块', 'Permission', 'module', '', '');
INSERT INTO `pd_control` VALUES ('53', '6', '修改操作模块', 'Permission', 'module_edit', '', '');
INSERT INTO `pd_control` VALUES ('54', '6', '添加操作模块信息', 'Permission', 'module_add', '', '');
INSERT INTO `pd_control` VALUES ('55', '6', '删除操作模块', 'Permission', 'module_delete', '', '');
INSERT INTO `pd_control` VALUES ('56', '6', '查看操作信息', 'Permission', 'index', '', '');
INSERT INTO `pd_control` VALUES ('57', '6', '修改操作', 'Permission', 'control_edit', '', '');
INSERT INTO `pd_control` VALUES ('58', '6', '删除模块', 'Permission', 'control_delete', '', '');
INSERT INTO `pd_control` VALUES ('59', '6', '添加操作', 'Permission', 'control_add', '', '');
INSERT INTO `pd_control` VALUES ('61', '9', 'smtp设置', 'Config', 'smtpConfig', '', '');
INSERT INTO `pd_control` VALUES ('62', '9', '删除状态', 'Config', 'deleteStatus', '', '');
INSERT INTO `pd_control` VALUES ('63', '9', '修改状态', 'Config', 'editStatus', '', '');
INSERT INTO `pd_control` VALUES ('64', '9', '添加状态', 'Config', 'addStatus', '', '');
INSERT INTO `pd_control` VALUES ('65', '9', '查看状态', 'Config', 'statusList', '', '');
INSERT INTO `pd_control` VALUES ('66', '9', '查看状态流', 'Config', 'flowList', '', '');
INSERT INTO `pd_control` VALUES ('67', '9', '添加状态流', 'Config', 'addStatusflow', '', '');
INSERT INTO `pd_control` VALUES ('68', '9', '删除状态流', 'Config', 'deleteStatusFlow', '', '');
INSERT INTO `pd_control` VALUES ('69', '9', '修改状态流信息', 'Config', 'editStatusFlow', '', '');

-- ----------------------------
-- Table structure for `pd_customer`
-- ----------------------------
DROP TABLE IF EXISTS `pd_customer`;
CREATE TABLE `pd_customer` (
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
  `nextstep_time` int(10) NOT NULL COMMENT '下次联系时间',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `is_locked` int(1) NOT NULL COMMENT '是否锁定',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `grade` varchar(255) NOT NULL DEFAULT '1' COMMENT '客户等级',
  `customer_code` varchar(255) NOT NULL DEFAULT '' COMMENT '客户编号',
  `address` varchar(500) NOT NULL COMMENT '客户地址',
  `lng` double(14,11) NOT NULL COMMENT '客户地理位置经度',
  `lat` double(14,11) NOT NULL COMMENT '客户地理位置纬度',
  `customer_owner_id` varchar(50) NOT NULL,
  `customer_status` varchar(255) NOT NULL DEFAULT '意向客户' COMMENT '客户状态',
  `crm_vwlnfx` varchar(255) NOT NULL DEFAULT '' COMMENT '客户电话',
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='本表存放客户的相关信息';

-- ----------------------------
-- Records of pd_customer
-- ----------------------------
INSERT INTO `pd_customer` VALUES ('1', '1', '1', '0', '王二强', '电话营销', '', '1554812958', '1555564544', '1555564544', '0', '0', '1', '0', '0', '5', '20190409_1', '\n\n\n', '0.00000000000', '0.00000000000', '', '已成交客户', '');
INSERT INTO `pd_customer` VALUES ('2', '1', '1', '0', '金先生', '电话营销', '对外贸易', '1554870979', '1554870979', '1554870979', '1556166900', '0', '1', '0', '0', '5', '20190410_2', '河南省\n郑州市\n金水区\n', '0.00000000000', '0.00000000000', '', '已成交客户', '');
INSERT INTO `pd_customer` VALUES ('3', '1', '1', '1', '事实上', '', '', '1555248831', '1555288643', '1555288643', '0', '0', '1', '0', '0', '1', '', '/n/n/n', '0.00000000000', '0.00000000000', '', '已成交客户', '');
INSERT INTO `pd_customer` VALUES ('4', '1', '1', '0', 'Laurens', '上门推销', '对外贸易', '1555568939', '1555568939', '1555568939', '1555568880', '0', '0', '0', '0', '5', '20190418_4', '\n\n\n', '0.00000000000', '0.00000000000', '', '已成交客户', '');

-- ----------------------------
-- Table structure for `pd_customer_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_customer_data`;
CREATE TABLE `pd_customer_data` (
  `customer_id` int(10) unsigned NOT NULL COMMENT '客户id',
  `no_of_employees` varchar(150) NOT NULL DEFAULT '' COMMENT '员工数',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户附表信息';

-- ----------------------------
-- Records of pd_customer_data
-- ----------------------------
INSERT INTO `pd_customer_data` VALUES ('1', '', '');
INSERT INTO `pd_customer_data` VALUES ('2', '', '');
INSERT INTO `pd_customer_data` VALUES ('3', '', '');
INSERT INTO `pd_customer_data` VALUES ('4', '10人以下', '');

-- ----------------------------
-- Table structure for `pd_customer_record`
-- ----------------------------
DROP TABLE IF EXISTS `pd_customer_record`;
CREATE TABLE `pd_customer_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL COMMENT '客户',
  `user_id` int(10) NOT NULL COMMENT '用户',
  `start_time` int(10) NOT NULL COMMENT '时间',
  `type` int(10) NOT NULL COMMENT '1：领取 2：分配',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COMMENT='客户记录表';

-- ----------------------------
-- Records of pd_customer_record
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_customer_share`
-- ----------------------------
DROP TABLE IF EXISTS `pd_customer_share`;
CREATE TABLE `pd_customer_share` (
  `share_id` int(10) NOT NULL AUTO_INCREMENT,
  `share_role_id` int(10) NOT NULL COMMENT '分享人ID',
  `by_sharing_id` int(10) NOT NULL COMMENT '被分享人ID',
  `customer_id` int(10) NOT NULL COMMENT '客户ID',
  `share_time` int(10) NOT NULL COMMENT '分享时间',
  PRIMARY KEY (`share_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_customer_share
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_cycel`
-- ----------------------------
DROP TABLE IF EXISTS `pd_cycel`;
CREATE TABLE `pd_cycel` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='自定义循环周期表';

-- ----------------------------
-- Records of pd_cycel
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_email_template`
-- ----------------------------
DROP TABLE IF EXISTS `pd_email_template`;
CREATE TABLE `pd_email_template` (
  `template_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `subject` varchar(200) NOT NULL COMMENT '主题',
  `title` varchar(100) NOT NULL,
  `content` varchar(500) NOT NULL COMMENT '内容',
  `order_id` int(4) NOT NULL COMMENT '顺序id',
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短信模板';

-- ----------------------------
-- Records of pd_email_template
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_event`
-- ----------------------------
DROP TABLE IF EXISTS `pd_event`;
CREATE TABLE `pd_event` (
  `event_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '活动id',
  `owner_role_id` int(10) NOT NULL COMMENT '所有人岗位',
  `subject` varchar(50) NOT NULL COMMENT '主题',
  `start_date` int(10) NOT NULL COMMENT '开始时间',
  `end_date` int(10) NOT NULL COMMENT '结束时间',
  `venue` varchar(100) NOT NULL COMMENT '活动地点',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者id',
  `create_date` int(10) NOT NULL COMMENT '创建时间',
  `update_date` int(10) NOT NULL COMMENT '修改时间',
  `send_email` int(1) NOT NULL COMMENT '发送通知邮件1不发送0',
  `recurring` int(1) NOT NULL COMMENT '重复1 不重复0',
  `description` text NOT NULL COMMENT '描述',
  `isclose` int(1) NOT NULL COMMENT '是否关闭0开启1关闭',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `color` varchar(50) NOT NULL COMMENT '颜色',
  `module` varchar(50) NOT NULL COMMENT '相关模块',
  `module_id` int(10) NOT NULL COMMENT '相关模块ID',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='活动信息表';

-- ----------------------------
-- Records of pd_event
-- ----------------------------
INSERT INTO `pd_event` VALUES ('1', '1', '合同到期', '1588348800', '1588435199', '', '1', '1554813003', '1554813003', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '1');
INSERT INTO `pd_event` VALUES ('2', '1', '合同到期', '1556553600', '1556639999', '', '1', '1554869149', '1554869149', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '2');
INSERT INTO `pd_event` VALUES ('3', '1', '合同到期', '1556553600', '1556639999', '', '1', '1554872557', '1554872557', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '3');
INSERT INTO `pd_event` VALUES ('4', '1', '合同到期', '1556553600', '1556639999', '', '1', '1554944787', '1554944787', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '4');
INSERT INTO `pd_event` VALUES ('5', '1', '444', '1554945625', '1554998399', '', '1', '1554945634', '1554945634', '0', '0', '发个', '0', '0', '0', '0', '#526069', '', '0');
INSERT INTO `pd_event` VALUES ('6', '1', '花港饭店', '1556640000', '1556726399', '', '1', '1554945646', '1554945646', '0', '0', '花港饭店', '0', '0', '0', '0', '#f2a654', '', '0');
INSERT INTO `pd_event` VALUES ('7', '1', '合同到期', '1555430400', '1555516799', '', '1', '1555469849', '1555469849', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '5');
INSERT INTO `pd_event` VALUES ('8', '1', '合同到期', '1555516800', '1555603199', '', '1', '1555552941', '1555552941', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '6');
INSERT INTO `pd_event` VALUES ('9', '1', '合同到期', '1555516800', '1555603199', '', '1', '1555553074', '1555553074', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '7');
INSERT INTO `pd_event` VALUES ('10', '1', '合同到期', '1555516800', '1555603199', '', '1', '1555553519', '1555553519', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '8');
INSERT INTO `pd_event` VALUES ('11', '1', '合同到期', '1555603200', '1555689599', '', '1', '1555567119', '1555567119', '0', '0', '', '0', '0', '0', '0', '#f96868', 'contract', '9');

-- ----------------------------
-- Table structure for `pd_examine`
-- ----------------------------
DROP TABLE IF EXISTS `pd_examine`;
CREATE TABLE `pd_examine` (
  `examine_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `creator_role_id` int(10) NOT NULL COMMENT '创建人',
  `owner_role_id` varchar(200) NOT NULL COMMENT '出差人',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `type` int(2) NOT NULL COMMENT '审批类型',
  `content` text NOT NULL COMMENT '审批内容',
  `examine_role_id` varchar(255) NOT NULL COMMENT '审批人',
  `cate` varchar(200) DEFAULT NULL COMMENT '审批事项',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `duration` float(10,1) NOT NULL COMMENT '时长：天',
  `money` decimal(18,2) NOT NULL COMMENT '报销金额/借款金额',
  `budget` decimal(18,2) NOT NULL COMMENT '普通报销、差旅、出差、借款（金额）',
  `advance` decimal(18,2) NOT NULL COMMENT '预支金额',
  `start_address` varchar(200) NOT NULL COMMENT '出发地',
  `vehicle` varchar(30) NOT NULL COMMENT '交通工具',
  `end_address` varchar(200) NOT NULL COMMENT '目的地 出差地',
  `examine_status` int(1) NOT NULL COMMENT '状态（0待审、1审批中、2通过、3失败）',
  `order_id` int(2) NOT NULL COMMENT '审核排序ID',
  `is_deleted` int(1) NOT NULL COMMENT '是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`examine_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='审批表';

-- ----------------------------
-- Records of pd_examine
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_examine_check`
-- ----------------------------
DROP TABLE IF EXISTS `pd_examine_check`;
CREATE TABLE `pd_examine_check` (
  `check_id` int(11) NOT NULL AUTO_INCREMENT,
  `examine_id` int(11) NOT NULL COMMENT '审批ID',
  `role_id` int(11) NOT NULL COMMENT '负责人ID',
  `is_checked` int(11) NOT NULL COMMENT '审核状态',
  `content` varchar(200) DEFAULT NULL COMMENT '审核内容',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  `order_id` int(10) NOT NULL COMMENT '审批流程步骤ID',
  `is_end` tinyint(1) NOT NULL COMMENT '1无效审批2为了区别新老逻辑',
  PRIMARY KEY (`check_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_examine_check
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_examine_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_examine_data`;
CREATE TABLE `pd_examine_data` (
  `examine_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='审批附表';

-- ----------------------------
-- Records of pd_examine_data
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_examine_file`
-- ----------------------------
DROP TABLE IF EXISTS `pd_examine_file`;
CREATE TABLE `pd_examine_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `examine_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件和审批对应关系表';

-- ----------------------------
-- Records of pd_examine_file
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_examine_status`
-- ----------------------------
DROP TABLE IF EXISTS `pd_examine_status`;
CREATE TABLE `pd_examine_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL COMMENT '审批类型',
  `name` varchar(30) NOT NULL COMMENT '审批名',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `type` int(11) NOT NULL COMMENT '0启用1停用',
  `option` int(11) NOT NULL COMMENT '0自选1设置',
  `description` varchar(500) NOT NULL COMMENT '审批类型描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_examine_status
-- ----------------------------
INSERT INTO `pd_examine_status` VALUES ('1', '1', '普通审批', '1486694160', '0', '0', '');
INSERT INTO `pd_examine_status` VALUES ('2', '2', '请假审批', '1486694160', '0', '0', '');
INSERT INTO `pd_examine_status` VALUES ('3', '3', '普通报销', '1486694160', '0', '0', '');
INSERT INTO `pd_examine_status` VALUES ('4', '4', '差旅报销', '1486694160', '0', '0', '');
INSERT INTO `pd_examine_status` VALUES ('5', '5', '出差申请', '1486694160', '0', '0', '');
INSERT INTO `pd_examine_status` VALUES ('6', '6', '借款申请', '1486694160', '0', '0', '');

-- ----------------------------
-- Table structure for `pd_examine_step`
-- ----------------------------
DROP TABLE IF EXISTS `pd_examine_step`;
CREATE TABLE `pd_examine_step` (
  `step_id` int(10) NOT NULL AUTO_INCREMENT,
  `department_id` int(10) NOT NULL COMMENT '部门ID',
  `process_id` int(10) NOT NULL COMMENT '所属流程',
  `name` varchar(50) NOT NULL COMMENT '步骤名称',
  `position_id` int(10) NOT NULL COMMENT '岗位',
  `role_id` varchar(200) NOT NULL COMMENT '审批人',
  `order_id` int(10) NOT NULL COMMENT '排序',
  `relation` tinyint(1) NOT NULL COMMENT '审批流程关系（1并2或）',
  PRIMARY KEY (`step_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='审批流程 - 步骤';

-- ----------------------------
-- Records of pd_examine_step
-- ----------------------------
INSERT INTO `pd_examine_step` VALUES ('1', '0', '2', '', '0', ',1,', '0', '1');

-- ----------------------------
-- Table structure for `pd_examine_travel`
-- ----------------------------
DROP TABLE IF EXISTS `pd_examine_travel`;
CREATE TABLE `pd_examine_travel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `examine_id` int(10) NOT NULL COMMENT '审批ID',
  `start_address` varchar(150) NOT NULL COMMENT '出发地',
  `start_time` int(10) NOT NULL COMMENT '出发时间',
  `end_address` varchar(150) NOT NULL COMMENT '目的地',
  `end_time` int(10) NOT NULL COMMENT '到达时间',
  `vehicle` varchar(40) NOT NULL COMMENT '交通工具',
  `duration` varchar(10) NOT NULL COMMENT '住宿(天)',
  `money` decimal(18,2) NOT NULL COMMENT '金额',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_examine_travel
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_exam_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_exam_log`;
CREATE TABLE `pd_exam_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) NOT NULL COMMENT '模块ID: pd_exam_type',
  `order_id` int(10) NOT NULL COMMENT '对应订单id',
  `step_id` int(10) NOT NULL COMMENT '审批步骤id',
  `role_id` int(10) NOT NULL COMMENT '审批人',
  `result` tinyint(1) NOT NULL COMMENT '结果。0：驳回，1：通过，2结束 3撤销',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_exam_log
-- ----------------------------
INSERT INTO `pd_exam_log` VALUES ('1', '1', '1', '0', '1', '2', '', '1554813230');
INSERT INTO `pd_exam_log` VALUES ('2', '1', '2', '0', '1', '2', '', '1554813704');
INSERT INTO `pd_exam_log` VALUES ('3', '1', '3', '0', '1', '2', '', '1554871294');
INSERT INTO `pd_exam_log` VALUES ('4', '4', '1', '0', '1', '2', '', '1554872956');
INSERT INTO `pd_exam_log` VALUES ('5', '4', '2', '0', '1', '2', '', '1554873730');
INSERT INTO `pd_exam_log` VALUES ('6', '4', '2', '0', '1', '3', '撤销审批', '1554873828');
INSERT INTO `pd_exam_log` VALUES ('7', '4', '2', '0', '1', '2', '', '1554873845');
INSERT INTO `pd_exam_log` VALUES ('8', '4', '3', '0', '1', '2', '', '1554873926');

-- ----------------------------
-- Table structure for `pd_exam_process`
-- ----------------------------
DROP TABLE IF EXISTS `pd_exam_process`;
CREATE TABLE `pd_exam_process` (
  `process_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) NOT NULL COMMENT '模块ID: pd_exam_type',
  `name` varchar(64) NOT NULL COMMENT '模块名称',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '启用与否：1是，0否',
  PRIMARY KEY (`process_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_exam_process
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_exam_step`
-- ----------------------------
DROP TABLE IF EXISTS `pd_exam_step`;
CREATE TABLE `pd_exam_step` (
  `step_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `process_id` int(10) NOT NULL COMMENT '审批流ID: pd_exam_process',
  `role_ids` varchar(128) NOT NULL COMMENT '审批人，逗号分隔两端加逗号',
  `relation` varchar(255) NOT NULL COMMENT '步骤逻辑，1：并且；2或者',
  `step` int(10) NOT NULL COMMENT '步骤',
  PRIMARY KEY (`step_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_exam_step
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_exam_type`
-- ----------------------------
DROP TABLE IF EXISTS `pd_exam_type`;
CREATE TABLE `pd_exam_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '模块名称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态默认0，0：不启用（自定义审批），1启用：审批流；',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`type_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_exam_type
-- ----------------------------
INSERT INTO `pd_exam_type` VALUES ('1', '采购', '0', '采购模块的审批', '0');
INSERT INTO `pd_exam_type` VALUES ('2', '采购退货', '0', '采购退货模块的审批', '0');
INSERT INTO `pd_exam_type` VALUES ('3', '销售退货', '0', '销售退货模块的审批', '0');
INSERT INTO `pd_exam_type` VALUES ('4', '库存调拨', '0', '库存调拨模块的审批', '0');

-- ----------------------------
-- Table structure for `pd_fields`
-- ----------------------------
DROP TABLE IF EXISTS `pd_fields`;
CREATE TABLE `pd_fields` (
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
  `is_recheck` int(1) NOT NULL COMMENT '是否查重1是0否',
  `is_null` int(1) NOT NULL COMMENT '是否允许为空',
  `is_validate` int(1) NOT NULL COMMENT '是否验证',
  `in_index` int(1) NOT NULL COMMENT '是否列表页显示1是0否',
  `in_add` int(1) NOT NULL DEFAULT '1' COMMENT '是否添加时显示1是0否',
  `input_tips` varchar(500) NOT NULL COMMENT '输入提示',
  `setting` text NOT NULL COMMENT '设置',
  `order_id` int(5) NOT NULL COMMENT '同一模块内的顺序id',
  `operating` int(1) NOT NULL COMMENT '0改删、1改、2无、3删',
  `is_show` int(1) NOT NULL COMMENT '是否在客户页显示',
  `status` int(10) NOT NULL COMMENT '模块类型（审批）',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 COMMENT='字段表';

-- ----------------------------
-- Records of pd_fields
-- ----------------------------
INSERT INTO `pd_fields` VALUES ('1', '', '1', 'owner_role_id', '负责人', 'user', '', '', '10', '0', '0', '0', '0', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('2', '', '1', 'creator_role_id', '创建人', 'user', '', '', '10', '0', '0', '0', '0', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('3', '', '1', 'delete_role_id', '删除人', 'user', '', '', '10', '0', '0', '0', '0', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('4', '', '1', 'is_deleted', '是否删除', 'deleted', '', '', '1', '0', '0', '0', '0', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('5', '', '1', 'create_time', '创建时间', 'datetime', '', '', '10', '0', '0', '0', '0', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('6', '', '1', 'update_time', '更新时间', 'datetime', '', '', '10', '0', '0', '0', '0', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('7', '', '1', 'delete_time', '删除时间', 'datetime', '', '', '10', '0', '0', '0', '0', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('8', 'customer', '1', 'name', '客户名称', 'text', '', '5521FF', '333', '1', '0', '1', '1', '1', '1', '测试客户', '', '0', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('9', 'customer', '1', 'origin', '客户信息来源', 'box', '', '333333', '150', '0', '0', '1', '1', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'电话营销\',2=>\'网络营销\',3=>\'上门推销\'))', '4', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('10', 'customer', '1', 'industry', '客户行业', 'box', '', '050505', '150', '0', '0', '0', '0', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'IT/教育\',2=>\'电子/商务\',3=>\'对外贸易\',4=>\'酒店、旅游\',5=>\'金融、保险\',6=>\'房产行业\',7=>\'医疗/保健\',8=>\'政府、机关\'))', '2', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('11', 'customer', '0', 'no_of_employees', '员工数', 'box', '', '0A0A0A', '150', '0', '0', '0', '0', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'10人以下\',2=>\'10--20人\',3=>\'20-50人\',4=>\'50人以上\'))', '5', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('12', 'customer', '0', 'description', '备注', 'textarea', '', '333333', '0', '0', '0', '0', '1', '0', '1', '', '', '7', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('13', 'customer', '1', 'customer_code', '客户编号', 'text', '', '333333', '0', '1', '0', '1', '1', '0', '1', '', '', '1', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('14', 'customer', '1', 'grade', '客户等级', 'box', '1', '333333', '0', '0', '0', '1', '0', '1', '1', '', 'array(\'type\'=>\'radio\',\'data\'=>array(1=>\'1\',2=>\'2\',3=>\'3\',4=>\'4\',5=>\'5\'))', '6', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('15', 'customer', '1', 'address', '客户地址', 'address', '', '333333', '0', '0', '0', '1', '0', '1', '1', '', '', '3', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('16', 'customer', '1', 'customer_owner_id', '客户负责人', 'text', '', '333333', '0', '0', '0', '0', '0', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('17', 'product', '1', 'sketch', '商品描述', 'text', '', '333333', '0', '0', '0', '1', '0', '0', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('18', 'product', '1', 'product_num', '产品编号', 'text', '', '333333', '0', '0', '0', '0', '0', '1', '1', '', '', '3', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('19', 'product', '1', 'standard', '单位', 'box', '', '333333', '200', '0', '0', '1', '1', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'个\',2=>\'箱\',3=>\'套\',4=>\'盒\',5=>\'瓶\',6=>\'块\',7=>\'只\',8=>\'把\',9=>\'枚\',10=>\'条\'))', '2', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('20', 'product', '0', 'description', '备注', 'textarea', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '6', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('21', 'product', '1', 'name', '产品名称', 'text', '', '021012', '200', '0', '0', '1', '1', '1', '1', '', '', '0', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('22', 'product', '1', 'cost_price', '成本价', 'floatnumber', '', '1F1F1F', '10', '0', '0', '0', '0', '1', '1', '', '', '4', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('23', 'product', '1', 'suggested_price', '建议售价', 'floatnumber', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '5', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('24', 'product', '1', 'category_id', '产品类别', 'p_box', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '1', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('25', 'contacts', '1', 'role', '角色', 'box', '', '333333', '0', '0', '0', '1', '1', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'普通人\',2=>\'决策人\',3=>\'分项决策人\',4=>\'商务决策\',5=>\'技术决策\',6=>\'财务决策\',7=>\'使用人\',8=>\'意见影响人\'))', '2', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('26', 'contacts', '1', 'saltname', '尊称', 'box', '', '333333', '50', '0', '0', '1', '0', '1', '1', '', 'array(\'type\'=>\'radio\',\'data\'=>array(1=>\'先生\',2=>\'女士\'))', '3', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('27', 'contacts', '1', 'customer_id', '所属客户', 'customer', '', '333333', '50', '0', '0', '0', '1', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('28', 'contacts', '1', 'post', '职位', 'text', '', '333333', '20', '0', '0', '1', '0', '1', '1', '', '', '4', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('29', 'contacts', '1', 'telephone', '手机', 'mobile', '', '333333', '50', '0', '0', '1', '0', '1', '1', '', '', '5', '1', '1', '0');
INSERT INTO `pd_fields` VALUES ('30', 'contacts', '1', 'email', '邮箱', 'email', '', '333333', '50', '0', '0', '1', '0', '1', '1', '', '', '7', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('31', 'contacts', '1', 'qq_no', 'QQ', 'text', '', '333333', '50', '0', '0', '1', '0', '1', '1', '', '', '6', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('32', 'contacts', '1', 'contacts_address', '地址', 'address', '', '333333', '100', '0', '0', '1', '1', '0', '1', '', '', '8', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('33', 'contacts', '1', 'name', '联系人姓名', 'text', '', '333333', '20', '0', '0', '1', '1', '1', '1', '', '', '1', '1', '1', '0');
INSERT INTO `pd_fields` VALUES ('34', 'contacts', '1', 'zip_code', '邮编', 'text', '', '333333', '20', '0', '0', '1', '0', '0', '1', '', '', '9', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('35', 'contacts', '1', 'description', '备注', 'textarea', '', '333333', '500', '0', '0', '1', '0', '0', '1', '', '', '10', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('36', 'leads', '1', 'nextstep_time', '下次联系时间', 'datetime', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '9', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('37', 'leads', '1', 'nextstep', '下次联系内容', 'text', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '10', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('38', 'leads', '1', 'contacts_name', '联系人姓名', 'text', '', '333333', '0', '0', '0', '1', '1', '1', '1', '', '', '2', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('39', 'leads', '1', 'saltname', '尊称', 'box', '', '333333', '0', '0', '0', '0', '0', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'女士\',2=>\'先生\'))', '5', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('40', 'leads', '1', 'mobile', '手机', 'mobile', '', '333333', '0', '0', '0', '0', '1', '1', '1', '', '', '6', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('41', 'leads', '1', 'email', '邮箱', 'email', '', '', '0', '0', '0', '0', '1', '0', '1', '', '', '7', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('42', 'leads', '1', 'position', '职位', 'text', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '4', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('43', 'leads', '1', 'address', '地址', 'address', '', '333333', '0', '0', '0', '0', '0', '0', '1', '', '', '8', '0', '0', '0');
INSERT INTO `pd_fields` VALUES ('44', 'leads', '0', 'description', '备注', 'textarea', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '11', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('45', 'leads', '1', 'name', '公司名', 'text', '', '05330E', '0', '0', '0', '1', '0', '1', '1', '', '', '3', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('46', 'leads', '1', 'source', '来源', 'box', '', '333333', '0', '0', '0', '1', '0', '0', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'网络营销\',2=>\'公开媒体\',3=>\'合作伙伴\',4=>\'员工介绍\',5=>\'广告\',6=>\'推销电话\',7=>\'其他\'))', '1', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('47', 'customer', '1', 'nextstep_time', '下次联系时间', 'datetime', '', '333333', '200', '0', '0', '1', '0', '0', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('48', 'customer', '1', 'customer_status', '客户状态', 'box', '意向客户', '333333', '0', '0', '0', '1', '0', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'意向客户\',2=>\'已成交客户\',3=>\'失败客户\'))', '1', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('49', 'business', '1', 'name', '商机名', 'text', '', '090D08', '0', '1', '0', '1', '1', '1', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('50', 'business', '1', 'customer_id', '客户', 'customer', '', '', '0', '0', '0', '0', '1', '1', '1', '', '', '1', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('51', 'business', '1', 'contacts_id', '联系人', 'contacts', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '2', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('52', 'business', '1', 'status_id', '商机进度', 'b_box', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '3', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('53', 'business', '1', 'possibility', '成交几率', 'p_box', '', '', '0', '0', '0', '0', '1', '1', '1', '', '', '4', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('54', 'business', '1', 'nextstep_time', '下次联系时间', 'datetime', '', '', '0', '0', '0', '0', '1', '1', '1', '', '', '5', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('55', 'business', '0', 'description', '备注', 'textarea', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '6', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('56', 'contract', '1', 'contract_name', '合同名称', 'text', '', '090D08', '0', '1', '0', '1', '1', '0', '1', '', '', '0', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('57', 'contract', '1', 'customer_id', '客户', 'customer', '', '', '0', '0', '0', '0', '1', '1', '1', '', '', '1', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('58', 'contract', '1', 'business_id', '商机', 'business', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '2', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('59', 'contract', '1', 'price', '合同金额(元)', 'text', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '3', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('60', 'contract', '1', 'due_time', '签约时间', 'datetime', '', '', '0', '0', '0', '1', '1', '1', '1', '', '', '4', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('61', 'contract', '1', 'start_date', '合同生效时间', 'datetime', '', '', '0', '0', '0', '0', '1', '0', '1', '', '', '5', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('62', 'contract', '1', 'end_date', '合同到期时间', 'datetime', '', '', '0', '0', '0', '0', '1', '0', '1', '', '', '6', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('63', 'contract', '1', 'description', '合同描述', 'textarea', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '7', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('64', 'business', '1', 'final_price', '商机金额', 'text', '', '090D08', '0', '0', '0', '1', '0', '0', '1', '', '', '7', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('65', 'market', '1', 'name', '活动名称', 'text', '', '333333', '255', '0', '0', '1', '1', '1', '1', '', '', '0', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('66', 'market', '1', 'start_time', '活动开始时间', 'datetime', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '2', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('67', 'market', '1', 'end_time', '活动结束时间', 'datetime', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '3', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('68', 'market', '1', 'type', '活动类型', 'box', '', '333333', '64', '0', '0', '1', '1', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'促销活动\',2=>\'品牌活动\',3=>\'会议销售\',4=>\'搜索引擎\',5=>\'互联网广告\',6=>\'平面媒体广告\',7=>\'电视媒体广告\',8=>\'关系公关\',9=>\'电话营销\',10=>\'短信营销\',11=>\'邮件营销\'))', '1', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('69', 'market', '1', 'address', '活动地址', 'text', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '4', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('70', 'market', '1', 'expected_cost', '预计成本', 'floatnumber', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '6', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('71', 'market', '1', 'expected_income', '预计收入', 'floatnumber', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '7', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('72', 'market', '1', 'real_cost', '实际成本', 'floatnumber', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '8', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('73', 'market', '1', 'real_income', '实际收入', 'floatnumber', '', '', '0', '0', '0', '0', '0', '1', '1', '', '', '9', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('74', 'market', '0', 'plan', '活动计划', 'textarea', '', '333333', '0', '0', '0', '0', '0', '0', '1', '', '', '10', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('75', 'market', '0', 'execution_description', '执行描述', 'textarea', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '11', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('76', 'market', '0', 'summary', '总结', 'textarea', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '13', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('77', 'market', '0', 'effect', '效果', 'textarea', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '12', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('78', 'market', '0', 'description', '描述', 'textarea', '', '', '0', '0', '0', '0', '0', '0', '1', '', '', '14', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('79', 'market', '1', 'status', '活动状态', 'box', '已计划', '333333', '0', '0', '0', '1', '1', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'已计划\',2=>\'进行中\',3=>\'已结束\',4=>\'终止\'))', '5', '2', '0', '0');
INSERT INTO `pd_fields` VALUES ('80', 'supplier', '1', 'crm_rkxjgu', '服务类型', 'text', '', '333333', '40', '0', '0', '0', '0', '1', '1', '', '', '3', '0', '0', '0');
INSERT INTO `pd_fields` VALUES ('81', 'supplier', '1', 'category', '供应商类别', 'box', '', '333333', '0', '0', '0', '0', '0', '1', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'默认类别\'))', '0', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('82', 'supplier', '1', 'name', '供应商名称', 'text', '', '333333', '50', '0', '0', '0', '0', '1', '1', '', '', '1', '1', '0', '0');
INSERT INTO `pd_fields` VALUES ('83', 'supplier', '0', 'crm_pexwas', '备注', 'text', '', '333333', '100', '0', '0', '0', '0', '0', '1', '', '', '4', '0', '0', '0');
INSERT INTO `pd_fields` VALUES ('84', 'supplier', '1', 'crm_gbzegv', '供应商等级', 'box', '', '333333', '0', '0', '0', '0', '0', '0', '1', '', 'array(\'type\'=>\'select\',\'data\'=>array(1=>\'A级\',2=>\'B级\',3=>\'C级\'))', '2', '0', '0', '0');
INSERT INTO `pd_fields` VALUES ('85', 'supplier', '1', 'crm_fekgue', '供应商性质', 'text', '', '333333', '0', '0', '0', '0', '0', '0', '1', '', '', '0', '0', '0', '0');
INSERT INTO `pd_fields` VALUES ('86', 'customer', '1', 'crm_vwlnfx', '客户电话', 'phone', '', '333333', '0', '0', '0', '0', '0', '0', '1', '', '', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `pd_file`
-- ----------------------------
DROP TABLE IF EXISTS `pd_file`;
CREATE TABLE `pd_file` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附件主键',
  `name` varchar(50) NOT NULL COMMENT '附件名',
  `role_id` int(10) NOT NULL COMMENT '创建者岗位',
  `size` int(10) NOT NULL COMMENT '文件大小字节',
  `create_date` int(10) NOT NULL COMMENT '创建时间',
  `file_path` varchar(200) NOT NULL COMMENT '文件路径',
  `file_path_thumb` varchar(200) NOT NULL COMMENT '图片缩略图',
  `oss` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否上传至OSS服务器 默认0否 1是',
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';

-- ----------------------------
-- Records of pd_file
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_finance_category`
-- ----------------------------
DROP TABLE IF EXISTS `pd_finance_category`;
CREATE TABLE `pd_finance_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL COMMENT '活动编号',
  `name` varchar(255) NOT NULL COMMENT '活动名称',
  `type` tinyint(1) NOT NULL,
  `account_ids` varchar(500) NOT NULL COMMENT '科目ID',
  `remark` varchar(1000) NOT NULL COMMENT '备注',
  `is_pause` tinyint(1) NOT NULL COMMENT '1停用',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='财务活动表';

-- ----------------------------
-- Records of pd_finance_category
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_finance_type`
-- ----------------------------
DROP TABLE IF EXISTS `pd_finance_type`;
CREATE TABLE `pd_finance_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `field` varchar(50) NOT NULL COMMENT '字段',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='财务相关类型';

-- ----------------------------
-- Records of pd_finance_type
-- ----------------------------
INSERT INTO `pd_finance_type` VALUES ('1', 'payables', '客户支出', '1', '1512113079', '1512113079');
INSERT INTO `pd_finance_type` VALUES ('2', 'payables', '报销', '1', '1512113270', '1512113270');
INSERT INTO `pd_finance_type` VALUES ('3', 'payables', '工资', '1', '1512113279', '1512113279');

-- ----------------------------
-- Table structure for `pd_import_error_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_import_error_data`;
CREATE TABLE `pd_import_error_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `error_data` longtext NOT NULL COMMENT '错误数据 json  行数：原因',
  `excel` varchar(64) NOT NULL COMMENT '导入文件名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_import_error_data
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_invoice`
-- ----------------------------
DROP TABLE IF EXISTS `pd_invoice`;
CREATE TABLE `pd_invoice` (
  `invoice_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '发票编号',
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
  `is_checked` tinyint(1) NOT NULL COMMENT '0待审1通过2失败',
  `check_role_id` int(10) NOT NULL COMMENT '审核人ID',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  `invoice_time` int(10) NOT NULL COMMENT '开票时间',
  `express` varchar(255) NOT NULL COMMENT '快递单号',
  PRIMARY KEY (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同发票表';

-- ----------------------------
-- Records of pd_invoice
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_invoice_check`
-- ----------------------------
DROP TABLE IF EXISTS `pd_invoice_check`;
CREATE TABLE `pd_invoice_check` (
  `check_id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) NOT NULL COMMENT '发票ID',
  `role_id` int(10) NOT NULL COMMENT '负责人ID',
  `is_checked` tinyint(1) NOT NULL COMMENT '1通过2驳回',
  `content` varchar(500) NOT NULL COMMENT '审核内容',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`check_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='发票审核记录表';

-- ----------------------------
-- Records of pd_invoice_check
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_kaoqin`
-- ----------------------------
DROP TABLE IF EXISTS `pd_kaoqin`;
CREATE TABLE `pd_kaoqin` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_kaoqin
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_kaoqin_config`
-- ----------------------------
DROP TABLE IF EXISTS `pd_kaoqin_config`;
CREATE TABLE `pd_kaoqin_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `shangban_time` time NOT NULL COMMENT '上班时间',
  `xiaban_time` time NOT NULL COMMENT '下班时间',
  `x` varchar(30) NOT NULL COMMENT 'x坐标',
  `y` varchar(30) NOT NULL COMMENT 'y坐标',
  `radius` int(10) NOT NULL COMMENT '半径（米）',
  `create_role_id` int(10) NOT NULL COMMENT '创建人',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `reg_address` varchar(1000) NOT NULL COMMENT '地理位置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='考勤规则表';

-- ----------------------------
-- Records of pd_kaoqin_config
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_knowledge`
-- ----------------------------
DROP TABLE IF EXISTS `pd_knowledge`;
CREATE TABLE `pd_knowledge` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='存放知识文章信息';

-- ----------------------------
-- Records of pd_knowledge
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_knowledge_category`
-- ----------------------------
DROP TABLE IF EXISTS `pd_knowledge_category`;
CREATE TABLE `pd_knowledge_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章类别id',
  `parent_id` int(11) NOT NULL COMMENT '父类别id',
  `name` varchar(30) NOT NULL COMMENT '类别名称',
  `description` varchar(100) NOT NULL COMMENT '备注',
  `to_department` varchar(200) NOT NULL COMMENT '权限部门id',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='知识文章分类信息表';

-- ----------------------------
-- Records of pd_knowledge_category
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_leads`
-- ----------------------------
DROP TABLE IF EXISTS `pd_leads`;
CREATE TABLE `pd_leads` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='线索表';

-- ----------------------------
-- Records of pd_leads
-- ----------------------------
INSERT INTO `pd_leads` VALUES ('1', '1', '1', 'dsf', '', 'sdf', '', '18110940587', '', '1553651660', '1553651679', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '1553651660', '0', '\n\n\n', '公开媒体');
INSERT INTO `pd_leads` VALUES ('2', '1', '1', '事实上', '事实上', '事实上', '女士', '18888888888', '', '1554944539', '1554944539', '0', '0', '0', '1', '1', '1', '3', '0', '', '0', '1554944539', '0', '天津市\n市辖县\n宁河县\n', '公开媒体');
INSERT INTO `pd_leads` VALUES ('3', '1', '1', 'admins', '计划', '89798', '女士', '', '', '1555380257', '1555380257', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '1555380257', '0', '\n\n\n', '公开媒体');
INSERT INTO `pd_leads` VALUES ('4', '1', '1', '联合医生', 'CEO', 'laurens', '先生', '18777777777', '52992899@qq.com', '1555565349', '1555568791', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '1555565349', '0', '\n\n\n', '');

-- ----------------------------
-- Table structure for `pd_leads_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_leads_data`;
CREATE TABLE `pd_leads_data` (
  `leads_id` int(10) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`leads_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_leads_data
-- ----------------------------
INSERT INTO `pd_leads_data` VALUES ('1', '');
INSERT INTO `pd_leads_data` VALUES ('2', '');
INSERT INTO `pd_leads_data` VALUES ('3', '');
INSERT INTO `pd_leads_data` VALUES ('4', '要联系');

-- ----------------------------
-- Table structure for `pd_leads_record`
-- ----------------------------
DROP TABLE IF EXISTS `pd_leads_record`;
CREATE TABLE `pd_leads_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `leads_id` int(10) NOT NULL,
  `owner_role_id` int(10) NOT NULL,
  `start_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_leads_record
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_log`;
CREATE TABLE `pd_log` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `role_id` int(11) NOT NULL COMMENT '创建者岗位',
  `category_id` int(10) NOT NULL,
  `status_id` int(10) NOT NULL COMMENT '跟进类型',
  `sign` int(1) NOT NULL DEFAULT '0' COMMENT '1关联签到',
  `create_date` int(10) NOT NULL COMMENT '创建时间',
  `update_date` int(10) NOT NULL COMMENT '更新时间',
  `nextstep_time` int(10) NOT NULL COMMENT '下次联系时间',
  `subject` varchar(200) NOT NULL COMMENT '主题',
  `content` text NOT NULL COMMENT '内容',
  `comment_id` int(10) NOT NULL COMMENT '评论id',
  `about_roles` varchar(200) NOT NULL COMMENT '新增相关人',
  `about_roles_name` varchar(500) NOT NULL COMMENT '新增相关人姓名',
  `status` tinyint(1) NOT NULL COMMENT '0未阅1已阅2已点评',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='日志表';

-- ----------------------------
-- Records of pd_log
-- ----------------------------
INSERT INTO `pd_log` VALUES ('1', '1', '1', '1', '0', '1555469487', '1555469487', '0', '', '坎坎坷坷', '0', '', '', '0');

-- ----------------------------
-- Table structure for `pd_log_category`
-- ----------------------------
DROP TABLE IF EXISTS `pd_log_category`;
CREATE TABLE `pd_log_category` (
  `category_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(200) NOT NULL COMMENT '分类名',
  `order_id` int(10) NOT NULL COMMENT '顺序id',
  `description` varchar(500) NOT NULL COMMENT '描述',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='日志类型表';

-- ----------------------------
-- Records of pd_log_category
-- ----------------------------
INSERT INTO `pd_log_category` VALUES ('1', '模块日志', '4', '其他模块的相关日志');
INSERT INTO `pd_log_category` VALUES ('2', '月报', '3', '每月工作总结');
INSERT INTO `pd_log_category` VALUES ('3', '周报', '2', '每周工作总结');
INSERT INTO `pd_log_category` VALUES ('4', '日报', '1', '每日工作总结');

-- ----------------------------
-- Table structure for `pd_log_reply`
-- ----------------------------
DROP TABLE IF EXISTS `pd_log_reply`;
CREATE TABLE `pd_log_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `content` varchar(1000) NOT NULL COMMENT '内容',
  `status_id` int(10) NOT NULL COMMENT '类型ID',
  `type` tinyint(1) NOT NULL COMMENT '1系统2个人',
  `role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='沟通日志自定义回复表';

-- ----------------------------
-- Records of pd_log_reply
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_log_status`
-- ----------------------------
DROP TABLE IF EXISTS `pd_log_status`;
CREATE TABLE `pd_log_status` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='沟通日志类型';

-- ----------------------------
-- Records of pd_log_status
-- ----------------------------
INSERT INTO `pd_log_status` VALUES ('1', '电话', '1', '1502792328', '1502792328');
INSERT INTO `pd_log_status` VALUES ('2', '发邮件', '1', '1502852220', '1502852220');
INSERT INTO `pd_log_status` VALUES ('3', '发短信', '1', '1502852228', '1502852228');
INSERT INTO `pd_log_status` VALUES ('4', '见面拜访', '1', '1502852239', '1502852239');

-- ----------------------------
-- Table structure for `pd_log_talk`
-- ----------------------------
DROP TABLE IF EXISTS `pd_log_talk`;
CREATE TABLE `pd_log_talk` (
  `talk_id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL COMMENT '组内分标示',
  `log_id` int(10) NOT NULL,
  `send_role_id` int(10) NOT NULL COMMENT '发送者id',
  `receive_role_id` int(10) NOT NULL COMMENT '接收者id',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(10) NOT NULL,
  `g_mark` varchar(50) NOT NULL COMMENT '标示',
  PRIMARY KEY (`talk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='日志评论回复表';

-- ----------------------------
-- Records of pd_log_talk
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_market`
-- ----------------------------
DROP TABLE IF EXISTS `pd_market`;
CREATE TABLE `pd_market` (
  `market_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名称',
  `start_time` int(10) NOT NULL COMMENT '活动开始时间',
  `end_time` int(10) NOT NULL COMMENT '活动结束时间',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '活动类型',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '活动地址',
  `expected_cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预计成本',
  `expected_income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预计收入',
  `real_cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际成本',
  `real_income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际收入',
  `status` varchar(255) NOT NULL DEFAULT '' COMMENT '活动状态',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者',
  `owner_role_id` int(10) NOT NULL COMMENT '所有者',
  `executor_role_ids` varchar(512) NOT NULL COMMENT '参与的人',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(10) NOT NULL COMMENT '添加时间',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '锁定 1是；0否',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '作废 1是；0否',
  PRIMARY KEY (`market_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_market
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_market_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_market_data`;
CREATE TABLE `pd_market_data` (
  `market_id` int(10) NOT NULL COMMENT '主键',
  `plan` text NOT NULL COMMENT '活动计划',
  `execution_description` longtext NOT NULL COMMENT '执行描述',
  `summary` longtext NOT NULL COMMENT '总结',
  `effect` longtext NOT NULL COMMENT '效果',
  `description` longtext NOT NULL COMMENT '描述',
  PRIMARY KEY (`market_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_market_data
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_member`
-- ----------------------------
DROP TABLE IF EXISTS `pd_member`;
CREATE TABLE `pd_member` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线上客户表';

-- ----------------------------
-- Records of pd_member
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_member_address`
-- ----------------------------
DROP TABLE IF EXISTS `pd_member_address`;
CREATE TABLE `pd_member_address` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '线上客户ID',
  `address` varchar(2000) NOT NULL COMMENT '地址',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线上客户地址表';

-- ----------------------------
-- Records of pd_member_address
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_message`
-- ----------------------------
DROP TABLE IF EXISTS `pd_message`;
CREATE TABLE `pd_message` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `to_role_id` int(11) unsigned NOT NULL,
  `from_role_id` int(11) unsigned NOT NULL,
  `content` text NOT NULL,
  `read_time` int(11) unsigned NOT NULL,
  `send_time` int(11) unsigned NOT NULL,
  `status` int(1) NOT NULL,
  `file_id` int(10) NOT NULL COMMENT '附件ID',
  `is_mark` tinyint(1) NOT NULL COMMENT '1已标记',
  `is_notifi` tinyint(1) NOT NULL COMMENT '1已提醒（桌面）',
  PRIMARY KEY (`message_id`),
  KEY `to_role_id` (`to_role_id`,`from_role_id`,`read_time`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_message
-- ----------------------------
INSERT INTO `pd_message` VALUES ('1', '1', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=1\">20190409-0001-销售合同1</a>》<font style=\"color:green;\">需要进行审核</font>！', '1554856941', '1554813003', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('2', '1', '0', '您创建的合同《<a href=\"/index.php?m=contract&a=view&id=1\">C_20190409-0001-销售合同1</a>》<font style=\"color:green;\">已通过审核</font>！', '1554856941', '1554813046', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('3', '1', '0', '<span>采购单<span>需要您的审批。<a href=\"/index.php?m=purchase&a=view&id=1\" target=\"_blank\">查看详情</a>', '1554856941', '1554813186', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('4', '1', '0', '您负责的 <span>采购单</span>，已通过审批。<a href=\"/index.php?m=purchase&a=view&id=1\" target=\"_blank\">查看详情</a>', '1554856941', '1554813230', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('5', '1', '0', '您创建的付款单《<a href=\"/index.php?m=finance&a=view&t=paymentorder&id=2\">FKD201904093718</a>》<font style=\"color:green;\">已审核</font>！', '1554856941', '1554813391', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('6', '1', '0', '<span>采购单<span>需要您的审批。<a href=\"/index.php?m=purchase&a=view&id=2\" target=\"_blank\">查看详情</a>', '1554856941', '1554813687', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('7', '1', '0', '您负责的 <span>采购单</span>，已通过审批。<a href=\"/index.php?m=purchase&a=view&id=2\" target=\"_blank\">查看详情</a>', '1554856941', '1554813704', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('8', '1', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=2\">20190410-0002-123</a>》<font style=\"color:green;\">需要进行审核</font>！', '1554869159', '1554869149', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('9', '1', '0', 'admins<font style=\"color:green;\">同意了</font>您创建的合同《<a href=\"/index.php?m=contract&a=view&id=2\">C_20190410-0002-123</a>》', '1554869236', '1554869223', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('10', '1', '0', '您有一个合同<a href=\"/index.php?m=contract&a=view&id=2\">C_20190410-0002-123</a>待审批！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class=\"role_info\" rel=\"\" href=\"javascript:void(0)\">admins</a> [办公室 - CEO]<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp', '1554869236', '1554869223', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('11', '1', '0', 'admins<font style=\"color:green;\">同意了</font>您创建的合同《<a href=\"/index.php?m=contract&a=view&id=2\">C_20190410-0002-123</a>》', '1554869408', '1554869319', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('12', '1', '0', '您有一个合同<a href=\"/index.php?m=contract&a=view&id=2\">C_20190410-0002-123</a>待审批！<br/> &nbsp; &nbsp; &nbsp; 内容如下：<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 申请人：<a class=\"role_info\" rel=\"\" href=\"javascript:void(0)\">admins</a> [办公室 - CEO]<br/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp', '1554869408', '1554869319', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('13', '1', '0', '您创建的合同《<a href=\"/index.php?m=contract&a=view&id=2\">C_20190410-0002-123</a>》<font style=\"color:green;\">已通过审核</font>！', '1554869408', '1554869397', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('14', '1', '0', '<span>采购单<span>需要您的审批。<a href=\"/index.php?m=purchase&a=view&id=3\" target=\"_blank\">查看详情</a>', '1554871521', '1554871265', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('15', '1', '0', '您负责的 <span>采购单</span>，已通过审批。<a href=\"/index.php?m=purchase&a=view&id=3\" target=\"_blank\">查看详情</a>', '1554871521', '1554871294', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('16', '1', '0', '您创建的付款单《<a href=\"/index.php?m=finance&a=view&t=paymentorder&id=1\">FKD201904099142</a>》<font style=\"color:green;\">已审核</font>！', '1554871521', '1554871432', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('17', '1', '0', 'admins&nbsp;&nbsp;创建了新的回款单《<a href=\"/index.php?m=finance&a=view&t=receivingorder&id=1\"></a>》<font style=\"color:green;\">需要进行审核</font>！', '1554872229', '1554872217', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('18', '1', '0', '您创建的回款单《<a href=\"/index.php?m=finance&a=view&t=receivingorder&id=1\">20190410-0001</a>》<font style=\"color:green;\">已审核</font>！', '1554873882', '1554872269', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('19', '1', '0', 'admins&nbsp;&nbsp;创建了新的回款单《<a href=\"/index.php?m=finance&a=view&t=receivingorder&id=2\"></a>》<font style=\"color:green;\">需要进行审核</font>！', '1554873882', '1554872309', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('20', '1', '0', '您创建的回款单《<a href=\"/index.php?m=finance&a=view&t=receivingorder&id=2\">20190410-0002</a>》<font style=\"color:green;\">已审核</font>！', '1554873882', '1554872325', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('21', '1', '0', '您创建的付款单《<a href=\"/index.php?m=finance&a=view&t=paymentorder&id=3\">FKD201904108107</a>》<font style=\"color:green;\">已审核</font>！', '1554873882', '1554872362', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('22', '1', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=3\">20190410-0003-手机出售</a>》<font style=\"color:green;\">需要进行审核</font>！', '1554873882', '1554872557', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('23', '1', '0', '您创建的合同《<a href=\"/index.php?m=contract&a=view&id=3\">C_20190410-0003-手机出售</a>》<font style=\"color:green;\">已通过审核</font>！', '1554873882', '1554872583', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('24', '1', '0', 'admins&nbsp;&nbsp;创建了新的回款单《<a href=\"/index.php?m=finance&a=view&t=receivingorder&id=3\"></a>》<font style=\"color:green;\">需要进行审核</font>！', '1554873882', '1554872688', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('25', '1', '0', '您创建的回款单《<a href=\"/index.php?m=finance&a=view&t=receivingorder&id=3\">20190410-0003</a>》<font style=\"color:green;\">已审核</font>！', '1554873882', '1554872710', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('26', '1', '0', '<span>库存调拨单<span>需要您的审批。<a href=\"/index.php?m=stock&a=transfer_view&transfer_id=1\" target=\"_blank\">查看详情</a>', '1554873882', '1554872942', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('27', '1', '0', '您负责的 <span>库存调拨单</span>，已通过审批。<a href=\"/index.php?m=stock&a=transfer_view&transfer_id=1\" target=\"_blank\">查看详情</a>', '1554873882', '1554872956', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('28', '1', '0', '<span>库存调拨单<span>需要您的审批。<a href=\"/index.php?m=stock&a=transfer_view&transfer_id=2\" target=\"_blank\">查看详情</a>', '1554873882', '1554873712', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('29', '1', '0', '您负责的 <span>库存调拨单</span>，已通过审批。<a href=\"/index.php?m=stock&a=transfer_view&transfer_id=2\" target=\"_blank\">查看详情</a>', '1554873882', '1554873729', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('30', '1', '0', '您负责的 <span>库存调拨单</span>，已通过审批。<a href=\"/index.php?m=stock&a=transfer_view&transfer_id=2\" target=\"_blank\">查看详情</a>', '1554873882', '1554873845', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('31', '1', '0', '<span>库存调拨单<span>需要您的审批。<a href=\"/index.php?m=stock&a=transfer_view&transfer_id=3\" target=\"_blank\">查看详情</a>', '1554874598', '1554873914', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('32', '1', '0', '您负责的 <span>库存调拨单</span>，已通过审批。<a href=\"/index.php?m=stock&a=transfer_view&transfer_id=3\" target=\"_blank\">查看详情</a>', '1554874598', '1554873926', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('33', '2', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=4\">20190411-0004-多少</a>》<font style=\"color:green;\">需要进行审核</font>！', '1554952295', '1554944788', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('34', '1', '0', '您创建的合同《<a href=\"/index.php?m=contract&a=view&id=4\">C_20190411-0004-多少</a>》<font style=\"color:green;\">已通过审核</font>！', '1555248871', '1554945275', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('35', '2', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=5\">20190417-0005-000</a>》<font style=\"color:green;\">需要进行审核</font>！', '0', '1555469850', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('36', '2', '0', '<span>采购单<span>需要您的审批。<a href=\"/index.php?m=purchase&a=view&id=4\" target=\"_blank\">查看详情</a>', '0', '1555550812', '0', '0', '0', '0');
INSERT INTO `pd_message` VALUES ('37', '1', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=6\">20190418-0006-12300000</a>》<font style=\"color:green;\">需要进行审核</font>！', '1555553890', '1555552941', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('38', '2', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=6\">20190418-0006-12300000</a>》<font style=\"color:green;\">需要进行审核</font>！', '0', '1555552941', '0', '0', '0', '0');
INSERT INTO `pd_message` VALUES ('39', '1', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=7\">20190418-0007-聂风雨</a>》<font style=\"color:green;\">需要进行审核</font>！', '1555553890', '1555553075', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('40', '2', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=7\">20190418-0007-聂风雨</a>》<font style=\"color:green;\">需要进行审核</font>！', '0', '1555553075', '0', '0', '0', '0');
INSERT INTO `pd_message` VALUES ('41', '1', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=8\">20190418-0008-123878769</a>》<font style=\"color:green;\">需要进行审核</font>！', '1555553890', '1555553519', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('42', '2', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=8\">20190418-0008-123878769</a>》<font style=\"color:green;\">需要进行审核</font>！', '0', '1555553519', '0', '0', '0', '0');
INSERT INTO `pd_message` VALUES ('43', '3', '0', '<span>采购单<span>需要您的审批。<a href=\"/index.php?m=purchase&a=view&id=5\" target=\"_blank\">查看详情</a>', '0', '1555566849', '0', '0', '0', '0');
INSERT INTO `pd_message` VALUES ('44', '1', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=9\">20190418-0009-123123123123123</a>》<font style=\"color:green;\">需要进行审核</font>！', '0', '1555567119', '0', '0', '0', '1');
INSERT INTO `pd_message` VALUES ('45', '2', '0', 'admins&nbsp;&nbsp;创建了新的合同《<a href=\"/index.php?m=contract&a=view&id=9\">20190418-0009-123123123123123</a>》<font style=\"color:green;\">需要进行审核</font>！', '0', '1555567119', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `pd_note`
-- ----------------------------
DROP TABLE IF EXISTS `pd_note`;
CREATE TABLE `pd_note` (
  `note_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL,
  `content` varchar(1000) NOT NULL COMMENT '内容',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='便笺表';

-- ----------------------------
-- Records of pd_note
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_order`
-- ----------------------------
DROP TABLE IF EXISTS `pd_order`;
CREATE TABLE `pd_order` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商城订单表';

-- ----------------------------
-- Records of pd_order
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_payables`
-- ----------------------------
DROP TABLE IF EXISTS `pd_payables`;
CREATE TABLE `pd_payables` (
  `payables_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '应付款id',
  `customer_id` int(10) NOT NULL COMMENT '客户id，当type_id等于-1时，值为供应商id的值',
  `contract_id` int(10) NOT NULL COMMENT '当type_id等于-1时，值为采购单id的值，-2时值为销售退货单id，即purchase_id,其他情况还为contract_id',
  `type_id` int(10) NOT NULL COMMENT '应付款类型 -1采购退款 -2销售退货退款 其他数字代表其他类型',
  `name` varchar(500) NOT NULL COMMENT '应付款名',
  `price` decimal(18,2) NOT NULL COMMENT '应付金额',
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='应付款表';

-- ----------------------------
-- Records of pd_payables
-- ----------------------------
INSERT INTO `pd_payables` VALUES ('1', '1', '1', '-1', '20190409-0001', '2700.00', '1', '1', '', '1554739200', '1554813230', '2', '1554813230', '0', '0', '0');
INSERT INTO `pd_payables` VALUES ('2', '1', '3', '-1', '20190410-0002', '600000.00', '1', '1', '', '1554825600', '1554871294', '2', '1554871294', '0', '0', '0');

-- ----------------------------
-- Table structure for `pd_paymentorder`
-- ----------------------------
DROP TABLE IF EXISTS `pd_paymentorder`;
CREATE TABLE `pd_paymentorder` (
  `paymentorder_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '付款单id',
  `name` varchar(500) NOT NULL COMMENT '付款单主题',
  `money` decimal(18,2) NOT NULL COMMENT '付款金额',
  `payables_id` int(10) NOT NULL COMMENT '应付款id',
  `type` int(10) NOT NULL COMMENT '类别（暂不用）',
  `description` text NOT NULL COMMENT '描述',
  `pay_time` int(10) NOT NULL COMMENT '付款时间',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者id',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态0待审1审核通过2审核失败',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `is_deleted` int(1) NOT NULL DEFAULT '0' COMMENT ' 是否删除',
  `delete_role_id` int(10) NOT NULL COMMENT '删除人',
  `delete_time` int(10) NOT NULL COMMENT '删除时间',
  `receipt_account` varchar(255) DEFAULT NULL COMMENT '银行账户',
  `examine_role_id` int(11) NOT NULL COMMENT '审核人',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  `check_des` varchar(200) NOT NULL COMMENT '审核备注',
  `bank_account_id` int(10) NOT NULL COMMENT '付款账户',
  `company` varchar(255) NOT NULL COMMENT '付款单位',
  PRIMARY KEY (`paymentorder_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='付款单';

-- ----------------------------
-- Records of pd_paymentorder
-- ----------------------------
INSERT INTO `pd_paymentorder` VALUES ('1', 'FKD201904099142', '0.00', '1', '0', '								', '1554739200', '1', '1', '1', '1554871432', '1554813362', '0', '0', '0', '62222002222', '1', '1554871432', '', '1', '111');
INSERT INTO `pd_paymentorder` VALUES ('2', 'FKD201904093718', '2700.00', '1', '0', '								', '1554739200', '1', '1', '1', '1554813391', '1554813375', '0', '0', '0', '62222002222', '1', '1554813391', '', '1', '111');
INSERT INTO `pd_paymentorder` VALUES ('3', 'FKD201904108107', '600000.00', '2', '0', '								', '1554825600', '1', '1', '1', '1554872362', '1554872176', '0', '0', '0', '62222002222', '1', '1554872362', '', '1', '111');

-- ----------------------------
-- Table structure for `pd_permission`
-- ----------------------------
DROP TABLE IF EXISTS `pd_permission`;
CREATE TABLE `pd_permission` (
  `permission_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `role_id` int(10) NOT NULL COMMENT '岗位id',
  `position_id` int(10) NOT NULL COMMENT '岗位组id',
  `url` varchar(50) NOT NULL COMMENT '对应模块操作',
  `description` varchar(200) NOT NULL COMMENT '权限备注',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1自己和下属2所有人3自己4部门所有人',
  PRIMARY KEY (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=367 DEFAULT CHARSET=utf8 COMMENT='本表用来存放权限信息';

-- ----------------------------
-- Records of pd_permission
-- ----------------------------
INSERT INTO `pd_permission` VALUES ('1', '0', '2', 'leads/index', '', '1');
INSERT INTO `pd_permission` VALUES ('2', '0', '2', 'leads/view', '', '1');
INSERT INTO `pd_permission` VALUES ('3', '0', '2', 'leads/add', '', '1');
INSERT INTO `pd_permission` VALUES ('4', '0', '2', 'leads/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('5', '0', '2', 'leads/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('6', '0', '2', 'leads/excelimport', '', '1');
INSERT INTO `pd_permission` VALUES ('7', '0', '2', 'leads/excelexport', '', '1');
INSERT INTO `pd_permission` VALUES ('8', '0', '2', 'leads/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('9', '0', '2', 'leads/del_public', '', '1');
INSERT INTO `pd_permission` VALUES ('10', '0', '2', 'customer/index', '', '1');
INSERT INTO `pd_permission` VALUES ('11', '0', '2', 'customer/view', '', '1');
INSERT INTO `pd_permission` VALUES ('12', '0', '2', 'customer/add', '', '1');
INSERT INTO `pd_permission` VALUES ('13', '0', '2', 'customer/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('14', '0', '2', 'customer/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('15', '0', '2', 'customer/customerlock', '', '1');
INSERT INTO `pd_permission` VALUES ('16', '0', '2', 'customer/excelimport', '', '1');
INSERT INTO `pd_permission` VALUES ('17', '0', '2', 'customer/excelexport', '', '1');
INSERT INTO `pd_permission` VALUES ('18', '0', '2', 'customer/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('19', '0', '2', 'customer/transfer_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('20', '0', '2', 'customer/share', '', '1');
INSERT INTO `pd_permission` VALUES ('21', '0', '2', 'customer/close_share', '', '1');
INSERT INTO `pd_permission` VALUES ('22', '0', '2', 'customer/nearby', '', '1');
INSERT INTO `pd_permission` VALUES ('23', '0', '2', 'remind/visitor_plan_analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('24', '0', '2', 'customer/del_resource', '', '1');
INSERT INTO `pd_permission` VALUES ('25', '0', '2', 'contacts/index', '', '1');
INSERT INTO `pd_permission` VALUES ('26', '0', '2', 'contacts/view', '', '1');
INSERT INTO `pd_permission` VALUES ('27', '0', '2', 'contacts/add', '', '1');
INSERT INTO `pd_permission` VALUES ('28', '0', '2', 'contacts/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('29', '0', '2', 'contacts/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('30', '0', '2', 'contacts/excel_import', '', '1');
INSERT INTO `pd_permission` VALUES ('31', '0', '2', 'contacts/excel_export', '', '1');
INSERT INTO `pd_permission` VALUES ('32', '0', '2', 'contract/index', '', '1');
INSERT INTO `pd_permission` VALUES ('33', '0', '2', 'contract/view', '', '1');
INSERT INTO `pd_permission` VALUES ('34', '0', '2', 'contract/add', '', '1');
INSERT INTO `pd_permission` VALUES ('35', '0', '2', 'contract/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('36', '0', '2', 'contract/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('37', '0', '2', 'contract/export', '', '1');
INSERT INTO `pd_permission` VALUES ('38', '0', '2', 'contract/collection', '', '1');
INSERT INTO `pd_permission` VALUES ('39', '0', '2', 'contract/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('40', '0', '2', 'contract/revokeCheck', '', '1');
INSERT INTO `pd_permission` VALUES ('41', '0', '2', 'contract/check', '', '1');
INSERT INTO `pd_permission` VALUES ('42', '0', '2', 'business/index', '', '1');
INSERT INTO `pd_permission` VALUES ('43', '0', '2', 'business/view', '', '1');
INSERT INTO `pd_permission` VALUES ('44', '0', '2', 'business/add', '', '1');
INSERT INTO `pd_permission` VALUES ('45', '0', '2', 'business/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('46', '0', '2', 'business/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('47', '0', '2', 'business/export', '', '1');
INSERT INTO `pd_permission` VALUES ('48', '0', '2', 'business/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('49', '0', '2', 'product/index', '', '1');
INSERT INTO `pd_permission` VALUES ('50', '0', '2', 'product/view', '', '1');
INSERT INTO `pd_permission` VALUES ('51', '0', '2', 'product/add', '', '1');
INSERT INTO `pd_permission` VALUES ('52', '0', '2', 'product/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('53', '0', '2', 'product/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('54', '0', '2', 'product/revert', '', '1');
INSERT INTO `pd_permission` VALUES ('55', '0', '2', 'product/excelimport', '', '1');
INSERT INTO `pd_permission` VALUES ('56', '0', '2', 'product/excelexport', '', '1');
INSERT INTO `pd_permission` VALUES ('57', '0', '2', 'product/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('58', '0', '2', 'finance/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('59', '0', '2', 'finance/revokeCheck', '', '1');
INSERT INTO `pd_permission` VALUES ('60', '0', '2', 'finance/check', '', '1');
INSERT INTO `pd_permission` VALUES ('61', '0', '2', 'finance/target', '', '1');
INSERT INTO `pd_permission` VALUES ('62', '0', '2', 'finance/index_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('63', '0', '2', 'finance/view_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('64', '0', '2', 'finance/add_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('65', '0', '2', 'finance/edit_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('66', '0', '2', 'finance/delete_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('67', '0', '2', 'finance/index_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('68', '0', '2', 'finance/view_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('69', '0', '2', 'finance/add_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('70', '0', '2', 'finance/edit_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('71', '0', '2', 'finance/delete_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('72', '0', '2', 'finance/index_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('73', '0', '2', 'finance/view_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('74', '0', '2', 'finance/add_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('75', '0', '2', 'finance/edit_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('76', '0', '2', 'finance/delete_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('77', '0', '2', 'finance/export_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('78', '0', '2', 'finance/index_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('79', '0', '2', 'finance/view_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('80', '0', '2', 'finance/add_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('81', '0', '2', 'finance/edit_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('82', '0', '2', 'finance/delete_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('83', '0', '2', 'invoice/index', '', '1');
INSERT INTO `pd_permission` VALUES ('84', '0', '2', 'invoice/view', '', '1');
INSERT INTO `pd_permission` VALUES ('85', '0', '2', 'invoice/add', '', '1');
INSERT INTO `pd_permission` VALUES ('86', '0', '2', 'invoice/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('87', '0', '2', 'invoice/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('88', '0', '2', 'invoice/revokeCheck', '', '1');
INSERT INTO `pd_permission` VALUES ('89', '0', '2', 'invoice/check', '', '1');
INSERT INTO `pd_permission` VALUES ('90', '0', '2', 'log/index', '', '1');
INSERT INTO `pd_permission` VALUES ('91', '0', '2', 'log/mylog_view', '', '1');
INSERT INTO `pd_permission` VALUES ('92', '0', '2', 'log/viewajax', '', '1');
INSERT INTO `pd_permission` VALUES ('93', '0', '2', 'log/mylog_add', '', '1');
INSERT INTO `pd_permission` VALUES ('94', '0', '2', 'log/mylog_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('95', '0', '2', 'log/log_delete', '', '1');
INSERT INTO `pd_permission` VALUES ('96', '0', '2', 'log/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('97', '0', '2', 'examine/index', '', '1');
INSERT INTO `pd_permission` VALUES ('98', '0', '2', 'examine/view', '', '1');
INSERT INTO `pd_permission` VALUES ('99', '0', '2', 'examine/add', '', '1');
INSERT INTO `pd_permission` VALUES ('100', '0', '2', 'examine/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('101', '0', '2', 'examine/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('102', '0', '2', 'examine/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('103', '0', '2', 'examine/add_examine', '', '1');
INSERT INTO `pd_permission` VALUES ('104', '0', '2', 'knowledge/index', '', '1');
INSERT INTO `pd_permission` VALUES ('105', '0', '2', 'knowledge/view', '', '1');
INSERT INTO `pd_permission` VALUES ('106', '0', '2', 'knowledge/add', '', '1');
INSERT INTO `pd_permission` VALUES ('107', '0', '2', 'knowledge/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('108', '0', '2', 'knowledge/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('109', '0', '2', 'knowledge/excelimport', '', '1');
INSERT INTO `pd_permission` VALUES ('110', '0', '2', 'knowledge/excelexport', '', '1');
INSERT INTO `pd_permission` VALUES ('111', '0', '2', 'announcement/index', '', '1');
INSERT INTO `pd_permission` VALUES ('112', '0', '2', 'announcement/view', '', '1');
INSERT INTO `pd_permission` VALUES ('113', '0', '2', 'announcement/add', '', '1');
INSERT INTO `pd_permission` VALUES ('114', '0', '2', 'announcement/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('115', '0', '2', 'announcement/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('116', '0', '2', 'announcement/read_list', '', '1');
INSERT INTO `pd_permission` VALUES ('117', '0', '2', 'sign/index', '', '1');
INSERT INTO `pd_permission` VALUES ('118', '0', '2', 'event/index', '', '1');
INSERT INTO `pd_permission` VALUES ('119', '0', '2', 'task/index', '', '1');
INSERT INTO `pd_permission` VALUES ('120', '0', '2', 'task/view', '', '1');
INSERT INTO `pd_permission` VALUES ('121', '0', '2', 'task/add', '', '1');
INSERT INTO `pd_permission` VALUES ('122', '0', '2', 'task/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('123', '0', '2', 'task/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('124', '0', '2', 'task/del', '', '1');
INSERT INTO `pd_permission` VALUES ('125', '0', '2', 'kaoqin/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('126', '0', '2', 'kaoqin/export', '', '1');
INSERT INTO `pd_permission` VALUES ('127', '0', '2', 'kaoqin/setting', '', '1');
INSERT INTO `pd_permission` VALUES ('128', '0', '2', 'template/index', '', '1');
INSERT INTO `pd_permission` VALUES ('129', '0', '2', 'template/add', '', '1');
INSERT INTO `pd_permission` VALUES ('130', '0', '2', 'template/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('131', '0', '2', 'setting/sendsms', '', '1');
INSERT INTO `pd_permission` VALUES ('132', '0', '2', 'setting/smsrecord', '', '1');
INSERT INTO `pd_permission` VALUES ('133', '0', '2', 'setting/sendemail', '', '1');
INSERT INTO `pd_permission` VALUES ('134', '0', '2', 'market/index', '', '1');
INSERT INTO `pd_permission` VALUES ('135', '0', '2', 'market/view', '', '1');
INSERT INTO `pd_permission` VALUES ('136', '0', '2', 'market/add', '', '1');
INSERT INTO `pd_permission` VALUES ('137', '0', '2', 'market/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('138', '0', '2', 'call/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('139', '0', '2', 'user/index', '', '1');
INSERT INTO `pd_permission` VALUES ('140', '0', '2', 'user/view', '', '1');
INSERT INTO `pd_permission` VALUES ('141', '0', '2', 'user/user_add', '', '1');
INSERT INTO `pd_permission` VALUES ('142', '0', '2', 'user/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('143', '0', '2', 'user/contacts', '', '1');
INSERT INTO `pd_permission` VALUES ('144', '0', '2', 'purchase/index', '', '1');
INSERT INTO `pd_permission` VALUES ('145', '0', '2', 'purchase/view', '', '1');
INSERT INTO `pd_permission` VALUES ('146', '0', '2', 'purchase/add', '', '1');
INSERT INTO `pd_permission` VALUES ('147', '0', '2', 'purchase/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('148', '0', '2', 'purchase/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('149', '0', '2', 'purchase/export', '', '1');
INSERT INTO `pd_permission` VALUES ('150', '0', '2', 'purchase/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('151', '0', '2', 'purchase/return_goods', '', '1');
INSERT INTO `pd_permission` VALUES ('152', '0', '2', 'purchase/return_goods_view', '', '1');
INSERT INTO `pd_permission` VALUES ('153', '0', '2', 'purchase/return_goods_add', '', '1');
INSERT INTO `pd_permission` VALUES ('154', '0', '2', 'purchase/return_goods_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('155', '0', '2', 'purchase/return_delete', '', '1');
INSERT INTO `pd_permission` VALUES ('156', '0', '2', 'sales/return_index', '', '1');
INSERT INTO `pd_permission` VALUES ('157', '0', '2', 'sales/return_view', '', '1');
INSERT INTO `pd_permission` VALUES ('158', '0', '2', 'sales/return_add', '', '1');
INSERT INTO `pd_permission` VALUES ('159', '0', '2', 'sales/return_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('160', '0', '2', 'sales/return_delete', '', '1');
INSERT INTO `pd_permission` VALUES ('161', '0', '2', 'stock/index', '', '1');
INSERT INTO `pd_permission` VALUES ('162', '0', '2', 'stock/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('163', '0', '2', 'stock/export', '', '1');
INSERT INTO `pd_permission` VALUES ('164', '0', '2', 'stock/instock', '', '1');
INSERT INTO `pd_permission` VALUES ('165', '0', '2', 'stock/outstock', '', '1');
INSERT INTO `pd_permission` VALUES ('166', '0', '2', 'stock/transfer', '', '1');
INSERT INTO `pd_permission` VALUES ('167', '0', '2', 'stock/transfer_view', '', '1');
INSERT INTO `pd_permission` VALUES ('168', '0', '2', 'stock/transfer_add', '', '1');
INSERT INTO `pd_permission` VALUES ('169', '0', '2', 'stock/transfer_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('170', '0', '2', 'warehouse/index', '', '1');
INSERT INTO `pd_permission` VALUES ('171', '0', '2', 'warehouse/add', '', '1');
INSERT INTO `pd_permission` VALUES ('172', '0', '2', 'warehouse/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('173', '0', '2', 'goods/sn_track', '', '1');
INSERT INTO `pd_permission` VALUES ('174', '0', '2', 'goods/sn_view', '', '1');
INSERT INTO `pd_permission` VALUES ('175', '0', '2', 'purchase/dialogaddsn', '', '1');
INSERT INTO `pd_permission` VALUES ('176', '0', '2', 'purchase/deletesn', '', '1');
INSERT INTO `pd_permission` VALUES ('177', '0', '2', 'supplier/index', '', '1');
INSERT INTO `pd_permission` VALUES ('178', '0', '2', 'supplier/view', '', '1');
INSERT INTO `pd_permission` VALUES ('179', '0', '2', 'supplier/add', '', '1');
INSERT INTO `pd_permission` VALUES ('180', '0', '2', 'supplier/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('181', '0', '2', 'supplier/contacts_add', '', '1');
INSERT INTO `pd_permission` VALUES ('182', '0', '2', 'supplier/contacts_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('183', '0', '2', 'supplier/contacts_delete', '', '1');
INSERT INTO `pd_permission` VALUES ('184', '0', '1', 'leads/index', '', '1');
INSERT INTO `pd_permission` VALUES ('185', '0', '1', 'leads/view', '', '1');
INSERT INTO `pd_permission` VALUES ('186', '0', '1', 'leads/add', '', '1');
INSERT INTO `pd_permission` VALUES ('187', '0', '1', 'leads/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('188', '0', '1', 'leads/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('189', '0', '1', 'leads/excelimport', '', '1');
INSERT INTO `pd_permission` VALUES ('190', '0', '1', 'leads/excelexport', '', '1');
INSERT INTO `pd_permission` VALUES ('191', '0', '1', 'leads/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('192', '0', '1', 'leads/del_public', '', '1');
INSERT INTO `pd_permission` VALUES ('193', '0', '1', 'customer/index', '', '1');
INSERT INTO `pd_permission` VALUES ('194', '0', '1', 'customer/view', '', '1');
INSERT INTO `pd_permission` VALUES ('195', '0', '1', 'customer/add', '', '1');
INSERT INTO `pd_permission` VALUES ('196', '0', '1', 'customer/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('197', '0', '1', 'customer/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('198', '0', '1', 'customer/customerlock', '', '1');
INSERT INTO `pd_permission` VALUES ('199', '0', '1', 'customer/excelimport', '', '1');
INSERT INTO `pd_permission` VALUES ('200', '0', '1', 'customer/excelexport', '', '1');
INSERT INTO `pd_permission` VALUES ('201', '0', '1', 'customer/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('202', '0', '1', 'customer/transfer_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('203', '0', '1', 'customer/share', '', '1');
INSERT INTO `pd_permission` VALUES ('204', '0', '1', 'customer/close_share', '', '1');
INSERT INTO `pd_permission` VALUES ('205', '0', '1', 'customer/nearby', '', '1');
INSERT INTO `pd_permission` VALUES ('206', '0', '1', 'remind/visitor_plan_analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('207', '0', '1', 'customer/del_resource', '', '1');
INSERT INTO `pd_permission` VALUES ('208', '0', '1', 'contacts/index', '', '1');
INSERT INTO `pd_permission` VALUES ('209', '0', '1', 'contacts/view', '', '1');
INSERT INTO `pd_permission` VALUES ('210', '0', '1', 'contacts/add', '', '1');
INSERT INTO `pd_permission` VALUES ('211', '0', '1', 'contacts/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('212', '0', '1', 'contacts/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('213', '0', '1', 'contacts/excel_import', '', '1');
INSERT INTO `pd_permission` VALUES ('214', '0', '1', 'contacts/excel_export', '', '1');
INSERT INTO `pd_permission` VALUES ('215', '0', '1', 'contract/index', '', '1');
INSERT INTO `pd_permission` VALUES ('216', '0', '1', 'contract/view', '', '1');
INSERT INTO `pd_permission` VALUES ('217', '0', '1', 'contract/add', '', '1');
INSERT INTO `pd_permission` VALUES ('218', '0', '1', 'contract/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('219', '0', '1', 'contract/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('220', '0', '1', 'contract/export', '', '1');
INSERT INTO `pd_permission` VALUES ('221', '0', '1', 'contract/collection', '', '1');
INSERT INTO `pd_permission` VALUES ('222', '0', '1', 'contract/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('223', '0', '1', 'contract/revokeCheck', '', '1');
INSERT INTO `pd_permission` VALUES ('224', '0', '1', 'contract/check', '', '1');
INSERT INTO `pd_permission` VALUES ('225', '0', '1', 'business/index', '', '1');
INSERT INTO `pd_permission` VALUES ('226', '0', '1', 'business/view', '', '1');
INSERT INTO `pd_permission` VALUES ('227', '0', '1', 'business/add', '', '1');
INSERT INTO `pd_permission` VALUES ('228', '0', '1', 'business/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('229', '0', '1', 'business/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('230', '0', '1', 'business/export', '', '1');
INSERT INTO `pd_permission` VALUES ('231', '0', '1', 'business/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('232', '0', '1', 'product/index', '', '1');
INSERT INTO `pd_permission` VALUES ('233', '0', '1', 'product/view', '', '1');
INSERT INTO `pd_permission` VALUES ('234', '0', '1', 'product/add', '', '1');
INSERT INTO `pd_permission` VALUES ('235', '0', '1', 'product/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('236', '0', '1', 'product/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('237', '0', '1', 'product/revert', '', '1');
INSERT INTO `pd_permission` VALUES ('238', '0', '1', 'product/excelimport', '', '1');
INSERT INTO `pd_permission` VALUES ('239', '0', '1', 'product/excelexport', '', '1');
INSERT INTO `pd_permission` VALUES ('240', '0', '1', 'product/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('241', '0', '1', 'finance/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('242', '0', '1', 'finance/revokeCheck', '', '1');
INSERT INTO `pd_permission` VALUES ('243', '0', '1', 'finance/check', '', '1');
INSERT INTO `pd_permission` VALUES ('244', '0', '1', 'finance/target', '', '1');
INSERT INTO `pd_permission` VALUES ('245', '0', '1', 'finance/index_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('246', '0', '1', 'finance/view_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('247', '0', '1', 'finance/add_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('248', '0', '1', 'finance/edit_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('249', '0', '1', 'finance/delete_receivables', '', '1');
INSERT INTO `pd_permission` VALUES ('250', '0', '1', 'finance/index_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('251', '0', '1', 'finance/view_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('252', '0', '1', 'finance/add_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('253', '0', '1', 'finance/edit_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('254', '0', '1', 'finance/delete_payables', '', '1');
INSERT INTO `pd_permission` VALUES ('255', '0', '1', 'finance/index_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('256', '0', '1', 'finance/view_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('257', '0', '1', 'finance/add_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('258', '0', '1', 'finance/edit_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('259', '0', '1', 'finance/delete_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('260', '0', '1', 'finance/export_receivingorder', '', '1');
INSERT INTO `pd_permission` VALUES ('261', '0', '1', 'finance/index_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('262', '0', '1', 'finance/view_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('263', '0', '1', 'finance/add_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('264', '0', '1', 'finance/edit_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('265', '0', '1', 'finance/delete_paymentorder', '', '1');
INSERT INTO `pd_permission` VALUES ('266', '0', '1', 'invoice/index', '', '1');
INSERT INTO `pd_permission` VALUES ('267', '0', '1', 'invoice/view', '', '1');
INSERT INTO `pd_permission` VALUES ('268', '0', '1', 'invoice/add', '', '1');
INSERT INTO `pd_permission` VALUES ('269', '0', '1', 'invoice/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('270', '0', '1', 'invoice/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('271', '0', '1', 'invoice/revokeCheck', '', '1');
INSERT INTO `pd_permission` VALUES ('272', '0', '1', 'invoice/check', '', '1');
INSERT INTO `pd_permission` VALUES ('273', '0', '1', 'log/index', '', '1');
INSERT INTO `pd_permission` VALUES ('274', '0', '1', 'log/mylog_view', '', '1');
INSERT INTO `pd_permission` VALUES ('275', '0', '1', 'log/viewajax', '', '1');
INSERT INTO `pd_permission` VALUES ('276', '0', '1', 'log/mylog_add', '', '1');
INSERT INTO `pd_permission` VALUES ('277', '0', '1', 'log/mylog_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('278', '0', '1', 'log/log_delete', '', '1');
INSERT INTO `pd_permission` VALUES ('279', '0', '1', 'log/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('280', '0', '1', 'examine/index', '', '1');
INSERT INTO `pd_permission` VALUES ('281', '0', '1', 'examine/view', '', '1');
INSERT INTO `pd_permission` VALUES ('282', '0', '1', 'examine/add', '', '1');
INSERT INTO `pd_permission` VALUES ('283', '0', '1', 'examine/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('284', '0', '1', 'examine/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('285', '0', '1', 'examine/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('286', '0', '1', 'examine/add_examine', '', '1');
INSERT INTO `pd_permission` VALUES ('287', '0', '1', 'knowledge/index', '', '1');
INSERT INTO `pd_permission` VALUES ('288', '0', '1', 'knowledge/view', '', '1');
INSERT INTO `pd_permission` VALUES ('289', '0', '1', 'knowledge/add', '', '1');
INSERT INTO `pd_permission` VALUES ('290', '0', '1', 'knowledge/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('291', '0', '1', 'knowledge/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('292', '0', '1', 'knowledge/excelimport', '', '1');
INSERT INTO `pd_permission` VALUES ('293', '0', '1', 'knowledge/excelexport', '', '1');
INSERT INTO `pd_permission` VALUES ('294', '0', '1', 'announcement/index', '', '1');
INSERT INTO `pd_permission` VALUES ('295', '0', '1', 'announcement/view', '', '1');
INSERT INTO `pd_permission` VALUES ('296', '0', '1', 'announcement/add', '', '1');
INSERT INTO `pd_permission` VALUES ('297', '0', '1', 'announcement/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('298', '0', '1', 'announcement/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('299', '0', '1', 'announcement/read_list', '', '1');
INSERT INTO `pd_permission` VALUES ('300', '0', '1', 'sign/index', '', '1');
INSERT INTO `pd_permission` VALUES ('301', '0', '1', 'event/index', '', '1');
INSERT INTO `pd_permission` VALUES ('302', '0', '1', 'task/index', '', '1');
INSERT INTO `pd_permission` VALUES ('303', '0', '1', 'task/view', '', '1');
INSERT INTO `pd_permission` VALUES ('304', '0', '1', 'task/add', '', '1');
INSERT INTO `pd_permission` VALUES ('305', '0', '1', 'task/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('306', '0', '1', 'task/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('307', '0', '1', 'task/del', '', '1');
INSERT INTO `pd_permission` VALUES ('308', '0', '1', 'kaoqin/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('309', '0', '1', 'kaoqin/export', '', '1');
INSERT INTO `pd_permission` VALUES ('310', '0', '1', 'kaoqin/setting', '', '1');
INSERT INTO `pd_permission` VALUES ('311', '0', '1', 'template/index', '', '1');
INSERT INTO `pd_permission` VALUES ('312', '0', '1', 'template/add', '', '1');
INSERT INTO `pd_permission` VALUES ('313', '0', '1', 'template/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('314', '0', '1', 'setting/sendsms', '', '1');
INSERT INTO `pd_permission` VALUES ('315', '0', '1', 'setting/smsrecord', '', '1');
INSERT INTO `pd_permission` VALUES ('316', '0', '1', 'setting/sendemail', '', '1');
INSERT INTO `pd_permission` VALUES ('317', '0', '1', 'market/index', '', '1');
INSERT INTO `pd_permission` VALUES ('318', '0', '1', 'market/view', '', '1');
INSERT INTO `pd_permission` VALUES ('319', '0', '1', 'market/add', '', '1');
INSERT INTO `pd_permission` VALUES ('320', '0', '1', 'market/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('321', '0', '1', 'call/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('322', '0', '1', 'user/index', '', '1');
INSERT INTO `pd_permission` VALUES ('323', '0', '1', 'user/view', '', '1');
INSERT INTO `pd_permission` VALUES ('324', '0', '1', 'user/user_add', '', '1');
INSERT INTO `pd_permission` VALUES ('325', '0', '1', 'user/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('326', '0', '1', 'user/contacts', '', '1');
INSERT INTO `pd_permission` VALUES ('327', '0', '1', 'purchase/index', '', '1');
INSERT INTO `pd_permission` VALUES ('328', '0', '1', 'purchase/view', '', '1');
INSERT INTO `pd_permission` VALUES ('329', '0', '1', 'purchase/add', '', '1');
INSERT INTO `pd_permission` VALUES ('330', '0', '1', 'purchase/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('331', '0', '1', 'purchase/delete', '', '1');
INSERT INTO `pd_permission` VALUES ('332', '0', '1', 'purchase/export', '', '1');
INSERT INTO `pd_permission` VALUES ('333', '0', '1', 'purchase/analytics', '', '1');
INSERT INTO `pd_permission` VALUES ('334', '0', '1', 'purchase/return_goods', '', '1');
INSERT INTO `pd_permission` VALUES ('335', '0', '1', 'purchase/return_goods_view', '', '1');
INSERT INTO `pd_permission` VALUES ('336', '0', '1', 'purchase/return_goods_add', '', '1');
INSERT INTO `pd_permission` VALUES ('337', '0', '1', 'purchase/return_goods_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('338', '0', '1', 'purchase/return_delete', '', '1');
INSERT INTO `pd_permission` VALUES ('339', '0', '1', 'sales/return_index', '', '1');
INSERT INTO `pd_permission` VALUES ('340', '0', '1', 'sales/return_view', '', '1');
INSERT INTO `pd_permission` VALUES ('341', '0', '1', 'sales/return_add', '', '1');
INSERT INTO `pd_permission` VALUES ('342', '0', '1', 'sales/return_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('343', '0', '1', 'sales/return_delete', '', '1');
INSERT INTO `pd_permission` VALUES ('344', '0', '1', 'stock/index', '', '1');
INSERT INTO `pd_permission` VALUES ('345', '0', '1', 'stock/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('346', '0', '1', 'stock/export', '', '1');
INSERT INTO `pd_permission` VALUES ('347', '0', '1', 'stock/instock', '', '1');
INSERT INTO `pd_permission` VALUES ('348', '0', '1', 'stock/outstock', '', '1');
INSERT INTO `pd_permission` VALUES ('349', '0', '1', 'stock/transfer', '', '1');
INSERT INTO `pd_permission` VALUES ('350', '0', '1', 'stock/transfer_view', '', '1');
INSERT INTO `pd_permission` VALUES ('351', '0', '1', 'stock/transfer_add', '', '1');
INSERT INTO `pd_permission` VALUES ('352', '0', '1', 'stock/transfer_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('353', '0', '1', 'warehouse/index', '', '1');
INSERT INTO `pd_permission` VALUES ('354', '0', '1', 'warehouse/add', '', '1');
INSERT INTO `pd_permission` VALUES ('355', '0', '1', 'warehouse/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('356', '0', '1', 'goods/sn_track', '', '1');
INSERT INTO `pd_permission` VALUES ('357', '0', '1', 'goods/sn_view', '', '1');
INSERT INTO `pd_permission` VALUES ('358', '0', '1', 'purchase/dialogaddsn', '', '1');
INSERT INTO `pd_permission` VALUES ('359', '0', '1', 'purchase/deletesn', '', '1');
INSERT INTO `pd_permission` VALUES ('360', '0', '1', 'supplier/index', '', '1');
INSERT INTO `pd_permission` VALUES ('361', '0', '1', 'supplier/view', '', '1');
INSERT INTO `pd_permission` VALUES ('362', '0', '1', 'supplier/add', '', '1');
INSERT INTO `pd_permission` VALUES ('363', '0', '1', 'supplier/edit', '', '1');
INSERT INTO `pd_permission` VALUES ('364', '0', '1', 'supplier/contacts_add', '', '1');
INSERT INTO `pd_permission` VALUES ('365', '0', '1', 'supplier/contacts_edit', '', '1');
INSERT INTO `pd_permission` VALUES ('366', '0', '1', 'supplier/contacts_delete', '', '1');

-- ----------------------------
-- Table structure for `pd_position`
-- ----------------------------
DROP TABLE IF EXISTS `pd_position`;
CREATE TABLE `pd_position` (
  `position_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '岗位id',
  `parent_id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `department_id` int(10) NOT NULL,
  `description` varchar(200) NOT NULL COMMENT '描述',
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='岗位表控制权限';

-- ----------------------------
-- Records of pd_position
-- ----------------------------
INSERT INTO `pd_position` VALUES ('1', '0', 'CEO', '1', '');
INSERT INTO `pd_position` VALUES ('2', '4', '销售部', '1', '');
INSERT INTO `pd_position` VALUES ('3', '1', '销售岗位', '2', '');
INSERT INTO `pd_position` VALUES ('4', '1', '111', '1', '');

-- ----------------------------
-- Table structure for `pd_praise`
-- ----------------------------
DROP TABLE IF EXISTS `pd_praise`;
CREATE TABLE `pd_praise` (
  `praise_id` int(10) NOT NULL AUTO_INCREMENT,
  `log_id` int(10) NOT NULL COMMENT '日志id',
  `role_id` int(10) NOT NULL COMMENT '赞的人role_id',
  PRIMARY KEY (`praise_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_praise
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_product`;
CREATE TABLE `pd_product` (
  `product_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '产品id',
  `category_id` int(11) NOT NULL COMMENT '产品类别的id',
  `name` varchar(200) NOT NULL DEFAULT '',
  `creator_role_id` int(10) NOT NULL COMMENT '产品信息添加者',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `suggested_price` decimal(10,2) NOT NULL COMMENT '建议售价',
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
  `has_sn` tinyint(1) NOT NULL COMMENT '是否有sn码',
  `enable_spec` tinyint(1) NOT NULL COMMENT '是否启用产品多规格 0否 1是',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_product
-- ----------------------------
INSERT INTO `pd_product` VALUES ('1', '1', '看看', '1', '0.00', '0.00', '1554811539', '1554811539', '块', '0', '', '0', '0', '0', '2336', '0', '就看见', '0', '1');
INSERT INTO `pd_product` VALUES ('2', '1', 'iphone', '1', '3000.00', '4000.00', '1554871225', '1554952429', '个', '0', '', '1', '1', '1555408483', '123456789', '0', 'iphone', '0', '1');
INSERT INTO `pd_product` VALUES ('3', '1', 'ad', '2', '0.00', '0.00', '1554953184', '1554957744', '个', '0', '', '0', '0', '0', '', '0', '', '1', '0');
INSERT INTO `pd_product` VALUES ('4', '1', 'sa', '2', '0.00', '0.00', '1554953296', '1554957655', '块', '0', '', '0', '0', '0', '', '0', '', '0', '0');
INSERT INTO `pd_product` VALUES ('5', '1', '123123', '1', '123.00', '123.00', '1555565990', '1555565990', '箱', '0', '123123123', '0', '0', '0', '123', '0', '123123', '0', '0');
INSERT INTO `pd_product` VALUES ('6', '1', 'test1123', '1', '0.00', '0.00', '1555566438', '1555566438', '箱', '0', 'aaaaaaaa', '0', '0', '0', '123456789', '0', 'test', '1', '1');

-- ----------------------------
-- Table structure for `pd_product_category`
-- ----------------------------
DROP TABLE IF EXISTS `pd_product_category`;
CREATE TABLE `pd_product_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '产品类别id',
  `parent_id` int(11) NOT NULL COMMENT '父类别id',
  `name` varchar(30) NOT NULL COMMENT '类别名称',
  `description` varchar(100) NOT NULL COMMENT '备注',
  `is_shop` tinyint(1) NOT NULL COMMENT '1展示',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_product_category
-- ----------------------------
INSERT INTO `pd_product_category` VALUES ('1', '0', '默认', '', '0');

-- ----------------------------
-- Table structure for `pd_product_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_product_data`;
CREATE TABLE `pd_product_data` (
  `product_id` int(10) NOT NULL COMMENT '主键',
  `description` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品信息附表';

-- ----------------------------
-- Records of pd_product_data
-- ----------------------------
INSERT INTO `pd_product_data` VALUES ('1', '');
INSERT INTO `pd_product_data` VALUES ('2', '');
INSERT INTO `pd_product_data` VALUES ('3', '');
INSERT INTO `pd_product_data` VALUES ('4', '');
INSERT INTO `pd_product_data` VALUES ('5', '123123123');
INSERT INTO `pd_product_data` VALUES ('6', 'aaaaaaaa');

-- ----------------------------
-- Table structure for `pd_product_images`
-- ----------------------------
DROP TABLE IF EXISTS `pd_product_images`;
CREATE TABLE `pd_product_images` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='产品图库';

-- ----------------------------
-- Records of pd_product_images
-- ----------------------------
INSERT INTO `pd_product_images` VALUES ('1', '2', '1', 'B009F47UJG_02_amzn.jpg', '5cad73b94c85c7439.jpg', '30.46', './Uploads/201904/10/5cad73b94c85c7439.jpg', './Uploads/201904/10/thumb_5cad73b94c85c7439.jpg', '1554871225', '1');
INSERT INTO `pd_product_images` VALUES ('2', '4', '1', 'lucky.png.php.%00.jpg', '5caec557c3c921843.jpg', '95.92', './Uploads/201904/11/5caec557c3c921843.jpg', './Uploads/201904/11/thumb_5caec557c3c921843.jpg', '1554957282', '2');
INSERT INTO `pd_product_images` VALUES ('3', '3', '1', 'lucky.png.php.%00.jpg', '5caec5b073d0d4905.jpg', '95.92', './Uploads/201904/11/5caec5b073d0d4905.jpg', './Uploads/201904/11/thumb_5caec5b073d0d4905.jpg', '1554957744', '3');
INSERT INTO `pd_product_images` VALUES ('4', '6', '1', 'sign_fail@4x.png', '5cb80f66456312819.png', '8.57', './Uploads/201904/18/5cb80f66456312819.png', './Uploads/201904/18/thumb_5cb80f66456312819.png', '1555566438', '4');

-- ----------------------------
-- Table structure for `pd_product_info`
-- ----------------------------
DROP TABLE IF EXISTS `pd_product_info`;
CREATE TABLE `pd_product_info` (
  `product_info_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` int(10) NOT NULL COMMENT '产品基本信息id',
  `number` varchar(255) NOT NULL COMMENT '商品编号',
  `bar_code` varchar(255) NOT NULL COMMENT '条形码',
  `price` decimal(16,2) NOT NULL COMMENT '建议售价',
  `price_cost` decimal(16,2) NOT NULL COMMENT '建议采购价',
  `price_cost_avg` decimal(16,2) NOT NULL COMMENT '进销存动态平均成本单价',
  `state` tinyint(1) NOT NULL COMMENT '0 正常  1下架 3不可用',
  PRIMARY KEY (`product_info_id`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COMMENT='产品表';

-- ----------------------------
-- Records of pd_product_info
-- ----------------------------
INSERT INTO `pd_product_info` VALUES ('1', '1', '', '', '0.00', '0.00', '0.00', '3');
INSERT INTO `pd_product_info` VALUES ('2', '1', '', '', '25.00', '18.00', '20.00', '0');
INSERT INTO `pd_product_info` VALUES ('3', '1', '', '', '25.00', '18.00', '20.00', '1');
INSERT INTO `pd_product_info` VALUES ('4', '2', '1234567890', '1234567891', '4000.00', '3000.00', '3000.00', '3');
INSERT INTO `pd_product_info` VALUES ('5', '3', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('6', '4', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('7', '5', '111111', '111111', '123.00', '123.00', '123.00', '0');
INSERT INTO `pd_product_info` VALUES ('8', '6', '', '', '0.00', '0.00', '0.00', '3');
INSERT INTO `pd_product_info` VALUES ('9', '6', '123123', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('10', '6', '123123', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('11', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('12', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('13', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('14', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('15', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('16', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('17', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('18', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('19', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('20', '6', '123', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('21', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('22', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('23', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('24', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('25', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('26', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('27', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('28', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('29', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('30', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('31', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('32', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('33', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('34', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('35', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('36', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('37', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('38', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('39', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('40', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('41', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('42', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('43', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('44', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('45', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('46', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('47', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('48', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('49', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('50', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('51', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('52', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('53', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('54', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('55', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('56', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('57', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('58', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('59', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('60', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('61', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('62', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('63', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('64', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('65', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('66', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('67', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('68', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('69', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('70', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('71', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('72', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('73', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('74', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('75', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('76', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('77', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('78', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('79', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('80', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('81', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('82', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('83', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('84', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('85', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('86', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('87', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('88', '6', '', '', '0.00', '0.00', '0.00', '0');
INSERT INTO `pd_product_info` VALUES ('89', '6', '', '', '0.00', '0.00', '0.00', '0');

-- ----------------------------
-- Table structure for `pd_product_spec`
-- ----------------------------
DROP TABLE IF EXISTS `pd_product_spec`;
CREATE TABLE `pd_product_spec` (
  `spec_id` int(10) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) NOT NULL COMMENT '产品类别id',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `spec_val` varchar(255) NOT NULL COMMENT '规格值',
  PRIMARY KEY (`spec_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='产品规格分类表';

-- ----------------------------
-- Records of pd_product_spec
-- ----------------------------
INSERT INTO `pd_product_spec` VALUES ('5', '1', '支重', '30-31,31-32,32-33,33-34');
INSERT INTO `pd_product_spec` VALUES ('2', '1', '规格', '槽钢8#,槽钢10#,槽钢12#');
INSERT INTO `pd_product_spec` VALUES ('4', '1', '长度', '6M,9M,12M');
INSERT INTO `pd_product_spec` VALUES ('6', '1', '12111', 'aaa,bbb,ccc');

-- ----------------------------
-- Table structure for `pd_product_spec_value`
-- ----------------------------
DROP TABLE IF EXISTS `pd_product_spec_value`;
CREATE TABLE `pd_product_spec_value` (
  `value_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_info_id` int(10) NOT NULL COMMENT '产品id',
  `spec_name` varchar(64) NOT NULL COMMENT '规格名称',
  `spec_value` varchar(64) NOT NULL COMMENT '规格值',
  PRIMARY KEY (`value_id`)
) ENGINE=MyISAM AUTO_INCREMENT=329 DEFAULT CHARSET=utf8 COMMENT='产品多规格值记录表';

-- ----------------------------
-- Records of pd_product_spec_value
-- ----------------------------
INSERT INTO `pd_product_spec_value` VALUES ('1', '2', '厚度', '11');
INSERT INTO `pd_product_spec_value` VALUES ('2', '2', '材质', '922钢');
INSERT INTO `pd_product_spec_value` VALUES ('3', '3', '厚度', '11');
INSERT INTO `pd_product_spec_value` VALUES ('4', '3', '材质', '彩钢');
INSERT INTO `pd_product_spec_value` VALUES ('5', '9', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('6', '9', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('7', '9', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('8', '9', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('9', '10', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('10', '10', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('11', '10', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('12', '10', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('13', '11', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('14', '11', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('15', '11', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('16', '11', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('17', '12', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('18', '12', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('19', '12', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('20', '12', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('21', '13', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('22', '13', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('23', '13', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('24', '13', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('25', '14', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('26', '14', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('27', '14', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('28', '14', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('29', '15', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('30', '15', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('31', '15', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('32', '15', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('33', '16', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('34', '16', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('35', '16', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('36', '16', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('37', '17', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('38', '17', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('39', '17', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('40', '17', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('41', '18', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('42', '18', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('43', '18', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('44', '18', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('45', '19', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('46', '19', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('47', '19', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('48', '19', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('49', '20', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('50', '20', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('51', '20', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('52', '20', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('53', '21', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('54', '21', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('55', '21', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('56', '21', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('57', '22', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('58', '22', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('59', '22', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('60', '22', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('61', '23', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('62', '23', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('63', '23', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('64', '23', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('65', '24', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('66', '24', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('67', '24', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('68', '24', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('69', '25', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('70', '25', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('71', '25', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('72', '25', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('73', '26', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('74', '26', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('75', '26', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('76', '26', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('77', '27', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('78', '27', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('79', '27', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('80', '27', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('81', '28', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('82', '28', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('83', '28', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('84', '28', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('85', '29', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('86', '29', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('87', '29', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('88', '29', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('89', '30', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('90', '30', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('91', '30', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('92', '30', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('93', '31', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('94', '31', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('95', '31', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('96', '31', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('97', '32', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('98', '32', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('99', '32', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('100', '32', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('101', '33', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('102', '33', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('103', '33', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('104', '33', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('105', '34', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('106', '34', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('107', '34', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('108', '34', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('109', '35', '支重', '31-32');
INSERT INTO `pd_product_spec_value` VALUES ('110', '35', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('111', '35', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('112', '35', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('113', '36', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('114', '36', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('115', '36', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('116', '36', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('117', '37', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('118', '37', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('119', '37', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('120', '37', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('121', '38', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('122', '38', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('123', '38', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('124', '38', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('125', '39', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('126', '39', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('127', '39', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('128', '39', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('129', '40', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('130', '40', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('131', '40', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('132', '40', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('133', '41', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('134', '41', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('135', '41', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('136', '41', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('137', '42', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('138', '42', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('139', '42', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('140', '42', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('141', '43', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('142', '43', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('143', '43', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('144', '43', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('145', '44', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('146', '44', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('147', '44', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('148', '44', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('149', '45', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('150', '45', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('151', '45', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('152', '45', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('153', '46', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('154', '46', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('155', '46', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('156', '46', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('157', '47', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('158', '47', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('159', '47', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('160', '47', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('161', '48', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('162', '48', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('163', '48', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('164', '48', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('165', '49', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('166', '49', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('167', '49', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('168', '49', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('169', '50', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('170', '50', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('171', '50', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('172', '50', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('173', '51', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('174', '51', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('175', '51', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('176', '51', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('177', '52', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('178', '52', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('179', '52', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('180', '52', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('181', '53', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('182', '53', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('183', '53', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('184', '53', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('185', '54', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('186', '54', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('187', '54', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('188', '54', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('189', '55', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('190', '55', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('191', '55', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('192', '55', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('193', '56', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('194', '56', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('195', '56', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('196', '56', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('197', '57', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('198', '57', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('199', '57', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('200', '57', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('201', '58', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('202', '58', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('203', '58', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('204', '58', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('205', '59', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('206', '59', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('207', '59', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('208', '59', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('209', '60', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('210', '60', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('211', '60', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('212', '60', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('213', '61', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('214', '61', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('215', '61', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('216', '61', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('217', '62', '支重', '32-33');
INSERT INTO `pd_product_spec_value` VALUES ('218', '62', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('219', '62', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('220', '62', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('221', '63', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('222', '63', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('223', '63', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('224', '63', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('225', '64', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('226', '64', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('227', '64', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('228', '64', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('229', '65', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('230', '65', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('231', '65', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('232', '65', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('233', '66', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('234', '66', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('235', '66', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('236', '66', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('237', '67', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('238', '67', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('239', '67', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('240', '67', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('241', '68', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('242', '68', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('243', '68', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('244', '68', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('245', '69', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('246', '69', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('247', '69', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('248', '69', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('249', '70', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('250', '70', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('251', '70', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('252', '70', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('253', '71', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('254', '71', '规格', '槽钢8#');
INSERT INTO `pd_product_spec_value` VALUES ('255', '71', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('256', '71', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('257', '72', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('258', '72', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('259', '72', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('260', '72', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('261', '73', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('262', '73', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('263', '73', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('264', '73', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('265', '74', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('266', '74', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('267', '74', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('268', '74', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('269', '75', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('270', '75', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('271', '75', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('272', '75', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('273', '76', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('274', '76', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('275', '76', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('276', '76', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('277', '77', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('278', '77', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('279', '77', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('280', '77', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('281', '78', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('282', '78', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('283', '78', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('284', '78', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('285', '79', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('286', '79', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('287', '79', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('288', '79', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('289', '80', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('290', '80', '规格', '槽钢10#');
INSERT INTO `pd_product_spec_value` VALUES ('291', '80', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('292', '80', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('293', '81', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('294', '81', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('295', '81', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('296', '81', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('297', '82', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('298', '82', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('299', '82', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('300', '82', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('301', '83', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('302', '83', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('303', '83', '长度', '6M');
INSERT INTO `pd_product_spec_value` VALUES ('304', '83', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('305', '84', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('306', '84', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('307', '84', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('308', '84', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('309', '85', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('310', '85', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('311', '85', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('312', '85', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('313', '86', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('314', '86', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('315', '86', '长度', '9M');
INSERT INTO `pd_product_spec_value` VALUES ('316', '86', '12111', 'ccc');
INSERT INTO `pd_product_spec_value` VALUES ('317', '87', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('318', '87', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('319', '87', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('320', '87', '12111', 'aaa');
INSERT INTO `pd_product_spec_value` VALUES ('321', '88', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('322', '88', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('323', '88', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('324', '88', '12111', 'bbb');
INSERT INTO `pd_product_spec_value` VALUES ('325', '89', '支重', '33-34');
INSERT INTO `pd_product_spec_value` VALUES ('326', '89', '规格', '槽钢12#');
INSERT INTO `pd_product_spec_value` VALUES ('327', '89', '长度', '12M');
INSERT INTO `pd_product_spec_value` VALUES ('328', '89', '12111', 'ccc');

-- ----------------------------
-- Table structure for `pd_purchase`
-- ----------------------------
DROP TABLE IF EXISTS `pd_purchase`;
CREATE TABLE `pd_purchase` (
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_purchase
-- ----------------------------
INSERT INTO `pd_purchase` VALUES ('1', 'CGD_20190409-0001', '采购-1', '1', '1', '2700.00', '0.00', '0.00', '1', '1', '0', '', '1554739200', '1554813185', '1554813185');
INSERT INTO `pd_purchase` VALUES ('2', 'CGD_20190409-0002', '采购2', '1', '1', '3600.00', '0.00', '0.00', '1', '1', '0', '', '1554739200', '1554813687', '1554813687');
INSERT INTO `pd_purchase` VALUES ('3', 'CGD_20190410-0003', 'iphone', '1', '1', '600000.00', '4000.00', '0.00', '1', '1', '0', '', '1554825600', '1554871265', '1554871265');
INSERT INTO `pd_purchase` VALUES ('4', 'CGD_20190418-0004', 'CGD_20190418-0004', '1', '1', '17.82', '0.00', '1.00', '1', '1', '0', '', '1555516800', '1555550812', '1555550812');
INSERT INTO `pd_purchase` VALUES ('5', 'CGD_20190418-0005', 'admins', '1', '1', '915.00', '0.00', '50.00', '1', '1', '0', '', '1555516800', '1555566849', '1555566849');

-- ----------------------------
-- Table structure for `pd_purchase_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_purchase_product`;
CREATE TABLE `pd_purchase_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) NOT NULL COMMENT '采购单id',
  `product_info_id` int(10) NOT NULL COMMENT '产品id',
  `price_cost` decimal(16,2) NOT NULL COMMENT '采购单价',
  `price` decimal(16,2) NOT NULL COMMENT '建议售价',
  `price_discount` decimal(16,2) NOT NULL COMMENT '采购折扣单价',
  `count` int(10) NOT NULL COMMENT '产品数量',
  `discount` tinyint(2) NOT NULL COMMENT '折扣百分比',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='采购单关联产品信息表';

-- ----------------------------
-- Records of pd_purchase_product
-- ----------------------------
INSERT INTO `pd_purchase_product` VALUES ('1', '1', '2', '18.00', '25.00', '18.00', '100', '0');
INSERT INTO `pd_purchase_product` VALUES ('2', '1', '3', '18.00', '25.00', '18.00', '50', '0');
INSERT INTO `pd_purchase_product` VALUES ('3', '2', '2', '18.00', '25.00', '18.00', '100', '0');
INSERT INTO `pd_purchase_product` VALUES ('4', '2', '3', '18.00', '25.00', '18.00', '100', '0');
INSERT INTO `pd_purchase_product` VALUES ('5', '3', '4', '3000.00', '4000.00', '3000.00', '200', '0');
INSERT INTO `pd_purchase_product` VALUES ('6', '4', '2', '18.00', '25.00', '18.00', '1', '0');
INSERT INTO `pd_purchase_product` VALUES ('7', '5', '7', '123.00', '123.00', '123.00', '10', '0');
INSERT INTO `pd_purchase_product` VALUES ('8', '5', '9', '20.00', '0.00', '20.00', '10', '0');
INSERT INTO `pd_purchase_product` VALUES ('9', '5', '10', '20.00', '0.00', '20.00', '10', '0');
INSERT INTO `pd_purchase_product` VALUES ('10', '5', '11', '20.00', '0.00', '20.00', '10', '0');

-- ----------------------------
-- Table structure for `pd_receivables`
-- ----------------------------
DROP TABLE IF EXISTS `pd_receivables`;
CREATE TABLE `pd_receivables` (
  `receivables_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '应收款id',
  `customer_id` int(10) NOT NULL DEFAULT '0' COMMENT '客户id / 供应商id',
  `payer` varchar(500) NOT NULL COMMENT '付款单位',
  `contract_id` int(10) NOT NULL DEFAULT '0' COMMENT '合同id / 采购单id',
  `type` int(10) NOT NULL DEFAULT '0' COMMENT '0：普通  1：销售  2：采购退货（0、1是之前有的，0没找到使用场景）',
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='应收款表';

-- ----------------------------
-- Records of pd_receivables
-- ----------------------------
INSERT INTO `pd_receivables` VALUES ('1', '1', '', '1', '1', 'M_20190409-0001', '50.00', '1', '1', '', '1554739200', '1554813046', '2', '1554813046', '0', '0', '0', 'M_');
INSERT INTO `pd_receivables` VALUES ('2', '1', '', '2', '1', 'M_20190410-0002', '25.00', '1', '1', '', '1554825600', '1554869397', '2', '1554869397', '0', '0', '0', 'M_');
INSERT INTO `pd_receivables` VALUES ('3', '2', '', '3', '1', 'M_20190410-0003', '40000.00', '1', '1', '', '1554825600', '1554872583', '2', '1554872583', '0', '0', '0', 'M_');
INSERT INTO `pd_receivables` VALUES ('4', '1', '', '4', '1', 'M_20190411-0004', '435.00', '1', '1', '', '1554912000', '1554945275', '0', '1554945275', '0', '0', '0', 'M_');

-- ----------------------------
-- Table structure for `pd_receivingorder`
-- ----------------------------
DROP TABLE IF EXISTS `pd_receivingorder`;
CREATE TABLE `pd_receivingorder` (
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
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`receivingorder_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='收款单';

-- ----------------------------
-- Records of pd_receivingorder
-- ----------------------------
INSERT INTO `pd_receivingorder` VALUES ('1', '20190410-0001', '25.00', '2', '0', '								', '1554825600', '1', '1', '1', '1554872269', '1554872217', '0', '0', '0', '2', '0', '', '0.00', '', '1', '62222002222', '1', '对公账户', '111', '1554872269');
INSERT INTO `pd_receivingorder` VALUES ('2', '20190410-0002', '50.00', '1', '0', '								', '1554825600', '1', '1', '1', '1554872325', '1554872309', '0', '0', '0', '1', '0', '', '0.00', '', '1', '62222002222', '1', '对公账户', '111', '1554872325');
INSERT INTO `pd_receivingorder` VALUES ('3', '20190410-0003', '40000.00', '3', '0', '								', '1554825600', '1', '1', '1', '1554872710', '1554872688', '0', '0', '0', '3', '0', '', '0.00', '', '1', '62222002222', '1', '对公账户', '111', '1554872710');
INSERT INTO `pd_receivingorder` VALUES ('4', '20190418-0004', '435.00', '4', '0', '', '1555516800', '1', '1', '0', '1555556670', '1555556670', '0', '0', '0', '4', '0', '', '0.00', '', '1', '62222002222', '0', '对公账户', '111', '0');

-- ----------------------------
-- Table structure for `pd_remind`
-- ----------------------------
DROP TABLE IF EXISTS `pd_remind`;
CREATE TABLE `pd_remind` (
  `remind_id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT '相关模块',
  `module_id` int(10) NOT NULL COMMENT '相关模块ID',
  `remind_time` int(10) NOT NULL COMMENT '提醒时间',
  `content` varchar(500) NOT NULL COMMENT '提醒内容',
  `create_role_id` int(10) NOT NULL COMMENT '提醒人ID',
  `is_remind` tinyint(1) NOT NULL COMMENT '1已提醒',
  PRIMARY KEY (`remind_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='相关提醒表';

-- ----------------------------
-- Records of pd_remind
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_role`
-- ----------------------------
DROP TABLE IF EXISTS `pd_role`;
CREATE TABLE `pd_role` (
  `role_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '岗位id',
  `position_id` int(10) NOT NULL COMMENT '岗位组名',
  `user_id` int(10) NOT NULL COMMENT '员工id',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='本表存放用户岗位信息';

-- ----------------------------
-- Records of pd_role
-- ----------------------------
INSERT INTO `pd_role` VALUES ('1', '1', '1');
INSERT INTO `pd_role` VALUES ('2', '2', '2');
INSERT INTO `pd_role` VALUES ('3', '3', '3');

-- ----------------------------
-- Table structure for `pd_role_department`
-- ----------------------------
DROP TABLE IF EXISTS `pd_role_department`;
CREATE TABLE `pd_role_department` (
  `department_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `parent_id` int(10) NOT NULL COMMENT '父类部门id',
  `name` varchar(50) NOT NULL COMMENT '部门名',
  `charge_position` int(10) NOT NULL COMMENT '部门最高级别岗位',
  `description` varchar(200) NOT NULL COMMENT '部门描述',
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='本表存放部门信息';

-- ----------------------------
-- Records of pd_role_department
-- ----------------------------
INSERT INTO `pd_role_department` VALUES ('1', '0', '办公室', '0', '公司文档管理、财务管理');
INSERT INTO `pd_role_department` VALUES ('2', '1', '销售部', '0', '');
INSERT INTO `pd_role_department` VALUES ('3', '2', '111', '0', '');
INSERT INTO `pd_role_department` VALUES ('4', '3', '1111', '0', '');

-- ----------------------------
-- Table structure for `pd_route`
-- ----------------------------
DROP TABLE IF EXISTS `pd_route`;
CREATE TABLE `pd_route` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `wifi_name` varchar(255) NOT NULL COMMENT 'wifi名称',
  `mac_address` varchar(255) NOT NULL COMMENT 'mac地址',
  `create_role_id` int(10) NOT NULL COMMENT '创建人',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='考勤路由';

-- ----------------------------
-- Records of pd_route
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_business_contacts`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_business_contacts`;
CREATE TABLE `pd_r_business_contacts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `contacts_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_business_contacts
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_business_contract`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_business_contract`;
CREATE TABLE `pd_r_business_contract` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL COMMENT '商机id',
  `contract_id` int(10) NOT NULL COMMENT '合同id',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='商机合同关系表';

-- ----------------------------
-- Records of pd_r_business_contract
-- ----------------------------
INSERT INTO `pd_r_business_contract` VALUES ('1', '0', '1');
INSERT INTO `pd_r_business_contract` VALUES ('2', '0', '2');
INSERT INTO `pd_r_business_contract` VALUES ('3', '0', '3');
INSERT INTO `pd_r_business_contract` VALUES ('4', '0', '4');
INSERT INTO `pd_r_business_contract` VALUES ('5', '1', '5');
INSERT INTO `pd_r_business_contract` VALUES ('6', '0', '6');
INSERT INTO `pd_r_business_contract` VALUES ('7', '0', '7');
INSERT INTO `pd_r_business_contract` VALUES ('8', '0', '8');
INSERT INTO `pd_r_business_contract` VALUES ('9', '0', '9');

-- ----------------------------
-- Table structure for `pd_r_business_customer`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_business_customer`;
CREATE TABLE `pd_r_business_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_business_customer
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_business_file`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_business_file`;
CREATE TABLE `pd_r_business_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `file_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_business_file
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_business_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_business_log`;
CREATE TABLE `pd_r_business_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  `contacts_id` int(10) NOT NULL COMMENT '客户联系人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='商机和日志id对应关系表';

-- ----------------------------
-- Records of pd_r_business_log
-- ----------------------------
INSERT INTO `pd_r_business_log` VALUES ('1', '1', '1', '0');

-- ----------------------------
-- Table structure for `pd_r_business_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_business_product`;
CREATE TABLE `pd_r_business_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_id` int(10) NOT NULL,
  `product_info_id` int(10) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_business_product
-- ----------------------------
INSERT INTO `pd_r_business_product` VALUES ('1', '1', '2', '25.00', '0.00', '25.00', '0.00', '0.00', '1', '块', '25.00', '');
INSERT INTO `pd_r_business_product` VALUES ('2', '1', '5', '0.00', '0.00', '0.00', '0.00', '0.00', '1', '个', '0.00', '');

-- ----------------------------
-- Table structure for `pd_r_business_status`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_business_status`;
CREATE TABLE `pd_r_business_status` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='商机状态阶段表';

-- ----------------------------
-- Records of pd_r_business_status
-- ----------------------------
INSERT INTO `pd_r_business_status` VALUES ('1', '1', '0', '3', '', '0', '1', '1554945158', '1', '35.00');
INSERT INTO `pd_r_business_status` VALUES ('2', '1', '0', '98', '', '0', '1', '1555469652', '1', '25.00');
INSERT INTO `pd_r_business_status` VALUES ('3', '1', '0', '100', '', '0', '1', '1555469674', '1', '25.00');
INSERT INTO `pd_r_business_status` VALUES ('4', '1', '0', '100', '', '0', '1', '1555469932', '1', '25.00');

-- ----------------------------
-- Table structure for `pd_r_contacts_customer`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_contacts_customer`;
CREATE TABLE `pd_r_contacts_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contacts_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_contacts_customer
-- ----------------------------
INSERT INTO `pd_r_contacts_customer` VALUES ('1', '1', '3');

-- ----------------------------
-- Table structure for `pd_r_contract_file`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_contract_file`;
CREATE TABLE `pd_r_contract_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL COMMENT '合同id',
  `file_id` int(10) NOT NULL COMMENT '文件id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同文件关系表';

-- ----------------------------
-- Records of pd_r_contract_file
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_contract_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_contract_log`;
CREATE TABLE `pd_r_contract_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL COMMENT '合同ID',
  `log_id` int(10) NOT NULL COMMENT '日志ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='合同日志表';

-- ----------------------------
-- Records of pd_r_contract_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_contract_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_contract_product`;
CREATE TABLE `pd_r_contract_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `sales_price` float(10,2) NOT NULL,
  `estimate_price` float(10,2) NOT NULL,
  `amount` int(10) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_contract_product
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_contract_sales`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_contract_sales`;
CREATE TABLE `pd_r_contract_sales` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL COMMENT '合同id',
  `sales_id` int(10) NOT NULL COMMENT '销售单id',
  `distribution_id` int(10) NOT NULL COMMENT '待配货ID',
  `sales_type` int(10) NOT NULL COMMENT '0销售1退货2待配货',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='合同与销售单关系表';

-- ----------------------------
-- Records of pd_r_contract_sales
-- ----------------------------
INSERT INTO `pd_r_contract_sales` VALUES ('1', '1', '1', '0', '0');
INSERT INTO `pd_r_contract_sales` VALUES ('2', '2', '2', '0', '0');
INSERT INTO `pd_r_contract_sales` VALUES ('3', '3', '3', '0', '0');
INSERT INTO `pd_r_contract_sales` VALUES ('4', '4', '4', '0', '0');
INSERT INTO `pd_r_contract_sales` VALUES ('5', '5', '5', '0', '0');
INSERT INTO `pd_r_contract_sales` VALUES ('6', '6', '6', '0', '0');
INSERT INTO `pd_r_contract_sales` VALUES ('7', '7', '7', '0', '0');
INSERT INTO `pd_r_contract_sales` VALUES ('8', '8', '8', '0', '0');
INSERT INTO `pd_r_contract_sales` VALUES ('9', '9', '9', '0', '0');

-- ----------------------------
-- Table structure for `pd_r_customer_file`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_customer_file`;
CREATE TABLE `pd_r_customer_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL,
  `file_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_customer_file
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_customer_invoice`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_customer_invoice`;
CREATE TABLE `pd_r_customer_invoice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL COMMENT '客户ID',
  `invoice_header` varchar(255) NOT NULL COMMENT '开票抬头',
  `taxes_num` varchar(255) NOT NULL COMMENT '纳税识别号',
  `opening_bank` varchar(255) NOT NULL COMMENT '开户行',
  `account_number` varchar(255) NOT NULL COMMENT '开户账号',
  `billing_address` varchar(500) NOT NULL COMMENT '开票地址',
  `telephone` varchar(50) NOT NULL COMMENT '电话',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `create_role_id` int(10) NOT NULL COMMENT '创建人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户发票信息表';

-- ----------------------------
-- Records of pd_r_customer_invoice
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_customer_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_customer_log`;
CREATE TABLE `pd_r_customer_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  `contacts_id` int(10) NOT NULL COMMENT '客户联系人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_customer_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_exam`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_exam`;
CREATE TABLE `pd_r_exam` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) NOT NULL COMMENT '模块ID: pd_exam_type',
  `order_id` int(10) NOT NULL COMMENT '对应订单id',
  `exam_status` tinyint(1) NOT NULL COMMENT '审批结果：0：待审；1审批中；2通过；3驳回',
  `role_ids` varchar(255) NOT NULL COMMENT '审批人role_id',
  `step_id` int(10) NOT NULL COMMENT '审批步骤表id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_r_exam
-- ----------------------------
INSERT INTO `pd_r_exam` VALUES ('1', '1', '1', '2', ',1,', '0');
INSERT INTO `pd_r_exam` VALUES ('2', '1', '2', '2', ',1,', '0');
INSERT INTO `pd_r_exam` VALUES ('3', '1', '3', '2', ',1,', '0');
INSERT INTO `pd_r_exam` VALUES ('4', '4', '1', '2', ',1,', '0');
INSERT INTO `pd_r_exam` VALUES ('5', '4', '2', '2', ',1,', '0');
INSERT INTO `pd_r_exam` VALUES ('6', '4', '3', '2', ',1,', '0');
INSERT INTO `pd_r_exam` VALUES ('7', '1', '4', '0', ',2,', '0');
INSERT INTO `pd_r_exam` VALUES ('8', '1', '5', '0', ',3,', '0');

-- ----------------------------
-- Table structure for `pd_r_file_finance`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_file_finance`;
CREATE TABLE `pd_r_file_finance` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `finance_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_file_finance
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_file_invoice`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_file_invoice`;
CREATE TABLE `pd_r_file_invoice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) NOT NULL COMMENT '发票ID',
  `file_id` int(10) NOT NULL COMMENT '附件ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='发票附件关系表';

-- ----------------------------
-- Records of pd_r_file_invoice
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_file_leads`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_file_leads`;
CREATE TABLE `pd_r_file_leads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `leads_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件和日志对应关系';

-- ----------------------------
-- Records of pd_r_file_leads
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_file_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_file_log`;
CREATE TABLE `pd_r_file_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件和日志对应关系表';

-- ----------------------------
-- Records of pd_r_file_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_file_market`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_file_market`;
CREATE TABLE `pd_r_file_market` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `market_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of pd_r_file_market
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_file_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_file_product`;
CREATE TABLE `pd_r_file_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_file_product
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_finance_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_finance_log`;
CREATE TABLE `pd_r_finance_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `finance_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_finance_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_leads_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_leads_log`;
CREATE TABLE `pd_r_leads_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `leads_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_leads_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_log_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_log_product`;
CREATE TABLE `pd_r_log_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `log_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_r_log_product
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_market_customer`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_market_customer`;
CREATE TABLE `pd_r_market_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `market_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of pd_r_market_customer
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_market_leads`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_market_leads`;
CREATE TABLE `pd_r_market_leads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `market_id` int(10) NOT NULL,
  `leads_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of pd_r_market_leads
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_market_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_market_log`;
CREATE TABLE `pd_r_market_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `market_id` int(10) NOT NULL,
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of pd_r_market_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_member_file`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_member_file`;
CREATE TABLE `pd_r_member_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '客户ID',
  `file_id` int(10) NOT NULL COMMENT '附件ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线上客户附件关系表';

-- ----------------------------
-- Records of pd_r_member_file
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_member_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_member_log`;
CREATE TABLE `pd_r_member_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '线上客户ID',
  `log_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='线上客户日志表';

-- ----------------------------
-- Records of pd_r_member_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_order_log`;
CREATE TABLE `pd_r_order_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `log_id` int(10) NOT NULL COMMENT '日志ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单进度日志表';

-- ----------------------------
-- Records of pd_r_order_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_order_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_order_product`;
CREATE TABLE `pd_r_order_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `product_id` int(10) NOT NULL COMMENT '产品ID',
  `unit_price` decimal(10,2) NOT NULL COMMENT '单价',
  `ori_price` decimal(10,2) NOT NULL COMMENT '建议售价',
  `amount` int(10) NOT NULL COMMENT '数量',
  `unit` varchar(50) NOT NULL COMMENT '单位',
  `subtotal` decimal(10,2) NOT NULL COMMENT '小计',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单产品关系表';

-- ----------------------------
-- Records of pd_r_order_product
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_r_task_file`
-- ----------------------------
DROP TABLE IF EXISTS `pd_r_task_file`;
CREATE TABLE `pd_r_task_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) NOT NULL COMMENT '任务ID',
  `file_id` int(10) NOT NULL COMMENT '附件ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务附件关系表';

-- ----------------------------
-- Records of pd_r_task_file
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_sales`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sales`;
CREATE TABLE `pd_sales` (
  `sales_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '销售单id',
  `customer_id` int(10) NOT NULL COMMENT '客户id',
  `creator_role_id` int(10) NOT NULL COMMENT '制单人',
  `sn_code` varchar(20) NOT NULL COMMENT '销售单序列号',
  `subject` varchar(100) NOT NULL COMMENT '主题',
  `prime_price` decimal(18,2) NOT NULL COMMENT '销售单整体价格未减折扣额时价格',
  `final_discount_rate` decimal(10,2) NOT NULL COMMENT '折扣率',
  `sales_price` decimal(18,2) NOT NULL COMMENT '折扣后销售单实际应付金额',
  `total_amount` int(10) NOT NULL COMMENT '总数量',
  `type` int(1) NOT NULL COMMENT '0：销售   1：退货',
  `status` int(10) NOT NULL COMMENT '97：未出库 98： 已出库 99：未入库   100：已入库',
  `is_checked` int(1) NOT NULL COMMENT '0：未审核   1：审核',
  `shipping_address` varchar(300) DEFAULT NULL COMMENT '发货地址',
  `discount_price` decimal(18,2) NOT NULL COMMENT '折扣额',
  `description` varchar(500) NOT NULL,
  `create_time` int(10) NOT NULL,
  `sales_time` int(10) NOT NULL COMMENT '销售日期',
  `outof_time` int(10) NOT NULL COMMENT '出库时间',
  `logistics_number` varchar(100) NOT NULL COMMENT '物流单号',
  `receiving_people` varchar(50) NOT NULL COMMENT '收件人',
  `receiving_phone` varchar(20) NOT NULL COMMENT '收件电话',
  `check_role_id` int(11) NOT NULL COMMENT '审核人',
  `owner_role_id` int(10) NOT NULL COMMENT '负责人ID',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`sales_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='销售表';

-- ----------------------------
-- Records of pd_sales
-- ----------------------------
INSERT INTO `pd_sales` VALUES ('1', '1', '1', 'XSD_20190409-0001', '', '50.00', '0.00', '50.00', '2', '0', '97', '1', null, '0.00', '', '1554813003', '1554813003', '0', '', '', '', '0', '1', '0');
INSERT INTO `pd_sales` VALUES ('2', '1', '1', 'XSD_20190410-0002', '', '25.00', '0.00', '25.00', '1', '0', '97', '1', null, '0.00', '', '1554869149', '1554869149', '0', '', '', '', '0', '1', '0');
INSERT INTO `pd_sales` VALUES ('3', '2', '1', 'XSD_20190410-0003', '', '40000.00', '0.00', '40000.00', '10', '0', '97', '1', null, '0.00', '', '1554872557', '1554872557', '0', '', '', '', '0', '1', '0');
INSERT INTO `pd_sales` VALUES ('4', '1', '1', 'XSD_20190411-0004', '', '0.00', '0.00', '0.00', '0', '0', '97', '1', null, '0.00', '', '1554944787', '1554944787', '0', '', '', '', '0', '1', '0');
INSERT INTO `pd_sales` VALUES ('5', '1', '1', 'XSD_20190417-0005', '', '0.00', '0.00', '25.00', '0', '0', '97', '0', null, '0.00', '', '1555469849', '1555469849', '0', '', '', '', '0', '1', '0');
INSERT INTO `pd_sales` VALUES ('6', '3', '1', 'XSD_20190418-0006', '', '0.00', '0.00', '200.00', '0', '0', '97', '0', null, '0.00', '', '1555552941', '1555552941', '0', '', '', '', '0', '1', '0');
INSERT INTO `pd_sales` VALUES ('7', '1', '1', 'XSD_20190418-0007', '', '0.00', '0.00', '200.00', '0', '0', '97', '0', null, '0.00', '', '1555553075', '1555553075', '0', '', '', '', '0', '1', '0');
INSERT INTO `pd_sales` VALUES ('8', '3', '1', 'XSD_20190418-0008', '', '0.00', '0.00', '0.00', '2', '0', '97', '0', null, '0.00', '', '1555553519', '1555553519', '0', '', '', '', '0', '1', '0');
INSERT INTO `pd_sales` VALUES ('9', '3', '1', 'XSD_20190418-0009', '', '220.00', '0.00', '220.00', '2', '0', '97', '0', null, '0.00', 'asdasdasdasd', '1555567119', '1555567119', '0', '', '', '', '0', '1', '0');

-- ----------------------------
-- Table structure for `pd_sales_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sales_log`;
CREATE TABLE `pd_sales_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) NOT NULL COMMENT '销售单ID',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `content` varchar(200) NOT NULL COMMENT '物流详情',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_sales_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_sales_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sales_product`;
CREATE TABLE `pd_sales_product` (
  `sales_product_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '销售单商品id',
  `sales_id` int(10) NOT NULL COMMENT '销售单id',
  `product_info_id` int(10) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='销售单商品表';

-- ----------------------------
-- Records of pd_sales_product
-- ----------------------------
INSERT INTO `pd_sales_product` VALUES ('1', '1', '2', '0', '1', '25.00', '25.00', '块', '20.00', '0.00', '0', '', '25.00');
INSERT INTO `pd_sales_product` VALUES ('2', '1', '3', '0', '1', '25.00', '25.00', '块', '20.00', '0.00', '0', '', '25.00');
INSERT INTO `pd_sales_product` VALUES ('3', '2', '2', '0', '1', '25.00', '25.00', '块', '20.00', '0.00', '0', '', '25.00');
INSERT INTO `pd_sales_product` VALUES ('4', '3', '4', '0', '10', '4000.00', '4000.00', '个', '3000.00', '0.00', '0', '', '40000.00');
INSERT INTO `pd_sales_product` VALUES ('5', '5', '2', '0', '1', '25.00', '25.00', '块', '20.00', '0.00', '0', '', '25.00');
INSERT INTO `pd_sales_product` VALUES ('6', '5', '5', '0', '1', '0.00', '0.00', '个', '0.00', '0.00', '0', '', '0.00');
INSERT INTO `pd_sales_product` VALUES ('7', '8', '5', '0', '1', '0.00', '0.00', '个', '0.00', '0.00', '0', '', '0.00');
INSERT INTO `pd_sales_product` VALUES ('8', '8', '6', '0', '1', '0.00', '0.00', '块', '0.00', '0.00', '0', '', '0.00');
INSERT INTO `pd_sales_product` VALUES ('9', '9', '9', '0', '1', '0.00', '110.00', '箱', '0.00', '0.00', '0', '', '110.00');
INSERT INTO `pd_sales_product` VALUES ('10', '9', '10', '0', '1', '0.00', '110.00', '箱', '0.00', '0.00', '0', '', '110.00');

-- ----------------------------
-- Table structure for `pd_scene`
-- ----------------------------
DROP TABLE IF EXISTS `pd_scene`;
CREATE TABLE `pd_scene` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL COMMENT '模块',
  `name` varchar(255) NOT NULL COMMENT '场景名称',
  `role_id` int(10) NOT NULL,
  `order_id` int(10) NOT NULL COMMENT '排序ID',
  `data` text NOT NULL COMMENT '属性值',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `is_hide` int(1) NOT NULL COMMENT '1隐藏',
  `type` tinyint(1) NOT NULL COMMENT '1系统0自定义',
  `by` varchar(50) NOT NULL COMMENT '系统参数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='自定义场景';

-- ----------------------------
-- Records of pd_scene
-- ----------------------------
INSERT INTO `pd_scene` VALUES ('1', 'customer', '我的客户', '0', '0', '', '0', '0', '0', '1', 'me');
INSERT INTO `pd_scene` VALUES ('2', 'customer', '下属客户', '0', '1', '', '0', '0', '0', '1', 'sub');
INSERT INTO `pd_scene` VALUES ('3', 'customer', '全部客户', '0', '3', '', '0', '0', '0', '1', 'all');
INSERT INTO `pd_scene` VALUES ('4', 'customer', '共享给我的', '0', '5', '', '0', '0', '0', '1', 'share');
INSERT INTO `pd_scene` VALUES ('5', 'customer', '我共享的', '0', '6', '', '0', '0', '0', '1', 'myshare');
INSERT INTO `pd_scene` VALUES ('6', 'contract', '我的合同', '0', '0', '', '0', '0', '0', '1', 'me');
INSERT INTO `pd_scene` VALUES ('7', 'contract', '下属合同', '0', '1', '', '0', '0', '0', '1', 'sub');
INSERT INTO `pd_scene` VALUES ('8', 'contract', '全部合同', '0', '3', '', '0', '0', '0', '1', 'all');
INSERT INTO `pd_scene` VALUES ('9', 'leads', '我的线索', '0', '0', '', '0', '0', '0', '1', 'me');
INSERT INTO `pd_scene` VALUES ('10', 'leads', '下属线索', '0', '1', '', '0', '0', '0', '1', 'sub');
INSERT INTO `pd_scene` VALUES ('11', 'leads', '全部线索', '0', '3', '', '0', '0', '0', '1', 'all');
INSERT INTO `pd_scene` VALUES ('12', 'business', '我的商机', '0', '0', '', '0', '0', '0', '1', 'me');
INSERT INTO `pd_scene` VALUES ('13', 'business', '下属商机', '0', '1', '', '0', '0', '0', '1', 'sub');
INSERT INTO `pd_scene` VALUES ('14', 'business', '全部商机', '0', '3', '', '0', '0', '0', '1', 'all');

-- ----------------------------
-- Table structure for `pd_scene_default`
-- ----------------------------
DROP TABLE IF EXISTS `pd_scene_default`;
CREATE TABLE `pd_scene_default` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT '模块',
  `role_id` int(10) NOT NULL,
  `scene_id` int(10) NOT NULL COMMENT '场景ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工默认场景关系表';

-- ----------------------------
-- Records of pd_scene_default
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_sign`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sign`;
CREATE TABLE `pd_sign` (
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_sign
-- ----------------------------
INSERT INTO `pd_sign` VALUES ('1', '0', '2', '34.754601', '113.648376', '河南省郑州市二七区大学路街道中原东路91附2', '', '1554875028', '河南省公路工程局集团有限公司(中原东路)', '0');
INSERT INTO `pd_sign` VALUES ('2', '0', '2', '34.756599', '113.650131', '郑州市二七区大学北路郑州大学医学院-西门', '', '1555422191', '凯凯饭店(大学路店)', '0');

-- ----------------------------
-- Table structure for `pd_sign_img`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sign_img`;
CREATE TABLE `pd_sign_img` (
  `img_id` int(10) NOT NULL AUTO_INCREMENT,
  `sign_id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '图片上传时名字',
  `save_name` varchar(100) NOT NULL COMMENT '图片保存名',
  `path` varchar(200) NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`img_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_sign_img
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_sms_record`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sms_record`;
CREATE TABLE `pd_sms_record` (
  `sms_record_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL COMMENT '发件人',
  `telephone` text NOT NULL COMMENT '发送号码',
  `content` text NOT NULL COMMENT '发送内容',
  `sendtime` int(10) NOT NULL COMMENT '发送时间',
  `phone_counts` int(11) NOT NULL COMMENT '短信数',
  PRIMARY KEY (`sms_record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短信发送记录表';

-- ----------------------------
-- Records of pd_sms_record
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_sms_template`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sms_template`;
CREATE TABLE `pd_sms_template` (
  `template_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `subject` varchar(200) NOT NULL COMMENT '主题',
  `content` varchar(500) NOT NULL COMMENT '内容',
  `order_id` int(4) NOT NULL COMMENT '顺序id',
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='短信模板';

-- ----------------------------
-- Records of pd_sms_template
-- ----------------------------
INSERT INTO `pd_sms_template` VALUES ('1', '默认模板', '有一个特别的日子，鲜花都为你展现；有一个特殊的日期，阳光都为你温暖；有一个美好的时刻，百灵都为你欢颜；有一个难忘的今天，亲朋都为你祝愿；那就是今天是你的生日，祝你幸福安康顺意连年！', '1');

-- ----------------------------
-- Table structure for `pd_sn`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sn`;
CREATE TABLE `pd_sn` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_sn
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_sn_log`
-- ----------------------------
DROP TABLE IF EXISTS `pd_sn_log`;
CREATE TABLE `pd_sn_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sn_id` int(10) NOT NULL COMMENT 'SN_ID',
  `type` tinyint(1) NOT NULL COMMENT '1：入库；2：出库',
  `type_id` int(10) NOT NULL COMMENT '关联ID 出库stock_out_id 入库 stock_in_id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of pd_sn_log
-- ----------------------------
INSERT INTO `pd_sn_log` VALUES ('1', '0', '2', '1');
INSERT INTO `pd_sn_log` VALUES ('2', '0', '2', '1');
INSERT INTO `pd_sn_log` VALUES ('3', '0', '2', '2');
INSERT INTO `pd_sn_log` VALUES ('4', '0', '2', '3');
INSERT INTO `pd_sn_log` VALUES ('5', '0', '2', '4');
INSERT INTO `pd_sn_log` VALUES ('6', '0', '2', '5');

-- ----------------------------
-- Table structure for `pd_stock`
-- ----------------------------
DROP TABLE IF EXISTS `pd_stock`;
CREATE TABLE `pd_stock` (
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
) ENGINE=MyISAM AUTO_INCREMENT=179 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='库存';

-- ----------------------------
-- Records of pd_stock
-- ----------------------------
INSERT INTO `pd_stock` VALUES ('1', '1', '1', '0', '0', '0', '0', '0', '0', '1554813613');
INSERT INTO `pd_stock` VALUES ('2', '2', '1', '0', '0', '0', '0', '0', '1554869460', '1554813613');
INSERT INTO `pd_stock` VALUES ('3', '3', '1', '0', '0', '0', '0', '0', '1554874645', '1554813613');
INSERT INTO `pd_stock` VALUES ('4', '4', '1', '0', '0', '0', '0', '100', '1554875446', '1554871225');
INSERT INTO `pd_stock` VALUES ('5', '1', '2', '0', '0', '0', '0', '0', '0', '1554872451');
INSERT INTO `pd_stock` VALUES ('6', '2', '2', '0', '0', '0', '0', '0', '0', '1554872451');
INSERT INTO `pd_stock` VALUES ('7', '3', '2', '0', '0', '0', '0', '1', '1554874729', '1554872451');
INSERT INTO `pd_stock` VALUES ('8', '4', '2', '0', '0', '0', '0', '90', '1554875570', '1554872451');
INSERT INTO `pd_stock` VALUES ('9', '5', '1', '0', '0', '0', '0', '0', '0', '1554953184');
INSERT INTO `pd_stock` VALUES ('10', '5', '2', '0', '0', '0', '0', '0', '0', '1554953184');
INSERT INTO `pd_stock` VALUES ('11', '6', '1', '0', '0', '0', '0', '0', '0', '1554953296');
INSERT INTO `pd_stock` VALUES ('12', '6', '2', '0', '0', '0', '0', '0', '0', '1554953296');
INSERT INTO `pd_stock` VALUES ('13', '7', '1', '0', '0', '0', '0', '0', '0', '1555565990');
INSERT INTO `pd_stock` VALUES ('14', '7', '2', '0', '0', '0', '0', '0', '0', '1555565990');
INSERT INTO `pd_stock` VALUES ('15', '8', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('16', '8', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('17', '9', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('18', '9', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('19', '10', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('20', '10', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('21', '11', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('22', '11', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('23', '12', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('24', '12', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('25', '13', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('26', '13', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('27', '14', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('28', '14', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('29', '15', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('30', '15', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('31', '16', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('32', '16', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('33', '17', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('34', '17', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('35', '18', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('36', '18', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('37', '19', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('38', '19', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('39', '20', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('40', '20', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('41', '21', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('42', '21', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('43', '22', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('44', '22', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('45', '23', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('46', '23', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('47', '24', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('48', '24', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('49', '25', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('50', '25', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('51', '26', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('52', '26', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('53', '27', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('54', '27', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('55', '28', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('56', '28', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('57', '29', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('58', '29', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('59', '30', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('60', '30', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('61', '31', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('62', '31', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('63', '32', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('64', '32', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('65', '33', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('66', '33', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('67', '34', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('68', '34', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('69', '35', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('70', '35', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('71', '36', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('72', '36', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('73', '37', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('74', '37', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('75', '38', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('76', '38', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('77', '39', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('78', '39', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('79', '40', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('80', '40', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('81', '41', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('82', '41', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('83', '42', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('84', '42', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('85', '43', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('86', '43', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('87', '44', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('88', '44', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('89', '45', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('90', '45', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('91', '46', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('92', '46', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('93', '47', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('94', '47', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('95', '48', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('96', '48', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('97', '49', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('98', '49', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('99', '50', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('100', '50', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('101', '51', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('102', '51', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('103', '52', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('104', '52', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('105', '53', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('106', '53', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('107', '54', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('108', '54', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('109', '55', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('110', '55', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('111', '56', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('112', '56', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('113', '57', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('114', '57', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('115', '58', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('116', '58', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('117', '59', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('118', '59', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('119', '60', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('120', '60', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('121', '61', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('122', '61', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('123', '62', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('124', '62', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('125', '63', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('126', '63', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('127', '64', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('128', '64', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('129', '65', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('130', '65', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('131', '66', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('132', '66', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('133', '67', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('134', '67', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('135', '68', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('136', '68', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('137', '69', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('138', '69', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('139', '70', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('140', '70', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('141', '71', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('142', '71', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('143', '72', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('144', '72', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('145', '73', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('146', '73', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('147', '74', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('148', '74', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('149', '75', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('150', '75', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('151', '76', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('152', '76', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('153', '77', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('154', '77', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('155', '78', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('156', '78', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('157', '79', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('158', '79', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('159', '80', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('160', '80', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('161', '81', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('162', '81', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('163', '82', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('164', '82', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('165', '83', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('166', '83', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('167', '84', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('168', '84', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('169', '85', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('170', '85', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('171', '86', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('172', '86', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('173', '87', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('174', '87', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('175', '88', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('176', '88', '2', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('177', '89', '1', '0', '0', '0', '0', '0', '0', '1555566438');
INSERT INTO `pd_stock` VALUES ('178', '89', '2', '0', '0', '0', '0', '0', '0', '1555566438');

-- ----------------------------
-- Table structure for `pd_stock_in`
-- ----------------------------
DROP TABLE IF EXISTS `pd_stock_in`;
CREATE TABLE `pd_stock_in` (
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_stock_in
-- ----------------------------
INSERT INTO `pd_stock_in` VALUES ('1', 'RKD_20190409-0001', '1', '1', '0', '1', '', '1554739200', '1554813498');
INSERT INTO `pd_stock_in` VALUES ('2', 'RKD_20190409-0002', '1', '2', '1', '1', '', '1554739200', '1554813716');
INSERT INTO `pd_stock_in` VALUES ('3', 'RKD_20190410-0003', '1', '3', '1', '1', '', '1554825600', '1554871575');
INSERT INTO `pd_stock_in` VALUES ('4', 'RKD_20190410-0004', '3', '3', '2', '1', '', '1554874729', '1554825600');
INSERT INTO `pd_stock_in` VALUES ('5', 'RKD_20190410-0005', '3', '1', '2', '1', '', '1554875474', '1554825600');

-- ----------------------------
-- Table structure for `pd_stock_in_productinfo`
-- ----------------------------
DROP TABLE IF EXISTS `pd_stock_in_productinfo`;
CREATE TABLE `pd_stock_in_productinfo` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `stock_in_id` int(10) NOT NULL COMMENT '入库ID',
  `product_info_id` int(10) NOT NULL COMMENT '产品ID',
  `count` int(10) NOT NULL COMMENT '数量',
  `remark` varchar(512) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_stock_in_productinfo
-- ----------------------------
INSERT INTO `pd_stock_in_productinfo` VALUES ('1', '1', '2', '100', '');
INSERT INTO `pd_stock_in_productinfo` VALUES ('2', '1', '3', '50', '');
INSERT INTO `pd_stock_in_productinfo` VALUES ('3', '2', '2', '2', '');
INSERT INTO `pd_stock_in_productinfo` VALUES ('4', '2', '3', '2', '');
INSERT INTO `pd_stock_in_productinfo` VALUES ('5', '3', '4', '200', '');
INSERT INTO `pd_stock_in_productinfo` VALUES ('6', '4', '3', '1', '');
INSERT INTO `pd_stock_in_productinfo` VALUES ('7', '5', '4', '100', '');

-- ----------------------------
-- Table structure for `pd_stock_out`
-- ----------------------------
DROP TABLE IF EXISTS `pd_stock_out`;
CREATE TABLE `pd_stock_out` (
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_stock_out
-- ----------------------------
INSERT INTO `pd_stock_out` VALUES ('1', 'CKD_20190409-0001', '1', '1', '1', '1', '', '1554813775', '1554739200', '');
INSERT INTO `pd_stock_out` VALUES ('2', 'CKD_20190410-0002', '1', '2', '1', '1', '', '1554869460', '1554825600', '');
INSERT INTO `pd_stock_out` VALUES ('3', 'CKD_20190410-0003', '3', '3', '1', '1', '', '1554874645', '1554825600', '');
INSERT INTO `pd_stock_out` VALUES ('4', 'CKD_20190410-0004', '3', '1', '1', '1', '', '1554875445', '1554825600', '');
INSERT INTO `pd_stock_out` VALUES ('5', 'CKD_20190410-0005', '1', '3', '2', '1', '', '1554875570', '1554825600', '');

-- ----------------------------
-- Table structure for `pd_stock_out_productinfo`
-- ----------------------------
DROP TABLE IF EXISTS `pd_stock_out_productinfo`;
CREATE TABLE `pd_stock_out_productinfo` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `stock_out_id` int(10) NOT NULL COMMENT '出库ID',
  `product_info_id` int(10) NOT NULL COMMENT '产品ID',
  `count` int(10) NOT NULL COMMENT '数量',
  `remark` varchar(512) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_stock_out_productinfo
-- ----------------------------
INSERT INTO `pd_stock_out_productinfo` VALUES ('1', '1', '2', '1', '');
INSERT INTO `pd_stock_out_productinfo` VALUES ('2', '1', '3', '1', '');
INSERT INTO `pd_stock_out_productinfo` VALUES ('3', '2', '2', '1', '');
INSERT INTO `pd_stock_out_productinfo` VALUES ('4', '3', '3', '1', '');
INSERT INTO `pd_stock_out_productinfo` VALUES ('5', '4', '4', '100', '');
INSERT INTO `pd_stock_out_productinfo` VALUES ('6', '5', '4', '10', '');

-- ----------------------------
-- Table structure for `pd_supplier`
-- ----------------------------
DROP TABLE IF EXISTS `pd_supplier`;
CREATE TABLE `pd_supplier` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='进销存供应商主表';

-- ----------------------------
-- Records of pd_supplier
-- ----------------------------
INSERT INTO `pd_supplier` VALUES ('1', 'S_20190409_0001', '供应商1', '1', '1554813138', '1', '1554813138', '0', '', '默认类别', 'A级', '');

-- ----------------------------
-- Table structure for `pd_supplier_contacts`
-- ----------------------------
DROP TABLE IF EXISTS `pd_supplier_contacts`;
CREATE TABLE `pd_supplier_contacts` (
  `contacts_id` int(10) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '联系人',
  `telephone` varchar(20) NOT NULL COMMENT '联系方式',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `address` varchar(100) NOT NULL COMMENT '地址',
  `remark` varchar(200) NOT NULL COMMENT '备注',
  PRIMARY KEY (`contacts_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_supplier_contacts
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_supplier_data`
-- ----------------------------
DROP TABLE IF EXISTS `pd_supplier_data`;
CREATE TABLE `pd_supplier_data` (
  `supplier_id` int(10) NOT NULL,
  `crm_pexwas` varchar(100) NOT NULL DEFAULT '' COMMENT '备注'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='供应商附表';

-- ----------------------------
-- Records of pd_supplier_data
-- ----------------------------
INSERT INTO `pd_supplier_data` VALUES ('1', '');

-- ----------------------------
-- Table structure for `pd_target`
-- ----------------------------
DROP TABLE IF EXISTS `pd_target`;
CREATE TABLE `pd_target` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='业绩目标设置表';

-- ----------------------------
-- Records of pd_target
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_task`
-- ----------------------------
DROP TABLE IF EXISTS `pd_task`;
CREATE TABLE `pd_task` (
  `task_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '任务id',
  `type_id` int(10) NOT NULL COMMENT '分类id',
  `owner_role_id` varchar(200) NOT NULL COMMENT '任务关注人',
  `about_roles` varchar(200) NOT NULL COMMENT '任务分配人',
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
  `module` varchar(50) NOT NULL COMMENT '相关模块',
  `module_id` int(10) NOT NULL COMMENT '相关模块ID',
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务信息表';

-- ----------------------------
-- Records of pd_task
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_task_action`
-- ----------------------------
DROP TABLE IF EXISTS `pd_task_action`;
CREATE TABLE `pd_task_action` (
  `action_id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) NOT NULL COMMENT '任务ID',
  `role_id` int(10) NOT NULL COMMENT '操作人ID',
  `create_date` date NOT NULL COMMENT '操作时间',
  `create_time` int(10) NOT NULL COMMENT '操作时间',
  `type` int(10) NOT NULL COMMENT '操作类型',
  `content` varchar(1000) NOT NULL COMMENT '操作内容',
  `about_role_id` varchar(500) NOT NULL COMMENT '分配人ID',
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务活动表';

-- ----------------------------
-- Records of pd_task_action
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_task_sub`
-- ----------------------------
DROP TABLE IF EXISTS `pd_task_sub`;
CREATE TABLE `pd_task_sub` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) NOT NULL COMMENT '主任务ID',
  `content` varchar(1000) NOT NULL COMMENT '内容',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `is_done` tinyint(1) NOT NULL COMMENT '1完成',
  `done_role_id` int(10) NOT NULL COMMENT '完成人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='子任务表';

-- ----------------------------
-- Records of pd_task_sub
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_task_talk`
-- ----------------------------
DROP TABLE IF EXISTS `pd_task_talk`;
CREATE TABLE `pd_task_talk` (
  `talk_id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL COMMENT '组内分标示',
  `task_id` int(10) NOT NULL,
  `send_role_id` int(10) NOT NULL COMMENT '发送者id',
  `receive_role_id` int(10) NOT NULL COMMENT '接收者id',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(10) NOT NULL,
  `g_mark` varchar(50) NOT NULL COMMENT '标示',
  PRIMARY KEY (`talk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务评论回复表';

-- ----------------------------
-- Records of pd_task_talk
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_task_type`
-- ----------------------------
DROP TABLE IF EXISTS `pd_task_type`;
CREATE TABLE `pd_task_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL COMMENT '分类名',
  `role_id` int(10) NOT NULL COMMENT '创建人ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `order_id` int(10) NOT NULL COMMENT '排序ID',
  `is_deleted` tinyint(1) NOT NULL COMMENT '1删除',
  `del_role_id` int(10) NOT NULL COMMENT '删除人ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务列表分类';

-- ----------------------------
-- Records of pd_task_type
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_template`
-- ----------------------------
DROP TABLE IF EXISTS `pd_template`;
CREATE TABLE `pd_template` (
  `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键，模板ID',
  `title` varchar(32) NOT NULL COMMENT '模板名称',
  `content` longtext NOT NULL COMMENT '模板内容',
  `object_id` varchar(32) NOT NULL COMMENT '模板所属对象',
  `creator_role_id` int(10) NOT NULL COMMENT '创建者ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `is_default` tinyint(1) NOT NULL COMMENT '是否采用默认模板。0否，1是',
  `default` longtext NOT NULL COMMENT '默认模板内容',
  `system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '系统默认模板，不可删除，1是，0否',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '模板状态：1启用；0禁用',
  PRIMARY KEY (`template_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_template
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_template_field_block`
-- ----------------------------
DROP TABLE IF EXISTS `pd_template_field_block`;
CREATE TABLE `pd_template_field_block` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_template_field_block
-- ----------------------------
INSERT INTO `pd_template_field_block` VALUES ('1', '产品信息', '<table align=\"center\" border=\"2\"><tbody><tr class=\"firstRow\">\n<td width=\"84\" valign=\"middle\" style=\"word-break: break-all;\" align=\"center\">产品名称</td><td width=\"84\" valign=\"middle\" style=\"word-break: break-all;\" align=\"center\">规格</td><td width=\"84\" valign=\"middle\" style=\"word-break: break-all;\" align=\"center\"><span style=\"text-align: -webkit-center;\">价格(元)</span></td><td width=\"84\" valign=\"middle\" style=\"word-break: break-all;\" align=\"center\">折扣(%)</td><td width=\"84\" valign=\"middle\" style=\"word-break: break-all;\" align=\"center\">销售单价(元)</td><td width=\"84\" valign=\"middle\" style=\"word-break: break-all;\" align=\"center\">数量</td><td width=\"84\" valign=\"middle\" style=\"word-break: break-all;\" align=\"center\">单位</td><td width=\"84\" valign=\"middle\" style=\"word-break: break-all;\" align=\"center\">小计</td></tr><tr php=\"each\"><td valign=\"middle\" align=\"center\" colspan=\"1\" rowspan=\"1\" style=\"word-break: break-all;\"><span class=\"variable-wrapper\" contenteditable=\"false\" title=\"产品名称\" data-original=\"product.product_name\">{{product.product_name}}</span></td><td valign=\"middle\" align=\"center\" colspan=\"1\" rowspan=\"1\" style=\"word-break: break-all;\"><span class=\"variable-wrapper\" contenteditable=\"false\" title=\"规格\" data-original=\"product.spec\">{{product.spec}}</span></td><td valign=\"middle\" align=\"center\" colspan=\"1\" rowspan=\"1\" style=\"word-break: break-all;\"><span class=\"variable-wrapper\" contenteditable=\"false\" title=\"价格(元)\" data-original=\"product.ori_price\">{{product.ori_price}}</span></td><td valign=\"middle\" align=\"center\" colspan=\"1\" rowspan=\"1\" style=\"word-break: break-all;\"><span class=\"variable-wrapper\" contenteditable=\"false\" title=\"折扣(%)\" data-original=\"product.discount_rate\">{{product.discount_rate}}</span></td><td valign=\"middle\" align=\"center\" colspan=\"1\" rowspan=\"1\" style=\"word-break: break-all;\"><span class=\"variable-wrapper\" contenteditable=\"false\" title=\"销售单价(元)\" data-original=\"product.unit_price\">{{product.unit_price}}</span></td><td valign=\"middle\" align=\"center\" colspan=\"1\" rowspan=\"1\" style=\"word-break: break-all;\"><span class=\"variable-wrapper\" contenteditable=\"false\" title=\"数量\" data-original=\"product.amount\">{{product.amount}}</span></td><td valign=\"middle\" align=\"center\" colspan=\"1\" rowspan=\"1\" style=\"word-break: break-all;\"><span class=\"variable-wrapper\" contenteditable=\"false\" title=\"单位\" data-original=\"product.unit\">{{product.unit}}</span></td><td valign=\"middle\" align=\"center\" colspan=\"1\" rowspan=\"1\" style=\"word-break: break-all;\"><span class=\"variable-wrapper\" contenteditable=\"false\" title=\"小计\" data-original=\"product.subtotal\">{{product.subtotal}}</span></td></tr></tbody></table>');

-- ----------------------------
-- Table structure for `pd_template_object`
-- ----------------------------
DROP TABLE IF EXISTS `pd_template_object`;
CREATE TABLE `pd_template_object` (
  `id` varchar(32) NOT NULL COMMENT '对象名称',
  `name` varchar(32) NOT NULL COMMENT '对象名称',
  `models` varchar(512) NOT NULL COMMENT '字段模块  @fields-model',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pd_template_object
-- ----------------------------
INSERT INTO `pd_template_object` VALUES ('contract', '合同订单', 'contacts,contract,customer');

-- ----------------------------
-- Table structure for `pd_top`
-- ----------------------------
DROP TABLE IF EXISTS `pd_top`;
CREATE TABLE `pd_top` (
  `top_id` int(10) NOT NULL AUTO_INCREMENT,
  `module_id` int(10) NOT NULL COMMENT '相关模块ID',
  `set_top` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1置顶',
  `top_time` int(10) NOT NULL COMMENT '置顶时间',
  `create_role_id` int(10) NOT NULL COMMENT '创建人ID',
  `module` varchar(50) NOT NULL DEFAULT 'business' COMMENT '置顶模块',
  PRIMARY KEY (`top_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='置顶表';

-- ----------------------------
-- Records of pd_top
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_transfer`
-- ----------------------------
DROP TABLE IF EXISTS `pd_transfer`;
CREATE TABLE `pd_transfer` (
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='库存调拨记录表';

-- ----------------------------
-- Records of pd_transfer
-- ----------------------------
INSERT INTO `pd_transfer` VALUES ('1', '1', '2', 'DBD_20190410-0001', '1554825600', '1554825600', '1', '', '1554872942', '1554875475', '1', '2');
INSERT INTO `pd_transfer` VALUES ('2', '1', '2', 'DBD_20190410-0002', '1554825600', '0', '1', '', '1554873712', '1554873712', '1', '0');
INSERT INTO `pd_transfer` VALUES ('3', '1', '2', 'DBD_20190410-0003', '1554825600', '1554825600', '1', '', '1554873914', '1554874729', '1', '2');

-- ----------------------------
-- Table structure for `pd_transfer_product`
-- ----------------------------
DROP TABLE IF EXISTS `pd_transfer_product`;
CREATE TABLE `pd_transfer_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transfer_id` int(10) NOT NULL COMMENT '调拨单id',
  `product_info_id` int(10) NOT NULL COMMENT '产品id',
  `count` int(10) NOT NULL COMMENT '产品数量',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='采购单关联产品信息表';

-- ----------------------------
-- Records of pd_transfer_product
-- ----------------------------
INSERT INTO `pd_transfer_product` VALUES ('1', '1', '4', '100', '');
INSERT INTO `pd_transfer_product` VALUES ('2', '2', '4', '2', '');
INSERT INTO `pd_transfer_product` VALUES ('3', '3', '3', '1', '');

-- ----------------------------
-- Table structure for `pd_user`
-- ----------------------------
DROP TABLE IF EXISTS `pd_user`;
CREATE TABLE `pd_user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `extid` int(4) NOT NULL COMMENT '坐席号',
  `role_id` int(10) NOT NULL COMMENT '当前使用岗位',
  `category_id` int(11) NOT NULL COMMENT '用户类别',
  `status` int(1) NOT NULL COMMENT '1启用2停用3未激活',
  `type` int(4) NOT NULL COMMENT '员工类型',
  `name` varchar(50) NOT NULL COMMENT '用户名',
  `prefixion` varchar(200) NOT NULL COMMENT '前缀',
  `number` varchar(200) NOT NULL COMMENT '编号',
  `customer_num` int(11) NOT NULL COMMENT '拥有客户数',
  `full_name` varchar(255) NOT NULL COMMENT '用户姓名',
  `img` varchar(100) NOT NULL COMMENT '头像',
  `thumb_path` varchar(255) NOT NULL COMMENT '头像缩略图',
  `password` varchar(32) NOT NULL COMMENT '用户密码',
  `salt` varchar(4) NOT NULL COMMENT '安全符',
  `sex` int(1) NOT NULL COMMENT '用户性别1男2女',
  `email` varchar(50) NOT NULL COMMENT '用户邮箱',
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
  `crm_version` varchar(20) NOT NULL COMMENT '版本信息',
  `call_status` tinyint(1) NOT NULL COMMENT '1启用',
  `kaoqin` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否考勤，1是，0否',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='本表用来存放用户的相关基本信息';

-- ----------------------------
-- Records of pd_user
-- ----------------------------
INSERT INTO `pd_user` VALUES ('1', '0', '1', '1', '1', '0', 'admins', 'K', 'K0001', '0', 'admins', './Uploads/head/5cb7f6e36eb4d8763.png', './Uploads/head/thumb_5cb7f6e36eb4d8763.png', 'd96a960b23d7853aae8cb91475609b89', '18b1', '0', '', '', '', '', '0000-00-00', '0000-00-00', '', '', '', '', '', 'a:2:{s:9:\"dashboard\";a:1:{i:1;a:5:{s:6:\"widget\";s:11:\"Salesfunnel\";s:5:\"level\";s:1:\"0\";s:5:\"title\";s:12:\"销售漏斗\";s:2:\"id\";i:1;s:14:\"status_type_id\";i:1;}}s:4:\"sort\";a:2:{i:0;s:1:\"1\";i:1;s:0:\"\";}}', '183.160.250.147', '1553651460', '0', '0', '', '{\"task\":1555240254,\"event\":1554945621}', '28c8edde3d61a0411511d3b1866f0636', '1555568640', '', '1', '', '0', '1');
INSERT INTO `pd_user` VALUES ('2', '0', '2', '2', '1', '1', 'administrator', 'K', 'K_0002', '0', '金先生', './Uploads/head/5caeded0557f08545.png', './Uploads/head/thumb_5caeded0557f08545.png', '4c4adbc9dea4d52a5fb55f8e8c2ac51a', '3d8b', '1', 'abc@163.com', '15000000000', '', '', '0000-00-00', '0000-00-00', '', '', '', '', '', '', '125.46.129.87', '1554874516', '0', '0', '', '{\"task\":1554953884}', '665f644e43731ff9db3d341da5c827e1', '1555422132', '', '0', '', '0', '1');
INSERT INTO `pd_user` VALUES ('3', '0', '3', '2', '1', '1', 'administrators', 'K', 'K_0003', '0', '金先生', '', '', '722b744f968d2cf829c8064107048dec', '569d', '1', 'abc@163.com', '15000000000', '', '', '0000-00-00', '0000-00-00', '', '', '', '', '', '', '125.46.129.87', '1554874770', '0', '0', '', '', '', '0', '', '0', '', '0', '1');

-- ----------------------------
-- Table structure for `pd_user_category`
-- ----------------------------
DROP TABLE IF EXISTS `pd_user_category`;
CREATE TABLE `pd_user_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '类别id',
  `name` varchar(20) NOT NULL COMMENT '类别的名字',
  `description` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='本表存放用户类别信息';

-- ----------------------------
-- Records of pd_user_category
-- ----------------------------
INSERT INTO `pd_user_category` VALUES ('1', '管理员', '');
INSERT INTO `pd_user_category` VALUES ('2', '员工', '');

-- ----------------------------
-- Table structure for `pd_user_smtp`
-- ----------------------------
DROP TABLE IF EXISTS `pd_user_smtp`;
CREATE TABLE `pd_user_smtp` (
  `smtp_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '发件箱名称',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `settinginfo` text NOT NULL COMMENT 'smtp设置',
  PRIMARY KEY (`smtp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='smtp设置表';

-- ----------------------------
-- Records of pd_user_smtp
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_visitor_plan`
-- ----------------------------
DROP TABLE IF EXISTS `pd_visitor_plan`;
CREATE TABLE `pd_visitor_plan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL COMMENT '外键ID',
  `plan_time` int(11) NOT NULL COMMENT '初次计划时间',
  `delay_time` int(11) NOT NULL DEFAULT '0' COMMENT '延期时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0 未完成；1 推迟；2 过期；3 放弃；4 完成；',
  `content` varchar(255) NOT NULL COMMENT '提醒内容',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '完成模块',
  `module_id` int(11) NOT NULL DEFAULT '0' COMMENT '完成模块ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pd_visitor_plan
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_voucher_account`
-- ----------------------------
DROP TABLE IF EXISTS `pd_voucher_account`;
CREATE TABLE `pd_voucher_account` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='财务科目';

-- ----------------------------
-- Records of pd_voucher_account
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_warehouse`
-- ----------------------------
DROP TABLE IF EXISTS `pd_warehouse`;
CREATE TABLE `pd_warehouse` (
  `warehouse_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '仓库id',
  `name` varchar(200) NOT NULL COMMENT '仓库名',
  `description` varchar(500) NOT NULL COMMENT '描述',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `number` varchar(64) NOT NULL COMMENT '编号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1启用 2禁用',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '软删除',
  `owner_role_id` varchar(128) NOT NULL COMMENT '仓库负责人，可多个',
  PRIMARY KEY (`warehouse_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='仓库表';

-- ----------------------------
-- Records of pd_warehouse
-- ----------------------------
INSERT INTO `pd_warehouse` VALUES ('1', '长裤-1', '', '', 'W_20190409_0001', '1', '0', '');
INSERT INTO `pd_warehouse` VALUES ('2', '1号仓库', '', '', 'W_20190410_0002', '1', '0', ',1,');

-- ----------------------------
-- Table structure for `pd_workrule`
-- ----------------------------
DROP TABLE IF EXISTS `pd_workrule`;
CREATE TABLE `pd_workrule` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL COMMENT '年份',
  `sdate` int(10) NOT NULL COMMENT '开始时间',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1休息2工作日',
  `status` tinyint(1) NOT NULL COMMENT '1自定义时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='工作日配置';

-- ----------------------------
-- Records of pd_workrule
-- ----------------------------

-- ----------------------------
-- Table structure for `pd_workrule_config`
-- ----------------------------
DROP TABLE IF EXISTS `pd_workrule_config`;
CREATE TABLE `pd_workrule_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL COMMENT '年份',
  `value` varchar(50) NOT NULL COMMENT '配置工作日',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='工作日配置表';

-- ----------------------------
-- Records of pd_workrule_config
-- ----------------------------

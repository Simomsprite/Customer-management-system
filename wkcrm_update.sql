/*CRM数据库更新-20190912
*用于更新数据库。
*格式如下
*/
/*2019-09-12 王泽军 -新增示例*/
select * from pd_log where log_id=1

/*2019-09-16 孙翔宇 -新增banner表*/
DROP TABLE IF EXISTS `pd_banner`;
CREATE TABLE `pd_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img_path` varchar(255) NOT NULL COMMENT '图片路径',
  `depict` varchar(100) NOT NULL COMMENT 'banner描述',
  `status` int(1) NOT NULL DEFAULT '2' COMMENT '1启用2停用',
  `url` varchar(100) DEFAULT NULL COMMENT '指向路径',
  `order` int(20) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
SET FOREIGN_KEY_CHECKS=1;
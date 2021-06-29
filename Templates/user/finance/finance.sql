CREATE TABLE `fn_finance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(50) NOT NULL DEFAULT '',
  `username` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(11) NOT NULL DEFAULT '',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `money_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1微信2支付宝',
  `qrcode_url` varchar(100) NOT NULL DEFAULT '',
  `roomid` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(255) NOT NULL DEFAULT '0' COMMENT '0未处理1已处理',
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
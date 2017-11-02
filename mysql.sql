create database reward default charset utf8mb4 collate utf8mb4_unicode_ci;
use reward;

create table `pay_detail` (
  `id` int not null auto_increment comment '自动id',
  `openid` varchar(100) not null comment '支付用户openid',
  `total_fee` int(8) not null default 0 comment '支付金额(分)',
  `transaction_id` varchar(100) not null comment '支付流水号(支付平台生成)',
  `trade_number` varchar(100) not null comment '业务订单号(平台生成)',
  `pay_time` datetime not null comment '支付时间',
  `create_time` datetime not null comment '创建时间',
  primary key (`id`),
  index openid(`openid`)
)engine=InnoDB default character set = utf8mb4 comment = '支付明细表';

create table `pay_user` (
  `openid` varchar(100) not null comment 'openid',
  `nickname` varchar(100) not null comment '昵称',
  `avatar_url` varchar(500) not null comment '头像',
  `create_time` datetime not null comment '创建时间',
  `update_time` datetime not null comment '更新时间',
  primary key (`openid`)
)engine=InnoDB default character set = utf8mb4 comment = '支付用户表';

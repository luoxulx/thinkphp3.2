<?php

return [
    
    'guest_title' => [
            'title' => '留言板标题',
            'type'  => 'text',
            'value' => '',
            'tip'   => '留言板标题描述内容'
    ],
    'guest_desc' => [
            'title'=>'留言板描述',
            'type'=>'textarea',
            'value'=>'',
            'tip'=>'留言板标题下方介绍内容'
    ],
    
    'subject'     => [
            'title' => '邮件通知标题',
            'type'  => 'text',
            'value' => '',
            'tip'   => '前提：已配置发送邮件，且访客留言时已勾选接收邮件通知！发送邮件通知标题'
    ],
    'infos' => [
            'title' => '邮件通知主题内容',
            'type'  => 'textarea',
            'value' => '',
            'tip'   => '前提：已配置发送邮件，且访客留言时已勾选接收邮件通知！发送邮件通知主体内容'
    ],
];
					
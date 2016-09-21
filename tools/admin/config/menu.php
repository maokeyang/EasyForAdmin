<?php
/* 后台管理功能菜单配置
 * 添加配置请参照此配置进行添加菜单
 * 注意：此配置目前只支持三层菜单
 */
return array(

    'h' => array(
        'title' => '操作日志',
        'css' => 'icon iconfont',
        'icon_val' => '&#xe607;',
        'sub' => array(
            '1' => array(
                'title' => '日志查询',
                'url' => '?mod=hqklog&act=hqkLog',
                'is_show' => true,
                'sub' => array(
                    '1' => array('title' => '添加日志', 'url' => '?mod=hqklog&act=hqkLog_edit', 'is_show'=>false),
                    '2' => array('title' => '编辑日志', 'url' => '?mod=hqklog&act=hqkLog_edit', 'is_show'=>false),
                    '3' => array('title' => '删除日志', 'url' => '?mod=hqklog&act=hqkLog_delete', 'is_show'=>false),
                ),
            ),
            // '2' => array(
            //     'title' => '财务结算日志',
            //     'url' => '?mod=finance_log&act=finance_opera_log',
            //     'is_show' => true,
            //     'sub' => array(
            //     ),
            // ),
            // '3' => array(
            //     'title' => '代理商结算日志',
            //     'url' => '?mod=channel_log&act=channel_opera_log',
            //     'is_show' => true,
            //     'sub' => array(
            //     ),
            // ),
        ),
    ),

    'z' => array(
        'title' => '系统设置',
        'css' => 'icon iconfont',
        'icon_val' => '&#xe61c;',
        'sub' => array(
            '1' => array(
                'title' => '用户帐号管理', 
                'url' => '?mod=users&act=admin_user', 
                'is_show' => true,
                'sub' => array(
                    '1' => array('title' => '添加用户帐号', 'url' => '?mod=users&act=admin_user_edit', 'is_show'=>false),
                    '2' => array('title' => '编辑用户帐号', 'url' => '?mod=users&act=admin_user_edit', 'is_show'=>false),
                    '3' => array('title' => '删除用户帐号', 'url' => '?mod=users&act=admin_user_delete', 'is_show'=>false),
                ),
            ),
            '2' => array('title' => '修改密码', 'url' => '?mod=users&act=change', 'is_show'=>true),
            '3' => array(
                'title' => '用户组管理',
                'url' => '?mod=user_group&act=list', 
                'is_show' => true,
                'sub' => array(
                    '1' => array('title' => '添加用户组', 'url' => '?mod=user_group&act=add', 'is_show'=>false),
                    '2' => array('title' => '编辑用户组', 'url' => '?mod=user_group&act=edit', 'is_show'=>false),
                    '3' => array('title' => '删除用户组', 'url' => '?mod=user_group&act=delete', 'is_show'=>false),
                ),
            ),
        ),
    ),
);


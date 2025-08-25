<?php

namespace App\Const\Admin;

class CodeConst
{
    // =======================
    // 用户登录错误代码：10000 系列
    // =======================
    public const LOGIN_REPEAT = 10401;                             // 重复登录
    public const LOGIN_USER_NOT_FOUND_OR_PASSWORD = 10402;         // 用户不存在
    public const LOGIN_ACCOUNT_DISABLED = 10403;                   // 账户被禁用
    public const LOGIN_ACCOUNT_LOCKED = 10404;                     // 账户被锁定
    public const LOGIN_ACCOUNT_EXPIRED = 10405;                    // 账户过期
    public const LOGIN_TOO_MANY_ATTEMPTS = 10406;                  // 登录尝试次数过多
    public const LOGIN_OUT = 10407;                                // 退出失败

    // =======================
    // 系统错误代码：90000 系列
    // =======================
    public const SYSTEM_BLACKLIST_RESTRICTED_ACCESS = 90402;        // 黑名单限制访问
    public const SYSTEM_MAINTENANCE = 90403;                        // 系统维护
    public const SYSTEM_INTERNAL_ERROR = 90404;                     // 内部错误
    public const SYSTEM_SERVICE_UNAVAILABLE = 90405;                // 服务不可用
    public const SYSTEM_TIMEOUT = 90406;                            // 系统超时

    // =======================
    // 权限/认证错误代码：11000 系列
    // =======================
    public const AUTH_UNAUTHORIZED = 11001;                         // 未授权
    public const AUTH_FORBIDDEN = 11002;                            // 禁止访问
    public const AUTH_TOKEN_EXPIRED = 11003;                        // token 过期
    public const AUTH_INVALID_TOKEN = 11004;                        // token 无效

    // =======================
    // 数据错误代码：12000 系列
    // =======================
    public const DATA_NOT_FOUND = 12001;                             // 数据未找到
    public const DATA_DUPLICATE = 12002;                             // 数据重复
    public const DATA_INVALID = 12003;                               // 数据无效
    public const DATA_SAVE_FAILED = 12004;                           // 数据保存失败
    public const DATA_UPDATE_FAILED = 12005;                         // 数据更新失败
    public const DATA_DELETE_FAILED = 12006;                         // 数据删除失败

    // =======================
    // 文件上传错误代码：13000 系列
    // =======================
    public const FILE_NOT_FOUND = 13001;                             // 未选择文件
    public const FILE_TOO_LARGE = 13002;                             // 文件过大
    public const FILE_INVALID_TYPE = 13003;                          // 文件类型不允许
    public const FILE_UPLOAD_FAILED = 13004;                         // 上传失败
    public const FILE_CHUNK_MISSING = 13005;                         // 缺少分片
    public const FILE_MERGE_FAILED = 13006;                          // 分片合并失败
    public const FILE_STORAGE_FAILED = 13007;                        // 存储失败
    public const FILE_SAVE_FAILED = 13008;                           // 分片重复
    public const FILE_COUNT_FAILED = 13009;                           // 分片数量异常
    public const FILE_MISSING_FAILED = 13010;                         // 找不到文件
    public const FILE_SUCCESS_FAILED = 13011;                         // 文件已上传成功


    // =======================
    // 参数错误代码：14000 系列
    // =======================
    public const PARAM_REQUIRED = 14001;         // 必填参数缺失
    public const PARAM_INVALID = 14002;          // 参数值无效
    public const PARAM_TYPE_ERROR = 14003;       // 参数类型错误
    public const PARAM_OUT_OF_RANGE = 14004;     // 参数超出允许范围
    public const PARAM_FORMAT_ERROR = 14005;     // 参数格式错误
    public const PARAM_TOO_LONG = 14006;         // 参数长度超出限制
    public const PARAM_TOO_SHORT = 14007;        // 参数长度过短

    // =======================
    // 参数错误代码：15000 系列
    // =======================
    public const GENERATE_FAILED        = 15001; // 通用生成失败
    public const EXPORT_FAILED          = 15002; // 文件导出失败
    public const IMPORT_FAILED          = 15003; // 文件导入失败
    public const TASK_CREATE_FAILED     = 15004; // 任务生成失败
    public const TEMPLATE_NOT_FOUND     = 15005; // 无可生成数据
    public const FILE_WRITE_ERROR       = 15006; // 文件写入失败
    public const FILE_PERMISSION_DENIED = 15007; // 文件权限不足

    // =======================
    // 方法获取错误提示信息
    // =======================
    public static function getErrorCodeConstMessages($code)
    {
        $messages = [
            // 登录错误
            self::LOGIN_REPEAT => __('请勿重新登录！'),
            self::LOGIN_USER_NOT_FOUND_OR_PASSWORD => __('用户不存在或密码错误！'),
            self::LOGIN_ACCOUNT_DISABLED => __('账户已被禁用！'),
            self::LOGIN_ACCOUNT_LOCKED => __('账户被锁定！'),
            self::LOGIN_ACCOUNT_EXPIRED => __('账户已过期！'),
            self::LOGIN_TOO_MANY_ATTEMPTS => __('尝试登录次数过多，请稍后再试！'),
            self::LOGIN_OUT => __('退出异常！'),

            // 系统错误
            self::SYSTEM_BLACKLIST_RESTRICTED_ACCESS => __('已限制访问，稍后再试！'),
            self::SYSTEM_MAINTENANCE => __('系统维护中，请稍后访问！'),
            self::SYSTEM_INTERNAL_ERROR => __('系统内部错误！'),
            self::SYSTEM_SERVICE_UNAVAILABLE => __('服务暂时不可用！'),
            self::SYSTEM_TIMEOUT => __('系统请求超时！'),

            // 权限认证错误
            self::AUTH_UNAUTHORIZED => __('未授权，请登录！'),
            self::AUTH_FORBIDDEN => __('禁止访问！'),
            self::AUTH_TOKEN_EXPIRED => __('登录已过期，请重新登录！'),
            self::AUTH_INVALID_TOKEN => __('无效的登录信息！'),

            // 数据错误
            self::DATA_NOT_FOUND => __('未找到相关数据！'),
            self::DATA_DUPLICATE => __('数据已存在，不能重复！'),
            self::DATA_INVALID => __('数据无效！'),
            self::DATA_SAVE_FAILED => __('保存数据失败！'),
            self::DATA_UPDATE_FAILED => __('更新数据失败！'),
            self::DATA_DELETE_FAILED => __('删除数据失败！'),

            // 文件上传错误
            self::FILE_NOT_FOUND => __('请选择要上传的文件！'),
            self::FILE_TOO_LARGE => __('文件大小超出限制！限制内存大小 %s,当前内存 %s'),
            self::FILE_INVALID_TYPE => __('不支持的文件类型！'),
            self::FILE_UPLOAD_FAILED => __('文件上传失败！'),
            self::FILE_CHUNK_MISSING => __('缺少文件分片！'),
            self::FILE_MERGE_FAILED => __('文件分片合并失败！'),
            self::FILE_STORAGE_FAILED => __('文件存储失败！'),
            self::FILE_SAVE_FAILED => __('上传分片已存在，已上传分片 %s 数据集'),
            self::FILE_COUNT_FAILED => __('分片数量异常'),
            self::FILE_MISSING_FAILED => __('找不到文件'),
            self::FILE_SUCCESS_FAILED => __('文件已上传成功'),

            // 参数错误
            self::PARAM_REQUIRED => __('缺少必填参数！'),
            self::PARAM_INVALID => __('参数值无效！'),
            self::PARAM_TYPE_ERROR => __('参数类型错误！'),
            self::PARAM_OUT_OF_RANGE => __('参数超出允许范围！'),
            self::PARAM_FORMAT_ERROR => __('参数格式错误！'),
            self::PARAM_TOO_LONG => __('参数长度超出限制！'),
            self::PARAM_TOO_SHORT => __('参数长度过短！'),


            // 生成错误
            self::GENERATE_FAILED        => __('通用生成失败！'),
            self::EXPORT_FAILED          => __('文件导出失败！'),
            self::IMPORT_FAILED          => __('文件导入失败！'),
            self::TASK_CREATE_FAILED     => __('任务生成失败！'),
            self::TEMPLATE_NOT_FOUND     => __('无可生成数据！'),
            self::FILE_WRITE_ERROR       => __('文件写入失败！'),
            self::FILE_PERMISSION_DENIED => __('文件权限不足！'),
        ];

        return $messages[$code] ?? __('未知错误');
    }
}

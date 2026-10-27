# 享享到家

上门服务项目。现在本地跑的是 Go 接口，管理后台、用户端、技师端都请求这一个接口。

旧的 PHP 站点还留在 `backend` 和 `frontweb`，只作对照，不要和 Go 同时占用 8088。

## 先看哪份文档

- 想把项目跑起来：[启动文档](dev/docs/启动文档.md)
- 想改代码：[开发文档](dev/docs/开发文档.md)

## 目录

| 目录 | 是什么 |
| --- | --- |
| `dev/server` | Go 接口，端口 8088 |
| `dev/admin` | 管理后台，Vite，端口 5173 |
| `dev/user` | 用户端，uni-app，用 HBuilderX 跑 |
| `dev/tech` | 技师端，uni-app，用 HBuilderX 跑 |
| `dev/smam_sql.sql` | 当前 MySQL 库的导出。`cd_log` 只留表结构，访问日志已删掉 |
| `backend` | 旧 PHP 接口和上传图片目录 |
| `frontweb` | 旧的用户端、技师端和旧 SQL |

## 本地地址

- 接口：http://127.0.0.1:8088/health
- 管理后台：http://127.0.0.1:5173/#/admin/login ，账号 `admin`，密码 `admin123`
- 技师端由 HBuilderX 打开，常见地址是 http://localhost:8080/h5/
- 用户端同样由 HBuilderX 打开，端口以编译窗口为准

一键启动运行仓库根目录的 `一键启动新后端.bat`。

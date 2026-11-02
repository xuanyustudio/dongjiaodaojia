# 享享到家

上门服务预约。用户选项目、选技师、下单；技师接单、签到、提交认证；管理后台管理技师、服务项目和订单。

一套 Go 接口同时给三个前端用：Vue 3 管理后台，以及 uni-app 写的用户端、技师端。数据在 MySQL。

Home-service booking. One Go API, a Vue 3 admin, and two uni-app clients (customer and technician), backed by MySQL.

## 文档

| 你要做的事 | 看这里 |
| --- | --- |
| 把项目在本机跑起来 | [启动文档](dev/docs/启动文档.md) |
| 改接口、页面、订单和分成 | [开发文档](dev/docs/开发文档.md) |

## 能做什么

- **用户端**：看项目和技师，填上门地址，下单，看订单，使用优惠券，管钱包。
- **技师端**：看新订单和进行中的订单，设置服务时间和上门范围，上传身份认证，紧急报警。
- **管理后台**：审核技师，维护服务项目和技师分成，处理订单、优惠券、提现和报警。

## 技术栈

| 部分 | 技术 |
| --- | --- |
| 接口 | Go 1.23，标准库 HTTP，MySQL |
| 管理后台 | Vue 3，Vite，Element Plus |
| 用户端、技师端 | uni-app（Vue 2），用 HBuilderX 跑 H5 |
| 数据 | MySQL 5.7，库名 `smam_sql`，表前缀 `cd_` |

## 目录

| 目录 | 是什么 |
| --- | --- |
| `dev/server` | Go 接口，默认端口 8088 |
| `dev/admin` | 管理后台，默认端口 5173 |
| `dev/user` | 用户端 |
| `dev/tech` | 技师端 |
| `dev/uploads` | 上传的图片和录音，由接口的 `/uploads/` 提供访问 |
| `dev/smam_sql.sql` | 当前库的导出。`cd_log` 只留表结构 |
| `dev/docs` | 启动文档、开发文档 |

仓库根目录的 `一键启动新后端.bat` 会拉起 MySQL（如果没开）、Go 接口、管理后台，并用 HBuilderX 打开两个 H5。停掉接口和后台用 `停止新后端.bat`。

本地地址、账号和导入数据库的命令在[启动文档](dev/docs/启动文档.md)。接口写好后看 http://127.0.0.1:8088/health 。

三个前端请求的路径都是 `/public/index.php/{端}/{控制器}/{方法}`。这是现在这套 Go 接口的地址，不是旧的 PHP 文件。

贡献指南
---

EasyWeChat 是一个开源的，社区驱动的项目。

我们欢迎广大开发者贡献大家的智慧，让我们共同让它变得更完美.

## 提 issue

- 使用版本前缀：`[3.0] xxxxxx` 或者 `[2.1] xxxxx`
- 正文请带上足够的信息，以便于大家能知道你**做了什么**，**在什么样的场景下**，**出现了什么错误**，并且知道 **你已经尝试过但没成功的解决方法**
- 只提供标题的将会被忽略。

## 贡献代码

请严格遵循以下代码标准:

- [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
- 使用 4 个空格作为缩进。
- 使用驼峰命名法
- 注释请使用英文
- 注释风格，描述与参数注释间空一行，不同类型的注释标签（如`@param` 与 `@return`）之间空一行，类型请使用带命名空间的全称（参考下面的`@return`），如：
    ```php
    /**
     * Parse JSON from response and check error.
     *
     * @param string $method
     * @param array  $args
     *
     * @return \EasyWeChat\Support\Collection
     */
    ```
- 方法的开始 `{` 在新的一行

我们使用 [styleCI](http://styleci.io) 来对代码格式进行检查，在你提交 PR 后，页面下方会（评论框上面部分）会有各项检查状态，如果有一项没有通过，请点击后来的 `details` 修好对应的地方，直到全部通过（会有绿色的 √ 符号）。

### 开发流程

1. Fork [overtrue/wechat](https://github.com/overtrue/wechat) 到本地.
2. 创建新的分支：

    ```shell
    $ git checkout -b new_feature
    ```

3. 编写代码。
4. Push 到你的分支:

    ```shell
    $ git push origin new_feature
    ```

5. 创建 Pull Request 并描述你完成的功能或者做出的修改。

## 更新文档

我们的文档也是开源的，源代码在 [EasyWeChat/docs](https://github.com/EasyWeChat/docs)。

### 文档提交流程

1. Fork [EasyWeChat/docs](https://github.com/EasyWeChat/docs)
2. Clone 到你的电脑：

    ```shell
    $ git clone https://github.com/<username>/site.git
    $ cd docs
    ```

3. 创建新的分支，编辑文档
4. Push 到你的分支。
5. 创建 Pull Request 并描述你完成的功能或者做出的修改。

## 报告 Bug

当你在使用过程中遇到问题，请查阅 [疑难解答](http://easywechat.org/docs/zh-cn/troubleshooting.html) 或者在这里提问 [GitHub](https://github.com/overtrue/wechat/issues). 如果还是不能解决你的问题，请到 GitHub 联系我们。

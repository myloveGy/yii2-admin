/**
 * Created by liujinxing on 2017/3/14.
 */

(function (window, $) {
    // 获取数组信息
    $.getValue = function (arrValue, key, defaultValue) {
        if (typeof key === "string") {
            var index = key.lastIndexOf(".");
            if (key.lastIndexOf(".") !== -1) {
                arrValue = $.getValue(arrValue, key.substr(0, index), defaultValue);
                key = key.substr(index + 1);
            }
        }

        if (!arrValue) {
            return defaultValue;
        }

        if (key in arrValue) {
            return arrValue[key];
        }

        return arrValue[key] ? arrValue[key] : defaultValue;
    };

    var other, html, i, mixLoading = null,
        meTables = function (options) {
            // 绑定事件
            this.bind = function () {
                var _self = this;

                // 按钮方法
                $(document).on("click", ".me-table-button-" + _self.options.unique, function (evt) {
                    evt.preventDefault();
                    var func = $(this).data("func");
                    if (func && $.isFunction($.getValue(_self, func))) {
                        _self[func]();
                    }
                });

                // 修改
                $(document).on("click", "#me-table-button-updateAll-" + _self.options.unique, function (evt) {
                    evt.preventDefault();
                    _self.updateAll();
                });

                // 添加保存事件
                $(document).on("click", "#" + _self.options.unique + "-save", function (evt) {
                    evt.preventDefault();
                    _self.save();
                });

                // 修改数据
                $(document).on("click", ".me-table-update-" + _self.options.unique, function (evt) {
                    evt.preventDefault();
                    _self.update($(this).data("row"));
                });

                // 删除数据
                $(document).on("click", ".me-table-delete-" + _self.options.unique, function (evt) {
                    evt.preventDefault();
                    _self.delete($(this).data("row"));
                });

                // 查询详情
                $(document).on("click", ".me-table-detail-" + _self.options.unique, function (evt) {
                    evt.preventDefault();
                    _self.detail($(this).data("row"));
                });

                // 行选择
                $(this).find("th input:checkbox").on("click", function () {
                    $(_self).find("input:checkbox").prop("checked", $(this).prop("checked"));
                });

                // 搜索表单的事件
                if (this.options.searchInputEvent) {
                    $(this.options.searchForm + ' input').on(this.options.searchInputEvent, function () {
                        _self.table.draw();
                    });
                }

                if (this.options.searchSelectEvent) {
                    $(this.options.searchForm + ' select').on(this.options.searchSelectEvent, function () {
                        _self.table.draw();
                    });
                }

                // 搜索表单提交执行搜索
                $(this.options.searchForm).submit(function (evt) {
                    evt.preventDefault();
                    _self.search();
                }).find("button:reset").on("click", function (evt) {
                    evt.preventDefault();
                    $(_self.options.searchForm).get(0).reset();
                    _self.search();
                });
            };

            // 初始化整个 meTables
            this.init = function (params) {
                this.action = "init";
                this.initRender();
                this.table = $(this.options.sTable).DataTable(this.options.table);	// 初始化主要表格
                var self = this;
                // 判断初始化处理(搜索添加位置)
                if (this.options.searchType === "middle") {
                    $(this.options.sTable + "_filter").html('<form id="' +
                        this.options.searchForm.replace("#", "") + '">' + this.options.searchHtml + '</form>');
                    $(this.options.sTable + "_wrapper div.row div.col-xs-6:first")
                        .removeClass("col-xs-6")
                        .addClass("col-xs-2")
                        .next()
                        .removeClass("col-xs-6")
                        .addClass("col-xs-10");	// 处理搜索信息
                } else {
                    // 添加搜索表单信息
                    if (this.options.search.render) {
                        this.options.searchHtml += '<button class="' + this.options.search.button.class + '">\
                    <i class="' + this.options.search.button.icon + '"></i>\
                    ' + meTables.getLanguage("search") + '\
                    </button>';
                        try {
                            $(this.options.searchForm)[this.options.search.type](this.options.searchHtml);
                        } catch (e) {
                            $(this.options.searchForm).append(this.options.searchHtml);
                        }
                    }
                }

                // 添加按钮
                try {
                    $(self.options.buttonSelector)[self.options.buttonType](self.options.buttonHtml);
                } catch (e) {
                    $(self.options.buttonSelector).append(self.options.buttonHtml);
                }

                // 判断开启editTable
                if (this.options.editable) {
                    $.fn.editableform.loading = "<div class='editableform-loading'><i class='ace-icon fa fa-spinner fa-spin fa-2x light-blue'></i></div>";
                    $.fn.editableform.buttons = '<button type="submit" class="btn btn-info editable-submit"><i class="ace-icon fa fa-check"></i></button>' +
                        '<button type="button" class="btn editable-cancel"><i class="ace-icon fa fa-times"></i></button>';
                    $.fn.editable.defaults.ajaxOptions = {type: "POST", dataType: 'json'};
                    $.fn.editable.defaults.mode = this.options.editableMode || 'inline';
                }

                // 文件上传
                if (!meTables.empty(self.options.fileSelector) && self.options.fileSelector.length > 0) {
                    for (var i in self.options.fileSelector) {
                        aceFileUpload(self.options.fileSelector[i], self.getUrl("upload"));
                    }
                }

                // 执行处理
                if (typeof params === "function") params();

                this.bind();

                return this;
            };

            var self = this;

            // 搜索
            this.search = function (params) {
                this.action = "search";
                if (!params) params = false;
                this.table.draw(params);
            };

            // 刷新
            this.refresh = function () {
                var objectForm = $(this.options.searchForm).get(0);
                if (objectForm) {
                    objectForm.reset();
                }

                this.action = "refresh";
                this.search(true);
            };

            // 数据新增
            this.create = function () {
                this.action = "create";
                this.initForm(null);
            };

            // 数据修改
            this.update = function (row) {
                this.action = "update";
                this.initForm(this.table.data()[row]);
            };

            // 修改
            this.updateAll = function () {
                var row = $(this.options.sTable + " tbody input:checkbox:checked:last").data('row'),
                    data = $.getValue(this.table.data(), parseInt(row));
                if (data) {
                    this.action = "update";
                    this.initForm(data);
                } else {
                    return layer.msg(meTables.getLanguage("noSelect"), {icon: 5});
                }

            };

            // 数据删除
            this.delete = function (row) {
                var self = this;
                this.action = "delete";
                // 询问框
                layer.confirm(meTables.getLanguage("confirm").replace("_LENGTH_", ""), {
                    title: meTables.getLanguage("confirmOperation"),
                    btn: [meTables.getLanguage("determine"), meTables.getLanguage("cancel")],
                    shift: 4,
                    icon: 0
                    // 确认删除
                }, function () {
                    self.save(self.table.data()[row]);
                    // 取消删除
                }, function () {
                    layer.msg(meTables.getLanguage("cancelOperation"), {time: 800});
                });

            };

            // 删除全部数据
            this.deleteAll = function () {
                this.action = "deleteAll";
                var self = this, data = [];

                // 数据添加
                $(this.options.sTable + " tbody input:checkbox:checked").each(function () {
                    var row = parseInt($(this).val()),
                        tmp = self.table.data()[row] ? self.table.data()[row] : null;
                    if (tmp && tmp[self.options.pk]) data.push(tmp[self.options.pk]);
                });

                // 数据为空提醒
                if (data.length < 1) {
                    return layer.msg(meTables.getLanguage("noSelect"), {icon: 5});
                }

                // 询问框
                layer.confirm(meTables.getLanguage("confirm").replace("_LENGTH_", data.length), {
                    title: meTables.getLanguage("confirmOperation"),
                    btn: [meTables.getLanguage("determine"), meTables.getLanguage("cancel")],
                    shift: 4,
                    icon: 0
                    // 确认删除
                }, function () {
                    self.save({"id": data.join(',')});
                    $(self.options.sTable + " input:checkbox:checked").prop("checked", false);
                    // 取消删除
                }, function () {
                    layer.msg(meTables.getLanguage("cancelOperation"), {time: 800});
                });
            };

            // 查看详情
            this.detail = function (row) {
                if (this.options.oLoading) {
                    return false;
                }

                var self = this, data = this.table.data()[row]
                t = self.options.title,
                    i = "#data-detail-" + self.options.unique;

                // 处理的数据
                if (data !== undefined) {
                    meTables.detailTable(this.options.table.columns, data, '.data-detail-', row);
                    // 弹出显示
                    var c = $.extend(true, {
                        title: self.options.title + $.getValue(meTables.language, "meTables.sInfo"),
                        content: $("#data-detail-" + self.options.unique).removeClass("hide"), 			// 捕获的元素
                        cancel: function (index) {
                            layer.close(index);
                        },
                        end: function () {
                            $('.views-info').html('');
                            $("#data-detail-" + self.options.unique).addClass("hide");
                            self.options.oLoading = null;
                        }
                    }, this.options.oViewConfig);

                    this.options.oLoading = layer.open(c);
                    // 展开全屏(解决内容过多问题)
                    if (this.options.bViewFull) {
                        layer.full(this.options.oLoading);
                    }
                }
            };

            this.save = function (data) {
                // 第一步： 验证操作类型
                if (!meTables.inArray(this.action, ["create", "update", "delete", "deleteAll"])) {
                    layer.msg(meTables.getLanguage("operationError"));
                    return false;
                }

                // 第二步：新增和修改验证数据、数据的处理
                if (meTables.inArray(this.action, ["create", "update"])) {
                    if (!$(this.options.sFormId).validate(this.options.formValidate).form()) {
                        return false;
                    }

                    data = $(this.options.sFormId).serializeArray();
                }

                // 第三步：验证数据
                if ($.isEmptyObject(data)) {
                    layer.msg($.getValue(meTables.language, "meTables.empty"));
                    return false;
                }

                // 第四步：执行之前的数据处理
                if ($.isFunction(this.beforeSave) && this.beforeSave(data) === false) {
                    return false;
                }

                var self = this;
                // ajax提交数据
                meTables.ajax({
                    url: this.getUrl(this.action),
                    type: "POST",
                    data: data,
                    dataType: "json"
                }).done(function (json) {
                    // 提示
                    layer.msg(self.options.getMessage(json), {
                        icon: self.options.isSuccess(json) ? 6 : 5
                    });

                    // 判断操作成功
                    if (self.options.isSuccess(json)) {
                        // 之后的操作
                        if ($.isFunction(self.afterSave) && self.afterSave(json.data) === false) {
                            return false;
                        }

                        // 执行之后的数据处理
                        self.table.draw(false);
                        $(self).find("input:checkbox").prop("checked", false);
                        $(self.options.sModal).modal('hide');
                    }
                });

                return false;
            };

            // 数据导出
            this.export = function () {
                this.action = "export";
                var self = this,
                    i = null,
                    csrf_param = $('meta[name=csrf-param]').attr('content') || "_csrf",
                    html = '<form action="' + this.getUrl("export") + '" target="_blank" method="POST" class="me-export" style="display:none">';
                html += '<input type="hidden" name="title" value="' + self.options.title + '"/>';
                html += '<input type="hidden" name="' + csrf_param + '" value="' + $('meta[name=csrf-token]').attr('content') + '"/>';

                // 添加字段信息
                this.options.table.columns.forEach(function (k, v) {
                    if (k.data && k.isExport !== false && k.bExport !== false && k.export !== false) {
                        html += '<input type="hidden" name="fields[' + k.data + ']" value="' + k.title + '"/>';
                    }
                });

                // 添加查询条件
                var value = $(self.options.searchForm).serializeArray();
                for (i in value) {
                    if (!meTables.empty(value[i]["value"]) && value[i]["value"] !== "All") {
                        var strName = meTables.getAttributeName(value[i]["name"], self.options.filters);
                        html += '<input type="hidden" name="' + strName + '" value="' + value[i]["value"] + '"/>';
                    }
                }

                // 默认查询参数添加
                for (i in this.options.params) {
                    html += '<input type="hidden" name="' + self.options.filters + '[' + i + ']" value="' + this.options.params[i] + '"/>';
                }

                // 表单提交
                var $form = $(html);
                $('body').append($form);
                var deferred = new $.Deferred,
                    temporary_iframe_id = 'temporary-iframe-' + (new Date()).getTime() + '-' + (parseInt(Math.random() * 1000)),
                    temp_iframe = $('<iframe id="' + temporary_iframe_id + '" name="' + temporary_iframe_id + '" \
								frameborder="0" width="0" height="0" src="about:blank"\
								style="position:absolute; z-index:-1; visibility: hidden;"></iframe>')
                        .insertAfter($form);
                $form.append('<input type="hidden" name="temporary-iframe-id" value="' + temporary_iframe_id + '" />');
                temp_iframe.data('deferrer', deferred);
                $form.attr({
                    method: 'POST',
                    enctype: 'multipart/form-data',
                    target: temporary_iframe_id //important
                });

                $form.get(0).submit();
                var ie_timeout = setTimeout(function () {
                    ie_timeout = null;
                    deferred.reject($(document.getElementById(temporary_iframe_id).contentDocument).text());
                    $('.me-export').remove();
                }, 500);

                deferred.fail(function (result) {
                    if (result) {
                        try {
                            result = $.parseJSON(result);
                            layer.msg(result.errMsg, {icon: result.errCode === 0 ? 6 : 5});
                        } catch (e) {
                            layer.msg(meTables.getLanguage("sServerError"), {icon: 5});
                        }
                    } else {
                        layer.msg(meTables.getLanguage("sExport"), {icon: 6});
                    }
                }).always(function () {
                    clearTimeout(ie_timeout);
                });
                deferred.promise();
            };

            // 获取连接地址
            this.getUrl = function (strType) {
                return this.options.urlPrefix + this.options.url[strType] + this.options.urlSuffix;
            };

            // 初始化页面渲染
            this.initRender = function () {
                var self = this,
                    form = '<form ' + meTables.handleParams(this.options.form) + '><fieldset>',
                    views = '<table class="table table-bordered table-striped table-detail-' + this.options.unique + '">',
                    aOrders = [],
                    aTargets = [];

                // 处理生成表单
                this.options.table.columns.forEach(function (k, v) {
                    // 查看详情信息
                    if (k.bViews !== false && k.view !== false && k.isViews !== false) {
                        views += meTables.detailTableCreate(k.title, k.data, v, self.options.detailTable);
                    }

                    if (k.edit !== undefined) form += meTables.formCreate(k, self.options.editFormParams);	// 编辑表单信息
                    if (k.search !== undefined) self.options.searchHtml += meTables.searchInputCreate(k, v, self.options.searchType);  // 搜索信息
                    if (k.defaultOrder) aOrders.push([v, k.defaultOrder]);							// 默认排序

                    // 是否隐藏
                    if (k.isHide || k.bHide || k.hide) {
                        aTargets.push(v);
                    }

                    // 判断行内编辑
                    if (self.options.editable && k.editable !== undefined) {
                        k.editable.name = k.editable.name ? k.editable.name : k.data;
                        // 默认修改参数
                        self.options.editable[k.editable.name] = {
                            source: k.value,
                            send: "always",
                            url: self.getUrl("editable"),
                            title: k.title,
                            success: function (response) {
                                if (response.errCode !== 0) {
                                    return response.errMsg;
                                }
                            },
                            error: function (e) {
                                layer.msg(meTables.getLanguage("sServerError"), {icon: 5});
                            }
                        };

                        console.info(self.options.editable[k.editable.name])
                        // 继承修改配置参数
                        self.options.editable[k.editable.name] = $.extend(true, {}, self.options.editable[k.editable.name], k.editable);
                        console.info(self.options.editable[k.editable.name])
                        k["class"] = "my-edit edit-" + k.editable.name;
                    }
                });

                // 判断添加行内编辑信息
                if (self.options.editable) {
                    self.options.table.fnDrawCallback = function () {
                        console.info(self.options.editable)
                        for (var key in self.options.editable) {
                            $(self.options.sTable + " tbody tr td.edit-" + key).each(function () {
                                var data = self.table.row($(this).closest('tr')).data(), mv = {};
                                // 判断存在重新赋值
                                if (data) {
                                    mv['value'] = data[key];
                                    mv['pk'] = data[self.options.pk];
                                }

                                $(this).editable($.extend(true, {}, self.options.editable[key], mv))
                            });
                        }
                    }
                }

                if (self.options.editFormParams.bMultiCols && meTables.empty(self.options.editFormParams.modalClass)) {
                    self.options.editFormParams.modalClass = "bs-example-modal-lg";
                    self.options.editFormParams.modalDialogClass = "modal-lg";
                }

                if (self.options.editFormParams.bMultiCols && self.options.editFormParams.index % self.options.editFormParams.iCols !== (self.options.editFormParams.iCols - 1)) {
                    form += '</div>';
                }

                // 生成HTML
                var html = meTables.modalCreate({
                        "params": {"id": self.options.sModal.replace("#", "")},
                        "html": form,
                        "button-id": self.options.sTable.replace("#", '') + '-save',
                        "modalClass": self.options.editFormParams.modalClass,
                        "modalDialogClass": self.options.editFormParams.modalDialogClass
                    },
                    {
                        "params": {"id": "data-detail-" + self.options.sTable.replace("#", "")}, "html": views
                    });

                // 添加处理表格排序配置
                if (aOrders.length > 0) {
                    this.options.table.order = aOrders;
                }

                // 添加处理表格隐藏字段
                if (aTargets.length > 0) {
                    if (this.options.table.columnDefs) {
                        this.options.table.columnDefs.push({"targets": aTargets, "visible": false});
                    } else {
                        this.options.table.columnDefs = [{"targets": aTargets, "visible": false}];
                    }
                }

                // 向页面添加HTML
                $("body").append(html);
            };

            // 初始化表单信息
            this.initForm = function (data) {
                layer.close(this.options.oLoading);
                // 显示之前的处理
                if (typeof this.beforeShow === 'function' && this.beforeShow(data) === false) {
                    return false;
                }

                // 确定操作的表单和模型
                var f = this.options.sFormId, m = this.options.sModal, t = this.options.title;

                $(m).find('h4').html(t + meTables.getLanguage(this.action === "create" ? "sCreate" : "sUpdate"));
                meTables.initForm(f, data);

                // 显示之后的处理
                if (typeof this.afterShow === 'function' && this.afterShow(data) === false) {
                    return false;
                }

                $(m).modal({backdrop: "static"});   // 弹出信息
            };

            this.push = function (obj, value, name) {
                if (value !== undefined && !meTables.empty(value)) {
                    if (name === undefined) {
                        for (i in value) {
                            obj.push({"name": i, "value": value[i]});
                        }
                    } else {
                        for (i in value) {
                            obj.push({"name": name + "[" + i + "]", "value": value[i]});
                        }
                    }
                }
            };

            // 配置初始化
            this.options = $.extend(true, {}, meTables.defaultOptions, options);
            this.table = null;
            this.action = "construct";
            this.options.unique = this.options.sTable.replace("#", "");

            // 没有配置ajax请求覆盖
            if (!this.options.table.ajax) {
                this.options.table.ajax = {
                    url: self.getUrl("search"),
                    data: function (d) {
                        $(self.options.sTable).find("input:checkbox").prop("checked", false);

                        // 第一步：分页必须的参数
                        var return_object = [];
                        return_object.push({name: "offset", value: d.start});
                        return_object.push({name: "limit", value: d.length});
                        return_object.push({name: "draw", value: d.draw});

                        // 第二步：查询的字段信息
                        for (var i in d.columns) {
                            if (d.columns[i].data) {
                                return_object.push({name: "columns[]", value: d.columns[i].data});
                            }
                        }

                        // 第三步：排序处理
                        var order = [];
                        for (var i in d.order) {
                            var index = d.order[i]["column"];
                            if (d.columns[index] && d.columns[index]["data"]) {
                                order.push(d.columns[index]["data"] + " " + d.order[i]["dir"]);
                            }
                        }

                        if (order.length > 0) {
                            return_object.push({name: "orderBy", value: order.join(",")});
                        }


                        // 第四步：表单的数据
                        var from_data = $(self.options.searchForm).serializeArray();
                        from_data.forEach(function (value) {
                            if (value.value !== "" && value.value !== "All") {
                                return_object.push({
                                    name: meTables.getAttributeName(value.name, self.options.filters),
                                    value: value.value
                                });
                            }
                        });

                        // 第五步：添加附加数据
                        if (self.options.params) {
                            for (var i in self.options.params) {
                                return_object.push({
                                    name: self.options.filters + "[" + i + "]",
                                    value: self.options.params[i]
                                });
                            }
                        }

                        return return_object;
                    }
                };
            }

            if (!this.options.table.language) {
                this.options.table.language = meTables.getLanguage("dataTables", "*")
            }

            this.options.form["id"] = this.options.sFormId.replace("#", "");

            // 兼容之前的代码
            this.options.table.columns = this.options.table.columns || this.options.table.aoColumns;

            // 添加序号
            if (this.options.number) {
                this.options.table.columns.unshift(this.options.number)
            }

            // 判断添加数据(多选)
            if (this.options.checkbox) {
                this.options.table.columns.unshift(this.options.checkbox)
            }

            // 判断添加数据(操作选项)
            if (this.options.operations) {
                var btn = this.options.operations.buttons;
                delete this.options.operations.buttons;
                this.options.operations.createdCell = function (td, data, rowArr, row) {
                    $(td).html(meTables.buttonsCreate(row, btn, self.options.unique, rowArr));
                };

                this.options.table.columns.push(this.options.operations);
            }

            // 处理搜索位置
            if (this.options.searchType !== "middle" && meTables.empty(this.options.table.sDom)) {
                this.options.table.sDom = "t<'row'<'col-xs-6'li><'col-xs-6'p>>";
            }

            // 处理按钮
            for (var i in this.options.buttons) {
                if (this.options.buttons[i]) {
                    this.options.buttonHtml += '<button data-func="' + $.getValue(this.options.buttons[i], "data-func") + '" class="' + this.options.buttons[i]["className"] + ' me-table-button-' + this.options.unique + '" id="' + this.options.unique + "-" + i + '">\
                                <i class="' + this.options.buttons[i]["icon"] + '"></i>\
                            ' + this.options.buttons[i]["text"] + '\
                            </button> ';
                }
            }

            return this;
        };

    // 时间格式化
    Date.prototype.Format = function (fmt) {
        var o = {
            "M+": this.getMonth() + 1,
            "d+": this.getDate(),
            "h+": this.getHours(),
            "m+": this.getMinutes(),
            "s+": this.getSeconds(),
            "q+": Math.floor((this.getMonth() + 3) / 3),
            "S": this.getMilliseconds()
        };

        if (/(y+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        }

        for (var k in o) {
            if (new RegExp("(" + k + ")").test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            }
        }

        return fmt;
    };


    /**
     * 获取字段名称
     * @param strAttributes string
     * @param params string
     */
    meTables.getAttributeName = function (strAttributes, params) {
        if (/\[/.test(strAttributes)) {
            var iIndex = strAttributes.indexOf("["),
                prefix = strAttributes.substring(0, iIndex),
                suffix = strAttributes.substring(iIndex);
        } else {
            var prefix = strAttributes,
                suffix = "";
        }

        return params + "[" + prefix + "]" + suffix;
    };

    // 扩展AJAX
    meTables.ajax = function (params) {
        mixLoading = layer.load();
        return $.ajax(params).always(function () {
            layer.close(mixLoading);
        }).fail(function () {
            layer.msg(meTables.getLanguage("sServerError"), {icon: 5});
        });
    };

    // 判断是否存在数组中
    meTables.inArray = function (value, array) {
        if (typeof array === "object") {
            for (var i in array) {
                if (array[i] === value) return true;
            }
        }

        return false;
    };

    // 是否为空
    meTables.empty = function (value) {
        return value === undefined || value === "" || value === null;
    };

    meTables.isObject = function (value) {
        return typeof value === "object";
    };

    // 处理参数
    meTables.handleParams = function (params, prefix) {
        other = "";
        if (params !== undefined && typeof params === "object") {
            prefix = prefix ? prefix : '';
            for (var i in params) {
                other += " " + i + '="' + prefix + params[i] + '"'
            }

            other += " ";
        }

        return other;
    };

    meTables.labelCreate = function (content, params) {
        return "<label" + this.handleParams(params) + "> " + content + " </label>";
    };

    meTables.inputCreate = function (params) {
        if (!params.type) params.type = "text";
        return "<input" + this.handleParams(params) + "/>";
    };

    meTables.textCreate = function (params) {
        params.type = "text";
        return this.inputCreate(params);
    };

    meTables.passwordCreate = function (params) {
        params.type = "password";
        return this.inputCreate(params);
    };

    meTables.fileCreate = function (params) {
        var o = params.options;
        delete params.options;
        html = '<input type="hidden" ' + this.handleParams(params) + '/>';
        o.type = "file";
        return html + this.inputCreate(o);
    };

    meTables.radioCreate = function (params, d) {
        html = "";
        if (d && this.isObject(d)) {
            params['class'] = "ace valid";
            var c = params.default;
            params = this.handleParams(params);
            for (i in d) {
                html += '<label class="line-height-1 blue"> ' +
                    '<input type="radio" ' + params + (c == i ? ' checked="checked" ' : "") + ' value="' + i + '"  /> ' +
                    '<span class="lbl"> ' + d[i] + " </span> " +
                    "</label>　 "
            }
        }

        return html;
    };

    meTables.checkboxCreate = function (params, d) {
        html = '';
        if (d && this.isObject(d)) {
            var o = params.all, c = params.divClass ? params.divClass : "col-xs-6";
            delete params.all;
            delete params.divClass;
            params["class"] = "ace m-checkbox";
            params = this.handleParams(params);
            if (o) {
                html += '<div class="checkbox col-xs-12">' +
                    '<label>' +
                    '<input type="checkbox" class="ace checkbox-all" onclick="var isChecked = $(this).prop(\'checked\');$(this).parent().parent().parent().find(\'input[type=checkbox]\').prop(\'checked\', isChecked);" />' +
                    '<span class="lbl"> ' + meTables.getLanguage("sSelectAll") + ' </span>' +
                    '</label>' +
                    '</div>';
            }
            for (i in d) {
                html += '<div class="checkbox ' + c + '">' +
                    '<label>' +
                    '<input type="checkbox" ' + params + ' value="' + i + '" />' +
                    '<span class="lbl"> ' + d[i] + ' </span>' +
                    '</label>' +
                    '</div>';
            }
        }

        return html;
    };

    meTables.selectCreate = function (params, d) {
        html = "";
        if (d && this.isObject(d)) {
            var c = params.default;
            delete params.default;
            if (params.multiple) {
                params.name += "[]";
            }
            html += "<select " + this.handleParams(params) + ">";
            for (i in d) {
                html += '<option value="' + i + '" ' + (i == c ? ' selected="selected" ' : "") + " >" + d[i] + "</option>";
            }

            html += "</select>";
        }

        return html
    };

    meTables.textareaCreate = function (params) {
        if (!params["class"]) params["class"] = "form-control";
        if (!params["rows"]) params["rows"] = 5;
        html = (params.value ? params.value : "") + "</textarea>";
        delete params.value;
        return "<textarea" + this.handleParams(params) + ">" + html;
    };

    // 搜索框表单元素创建
    meTables.searchInputCreate = function (k, v, searchType) {
        // 默认值
        if (!k.search.name) {
            k.search.name = k.data;
        }

        if (!k.search.title) {
            k.search.title = k.title;
        }

        // 类型处理
        if (!k.search.type) {
            k.search.type = "text";
        }

        // select 默认选中
        var defaultObject = k.search.type === "select" ? {"All": meTables.getLanguage("all")} : null,
            func = k.search.type + "SearchMiddleCreate",
            defaultFunc = "textSearchMiddleCreate";
        if (searchType !== "middle") {
            func = func.replace("Middle", "");
            defaultFunc = defaultFunc.replace("Middle", "");
        }

        try {
            html = this[func](k.search, k.value, defaultObject);
        } catch (e) {
            html = this[defaultFunc](k.search);
        }

        return html;
    };

    meTables.buttonsCreate = function (index, data, unique, rowArray) {
        var div1 = '<div class="hidden-sm hidden-xs btn-group">',
            div2 = '<div class="hidden-md hidden-lg">' +
                '<div class="inline position-relative">' +
                '<button data-position="auto" data-toggle="dropdown" class="btn btn-minier btn-primary dropdown-toggle">' +
                '<i class="ace-icon fa fa-cog icon-only bigger-110"></i>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">';
        // 添加按钮信息
        if (data !== undefined && typeof data === "object") {
            for (var i in data) {

                if (!data[i] || $.isEmptyObject(data[i]) || ($.isFunction(data[i]['show']) && !data[i]['show'](rowArray))) {
                    continue;
                }

                div1 += ' <button class="btn ' + data[i]['className'] + ' ' + data[i]['cClass'] + '-' + unique + ' btn-xs" data-row="' + index + '"><i class="ace-icon fa ' + data[i]["icon"] + ' bigger-120"></i> ' + (data[i]["button-title"] ? data[i]["button-title"] : '') + '</button> ';
                div2 += '<li><a title="' + data[i]['title'] + '" data-rel="tooltip" class="tooltip-info ' + data[i]['cClass'] + '-' + unique + '" href="javascript:;" data-original-title="' + data[i]['title'] + '" data-row="' + index + '"><span class="' + data[i]['sClass'] + '"><i class="ace-icon fa ' + data[i]['icon'] + ' bigger-120"></i></span></a></li>';
            }
        }

        return div1 + '</div>' + div2 + '</ul></div></div>';
    };

    meTables.formCreate = function (k, oParams) {
        var form = '';
        if (!oParams.index) oParams.index = 0;

        // 处理其他参数
        if (!k.edit.type) k.edit.type = "text";
        if (!k.edit.name) k.edit.name = k.data;

        if (k.edit.type === "hidden") {
            form += this.inputCreate(k.edit);
        } else {
            k.edit["class"] = "form-control " + (k.edit["class"] ? k.edit["class"] : "");
            // 处理多列
            if (oParams.iMultiCols > 1 && !oParams.aCols) {
                oParams.aCols = [];
                var iLength = Math.ceil(12 / oParams.iMultiCols);
                oParams.aCols[0] = Math.floor(iLength * 0.3);
                oParams.aCols[1] = iLength - oParams.aCols[0];
            }

            if (!oParams.bMultiCols || (oParams.iColsLength > 1 && oParams.index % oParams.iColsLength === 0)) {
                form += '<div class="form-group">';
            }

            var div_name = k.edit.name.replace("[]", "");
            form += this.labelCreate(k.title, {"class": "col-sm-" + oParams.aCols[0] + " control-label div-left-" + div_name});
            form += '<div class="col-sm-' + oParams.aCols[1] + ' div-right-' + div_name + '">';

            // 使用函数
            try {
                form += this[k.edit.type + "Create"](k.edit, k.value);
            } catch (e) {
                k.edit.type = "text";
                form += this["inputCreate"](k.edit);
            }

            form += '</div>';

            if (!oParams.bMultiCols || (oParams.iColsLength > 1 && oParams.index % oParams.iColsLength === (oParams.iColsLength - 1))) {
                form += '</div>';
            }

            oParams.index++;
        }

        return form;
    };

    meTables.selectInput = function (params, value, defaultObject) {
        html = "";
        if (defaultObject) {
            for (i in defaultObject) {
                html += '<option value="' + i + '" selected="selected">' + defaultObject[i] + '</option>';
            }
        }

        if (value) {
            for (i in value) {
                html += '<option value="' + i + '">' + value[i] + '</option>';
            }
        }

        if (params.multiple) params.name += "[]";
        return '<select ' + this.handleParams(params) + '>' + html + '</select>';
    };

    meTables.textSearchMiddleCreate = function (params) {
        params["id"] = "search-" + params.name;
        return '<label for="search-' + params.name + '"> ' + params.title + ': ' + this.inputCreate(params) + '</label>';
    };

    meTables.selectSearchMiddleCreate = function (params, value, defaultObject) {
        params["id"] = "search-" + params.name;
        return '<label for="search-' + params.name + '"> ' + params.title + ': ' + this.selectInput(params, value, defaultObject) + '</label>';
    };

    meTables.searchParams = function (params) {
        var defaultParams = {
            "id": "search-" + params.name,
            "name": params.name,
            // "placeholder": meGrid.fn.getLanguage("pleaseInput") + params.title,
            "class": "form-control"
        }, defaultLabel = {
            // "class": "sr-only",
            "for": "search-" + params.name
        }, options = params.options, labelOptions = params.labelOptions;

        // 删除多余信息
        delete params.options;
        delete params.labelOptions;

        defaultParams = this.extend(defaultParams, params);
        if (options) {
            defaultParams = this.extend(defaultParams, options);
        }

        if (labelOptions) {
            defaultLabel = this.extend(defaultLabel, labelOptions);
        }

        return {
            input: defaultParams,
            label: defaultLabel
        }
    };

    meTables.textSearchCreate = function (params) {
        // 默认赋值
        if (!params.placeholder) {
            params.placeholder = meTables.getLanguage("pleaseInput") + params.title;
        }

        if (!params.labelOptions) {
            params.labelOptions = {"class": "sr-only"};
        }

        var options = this.searchParams(params);

        return '<div class="form-group">\
                <label' + this.handleParams(options.label) + '>' + params.title + '</label>\
                <input type="text"' + this.handleParams(options.input) + '>\
                </div> ';
    };

    meTables.selectSearchCreate = function (params, value, defaultObject) {
        var options = this.searchParams(params), i = null;
        return '<div class="form-group">\
                <label' + this.handleParams(options.label) + '>' + params.title + '</label>\
                ' + this.selectInput(options.input, value, defaultObject) + '\
                </div> ';
    };

    // 初始化表单信息
    meTables.initForm = function (select, data) {
        var $fm = $(select);
        objForm = $fm.get(0); // 获取表单对象
        if (objForm !== undefined) {
            $fm.find('input[type=hidden]').val('');
            $fm.find('input[type=checkbox]').each(function () {
                $(this).attr('checked', false);
                if ($(this).get(0)) $(this).get(0).checked = false;
            });                                                                             // 多选菜单
            objForm.reset();                                                                // 表单重置
            if (data !== undefined) {
                for (var i in data) {
                    // 多语言处理 以及多选配置
                    if (typeof data[i] === 'object') {
                        for (var x in data[i]) {
                            var key = i + '[' + x + ']';
                            // 对语言
                            if (objForm[key] !== undefined) {
                                objForm[key].value = data[i][x];
                            } else {
                                // 多选按钮
                                if (parseInt(data[i][x]) > 0) {
                                    $('input[type=checkbox][name=' + i + '\\[\\]][value=' + data[i][x] + ']').attr('checked', true).each(function () {
                                        this.checked = true
                                    });
                                }
                            }
                        }
                    }

                    // 其他除密码的以外的数据
                    if (objForm[i] !== undefined && objForm[i].type !== "password") {
                        var obj = $(objForm[i]), tmp = data[i];
                        // 时间处理
                        if (obj.hasClass('time-format')) {
                            tmp = mt.timeFormat(parseInt(tmp), obj.attr('time-format') ? obj.attr('time-format') : "yyyy-MM-dd hh:mm:ss");
                        }
                        objForm[i].value = tmp;
                    }
                }
            }
        }
    };

    meTables.divCreate = function (params) {
        return '<div' + this.handleParams(params) + '></div>'
    };

    meTables.dateCreate = function (params) {
        return '<div class="input-group bootstrap-datepicker"> \
                <input class="form-control date-picker ' + (params["class"] ? params["class"] : "") + '"  type="text" ' + this.handleParams(params) + '/> \
                <span class="input-group-addon"><i class="fa fa-calendar bigger-110"></i></span> \
                </div>';
    };

    meTables.timeCreate = function (params) {
        return '<div class="input-group bootstrap-timepicker"> \
                <input type="text" class="form-control time-picker ' + (params["class"] ? params["class"] : "") + '" ' + this.handleParams(params) + '/> \
                <span class="input-group-addon"><i class="fa fa-clock-o bigger-110"></i></span> \
                </div>';
    };

    // 添加时间
    meTables.dateTimeCreate = function (params) {
        return '<div class="input-group bootstrap-datetimepicker"> \
                <input type="text" class="form-control datetime-picker ' + (params["class"] ? params["class"] : "") + '" ' + this.handleParams(params) + '/> \
                <span class="input-group-addon"><i class="fa fa-clock-o bigger-110"></i></span> \
                </div>';
    };

    // 时间段
    meTables.timeRangeCreate = function (params) {
        return '<div class="input-daterange input-group"> \
                <input type="text" class="input-sm form-control" name="start" /> \
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span> \
                <input type="text" class="input-sm form-control" name="end" /> \
            </div>';
    };

    // 添加时间段
    meTables.dateRangeCreate = function (params) {
        return '<div class="input-group"> \
                <span class="input-group-addon"><i class="fa fa-calendar bigger-110"></i></span> \
                <input class="form-control daterange-picker ' + (params["class"] ? params["class"] : "") + '" type="text" ' + this.handleParams(params) + ' /> \
            </div>';
    };

    meTables.detailTable = function (object, data, tClass, row) {
        // 循环处理显示信息
        object.forEach(function (k) {
            var tmpKey = k.data, tmpValue = data[tmpKey], dataInfo = $(tClass + tmpKey);
            if (k.edit !== undefined && k.edit.type === 'password') tmpValue = "******";
            (k.createdCell !== undefined && typeof k.createdCell === "function") ? k.createdCell(dataInfo, tmpValue, data, row, undefined) : dataInfo.html(tmpValue);
        });
    };

    meTables.detailTableCreate = function (title, data, iKey, aParams) {
        html = '';
        if (aParams && aParams.bMultiCols) {
            if (aParams.iColsLength > 1 && iKey % aParams.iColsLength === 0) {
                html += '<tr>';
            }

            html += '<td class="text-right" width="25%">' + title + '</td><td class="views-info data-detail-' + data + '"></td>';

            if (aParams.iColsLength > 1 && iKey % aParams.iColsLength === (aParams.iColsLength - 1)) {
                html += '</tr>';
            }
        } else {
            html += '<tr><td class="text-right" width="25%">' + title + '</td><td class="views-info data-detail-' + data + '"></td></tr>';
        }

        return html;
    };

    meTables.modalCreate = function (oModal, oViews) {
        return '<div class="hide" ' + this.handleParams(oViews['params']) + '> ' + oViews['html'] + ' </table></div> \
            <div class="modal fade ' + (oModal["modalClass"] ? oModal["modalClass"] : "") + '" ' + this.handleParams(oModal['params']) + ' tabindex="-1" role="dialog" > \
                <div class="modal-dialog ' + (oModal["modalDialogClass"] ? oModal["modalDialogClass"] : "") + '" role="document"> \
                    <div class="modal-content"> \
                        <div class="modal-header"> \
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> \
                            <h4 class="modal-title"></h4> \
                        </div> \
                        <div class="modal-body">' + oModal['html'] + '</fieldset></form></div> \
                        <div class="modal-footer"> \
                            <button type="button" class="btn btn-default" data-dismiss="modal">' + meTables.getLanguage("sBtnCancel") + '</button> \
                            <button type="button" class="btn btn-primary ' + (oModal['bClass'] ? oModal['bClass'] : '') + '" ' + (oModal["button-id"] ? 'id="' + oModal["button-id"] + '"' : "") + '>' + meTables.getLanguage("sBtnSubmit") + '</button> \
                        </div> \
                    </div> \
                </div> \
            </div>';
    };

    // 根据时间戳返回时间字符串
    meTables.timeFormat = function (time, str) {
        if (!str) str = "yyyy-MM-dd";
        var date = new Date(time * 1000);
        return date.Format(str);
    };

    // 时间戳转字符日期
    meTables.dateTimeString = function (td, data) {
        $(td).html(meTables.timeFormat(data, 'yyyy-MM-dd hh:mm:ss'));
    };

    // 状态信息
    meTables.statusString = function (td, data) {
        $(td).html('<span class="label label-' + (parseInt(data) === 1 ? 'success">启用' : 'warning">禁用') + '</span>');
    };

    // 用户显示
    meTables.adminString = function (td, data) {
        $(td).html(aAdmins[data]);
    };

    // 显示标签
    meTables.valuesString = function (data, color, value, defaultClass) {
        if (!defaultClass) defaultClass = 'label label-sm ';
        return '<span class="' + defaultClass + ' ' + (color[value] ? color[value] : '') + '"> ' + (data[value] ? data[value] : value) + ' </span>';
    };

    // 获取语言配置信息
    meTables.getLanguage = function () {
        if (arguments.length > 1 && $.getValue(meTables.language, arguments[0])) {
            return arguments[1] === "*" ?
                $.getValue(meTables.language, arguments[0]) :
                $.getValue(meTables.language, arguments[0] + "." + arguments[1]);
        }

        return $.getValue(meTables.language, "meTables." + arguments[0]);
    };


    // 语言配置
    meTables.language = {
        // 我的信息
        meTables: {
            "operations": "操作",
            "operations_see": "查看",
            "operations_update": "编辑",
            "operations_delete": "删除",
            "sBtnCancel": "取消",
            "sBtnSubmit": "确定",
            "sSelectAll": "选择全部",
            "sInfo": "详情",
            "sCreate": "新增",
            "sUpdate": "编辑",
            "sExport": "数据正在导出, 请稍候...",
            "sAppearError": "出现错误",
            "sServerError": "服务器繁忙,请稍候再试...",
            "determine": "确定",
            "cancel": "取消",
            "confirm": "您确定需要删除这_LENGTH_条数据吗?",
            "confirmOperation": "确认操作",
            "cancelOperation": "您取消了删除操作!",
            "noSelect": "没有选择需要操作的数据",
            "operationError": "操作有误",
            "search": "搜索",
            "create": "添加",
            "updateAll": "修改",
            "deleteAll": "删除",
            "refresh": "刷新",
            "export": "导出",
            "pleaseInput": "请输入",
            "all": "全部",
            "number": "序号",
            "empty": "没有数据"
        },

        // dataTables 表格
        dataTables: {
            // 显示
            "sLengthMenu": "每页 _MENU_ 条记录",
            "sZeroRecords": "没有找到记录",
            "sInfo": "显示 _START_ 到 _END_ 共有 _TOTAL_ 条数据",
            "sInfoEmpty": "无记录",
            "sInfoFiltered": "(从 _MAX_ 条记录过滤)",
            "sSearch": "搜索：",
            // 分页
            "oPaginate": {
                "sFirst": "首页",
                "sPrevious": "上一页",
                "sNext": "下一页",
                "sLast": "尾页"
            }
        }
    };

    // 设置默认配置信息
    meTables.defaultOptions = {
        title: "",                  // 表格的标题
        language: "zh-cn",          // 使用语言
        pk: "id",		            // 行内编辑pk索引值
        sModal: "#table-modal",     // 编辑Modal选择器
        sTable: "#show-table", 	// 显示表格选择器
        sFormId: "#edit-form",		// 编辑表单选择器
        sMethod: "POST",			// 查询数据的请求方式
        params: null,				// 请求携带参数
        searchHtml: "",				// 搜索信息额外HTML
        searchType: "middle",		// 搜索表单位置
        searchForm: "#search-form",	// 搜索表单选择器
        bEvent: true,               // 是否监听事件
        searchInputEvent: "blur",   // 搜索表单input事件
        searchSelectEvent: "change",// 搜索表单select事件
        filters: "filters",         // 查询参数

        // 请求相关
        isSuccess: function (json) {
            return json.errCode === 0;
        },

        // 获取消息
        getMessage: function (json) {
            return json.errMsg;
        },

        // 搜索信息(只对searchType !== "middle") 情况
        search: {
            render: true,
            type: "append",
            button: {
                "class": "btn btn-info btn-sm",
                "icon": "ace-icon fa fa-search"
            }
        },

        fileSelector: [],			// 上传文件选择器

        // 编辑表单信息
        form: {
            "method": "post",
            "class": "form-horizontal",
            "name": "edit-form"
        },

        // 编辑表单验证方式
        formValidate: {
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },
            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
                $(e).remove();
            }
        },

        // 表单编辑其他信息
        editFormParams: {				// 编辑表单配置
            bMultiCols: false,          // 是否多列
            iColsLength: 1,             // 几列
            aCols: [3, 9],              // label 和 input 栅格化设置
            sModalClass: "",			// 弹出模块框配置
            sModalDialogClass: ""		// 弹出模块的class
        },

        // 关于详情的配置
        bViewFull: false, // 详情打开的方式 1 2 打开全屏
        oViewConfig: {
            type: 1,
            shade: 0.3,
            shadeClose: true,
            maxmin: true,
            area: ['50%', 'auto']
        },

        detailTable: {                   // 查看详情配置信息
            bMultiCols: false,
            iColsLength: 1
        },

        // 关于地址配置信息
        urlPrefix: "",
        urlSuffix: "",
        url: {
            search: "search",
            create: "create",
            update: "update",
            delete: "delete",
            export: "export",
            upload: "upload",
            editable: "editable",
            deleteAll: "delete-all"
        },

        // dataTables 表格默认配置对象信息
        table: {
            // "fnServerData": fnServerData,		// 获取数据的处理函数
            // "sAjaxSource":      "search",		// 获取数据地址
            "bLengthChange": true, 			// 是否可以调整分页
            "bAutoWidth": false,           	// 是否自动计算列宽
            "bPaginate": true,			    // 是否使用分页
            "iDisplayStart": 0,
            "iDisplayLength": 10,
            "bServerSide": true,		 	// 是否开启从服务器端获取数据
            "bRetrieve": true,
            "bDestroy": true,
            // "processing": true,		    // 是否使用加载进度条
            // "searching": false,
            "sPaginationType": "full_numbers"     // 分页样式
            // "order": [[1, "desc"]]       // 默认排序，
            // sDom: "t<'row'<'col-xs-6'li><'col-xs-6'p>>"
        },

        // 开启行处理
        editable: null,
        editableMode: "inline",

        // 默认按钮信息
        buttonHtml: "",
        // 按钮添加容器
        buttonSelector: "#me-table-buttons",
        // 按钮添加方式
        buttonType: "append",
        // 默认按钮信息
        buttons: {
            create: {
                "data-func": "create",
                text: meTables.getLanguage("create"),
                icon: "ace-icon fa fa-plus-circle blue",
                className: "btn btn-white btn-primary btn-bold"
            },
            updateAll: {
                "data-func": "updateAll",
                text: meTables.getLanguage("updateAll"),
                icon: "ace-icon fa fa-pencil-square-o orange",
                className: "btn btn-white btn-info btn-bold"
            },
            deleteAll: {
                "data-func": "deleteAll",
                text: meTables.getLanguage("deleteAll"),
                icon: "ace-icon fa fa-trash-o red",
                className: "btn btn-white btn-danger btn-bold"
            },
            refresh: {
                "data-func": "refresh",
                text: meTables.getLanguage("refresh"),
                icon: "ace-icon fa  fa-refresh",
                className: "btn btn-white btn-success btn-bold"
            },
            export: {
                "data-func": "export",
                text: meTables.getLanguage("export"),
                icon: "ace-icon glyphicon glyphicon-export",
                className: "btn btn-white btn-warning btn-bold"
            }
        }

        // 需要序号
        , number: {
            title: meTables.getLanguage("meTables", "number"),
            data: null,
            view: false,
            render: function (data, type, row, meta) {
                if (!meta || $.isEmptyObject(meta)) {
                    return false;
                }

                return meta.row + 1 + meta.settings._iDisplayStart;
            },
            sortable: false
        }

        // 需要多选框
        , checkbox: {
            data: null,
            sortable: false,
            class: "center text-center",
            title: "<label class=\"position-relative\">" +
                "<input type=\"checkbox\" class=\"ace\" /><span class=\"lbl\"></span></label>",
            view: false,
            createdCell: function (td, data, array, row) {
                $(td).html('<label class="position-relative">' +
                    '<input type="checkbox" class="ace" data-row="' + row + '" value="' + row + '"/>' +
                    '<span class="lbl"></span>' +
                    '</label>');
            }
        }

        // 操作选项
        , operations: {
            title: meTables.getLanguage("meTables", "operations"),
            width: "120px",
            defaultContent: "",
            sortable: false,
            data: null,
            buttons: {
                see: {
                    title: meTables.getLanguage("meTables", "operations_see"),
                    className: "btn-success",
                    cClass: "me-table-detail",
                    icon: "fa-search-plus",
                    sClass: "blue"
                },
                update: {
                    title: meTables.getLanguage("meTables", "operations_update"),
                    className: "btn-info",
                    cClass: "me-table-update",
                    icon: "fa-pencil-square-o",
                    sClass: "green"
                },
                delete: {
                    title: meTables.getLanguage("meTables", "operations_delete"),
                    className: "btn-danger",
                    cClass: "me-table-delete",
                    icon: "fa-trash-o",
                    sClass: "red"
                }
            }
        }
    };

    window.meTables = window.mt = function (options) {
        return new meTables(options)
    };

    window.MeTables = meTables;
    return meTables
})(window, jQuery);
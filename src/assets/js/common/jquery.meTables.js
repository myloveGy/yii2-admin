(function ($) {
    var other, html, i, mixLoading;
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

    // 初始化处理
    var MeTables = function (options) {
        // 获取地址
        this.getUrl = function (url) {
            return this.options.urlPrefix + this.options.url[url] + this.options.urlSuffix;
        };

        // 初始化页面渲染
        this.render = function () {
            this.options.form.id = this.options.formSelector.replace("#", "");
            var self = this,
                form = '<form ' + MeTables.handleParams(this.options.form) + '><fieldset>',
                views = '<table class="table table-bordered table-striped table-detail">',
                aOrders = [],
                aTargets = [];

            // 处理生成表单
            this.options.table.columns.forEach(function (k, v) {
                // 查看详情信息
                $.getValue(k, "view", true) && (views += MeTables.detailTableCreate(k.title, k.data, v, self.options.detailTable));
                $.getValue(k, "edit", null) && (form += MeTables.formCreate(k, self.options.editFormParams));
                $.getValue(k, "search", null) && (self.options.searchHtml += MeTables.searchInputCreate(k, v, self.options.searchType));
                $.getValue(k, "defaultOrder", null) && aOrders.push([v, k.defaultOrder]);
                $.getValue(k, "hide") && aTargets.push(v);

                // 判断行内编辑
                if (!$.isEmptyObject($.getValue(k, "editable"))) {
                    k.editable.name = k.editable.name || k.data;
                    // 默认修改参数
                    self.options.editable[k.editable.name] = {
                        source: k.value,
                        send: "always",
                        url: self.getUrl("editable"),
                        title: k.title,
                        success: function (response) {
                            if (!self.options.isSuccess(response)) {
                                return self.options.getMessage(response);
                            }
                        },
                        error: function () {
                            layer.msg($.getValue(MeTables.language, "serverError"), {icon: 5});
                        }
                    };

                    // 继承修改配置参数
                    self.options.editable[k.editable.name] = self.extend(self.options.editable[k.editable.name], k.editable);
                    k["class"] = "my-edit edit-" + k.editable.name;
                }
            });

            // 判断添加行内编辑信息
            if (self.options.editable) {
                self.options.table.fnDrawCallback = function () {
                    for (var key in self.options.editable) {
                        $(this).find("tbody tr td.edit-" + key).each(function () {
                            var data = self.table.row($(this).closest('tr')).data(), mv = {};
                            // 判断存在重新赋值
                            if (data) {
                                mv['value'] = $.getValue(data, key);
                                mv['pk'] = data[self.options.pk];
                            }

                            $(this).editable($.extend(true, {}, self.options.editable[key], mv))
                        });
                    }
                }
            }

            if (self.options.editFormParams.multiCols && MeTables.empty(self.options.editFormParams.modalClass)) {
                self.options.editFormParams.modalClass = "modal-lg";
            }

            if (self.options.editFormParams.multiCols && self.options.editFormParams.index % self.options.editFormParams.cols !== (self.options.editFormParams.cols - 1)) {
                form += '</div>';
            }

            // 生成HTML
            var modalHtml = MeTables.modalCreate({
                    "params": {
                        "id": self.options.modalSelector.replace("#", "")
                    },
                    "html": form,
                    "buttonId": self.options.unique + '-save',
                    "modalClass": self.options.editFormParams.modalClass
                },
                {
                    "params": {"id": "data-detail-" + self.options.unique},
                    "html": views
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
            $("body").append(modalHtml);
        };

        // 搜索表单
        this.searchRender = function () {
            // 判断初始化处理(搜索添加位置)
            if (this.options.searchType === "middle") {
                // 自定义处理
                if ($.isFunction($.getValue(this.options, "searchMiddleHandle"))) {
                    this.options.searchMiddleHandle(this);
                    return;
                }

                $(this).parent().parent().parent().find("div.row:first>div.col-sm-6:first")
                    .removeClass("col-sm-6")
                    .addClass("col-sm-2")
                    .next()
                    .removeClass("col-sm-6")
                    .addClass("col-sm-10").html('<form id="' +
                    this.options.searchForm.replace("#", "") + '" class="pull-right">' + this.options.searchHtml + '</form>');	// 处理搜索信息
            } else {
                // 添加搜索表单信息
                if (this.options.search.render) {
                    this.options.searchHtml += '<button class="' + this.options.search.button.class + '">\
                    <i class="' + this.options.search.button.icon + '"></i>\
                    ' + $.getValue(MeTables.language, "meTables.search") + '\
                    </button>';
                    try {
                        $(this.options.searchForm)[this.options.search.type](this.options.searchHtml);
                    } catch (e) {
                        $(this.options.searchForm).append(this.options.searchHtml);
                    }
                }
            }
        };

        // 按钮组处理
        this.buttonRender = function () {
            if (this.options.buttonSelector) {
                // 处理按钮
                for (var i in this.options.buttons) {
                    if (this.options.buttons[i]) {
                        this.options.buttonHtml += '<button ' +
                            'class="' + this.options.buttons[i]["class"] + ' me-table-button-' + this.options.unique + '"';
                        this.options.buttonHtml += ' data-func="' + ($.getValue(this.options.buttons[i], "func") || i) + '">\
                                <i class="' + this.options.buttons[i]["icon"] + '"></i>\
                            ' + $.getValue(this.options.buttons[i], "text", $.getValue(MeTables.language, "meTables." + i)) + '\
                            </button> ';
                    }
                }

                // 添加按钮
                try {
                    $(this.options.buttonSelector)[this.options.buttonType](this.options.buttonHtml);
                } catch (e) {
                    $(this.options.buttonSelector).append(this.options.buttonHtml);
                }
            }
        };

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

        // 搜索&重写加载
        this.search = function () {
            this.action = "search";
            this.table.ajax.reload();
        };

        // 创建数据
        this.create = function () {
            this.action = "create";
            this.initForm();
        };

        // 数据修改
        this.update = function (row) {
            this.action = "update";
            this.initForm($.getValue(this.table.data(), row));
        };

        // 修改多个
        this.updateAll = function () {
            var row = $(this).find("tbody input:checkbox:checked:last").data("row");
            if (row) {
                this.update(row);
            } else {
                return layer.msg($.getValue(MeTables.language, "meTables.noSelect", "请选择修改数据"), {icon: 5});
            }
        };

        // 删除数据
        this.delete = function (row) {
            var self = this;
            this.action = "delete";
            // 询问框
            layer.confirm($.getValue(MeTables.language, "meTables.confirm").replace("_LENGTH_", ""), {
                title: $.getValue(MeTables.language, "meTables.confirmOperation"),
                btn: [
                    $.getValue(MeTables.language, "meTables.determine"),
                    $.getValue(MeTables.language, "meTables.cancel")
                ],
                icon: 0
                // 确认删除
            }, function () {
                self.save($.getValue(self.table.data(), row));
                // 取消删除
            }, function () {
                layer.msg($.getValue(MeTables.language, "meTables.cancelOperation"), {time: 800});
            });
        };

        // 删除全部数据
        this.deleteAll = function () {
            this.action = "deleteAll";
            var self = this, data = [];
            // 数据添加
            $(this).find("tbody input:checkbox:checked").each(function () {
                var row = parseInt($(this).data("row")),
                    tmp = self.table.data()[row] || null;

                if ($.getValue(tmp, self.options.pk)) {
                    data.push(tmp[self.options.pk]);
                }
            });

            // 数据为空提醒
            if (data.length < 1) {
                return layer.msg($.getValue(MeTables.language, "meTables.noSelect"), {icon: 5});
            }

            // 询问框
            layer.confirm($.getValue(MeTables.language, "meTables.confirm").replace("_LENGTH_", data.length), {
                title: $.getValue(MeTables.language, "meTables.confirmOperation"),
                btn: [
                    $.getValue(MeTables.language, "meTables.determine"),
                    $.getValue(MeTables.language, "meTables.cancel")
                ],
                icon: 0
                // 确认删除
            }, function () {
                self.save({"id": data.join(',')});
                $(this).find("input:checkbox:checked").prop("checked", false);
                // 取消删除
            }, function () {
                layer.msg($.getValue(MeTables.language, "meTables.cancelOperation"), {time: 800});
            });
        };

        // 查看详情
        this.detail = function (row) {
            if (this.options.oLoading) {
                return false;
            }

            var self = this, data = $.getValue(this.table.data(), row);
            // 处理的数据
            if (!$.isEmptyObject(data)) {
                MeTables.detailTable(this.options.table.columns, data, '.data-detail-', row);
                // 弹出显示
                var c = $.extend(true, {
                    title: self.options.title + $.getValue(MeTables.language, "meTables.info"),
                    content: $("#data-detail-" + self.options.unique).removeClass("hide"), 			// 捕获的元素
                    cancel: function (index) {
                        layer.close(index);
                    },
                    end: function () {
                        $('.views-info').html('');
                        $("#data-detail-" + self.options.unique).addClass("hide");
                        self.options.oLoading = null;
                    }
                }, this.options.viewConfig);

                this.options.oLoading = layer.open(c);
                // 展开全屏(解决内容过多问题)
                if (this.options.viewFull) {
                    layer.full(this.options.oLoading);
                }
            }
        };

        this.save = function (data) {
            // 第一步: 验证操作
            if ($.inArray(this.action, ["create", "update", "delete", "deleteAll"]) === -1) {
                layer.msg($.getValue(MeTables.language, "meTables.operationError"));
                return false;
            }

            // 第二步：新增和修改验证数据、数据的处理
            if ($.inArray(this.action, ["create", "update"]) !== -1) {
                if (!$(this.options.formSelector).validate(this.options.formValidate).form()) {
                    return false;
                }

                data = $(this.options.formSelector).serializeArray();
            }

            // 第三步：验证数据
            if ($.isEmptyObject(data)) {
                layer.msg($.getValue(MeTables.language, "meTables.empty"));
                return false;
            }

            // 第四步：执行之前的数据处理
            if ($.isFunction(this.beforeSave) && this.beforeSave(data) === false) {
                return false;
            }

            var _self = this;
            // ajax提交数据
            MeTables.ajax({
                url: this.getUrl(this.action),
                type: "POST",
                data: data,
                dataType: "json"
            }).done(function (json) {
                // 提示
                layer.msg(_self.options.getMessage(json), {
                    icon: _self.options.isSuccess(json) ? 6 : 5
                });

                // 判断操作成功
                if (_self.options.isSuccess(json)) {
                    // 之后的操作
                    if ($.isFunction(_self.afterSave) && _self.afterSave(json.data) === false) {
                        return false;
                    }

                    // 执行之后的数据处理
                    _self.table.draw(false);
                    $(_self).find("input:checkbox").prop("checked", false);
                    $(_self.options.modalSelector).modal('hide');
                }
            });
            return false;
        };

        // 初始化表单信息
        this.initForm = function (data) {
            layer.close(this.options.oLoading);

            // 显示之前的处理
            if ($.isFunction(this.beforeShow) && this.beforeShow(data) === false) {
                return false;
            }

            // 确定操作的表单和模型
            var t = $.getValue(MeTables.language, this.action === "create" ? "meTables.insert" : "meTables.update");
            $(this.options.modalSelector).find('h4').html(this.options.title + t);
            try {
                $(this.options.formSelector).find(".has-error").removeClass("has-error");
                $(this.options.formSelector).validate(this.options.formValidate).resetForm();
            } catch (e) {
            }
            MeTables.initForm(this.options.formSelector, data);

            // 显示之后的处理
            if ($.isFunction(this.afterShow) && this.afterShow(data) === false) {
                return false;
            }

            $(this.options.modalSelector).modal({backdrop: "static"});   // 弹出信息
        };


        // 数据导出
        this.export = function () {
            this.action = "export";
            var _self = this,
                csrf_param = $('meta[name=csrf-param]').attr('content') || "_csrf",
                html = '<form action="' + _self.getUrl("export") + '" target="_blank" method="POST" class="me-export" style="display:none">';
            html += '<input type="hidden" name="title" value="' + _self.options.title + '"/>';
            html += '<input type="hidden" name="' + csrf_param + '" value="' + $('meta[name=csrf-token]').attr('content') + '"/>';

            // 添加字段信息
            _self.options.table.columns.forEach(function (value) {
                if (value.data && $.getValue(value, "export", true)) {
                    html += '<input type="hidden" name="columns[' + value.data + ']" value="' + value.title + '"/>';
                }
            });

            // 添加查询条件
            var value = $(_self.options.searchForm).serializeArray();
            for (var i in value) {
                if (!MeTables.empty(value[i]["value"]) && value[i]["value"] !== "All") {
                    var strName = MeTables.getAttributeName(value[i]["name"], _self.options.filter);
                    html += '<input type="hidden" name="' + strName + '" value="' + value[i]["value"] + '"/>';
                }
            }

            // 默认查询参数添加
            for (i in _self.options.defaultFilters) {
                html += '<input type="hidden" name="';
                html += _self.options.filters + '[' + i + ']" value="' + _self.options.defaultFilters[i] + '"/>';
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
                        layer.msg(_self.options.getMessage(result), {
                            icon: _self.options.isSuccess(result) === 0 ? 6 : 5
                        });
                    } catch (e) {
                        layer.msg($.getValue(MeTables.language, "meTables.serverError"), {icon: 5});
                    }
                } else {
                    layer.msg($.getValue(MeTables.language, "meTables.export"), {icon: 6});
                }
            }).always(function () {
                clearTimeout(ie_timeout);
            });
            deferred.promise();
        };

        // 配置覆盖
        this.options = $.extend(true, {}, MeTables.defaults, options);
        this.options.unique = this.selector.replace("#", "").replace(".", "");

        var _self = this;

        // 没有配置ajax请求覆盖
        if (!this.options.table.ajax) {
            this.options.table.ajax = {
                url: _self.getUrl("search"),
                data: function (d) {
                    $(_self).find("th input:checkbox").prop("checked", false);

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
                    var from_data = $(_self.options.searchForm).serializeArray();
                    from_data.forEach(function (value) {
                        if (value.value !== "") {
                            return_object.push({
                                name: MeTables.getAttributeName(value.name, _self.options.filters),
                                value: value.value
                            });
                        }
                    });

                    // 第五步：添加附加数据
                    if (_self.options.defaultFilters) {
                        for (var i in _self.options.defaultFilters) {
                            return_object.push({
                                name: _self.options.filters + "[" + i + "]",
                                value: _self.options.defaultFilters[i]
                            });
                        }
                    }

                    return return_object;
                }
            };
        }

        // 操作按钮
        if (this.options.operations) {
            var btn = this.options.operations.buttons;
            delete this.options.operations.buttons;
            this.options.operations.createdCell = function (td, data, rowArr, row) {
                $(td).html(MeTables.buttonsCreate(row, btn, _self.options.unique, rowArr));
            };

            this.options.table.columns.push(this.options.operations);
        }

        // 序号
        this.options.number && this.options.table.columns.unshift(this.options.number);

        // 多选项
        this.options.checkbox && this.options.table.columns.unshift(this.options.checkbox);

        // 处理搜索位置
        if (this.options.searchType !== "middle" && MeTables.empty(this.options.table.dom)) {
            this.options.table.dom = "t<'row'<'col-xs-6'li><'col-xs-6'p>>";
        }

        this.render();
        // 初始化主要表格
        this.table = $(this).DataTable(this.options.table);
        this.searchRender();
        this.buttonRender();
        this.bind();

        // 判断开启editTable
        if (this.options.editable) {
            $.fn.editable.defaults.mode = this.options.editableMode || 'inline';
            $.fn.editableform.loading = "<div class='editableform-loading'><i class='ace-icon fa fa-spinner fa-spin fa-2x light-blue'></i></div>";
            $.fn.editableform.buttons = '<button type="submit" class="btn btn-info editable-submit"><i class="ace-icon fa fa-check"></i></button>' +
                '<button type="button" class="btn editable-cancel"><i class="ace-icon fa fa-times"></i></button>';
            $.fn.editable.defaults.ajaxOptions = {type: "POST", dataType: 'json'};
        }

        // 文件上传
        if (!$.isEmptyObject(this.options.fileSelector)) {
            for (var i in this.options.fileSelector) {
                aceFileUpload(this.options.fileSelector[i], this.getUrl("upload"));
            }
        }

        return this;
    };

    // 语言配置
    MeTables.language = {
        // 我的信息
        meTables: {
            number: "序号",
            operations: "操作",
            operationsSee: "查看",
            operationsUpdate: "编辑",
            operationsDelete: "删除",
            btnCancel: "取消",
            btnSubmit: "确定",
            selectAll: "选择全部",
            info: "详情",
            insert: "新增",
            update: "编辑",
            exporting: "数据正在导出, 请稍候...",
            appearError: "出现错误",
            serverError: "服务器繁忙,请稍候再试...",
            determine: "确定",
            cancel: "取消",
            confirm: "您确定需要删除这_LENGTH_条数据吗?",
            confirmOperation: "确认操作",
            cancelOperation: "您取消了删除操作!",
            noSelect: "没有选择需要操作的数据",
            operationError: "操作有误",
            empty: "没有数据",
            search: "搜索",
            create: "添加",
            updateAll: "修改",
            deleteAll: "删除",
            refresh: "刷新",
            export: "导出",
            pleaseInput: "请输入",
            all: "全部",
            pleaseSelect: "请选择",
            clear: "清除"
        },

        // dataTables 表格
        dataTables: {
            decimal: "",
            emptyTable: "没有数据呢 ^.^",
            info: "显示 _START_ 到 _END_ 共有 _TOTAL_ 条数据",
            infoEmpty: "无记录",
            infoFiltered: "(从 _MAX_ 条记录过滤)",
            infoPostFix: "",
            thousands: ",",
            lengthMenu: "每页 _MENU_ 条记录",
            loadingRecords: "加载中...",
            processing: "数据加载中...",
            search: "搜索:",
            zeroRecords: "没有找到记录",
            paginate: {
                first: "首页",
                last: "尾页",
                next: "下一页",
                previous: "上一页"
            },
            aria: {
                sortAscending: ": 正在进行正序排序",
                sortDescending: ": 正在进行倒序排序"
            }
        }
    };

    //  默认配置信息
    MeTables.defaults = {
        title: "",                      // 表格的标题
        pk: "id",		                // 行内编辑pk索引值
        modalSelector: "#table-modal",  // 编辑Modal选择器
        formSelector: "#edit-form",	    // 编辑表单选择器
        defaultFilters: null,			// 默认查询条件 {id: 1, type: 2}
        filters: "filters",             // 查询参数名称

        // 请求相关
        isSuccess: function (json) {
            return json.code === 0;
        },

        getMessage: function (json) {
            return json.msg;
        },

        // 搜索相关
        searchHtml: "",				    // 搜索信息额外HTML
        searchType: "middle",		    // 搜索表单位置
        searchForm: "#search-form",	    // 搜索表单选择器
        searchInputEvent: "blur",       // 搜索表单input事件
        searchSelectEvent: "change",    // 搜索表单select事件
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
        editFormParams: {			// 编辑表单配置
            multiCols: false,       // 是否多列
            colsLength: 1,          // 几列
            cols: [3, 9],           // label 和 input 栅格化设置
            modalClass: "",			// 弹出模块框配置
            modalDialogClass: ""	// 弹出模块的class
        },

        // 关于详情的配置
        viewFull: false, // 详情打开的方式 1 2 打开全屏
        viewConfig: {
            type: 1,
            shade: 0.3,
            shadeClose: true,
            maxmin: true,
            area: ['50%', 'auto']
        },

        detailTable: {                   // 查看详情配置信息
            multiCols: false,
            colsLength: 1
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
            paging: true,
            lengthMenu: [10, 30, 50, 100],
            searching: false,
            ordering: true,
            info: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            paginationType: "full_numbers",
            language: $.getValue(MeTables.language, "dataTables"),
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
                icon: "ace-icon fa fa-plus-circle blue",
                class: "btn btn-white btn-primary btn-bold"
            },
            updateAll: {
                icon: "ace-icon fa fa-pencil-square-o orange",
                class: "btn btn-white btn-info btn-bold"
            },
            deleteAll: {
                icon: "ace-icon fa fa-trash-o red",
                class: "btn btn-white btn-danger btn-bold"
            },
            refresh: {
                func: "search",
                icon: "ace-icon fa  fa-refresh",
                class: "btn btn-white btn-success btn-bold"
            },
            export: {
                icon: "ace-icon glyphicon glyphicon-export",
                class: "btn btn-white btn-warning btn-bold"
            }
        }

        // 需要序号
        , number: {
            title: $.getValue(MeTables.language, "meTables.number"),
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
                    '<input type="checkbox" class="ace" data-row="' + row + '" />' +
                    '<span class="lbl"></span>' +
                    '</label>');
            }
        }
        // 操作选项
        , operations: {
            width: "120px",
            defaultContent: "",
            title: $.getValue(MeTables.language, "meTables.operations"),
            sortable: false,
            data: null,
            buttons: {
                see: {
                    title: $.getValue(MeTables.language, "meTables.see"),
                    btnClass: "btn-success",
                    operationClass: "me-table-detail",
                    icon: "fa-search-plus",
                    colorClass: "blue"
                },
                update: {
                    title: $.getValue(MeTables.language, "meTables.update"),
                    btnClass: "btn-info",
                    operationClass: "me-table-update",
                    icon: "fa-pencil-square-o",
                    colorClass: "green"
                },
                delete: {
                    title: $.getValue(MeTables.language, "meTables.delete"),
                    btnClass: "btn-danger",
                    operationClass: "me-table-delete",
                    icon: "fa-trash-o",
                    colorClass: "red"
                }
            }
        },
        version: "1.0.0",
        author: {
            name: "liujinxing",
            email: "jinxing.liu@qq.com",
            github: "https://github.com/myloveGy"
        }
    };

    $.extend(MeTables, {
        /**
         * 获取字段名称
         * @param strAttributes string
         * @param params string
         */
        getAttributeName: function (strAttributes, params) {
            if (/\[/.test(strAttributes)) {
                var iIndex = strAttributes.indexOf("["),
                    prefix = strAttributes.substring(0, iIndex),
                    suffix = strAttributes.substring(iIndex);
            } else {
                var prefix = strAttributes,
                    suffix = "";
            }

            return params + "[" + prefix + "]" + suffix;
        },

        // 扩展AJAX
        ajax: function (params) {
            mixLoading = layer.load();
            return $.ajax(params).always(function () {
                layer.close(mixLoading);
            }).fail(function () {
                layer.msg($.getValue(MeTables.language, "meTables.serverError"), {icon: 5});
            });
        },

        // 是否为空
        empty: function (value) {
            return value === undefined || value === "" || value === null;
        },

        isObject: function (value) {
            return typeof value === "object";
        },

        // 处理参数
        handleParams: function (params, prefix) {
            other = "";
            if (!$.isEmptyObject(params)) {
                prefix = prefix || '';
                for (var i in params) {
                    other += " " + i + '="' + prefix + params[i] + '"'
                }

                other += " ";
            }

            return other;
        },

        buttonsCreate: function (index, data, unique, rowArray) {
            unique = unique || "";
            var div1 = '<div class="hidden-sm hidden-xs btn-group">',
                div2 = '<div class="hidden-md hidden-lg">' +
                    '<div class="inline position-relative">' +
                    '<button data-position="auto" data-toggle="dropdown" class="btn btn-minier btn-primary dropdown-toggle">' +
                    '<i class="ace-icon fa fa-cog icon-only bigger-110"></i>' +
                    '</button>' +
                    '<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">';
            // 添加按钮信息
            if (!$.isEmptyObject(data)) {
                for (var i in data) {
                    if (!data[i] || $.isEmptyObject(data[i])  || ($.isFunction(data[i]['show']) && !data[i]['show'](rowArray)) ) {
                        continue;
                    }

                    var operationClass = data[i]['operationClass'] + "-" + unique;
                    div1 += ' <button class="btn ' + data[i]['btnClass'] + ' ' + operationClass + ' btn-xs" data-row="' + index + '">' +
                        '<i class="ace-icon fa ' + data[i]["icon"] + ' bigger-120"></i> ' + (data[i]["button-title"] ? data[i]["button-title"] : '') + '</button> ';
                    div2 += '<li><a title="' + data[i]['title'] + '" data-rel="tooltip" class="tooltip-info ' + operationClass + '" href="javascript:;" data-original-title="' + data[i]['title'] + '" data-row="' + index + '">' +
                        '<span class="' + data[i]['colorClass'] + '">' +
                        '<i class="ace-icon fa ' + data[i]['icon'] + ' bigger-120"></i>' +
                        '</span>' +
                        '</a>' +
                        '</li>';
                }
            }

            return div1 + '</div>' + div2 + '</ul></div></div>';
        },

        labelCreate: function (content, params) {
            return "<label" + this.handleParams(params) + "> " + content + " </label>";
        },

        inputCreate: function (params) {
            params.type = params.type || "text";
            return "<input" + this.handleParams(params) + "/>";
        },

        textCreate: function (params) {
            params.type = "text";
            return this.inputCreate(params);
        },

        passwordCreate: function (params) {
            params.type = "password";
            return this.inputCreate(params);
        },

        fileCreate: function (params) {
            var o = params.options;
            delete params.options;
            html = '<input type="hidden" ' + this.handleParams(params) + '/>';
            o.type = "file";
            return html + this.inputCreate(o);
        },

        radioCreate: function (params, d) {
            html = "";
            if (!$.isEmptyObject(d)) {
                params.class = params.class || "ace valid";
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
        },

        checkboxCreate: function (params, d) {
            html = '';
            if (!$.isEmptyObject(d)) {
                var o = params.all, c = params.divClass || "col-xs-6";
                delete params.all;
                delete params.divClass;
                params.class = params.class || "ace m-checkbox";
                params = this.handleParams(params);
                if (o) {
                    html += '<div class="checkbox col-xs-12">' +
                        '<label>' +
                        '<input type="checkbox" class="ace checkbox-all" onclick="var isChecked = $(this).prop(\'checked\');$(this).parent().parent().parent().find(\'input[type=checkbox]\').prop(\'checked\', isChecked);" />' +
                        '<span class="lbl"> ' + $.getValue(MeTables.language, "meTables.pleaseSelect") + ' </span>' +
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
        },

        selectCreate: function (params, d) {
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
        },

        textareaCreate: function (params) {
            params.class = params.class || "form-control";
            params.rows = params.rows || 5;
            html = (params.value ? params.value : "") + "</textarea>";
            delete params.value;
            return "<textarea" + this.handleParams(params) + ">" + html;
        },

        // 搜索框表单元素创建
        searchInputCreate: function (k, v, searchType) {
            // 默认值
            k.search.name = $.getValue(k, "search.name", k.data);
            k.search.title = $.getValue(k, "search.title", k.title);
            k.search.type = $.getValue(k, "search.type", "text");

            // select 默认选中
            var defaultObject = k.search.type === "select" ? {"": $.getValue(MeTables.language, "meTables.pleaseSelect")} : null;
            if (searchType === "middle") {
                try {
                    html = this[k.search.type + "SearchMiddleCreate"](k.search, k.value, defaultObject);
                } catch (e) {
                    html = this.textSearchMiddleCreate(k.search);
                }
            } else {
                try {
                    html = this[k.search.type + "SearchCreate"](k.search, k.value, defaultObject);
                } catch (e) {
                    html = this.textSearchCreate(k.search);
                }
            }

            return html;
        },

        formCreate: function (k, oParams) {
            // 处理其他参数
            k.edit.type = $.getValue(k, "edit.type", "text");
            k.edit.name = $.getValue(k, "edit.name", k.data);

            if (k.edit.type === "hidden") {
                return this.inputCreate(k.edit);
            }

            var form = '';
            oParams.index = $.getValue(oParams, "index", 0);
            k.edit["class"] = "form-control " + (k.edit["class"] ? k.edit["class"] : "");
            // 处理多列
            if (oParams.multiCols > 1 && !oParams.cols) {
                oParams.cols = [];
                var iLength = Math.ceil(12 / oParams.multiCols);
                oParams.cols[0] = Math.floor(iLength * 0.3);
                oParams.cols[1] = iLength - oParams.cols[0];
            }

            if (!oParams.multiCols || (oParams.colsLength > 1 && oParams.index % oParams.colsLength === 0)) {
                form += '<div class="form-group">';
            }

            var div_name = k.edit.name.replace("[]", "");
            form += this.labelCreate(k.title, {"class": "col-sm-" + oParams.cols[0] + " control-label div-left-" + div_name});
            form += '<div class="col-sm-' + oParams.cols[1] + ' div-right-' + div_name + '">';

            // 使用函数
            try {
                form += this[k.edit.type + "Create"](k.edit, k.value);
            } catch (e) {
                form += this["textCreate"](k.edit);
            }

            form += '</div>';
            if (!oParams.multiCols || (oParams.colsLength > 1 && oParams.index % oParams.colsLength === (oParams.colsLength - 1))) {
                form += '</div>';
            }

            oParams.index++;
            return form;
        },

        selectInput: function (params, value, defaultObject) {
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
        },

        textSearchMiddleCreate: function (params) {
            params["id"] = "search-" + params.name;
            return '<label for="search-' + params.name + '"> ' + params.title + ': ' + this.inputCreate(params) + '</label>';
        },

        selectSearchMiddleCreate: function (params, value, defaultObject) {
            params["id"] = "search-" + params.name;
            return '<label for="search-' + params.name + '"> ' + params.title + ': ' + this.selectInput(params, value, defaultObject) + '</label>';
        },

        searchParams: function (params) {
            var defaultParams = {
                "id": "search-" + params.name,
                "name": params.name,
                "class": "form-control"
            }, defaultLabel = {
                "for": "search-" + params.name
            }, options = params.options, labelOptions = params.labelOptions;

            // 删除多余信息
            delete params.options;
            delete params.labelOptions;

            defaultParams = $.extend(defaultParams, params);
            if (options) {
                defaultParams = $.extend(defaultParams, options);
            }

            if (labelOptions) {
                defaultLabel = $.extend(defaultLabel, labelOptions);
            }

            return {
                input: defaultParams,
                label: defaultLabel
            }
        },

        textSearchCreate: function (params) {
            // 默认赋值
            params.placeholder = params.placeholder || $.getValue(MeTables.language, "meTables.pleaseInput") + params.title;
            params.labelOptions = params.labelOptions || {"class": "sr-only"};
            var options = this.searchParams(params);
            return '<div class="form-group">\
                <label' + this.handleParams(options.label) + '>' + params.title + '</label>\
                <input type="text"' + this.handleParams(options.input) + '>\
                </div> ';
        },

        selectSearchCreate: function (params, value, defaultObject) {
            var options = this.searchParams(params), i = null;
            return '<div class="form-group">\
                <label' + this.handleParams(options.label) + '>' + params.title + '</label>\
                ' + this.selectInput(options.input, value, defaultObject) + '\
                </div> ';
        },

        // 初始化表单信息
        initForm: function (select, data) {
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
                                tmp = MeTables.timeFormat(parseInt(tmp), obj.attr('time-format') ? obj.attr('time-format') : "yyyy-MM-dd hh:mm:ss");
                            }
                            objForm[i].value = tmp;
                        }
                    }
                }
            }
        },

        divCreate: function (params) {
            return '<div' + this.handleParams(params) + '></div>'
        },

        dateCreate: function (params) {
            return '<div class="input-group bootstrap-datepicker"> \
                <input class="form-control date-picker ' + (params["class"] ? params["class"] : "") + '"  type="text" ' + this.handleParams(params) + '/> \
                <span class="input-group-addon"><i class="fa fa-calendar bigger-110"></i></span> \
                </div>';
        },

        timeCreate: function (params) {
            return '<div class="input-group bootstrap-timepicker"> \
                <input type="text" class="form-control time-picker ' + (params["class"] ? params["class"] : "") + '" ' + this.handleParams(params) + '/> \
                <span class="input-group-addon"><i class="fa fa-clock-o bigger-110"></i></span> \
                </div>';
        },

        // 添加时间
        dateTimeCreate: function (params) {
            return '<div class="input-group bootstrap-datetimepicker"> \
                <input type="text" class="form-control datetime-picker ' + (params["class"] ? params["class"] : "") + '" ' + this.handleParams(params) + '/> \
                <span class="input-group-addon"><i class="fa fa-clock-o bigger-110"></i></span> \
                </div>';
        },

        // 时间段
        timeRangeCreate: function (params) {
            return '<div class="input-daterange input-group"> \
                <input type="text" class="input-sm form-control" name="start" /> \
                <span class="input-group-addon"><i class="fa fa-exchange"></i></span> \
                <input type="text" class="input-sm form-control" name="end" /> \
            </div>';
        },

        // 添加时间段
        dateRangeCreate: function (params) {
            return '<div class="input-group"> \
                <span class="input-group-addon"><i class="fa fa-calendar bigger-110"></i></span> \
                <input class="form-control daterange-picker ' + (params["class"] ? params["class"] : "") + '" type="text" ' + this.handleParams(params) + ' /> \
            </div>';
        },

        detailTable: function (object, data, tClass, row) {
            // 循环处理显示信息
            object.forEach(function (k) {
                var tmpKey = k.data, tmpValue = data[tmpKey], dataInfo = $(tClass + tmpKey);
                if ($.getValue(k, "view", true)) {
                    // 处理值
                    if ($.getValue(k, "edit.type") === 'password') {
                        tmpValue = "******";
                    }

                    // createdCell 函数
                    if ($.isFunction($.getValue(k, "createdCell"))) {
                        k.createdCell(dataInfo, tmpValue, data, row, undefined);
                    } else {
                        // render 修改值
                        if ($.isFunction($.getValue(k, "render"))) {
                            tmpValue = k.render(tmpValue, true, row);
                        }

                        dataInfo.html(tmpValue)
                    }
                }
            });
        },

        detailTableCreate: function (title, data, iKey, aParams) {
            html = '';
            if (aParams && aParams.bMultiCols) {
                if (aParams.colsLength > 1 && iKey % aParams.colsLength === 0) {
                    html += '<tr>';
                }

                html += '<td class="text-right" width="25%">' + title + '</td><td class="views-info data-detail-' + data + '"></td>';

                if (aParams.colsLength > 1 && iKey % aParams.colsLength === (aParams.colsLength - 1)) {
                    html += '</tr>';
                }
            } else {
                html += '<tr><td class="text-right" width="25%">' + title + '</td><td class="views-info data-detail-' + data + '"></td></tr>';
            }

            return html;
        },

        modalCreate: function (oModal, oViews) {
            return '<div class="hide" ' + this.handleParams(oViews['params']) + '> ' + oViews['html'] + ' </table></div> \
            <div class="modal fade" ' + this.handleParams(oModal['params']) + ' tabindex="-1" role="dialog" > \
                <div class="modal-dialog ' + $.getValue(oModal, "modalClass", "") + '" role="document"> \
                    <div class="modal-content"> \
                        <div class="modal-header"> \
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> \
                            <h4 class="modal-title"></h4> \
                        </div> \
                        <div class="modal-body">' + oModal['html'] + '</fieldset></form></div> \
                        <div class="modal-footer"> \
                            <button type="button" class="btn btn-default" data-dismiss="modal">' + $.getValue(MeTables.language, "meTables.btnCancel") + '</button> \
                            <button type="button" class="btn btn-primary ' + $.getValue(oModal, "btnClass", "")  + '" ' + (oModal["buttonId"] ? 'id="' + oModal["buttonId"] + '"' : "") + '>' + $.getValue(MeTables.language, "meTables.btnSubmit") + '</button> \
                        </div> \
                    </div> \
                </div> \
            </div>';
        },

        // 根据时间戳返回时间字符串
        timeFormat: function (time, str) {
            str = str || "yyyy-MM-dd";
            var date = new Date(time * 1000);
            return date.Format(str);
        },

        // 时间戳转字符日期
        dateTimeString: function (td, data) {
            $(td).html(MeTables.timeFormat(data, 'yyyy-MM-dd hh:mm:ss'));
        },

        // 状态信息
        statusString: function (td, data) {
            $(td).html('<span class="label label-' + (parseInt(data) === 1 ? 'success">启用' : 'warning">禁用') + '</span>');
        },

        // 用户显示
        adminString: function (td, data) {
            $(td).html(aAdmins[data]);
        },

        // 显示标签
        valuesString: function (data, color, value, defaultClass) {
            if (!defaultClass) defaultClass = 'label label-sm ';
            return '<span class="' + defaultClass + ' ' + (color[value] ? color[value] : '') + '"> ' + (data[value] ? data[value] : value) + ' </span>';
        }

    });

    // 辅助函数
    $.fn.meTables = MeTables;

    $.fn.MeTables = function (opts) {
        return $(this).meTables(opts);
    };

    return $.fn.meTables
})(jQuery);



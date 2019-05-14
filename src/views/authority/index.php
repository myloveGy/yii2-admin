<?php

use yii\helpers\Json;
use jinxing\admin\models\Auth;
use jinxing\admin\widgets\MeTable;

// 获取权限
$auth = Auth::getDataTableAuth(Yii::$app->controller->module->user);

// 定义标题
$this->title = '权限信息';
?>
<?= MeTable::widget() ?>
<?php $this->beginBlock('javascript') ?>
    <script type="text/javascript">
        var rules = <?=$rules?>,
            iType = <?=$type?>;
        var m = mt({
            title: "权限信息",
            pk: "name",
            buttons: <?=Json::encode($auth['buttons'])?>,
            operations: {
                buttons: <?=Json::encode($auth['operations'])?>
            },
            number: false,
            table: {
                columns: [
                    {
                        title: "类型",
                        data: "type",
                        hide: true,
                        edit: {type: "hidden", value: iType}
                    },
                    {
                        title: "名称",
                        data: "name",
                        hide: true,
                        edit: {type: "hidden"},
                        search: {name: "name"},
                        sortable: false
                    },
                    {
                        title: "权限名称",
                        data: "name",
                        edit: {
                            required: true,
                            name: "newName",
                            rangeLength: "[2, 64]",
                            placeholder: "请输入英文字母、数字、_、/等字符串"
                        },
                        sortable: false
                    },
                    {
                        title: "说明描述",
                        data: "description",
                        edit: {
                            required: true,
                            rangeLength: "[2, 64]",
                            placeholder: "请输入简单描述信息"
                        },
                        search: {name: "description"},
                        sortable: false
                    },
                    {
                        title: "使用规则",
                        data: "rule_name",
                        value: rules,
                        edit: {"type": "select"},
                        search: {name: "rule_name"},
                        sortable: false,
                        createdCell: function (td, data) {
                            $(td).html(rules[data] ? rules[data] : data);
                        }
                    },
                    {
                        title: "创建时间",
                        data: "created_at",
                        createdCell: MeTables.dateTimeString,
                        defaultOrder: "desc"
                    },
                    {
                        title: "修改时间",
                        data: "updated_at",
                        createdCell: MeTables.dateTimeString
                    }
                ]
            }
        });

        $.extend(m, {
            beforeShow: function (data) {
                if (this.action === "update") {
                    data.newName = data.name;
                }

                return true;
            },
            afterShow: function () {
                $(this.options.sFormId).find('input[name=type]').val(iType);
                return true;
            }
        });

        $(function () {
            m.init();
        })
    </script>
<?php $this->endBlock(); ?>
<?php

use yii\helpers\Json;
use jinxing\admin\models\AdminLog;
use jinxing\admin\models\Auth;
use jinxing\admin\widgets\MeTable;

// 获取权限
$auth = Auth::getDataTableAuth(Yii::$app->controller->module->user);

// 定义标题和面包屑信息
$this->title = '操作日志';
?>
<?= MeTable::widget() ?>
<?php $this->beginBlock('javascript') ?>
    <script type="text/javascript">
        var oTypes = <?=Json::encode(AdminLog::getTypeDescription())?>,
            aAdmins = <?=Json::encode($admins)?>;
        var m = meTables({
            title: "操作日志",
            buttons: <?=Json::encode($auth['buttons'])?>,
            operations: {
                width: "auto",
                buttons: <?=Json::encode($auth['operations'])?>
            },
            table: {
                "aoColumns": [
                    {
                        "title": "操作人",
                        "data": "admin_name",
                    },
                    {
                        "title": "操作方法",
                        "data": "action",
                        "search": {"type": "text"},
                        "bSortable": false
                    },
                    {
                        "title": "请求URL",
                        "data": "url",
                        "search": {"type": "text"},
                        "bSortable": false
                    },
                    {
                        "title": "数据唯一标识",
                        "data": "index",
                        "bSortable": false
                    },
                    {
                        "title": "请求参数",
                        "data": "request",
                        "bSortable": false,
                        "isHide": true,
                        "createdCell": function (td, data) {
                            var json = data, x, html = "[ <br/>";
                            try {
                                json = JSON.parse(data);
                                if (typeof json == 'object') {
                                    for (x in json) {
                                        html += "   " + x + " => " + json[x] + "<br/>";
                                    }
                                }
                            } catch (e) {

                            }

                            html += "]";
                            $(td).html(html);
                        }
                    },
                    {
                        "title": "请求IP",
                        "data": "ip"
                    },
                    {
                        "title": "创建时间",
                        "data": "created_at",
                        "createdCell": meTables.dateTimeString,
                        "defaultOrder": "desc"
                    }
                ]
            }
        });

        $(function () {
            m.init();
        });
    </script>
<?php $this->endBlock(); ?>
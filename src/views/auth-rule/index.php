<?php

use yii\helpers\Json;
use jinxing\admin\models\Auth;
use jinxing\admin\widgets\MeTable;

// 获取权限
$auth = Auth::getDataTableAuth(Yii::$app->controller->module->user);

// 定义标题和面包屑信息
$this->title = '规则管理';
?>
<?= MeTable::widget() ?>
<?php $this->beginBlock('javascript') ?>
    <script type="text/javascript">
        var m = meTables({
            title: "规则管理",
            pk: "name",
            buttons: <?=Json::encode($auth['buttons'])?>,
            operations: {
                buttons: <?=Json::encode($auth['operations'])?>
            },
            table: {
                columns: [
                    {
                        title: "名称",
                        data: "name",
                        defaultOrder: "desc",
                        edit: {type: "hidden"},
                        search: {type: "text"}
                    },
                    {
                        title: "名称",
                        data: "name",
                        edit: {type: "text", name: "newName", required: true, rangeLength: "[2, 64]"},
                        isHide: true
                    },
                    {
                        title: "对应规则类",
                        data: "data",
                        edit: {type: "text", required: true, rangeLength: "[2, 100]"},
                        sortable: false
                    },
                    {
                        title: "创建时间",
                        data: "created_at",
                        createdCell: MeTables.dateTimeString
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
            }
        });

        $(function () {
            m.init();
        });
    </script>
<?php $this->endBlock(); ?>
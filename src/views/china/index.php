<?php

use yii\helpers\Json;
use jinxing\admin\widgets\MeTable;

$this->title = '地址信息';
?>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var arrParent = <?=Json::encode($parent)?>,
        m = meTables({
            title: "地址信息",
            table: {
                "aoColumns":[
                    {"title": "id", "data": "id", "sName": "id",  "defaultOrder": "desc",
                        "edit": {"type": "text", "required":true,"number":true}
                    },
                    {"title": "地址名称", "data": "name", "sName": "name",
                        "edit": {"type": "text", "required": true, "rangelength":"[2, 40]"},
                        "search": {"type": "text"},
                        "bSortable": false
                    },
                    {"title": "父类ID", "data": "pid", "sName": "pid", "value": arrParent,
                        "edit": {"type": "text", "required": true, "number": true},
                        "search": {"type":"select"}
                    }
                ]
            }
        });

    $(function(){
        m.init();
    })
</script>
<?php $this->endBlock(); ?>
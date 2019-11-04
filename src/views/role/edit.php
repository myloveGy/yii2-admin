<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use jinxing\admin\widgets\Alert;
use jinxing\admin\helpers\Helper;

$this->title = '角色信息分配权限';
$url         = Helper::getAssetUrl();
$depends     = ['depends' => 'jinxing\admin\web\AdminAsset'];
$this->registerJsFile($url . '/js/jstree/jstree.min.js', $depends);
$this->registerCssFile($url . '/js/jstree/default/style.css', $depends);

?>
<?= Alert::widget() ?>
<?php $form = ActiveForm::begin(['enableClientValidation' => true]); ?>
<div class="col-xs-12 col-sm-3">
    <div class="col-xs-12 col-sm-12 widget-container-col  ui-sortable">
        <!-- #section:custom/widget-box -->
        <div class="widget-box  ui-sortable-handle">
            <div class="widget-header">
                <h5 class="widget-title"><?= Yii::t('admin', 'Role'); ?></h5>
                <!-- #section:custom/widget-box.toolbar -->
                <div class="widget-toolbar">
                    <a class="orange2" data-action="fullscreen" href="#">
                        <i class="ace-icon fa fa-expand"></i>
                    </a>
                    <a data-action="reload" href="#">
                        <i class="ace-icon fa fa-refresh"></i>
                    </a>
                    <a data-action="collapse" href="#">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>
                </div>

                <!-- /section:custom/widget-box.toolbar -->
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <input type="hidden" name="Auth[type]" value="<?= $model->type ?>"/>
                    <?php
                    echo $form->field($model, 'name')->textInput($model->isNewRecord ? [] : ['disabled' => 'disabled']) .
                        $form->field($model, 'description')->textarea(['style' => 'height: 100px']) .
                        Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Save') : Yii::t('admin', 'Update'), [
                            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 widget-container-col  ui-sortable">
        <!-- #section:custom/widget-box -->
        <div class="widget-box ui-sortable-handle">
            <div class="widget-header">
                <h5 class="widget-title">导航栏</h5>
                <!-- #section:custom/widget-box.toolbar -->
                <div class="widget-toolbar">
                    <a class="orange2" data-action="fullscreen" href="#">
                        <i class="ace-icon fa fa-expand"></i>
                    </a>
                    <a data-action="reload" href="#">
                        <i class="ace-icon fa fa-refresh"></i>
                    </a>
                    <a data-action="collapse" href="#">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>
                </div>

                <!-- /section:custom/widget-box.toolbar -->
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <div id="tree-one" class="tree tree-selectable"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xs-12 col-sm-9 widget-container-col  ui-sortable">
    <!-- #section:custom/widget-box -->
    <div class="widget-box ui-sortable-handle">
        <div class="widget-header">
            <h5 class="widget-title"><?= Yii::t('admin', 'Permissions'); ?></h5>
            <!-- #section:custom/widget-box.toolbar -->
            <div class="widget-toolbar">
                <a class="orange2" data-action="fullscreen" href="#">
                    <i class="ace-icon fa fa-expand"></i>
                </a>
                <a data-action="reload" href="#">
                    <i class="ace-icon fa fa-refresh"></i>
                </a>
                <a data-action="collapse" href="#">
                    <i class="ace-icon fa fa-chevron-up"></i>
                </a>
            </div>

        </div>

        <div class="widget-body">
            <div class="widget-main no-padding">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="20%" class="text-center">分组</th>
                        <th class="text-center">权限</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td colspan="2">
                            <label style="margin: 5px;">
                                <input class="ace ace-checkbox-2 all-checked" type="checkbox"/>
                                <span class="lbl" style="padding-left: 3px;">
                                        全选
                                </span>
                            </label>
                        </td>
                    </tr>
                    <?php foreach ($permissions as $key => $children) : ?>
                        <tr>
                            <td style="vertical-align: middle;">
                                <label style="margin: 5px;">
                                    <input class="ace ace-checkbox-2 parent-checkbox" type="checkbox"
                                           value="<?= $key ?>"/>
                                    <span class="lbl" style="padding-left: 3px;">
                                        <?= $key ?>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <?php foreach ($children as $name => $child) : ?>
                                    <label style="margin: 5px;">
                                        <input class="ace ace-checkbox-2 children-checkbox"
                                               type="checkbox"
                                               name="Auth[_permissions][]"
                                               value="<?= $name ?>"
                                            <?= in_array($name, $model->_permissions) ? 'checked="checked"' : '' ?>
                                        />
                                        <span class="lbl" style="padding-left: 3px;">
                                            <?php echo $child; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">

    function getChildrenAttributes(data, parent_object) {
        var array_attributes = [], length = data.children.length;
        if (length > 0) {
            for (var i = 0; i < length; i++) {
                var tmp_data = parent_object.instance.get_node(data.children[i]);
                array_attributes.push.apply(array_attributes, getChildrenAttributes(tmp_data, parent_object));
            }
        } else if (data.data != null) {
            var array_data = data.data.split("/");

            if (array_data && array_data.length > 0) {
                array_data.pop();
            }

            if (array_data.length > 0) {
                array_attributes.push(array_data.join("/"));
            }
        }

        return array_attributes;
    }

    $(function () {
        $("#tree-one").jstree({
            "plugins": ["checkbox"],
            core: {
                "animation": 0,
                "check_callback": true,
                data: <?=Json::encode($trees)?>
            }
        }).on("changed.jstree", function (e, data) {
            if (data.action === "select_node" || data.action === "deselect_node") {
                var isChecked = data.action === "select_node",
                    attributes = getChildrenAttributes(data.node, data);
                attributes.forEach(function (attribute) {
                    $("input[value^='" + attribute + "/']").prop("checked", isChecked);
                });
            }
        });

        // 分组选择
        $(".parent-checkbox").click(function () {
            $(this).parent().parent().next("td").find("input[type=checkbox]").prop("checked", this.checked);
        });

        // 分组下面的子项选择
        $(".children-checkbox").click(function () {
            var $parent = $(this).parent().parent();
            var checked = this.checked ? $parent.find("input[type=checkbox]").length == $parent.find("input[type=checkbox]:checked").length : false;
            $parent.prev("td").find("input[type=checkbox]").prop("checked", checked);
        })

        // 全部选择
        $(".all-checked").click(function () {
            $("input[type=checkbox]").prop("checked", this.checked);
        });
    });
</script>
<?php $this->endBlock(); ?>

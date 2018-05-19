<?php

use jinxing\admin\AdminAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use jinxing\admin\widgets\Alert;
use yii\helpers\Json;

$this->title = '角色信息分配权限';
list(, $url) = list(, $url) = Yii::$app->assetManager->publish((new AdminAsset())->sourcePath);
$depends = ['depends' => 'jinxing\admin\AdminAsset'];
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
                            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
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
            <div class="widget-main">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="checkbox col-sm-10" style="padding:5px;">
                            <label>
                                <input class="ace ace-checkbox-2 allChecked" type="checkbox"/>
                                <span class="lbl">  全部选择 </span>
                            </label>
                        </div>
                        <?php foreach ($permissions as $key => $value) : ?>
                            <div class="checkbox col-sm-4" style="padding:5px;">
                                <label>
                                    <input class="ace ace-checkbox-2"
                                           type="checkbox"
                                           name="Auth[_permissions][]"
                                           value="<?= $key ?>"
                                        <?= in_array($key, $model->_permissions) ? 'checked="checked"' : '' ?>
                                    />
                                    <span class="lbl"> <?= $value ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
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

        // 全部选择
        $(".allChecked").click(function () {
            $("input[type=checkbox]").prop("checked", this.checked);
        });
    });
</script>
<?php $this->endBlock(); ?>

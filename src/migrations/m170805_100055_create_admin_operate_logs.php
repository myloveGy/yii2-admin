<?php

use yii\db\Migration;

class m170805_100055_create_admin_operate_logs extends Migration
{
    /**
     * @var string 定义表名
     */
    private $table = '{{%admin_operate_logs}}';

    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB comment "管理员操作日志记录信息表"';
        }

        // 创建表
        $this->createTable($this->table, [
            'id'         => $this->primaryKey()->notNull()->comment('日志ID'),
            'admin_id'   => $this->integer(11)->notNull()->defaultValue(0)->comment('操作管理员ID'),
            'admin_name' => $this->string(20)->notNull()->defaultValue('')->comment('操作管理员名称'),
            'action'     => $this->string(64)->notNull()->defaultValue('')->comment('方法'),
            'index'      => $this->string(100)->notNull()->defaultValue('')->comment('数据标识'),
            'request'    => $this->text()->notNull()->comment('请求参数'),
            'response'   => $this->text()->notNull()->comment('响应数据'),
            'ip'         => $this->char(20)->notNull()->defaultValue('')->comment('请求IP'),
            'created_at' => $this->integer(11)->notNull()->defaultValue(0)->comment('创建时间'),
            'KEY `admin_name` (`admin_name`) USING BTREE COMMENT "管理员"'
        ], $tableOptions);
    }

    public function safeDown()
    {
        echo "m170805_100055_create_admin_operate_logs cannot be reverted.\n";
        $this->dropTable($this->table);
        return false;
    }
}

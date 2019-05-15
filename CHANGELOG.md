Yii2-admin extension Change Log
===============================

1.2.0   2019-05-19
------------------

- Change: 不在支持表达式查询的方式
    - remove: 删除控制器的 getDefaultWhere 方法
    - change: 视图中查询字段去掉表达式
    - add: 控制器添加 where 方法处理前端查询字段和查询方式
- Change: meTable 的查询 input 添加回车查询数据

1.1.5   2019-05-10
------------------

- bug: 修复导出数据，查询条件无效问题   
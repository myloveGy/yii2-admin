About the configuration of meTables
-----------------------------------

## basic configuration

Configuration name | Types of | Defaults | Description
:------------------|:-----------|:-----------|:---------------
title              | string     |            | Define the title
pk                 | string     | id         | Primary key name, bulk delete is passed to the background service
bCheckbox          | boolean    | true       | Whether to open the multiple selection bar
params             | object     | null       | Requests with parameters, will be passed to the back end at the time of the query

## About the configuration of the request address
Configuration name | Types of | Defaults | Description
:------------------|:-----------|:-----------|:---------------
urlPrefix          | string     |            | All requested address prefixes
urlSuffix          | string     |            | All request address suffixes
url                | object     |            | All operations corresponding to the address
url.search         | string     | search     | Search data address
url.create         | string     | create     | Create data address
url.update         | string     | update     | Modify data address
url.delete         | string     | delete     | Delete data address
url.export         | string     | export     | Export data address
url.upload         | string     | upload     | Upload file address
url.editable       | string     | editable   | Inline address modification
url.deleteAll      | string     | delete-all | Bulk delete address

Configuration examples and descriptions (configurations that perform the current override defaults):
```js
var m = meTables({
    urlPrefix: "/admin/",
    urlSuffix: ".html",
    url: {
        search: "me-search"  
    },
    ...
}); 
```
The above configuration generates the search data and modified addresses as follows:
> search: localhost/admin/me-search.html

> update: localhost/admin/update.html

**Address generation rules:urlPrefix + url.action + urlSuffix**

## About the configuration of the top button group
Configuration name | Types of | Defaults | Description
:-----------------------|:-----------|:--------------------|:---------------
buttonSelector          | string     | #me-table-buttons                | Jquery selector, the button will be placed in that container
buttonType              | string     | append                           | How to add to a container
buttons.create.bShow    | boolean    | true                             | Add button is displayed
buttons.create.icon     | string     | ace-icon fa fa-plus-circle blue  | Add a small icon used by the button
buttons.create.className| string     | btn btn-white btn-primary btn-bold| Add the class used by the button

There are 5 default button groups, which are:
1. create: Create data
2. updateAll: Select modify data
3. deleteAl: Select delete data
4. refresh: Refresh table data
5. export: export data

> Each button has three attribute configurations: **bShow, icon, className** 

If you do not need these buttons, you can set the button bShow to false, for example：
```js
var m = meTables({
    buttons: {
        create: {
            bShow: false
        },
        updateAll: {
            bShow: false
        }
    },
    ...
});
```

If you need to add a custom button, you can add your own button in the buttons, for example:
```js
var m = meTables({
    buttons: {
        customize: {
            bShow: true,
            icon: "ace-icon fa fa-plus-circle yellow",
            className: "btn btn-white btn-primary btn-bold"
        }
    },
    ...
});

// However, it is not enough to do the above configuration, but also need to add button click processing event, the name and button name consistent
m.fn.extend({
    customize: function () {
        alert("My custom button");
    }
});
```

## About the right side of the table operation option button configuration
Configuration name | Types of | Defaults | Description
:-----------------------|:-----------|:--------------------|:---------------
operations.bOpen        | boolean    | true                | Whether to open
operations.width        | string     | 120px               | Set the width of the column
operations.buttons.see.bShow        | boolean | true             | Whether to show
operations.buttons.see.title        | string  |                  | Button text
operations.buttons.see.button-title | string  |                  | Button text
operations.buttons.see.className    | string  | btn-success      |
operations.buttons.see.cClass       | string  | me-table-detail  |
operations.buttons.see.icon         | string  | fa-search-plus   | 
operations.buttons.see.sClass       | string  | blue             | 



There are 3 default button groups, which are:
1. see: see details
2. update: change the data
3. delete: delete data

> Each button has three attribute configurations: **bShow, className, title, button-title, cClass, icon, sClass** 

If you need to add a custom button, you can add your own button in the buttons, for example:
```js
var m = meTables({
    buttons: {
        operations: {
            buttons: {
                // Add custom button
                other: {
                    bShow: true,
                    title: "编辑权限",
                    "button-title": "编辑权限",
                    className: "btn-warning",
                    cClass: "role-edit",
                    icon: "fa-pencil-square-o",
                    sClass: "yellow"
                },   
            }  
        },
        
    },
    ...
});

// Corresponding to the button's processing event, the button configured cClass is the monitor object selector
$(document).on('click', '.role-edit', function () {
    
    // Fixed writing to get the data for clicking this row
    var data = m.table.data()[$(this).attr('table-data')];
    
    if (data) {
        alert("My custom button");
    }
});
```

## About the configuration of jquery.dataTables.js

Configuration name | Types of | Defaults | Description
:-----------------------|:-----------|:--------------------|:---------------
table                   | object     |                     | Configuration of jquery.dataTables.js

### Introduces the configuration of the aoColumns table column，[more](http://www.datatables.club/reference/option/)
Configuration name | Types of | Description
:------------|:-----------|:---------------
title        | string     | The name of the column                 
data         | string     | Column data field
render       | function   | Rendering function[more description](http://www.datatables.club/reference/option/columns.render.html)         
createdCell  | function   | Line creation processing function[more description](http://www.datatables.club/reference/option/columns.createdCell.html)

Configuration example:
```js
var m = meTables({
    table: {
        aoColumns: [
            {
                title: "id",
                data: "id",
                render: function (data) {
                    return data === 1 ? "yes" : "no";
                }
            },
            {
                title: "name",
                data: "name",
                createdCell: function (td, data) {
                    $(td).html(data === 1 ? "username": "name");
                }
            }
        ],
    },
    ...
});
```
### meTable self-defined aoColumns configuration
Configuration name | Types of |  Defaults | Description
:------------|:----------- |:-----------|:-----------------
bHide        | boolean     | false      | Hide and divert
isHide       | boolean     | false      | Hide and divert(bHide Alias)        
bExport      | boolean     | false      | Export diverted
isExport     | boolean     | false      | Export diverted(bExport Alias)
bViews       | boolean     | true       | Details show this line
defaultOrder | string      | null       | Default sorting method(asc or desc)
search       | object      | undefined  | Search processing configuration
edit         | object      | undefined  | Edit processing configuration
value        | object      | undefined  | Provide search and edit support data

Configuration example:
```js
var m = meTables({
    table: {
        aoColumns: [
            {
                title: "id",
                data: "id",
                render: function (data) {
                    return data === 1 ? "yes" : "no";
                },
                defaultOrder: "desc",
                search: {type: "text"},
                edit: {type: "hidden"}
            },
            {
                title: "name",
                data: "name",
                createdCell: function (td, data) {
                    $(td).html(data === 1 ? "username": "name");
                },
                
                /**
                 * This configuration will generate a drop-down box
                 * <select name="username" required=true number=true>
                 *     <option value="1">123</option>
                 *     <option value="2">456</option>
                 * </select>    
                 */
                value: {"1": "123", "2": "456"},
                edit: {type: "select", required: true, number: true}
            }
        ],
    },
    ...
});
```

#### About type support for search configuration in aoColumns:
1. text
2. select

**You can also customize:**
```js
meTables.extend({
    /**
     * 定义搜索表达(函数后缀名SearchCreate)
     * 使用配置 search: {"type": "email", "id": "search-email"}
     * search 里面配置的信息都通过 params 传递给函数
     */
    "emailSearchCreate": function(params) {
        return '<input type="text" name="' + params.name +'">';
    }
});
```

#### About type support for edit configuration in aoColumns:
1. text
2. select
3. radio
4. checkbox
5. hidden
6. file
7. textarea
8. password

**You can also customize:**
```js
meTables.extend({
    /**
     * 定义编辑表单(函数后缀名Create)
     * 使用配置 edit: {"type": "email", "id": "user-email"}
     * edit 里面配置的信息都通过 params 传递给函数
     */
    "emailCreate": function(params) {
        return '<input type="email" name="' + params.name + '"/>';
    }
});
```

#### The editor supports the validation configuration. For details, see the configuration of [jquery.validate.js](https://jqueryvalidation.org/documentation/). You just configure the validation rules in edit.
```js
var m = meTables({
    table: {
        aoColumns: [
            {
                title: "id",
                data: "id",
                // Verification cannot be empty, between 2,100 characters in length
                edit: {type: "text", required: true, rangelength: "[2, 100]"}
            }
        ],
    },
    ...
});
```
[←  Controller description](./controller.md)
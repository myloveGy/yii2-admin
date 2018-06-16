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


[←  Controller description](./controller.md)
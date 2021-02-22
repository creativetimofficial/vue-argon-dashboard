# Popovers

Documentation and examples for Bootstrap’s powerful, responsive navigation header, the navbar. Includes support for branding, navigation, and more, including support for our collapse plugin.

<hr>

#### Examples

<div>
  <base-button size="sm" type="default"
               v-b-popover.hover.left="'Vivamus sagittis lacus vel augue laoreet rutrum faucibus.'"
               title="Popover On Left">On left
  </base-button>

  <base-button size="sm" type="default"
               v-b-popover.hover.top="'Vivamus sagittis lacus vel augue laoreet rutrum faucibus.'"
               title="Popover On Top">On top
  </base-button>
  <base-button size="sm" type="default"
               v-b-popover.hover.right="'Vivamus sagittis lacus vel augue laoreet rutrum faucibus.'"
               title="Popover On right">On right
  </base-button>
  <base-button size="sm" type="default"
               v-b-popover.hover.bottom="'Vivamus sagittis lacus vel augue laoreet rutrum faucibus.'"
               title="Popover On bottom">On bottom
  </base-button>
</div>

```html
<el-popover
 placement="top"
 title="Popover On Top"
 :width="200"
 trigger="click"
 content="Popover top"
>
 <template #reference>
   <el-button class="btn btn-default btn-sm">On top</el-button>
 </template>
</el-popover>

<el-popover
placement="right"
title="Popover On Right"
:width="200"
trigger="click"
content="Popover right"
>
<template #reference>
  <el-button class="btn btn-default btn-sm">On right</el-button>
</template>
</el-popover>

<el-popover
 placement="bottom"
 title="Popover On Bottom"
 :width="200"
 trigger="click"
 content="Popover bottom"
>
 <template #reference>
   <el-button class="btn btn-default btn-sm">On bottom</el-button>
 </template>
</el-popover>
```



#### Props

We used [Bootstrap Vue popovers](https://bootstrap-vue.js.org/docs/components/popover).
For more info check out their docs on how to use popovers.

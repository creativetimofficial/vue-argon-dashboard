# Tooltips

Documentation and examples for Bootstrap’s powerful, responsive navigation header, the navbar. Includes support for branding, navigation, and more, including support for our collapse plugin.

<hr>

#### Examples

<div>
  <base-button size="sm" type="primary" class="btn-tooltip"
               v-b-tooltip.hover.left title="Tooltip on left">On left
  </base-button>
  <base-button size="sm" type="primary" class="btn-tooltip"
               v-b-tooltip.hover.top title="Tooltip on top">On top
  </base-button>
  <base-button size="sm" type="primary" class="btn-tooltip"
               v-b-tooltip.hover.bottom title="Tooltip on bottom">On bottom
  </base-button>
  <base-button size="sm" type="primary" class="btn-tooltip"
               v-b-tooltip.hover.right title="Tooltip on right">On right
  </base-button>
</div>

```html
<el-tooltip placement="left" content="Tooltip on left">
  <base-button size="sm" type="primary">
    On left
  </base-button>
</el-tooltip>

<el-tooltip placement="top" content="Tooltip on top">
  <base-button size="sm" type="primary">
    On top
  </base-button>
</el-tooltip>

<el-tooltip placement="bottom" content="Tooltip on bottom">
  <base-button size="sm" type="primary">
    On bottom
  </base-button>
</el-tooltip>

<el-tooltip placement="right" content="Tooltip on right">
  <base-button size="sm" type="primary">
    On right
  </base-button>
</el-tooltip>
```


#### Props

We used [Element Plus](https://element-plus.org/#/en-US/component/tooltip).
For more info check out their docs on how to use tooltips.

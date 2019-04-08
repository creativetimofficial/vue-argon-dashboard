# Contents

Discover what’s included in Bootstrap, including our precompiled and source code flavors.
Remember, Bootstrap’s JavaScript components require Bootstrap Vue.

<hr>

#### Argon Structure

Once downloaded, unzip the compressed folder and you’ll see something like this:

```
|-- Vue Argon Dashboard
    |-- .gitignore
    |-- CHANGELOG.md
    |-- ISSUES_TEMPLATE.md
    |-- LICENSE.md
    |-- README.md
    |-- babel.config.js
    |-- package.json
    |-- public
    |   |-- favicon.ico
    |   |-- index.html
    |   |-- manifest.json
    |   |-- robots.txt
    |   |-- img
    |-- src
        |-- App.vue
        |-- main.js
        |-- registerServiceWorker.js
        |-- router.js
        |-- assets
        |   |-- logo.png
        |   |-- scss
        |   |   |-- argon.scss
        |   |   |-- core
        |   |   |   |-- alerts
        |   |   |   |   |-- _alert-dismissible.scss
        |   |   |   |   |-- _alert.scss
        |   |   |   |-- avatars
        |   |   |   |   |-- _avatar-group.scss
        |   |   |   |   |-- _avatar.scss
        |   |   |   |-- badges
        |   |   |   |   |-- _badge-circle.scss
        |   |   |   |   |-- _badge-dot.scss
        |   |   |   |   |-- _badge.scss
        |   |   |   |-- buttons
        |   |   |   |   |-- _button-brand.scss
        |   |   |   |   |-- _button-icon.scss
        |   |   |   |   |-- _button.scss
        |   |   |   |-- cards
        |   |   |   |   |-- _card-animations.scss
        |   |   |   |   |-- _card-blockquote.scss
        |   |   |   |   |-- _card-profile.scss
        |   |   |   |   |-- _card-stats.scss
        |   |   |   |   |-- _card.scss
        |   |   |   |-- charts
        |   |   |   |   |-- _chart.scss
        |   |   |   |-- close
        |   |   |   |   |-- _close.scss
        |   |   |   |-- custom-forms
        |   |   |   |   |-- _custom-checkbox.scss
        |   |   |   |   |-- _custom-control.scss
        |   |   |   |   |-- _custom-form.scss
        |   |   |   |   |-- _custom-radio.scss
        |   |   |   |   |-- _custom-toggle.scss
        |   |   |   |-- dropdowns
        |   |   |   |   |-- _dropdown.scss
        |   |   |   |-- footers
        |   |   |   |   |-- _footer.scss
        |   |   |   |-- forms
        |   |   |   |   |-- _form-validation.scss
        |   |   |   |   |-- _form.scss
        |   |   |   |   |-- _input-group.scss
        |   |   |   |-- headers
        |   |   |   |   |-- _header.scss
        |   |   |   |-- icons
        |   |   |   |   |-- _icon-shape.scss
        |   |   |   |   |-- _icon.scss
        |   |   |   |-- list-groups
        |   |   |   |   |-- _list-group.scss
        |   |   |   |-- maps
        |   |   |   |   |-- _map.scss
        |   |   |   |-- masks
        |   |   |   |   |-- _mask.scss
        |   |   |   |-- mixins
        |   |   |   |   |-- _alert.scss
        |   |   |   |   |-- _background-variant.scss
        |   |   |   |   |-- _badge.scss
        |   |   |   |   |-- _buttons.scss
        |   |   |   |   |-- _forms.scss
        |   |   |   |   |-- _icon.scss
        |   |   |   |   |-- _modals.scss
        |   |   |   |   |-- _popover.scss
        |   |   |   |-- modals
        |   |   |   |   |-- _modal.scss
        |   |   |   |-- navbars
        |   |   |   |   |-- _navbar-collapse.scss
        |   |   |   |   |-- _navbar-dropdown.scss
        |   |   |   |   |-- _navbar-search.scss
        |   |   |   |   |-- _navbar-vertical.scss
        |   |   |   |   |-- _navbar.scss
        |   |   |   |-- navs
        |   |   |   |   |-- _nav-pills.scss
        |   |   |   |   |-- _nav.scss
        |   |   |   |-- paginations
        |   |   |   |   |-- _pagination.scss
        |   |   |   |-- popovers
        |   |   |   |   |-- _popover.scss
        |   |   |   |-- progresses
        |   |   |   |   |-- _progress.scss
        |   |   |   |-- separators
        |   |   |   |   |-- _separator.scss
        |   |   |   |-- tables
        |   |   |   |   |-- _table.scss
        |   |   |   |-- type
        |   |   |   |   |-- _article.scss
        |   |   |   |   |-- _display.scss
        |   |   |   |   |-- _heading.scss
        |   |   |   |   |-- _type.scss
        |   |   |   |-- utilities
        |   |   |   |   |-- _backgrounds.scss
        |   |   |   |   |-- _blurable.scss
        |   |   |   |   |-- _floating.scss
        |   |   |   |   |-- _helper.scss
        |   |   |   |   |-- _image.scss
        |   |   |   |   |-- _opacity.scss
        |   |   |   |   |-- _overflow.scss
        |   |   |   |   |-- _position.scss
        |   |   |   |   |-- _shadows.scss
        |   |   |   |   |-- _sizing.scss
        |   |   |   |   |-- _spacing.scss
        |   |   |   |   |-- _text.scss
        |   |   |   |   |-- _transform.scss
        |   |   |   |-- vendors
        |   |   |       |-- _bootstrap-datepicker.scss
        |   |   |       |-- _headroom.scss
        |   |   |       |-- _nouislider.scss
        |   |   |       |-- _scrollbar.scss
        |   |   |-- custom
        |   |       |-- _alert.scss
        |   |       |-- _avatar.scss
        |   |       |-- _badge.scss
        |   |       |-- _buttons.scss
        |   |       |-- _card.scss
        |   |       |-- _chart.scss
        |   |       |-- _close.scss
        |   |       |-- _components.scss
        |   |       |-- _content.scss
        |   |       |-- _custom-forms.scss
        |   |       |-- _dropdown.scss
        |   |       |-- _footer.scss
        |   |       |-- _forms.scss
        |   |       |-- _functions.scss
        |   |       |-- _header.scss
        |   |       |-- _icons.scss
        |   |       |-- _input-group.scss
        |   |       |-- _list-group.scss
        |   |       |-- _map.scss
        |   |       |-- _mask.scss
        |   |       |-- _mixins.scss
        |   |       |-- _modal.scss
        |   |       |-- _nav.scss
        |   |       |-- _navbar.scss
        |   |       |-- _pagination.scss
        |   |       |-- _popover.scss
        |   |       |-- _progress.scss
        |   |       |-- _reboot.scss
        |   |       |-- _section.scss
        |   |       |-- _separator.scss
        |   |       |-- _tables.scss
        |   |       |-- _type.scss
        |   |       |-- _utilities.scss
        |   |       |-- _variables.scss
        |   |       |-- _vendors.scss
        |   |-- vendor
        |       |-- @fortawesome
        |       |-- nucleo
        |-- components
        |   |-- Badge.vue
        |   |-- BaseAlert.vue
        |   |-- BaseButton.vue
        |   |-- BaseCheckbox.vue
        |   |-- BaseDropdown.vue
        |   |-- BaseHeader.vue
        |   |-- BaseInput.vue
        |   |-- BaseNav.vue
        |   |-- BasePagination.vue
        |   |-- BaseProgress.vue
        |   |-- BaseRadio.vue
        |   |-- BaseSlider.vue
        |   |-- BaseSwitch.vue
        |   |-- BaseTable.vue
        |   |-- Card.vue
        |   |-- CloseButton.vue
        |   |-- Modal.vue
        |   |-- NavbarToggleButton.vue
        |   |-- StatsCard.vue
        |   |-- stringUtils.js
        |   |-- Charts
        |   |   |-- BarChart.js
        |   |   |-- DoughnutChart.js
        |   |   |-- LineChart.js
        |   |   |-- PieChart.js
        |   |   |-- config.js
        |   |   |-- globalOptionsMixin.js
        |   |   |-- optionHelpers.js
        |   |-- SidebarPlugin
        |   |   |-- SideBar.vue
        |   |   |-- SidebarItem.vue
        |   |   |-- index.js
        |   |-- Tabs
        |       |-- PillsLayout.vue
        |       |-- Tab.vue
        |       |-- TabPane.vue
        |       |-- Tabs.vue
        |       |-- TabsLayout.vue
        |-- directives
        |   |-- click-ouside.js
        |-- layout
        |   |-- AuthLayout.vue
        |   |-- Content.vue
        |   |-- ContentFooter.vue
        |   |-- DashboardLayout.vue
        |   |-- DashboardNavbar.vue
        |   |-- LoadingMainPanel.vue
        |-- plugins
        |   |-- argon-dashboard.js
        |   |-- globalComponents.js
        |   |-- globalDirectives.js
        |-- views
            |-- Dashboard.vue
            |-- Icons.vue
            |-- Login.vue
            |-- Maps.vue
            |-- Register.vue
            |-- Tables.vue
            |-- UserProfile.vue
            |-- Dashboard
            |   |-- PageVisitsTable.vue
            |   |-- SocialTrafficTable.vue
            |-- Tables
                |-- ProjectsTable.vue
```


#### Bootstrap components

Here is the list of Bootstrap 4 components that were restyled in Argon:

<div class="row row-grid mt-5">
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Alerts</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Badge</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Buttons</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Carousel</h6>
      </div>
    </div>
  </div>
</div>

<div class="row row-grid">
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Dropdowns</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Forms</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Modal</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Navs</h6>
      </div>
    </div>
  </div>
</div>

#### Argon components

Besides giving the existing Bootstrap elements a new look, we added new ones, so that the interface and consistent and homogenous. Going through them, we added:

<div class="row row-grid mt-5">
    <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Datepicker</h6>
      </div>
    </div>
    </div>
    <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Sliders</h6>
      </div>
    </div>
    </div>
    <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Checkboxes</h6>
      </div>
    </div>
    </div>
    <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Radio buttons</h6>
      </div>
    </div>
    </div>
 </div>

<div class="row row-grid">
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Toggle buttons</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Font Awesome</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Nucleo icons</h6>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="p-4 text-center">
        <h6 class="mb-0">Modals</h6>
      </div>
    </div>
  </div>
</div>

<template>
  <div class="card h-100">
    <div class="card-header">
      <h5 class="mb-0 text-capitalize">To do list</h5>
    </div>
    <div class="card-body pt-0">
      <ul class="list-group list-group-flush" data-toggle="checklist">
        <li class="checklist-entry list-group-item px-0">
          <div class="checklist-item checklist-item-success checklist-item-checked d-flex">
            <div class="checklist-info">
              <h6 class="checklist-title mb-0">Call with Dave</h6>
              <small class="text-xs">09:30 AM</small>
            </div>
            <div class="form-check my-auto ms-auto">
              <input class="form-check-input" type="checkbox" id="customCheck1" checked />
            </div>
          </div>
        </li>
        <li class="checklist-entry list-group-item px-0">
          <div class="checklist-item checklist-item-warning d-flex">
            <div class="checklist-info">
              <h6 class="checklist-title mb-0">Brunch Meeting</h6>
              <small class="text-xs">11:00 AM</small>
            </div>
            <div class="form-check my-auto ms-auto">
              <input class="form-check-input" type="checkbox" id="customCheck1" />
            </div>
          </div>
        </li>
        <li class="checklist-entry list-group-item px-0">
          <div class="checklist-item checklist-item-info d-flex">
            <div class="checklist-info">
              <h6 class="checklist-title mb-0">Argon Dashboard Launch</h6>
              <small class="text-xs">02:00 PM</small>
            </div>
            <div class="form-check my-auto ms-auto">
              <input class="form-check-input" type="checkbox" id="customCheck1" />
            </div>
          </div>
        </li>
        <li class="checklist-entry list-group-item px-0">
          <div class="checklist-item checklist-item-danger checklist-item-checked d-flex">
            <div class="checklist-info">
              <h6 class="checklist-title mb-0">Winter Hackaton</h6>
              <small>10:30 AM</small>
            </div>
            <div class="form-check my-auto ms-auto">
              <input class="form-check-input" type="checkbox" id="customCheck2" checked />
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: "todo-list-card",
};
</script>

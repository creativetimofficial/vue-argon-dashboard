import bootstrap from "bootstrap/dist/js/bootstrap";

// initialization of Tooltips
export default function setTooltip() {
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]'),
  );
  // eslint-disable-next-line no-unused-vars
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

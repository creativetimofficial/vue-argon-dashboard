<template>
  <div class="card">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6 mb-lg-0 mb-4">
          <div class="d-flex flex-column h-100">
            <p class="mb-1 pt-2 text-bold">بناها المطورون</p>
            <h5 class="font-weight-bolder">Argon Dashboard 2</h5>
            <p class="mb-5">
              من الألوان والبطاقات والطباعة إلى العناصر المعقدة ، ستجد الوثائق
              الكاملة.
            </p>
            <a
              class="text-dark font-weight-bold ps-1 mb-0 icon-move-left mt-auto"
              href="javascript:;"
            >
              اقرأ المستندات
              <i class="fas fa-arrow-left text-sm ms-1" aria-hidden="true"></i>
            </a>
          </div>
        </div>
        <div class="col-lg-4 me-auto ms-0 text-center">
          <div class="bg-gradient-primary border-radius-lg min-height-200">
            <img
              src="../../assets/img/shapes/waves-white.svg"
              class="position-absolute h-100 top-0 d-md-block d-none"
              alt="waves"
            />
            <div class="position-relative pt-5 pb-4">
              <img
                class="max-width-500 w-100 position-relative z-index-2"
                src="../../assets/img/illustrations/rocket-white.png"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "developer-card"
};
</script>

<template>
  <div class="card h-100 p-3">
    <div
      class="overflow-hidden position-relative border-radius-lg bg-cover h-100"
      :style="{
        backgroundImage: 'url(' + require('@/assets/img/ivancik.jpg') + ')',
      }"
    >
      <span class="mask bg-gradient-dark"></span>
      <div class="card-body position-relative z-index-1 p-3 h-100">
        <div class="d-flex flex-column h-100">
          <h5 class="text-white font-weight-bolder mb-4 pt-2">
            العمل مع الصواريخ
          </h5>
          <p class="text-white mb-5">
            تكوين الثروة هو لعبة تطوري حديثة ذات حصيلة إيجابية. الأمر كله يتعلق
            بمن يغتنم الفرصة أولاً هذه بطاقة بسيطة.
          </p>
          <a
            class="text-white font-weight-bold ps-1 mb-0 icon-move-left mt-auto"
            href="javascript:;"
          >
            اقرأ المستندات
            <i class="fas fa-arrow-left text-sm ms-1" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

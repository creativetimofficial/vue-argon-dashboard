<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-3 col-sm-6 mb-lg-0">
        <card
          class="p-0 bg-white"
          title="أموال اليوم"
          value="$53,000"
          iconClass="text-white ni ni-money-coins"
          titleColor="opacity-7 text-dark"
          iconBackground="bg-gradient-success"
          valueColor="text-dark"
          percentage="55%+"
          percentageColor="text-success"
          directionReverse
        />
      </div>
      <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
        <card
          class="p-0 bg-white"
          title="مستخدمو اليوم"
          value="2,300"
          iconClass="text-white ni ni-world"
          titleColor="opacity-7 text-dark"
          iconBackground="bg-gradient-success"
          valueColor="text-dark"
          percentage="+33%"
          percentageColor="text-success"
          directionReverse
        />
      </div>
      <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
        <card
          class="p-0 bg-white"
          title="عملاء جدد"
          value=" +3,462"
          iconClass="text-white ni ni-paper-diploma"
          titleColor="opacity-7 text-dark"
          iconBackground="bg-gradient-success"
          valueColor="text-dark"
          percentage="-2%"
          percentageColor="text-danger"
          directionReverse
        />
      </div>
      <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
        <card
          class="p-0 bg-white"
          title="مبيعات"
          value="$103,430"
          iconClass="text-white ni ni-cart"
          titleColor="opacity-7 text-dark"
          iconBackground="bg-gradient-success"
          valueColor="text-dark"
          percentage="+5%"
          percentageColor="text-success"
          directionReverse
        />
      </div>
    </div>
    <div class="row">
      <div class="col-lg-7 mb-lg-0 mb-4">
        <developer-card />
      </div>
      <div class="col-lg-5">
        <rocket-card />
      </div>
    </div>
    <div class="mt-4 row">
      <div class="col-lg-5 mb-lg-0 mb-4">
        <active-users-chart />
      </div>
      <div class="col-lg-7">
        <gradient-line-chart
          title="نظرة عامة على المبيعات"
          detail1="4% أكثر"
          detail2="في عام 2021"
        />
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
        <project-card
          action="عمل"
          AnotherAction="عمل اخر"
          SomethingElse="شی اخر هنا"
          th1="المشروع"
          th2="أعضاء"
          th3="ميزانية"
          th4="إكمال"
          title="المشاريع"
          desc="هذا الشهر"
          descBold="انتهى30 "
          data-1="الإصدار"
          data-2="أضف مسار التقدم إلى التطبيق الداخلي"
          data-3="إصلاح أخطاء النظام الأساسي"
          data-5="أضف صفحة التسعير الجديدة"
          data-6="إعادة تصميم متجر جديد على الإنترنت"
          data-4="إطلاق تطبيق الهاتف المحمول الخاص بنا"
          empty="غير مضبوط"
        />
      </div>
      <div class="col-lg-4 col-md-6">
        <orders-card
          title="نظرة عامة على الطلبات"
          text="هذا الشهر"
          order="$2400, تغييرات في التصميم"
          newOrder="طلب جديد #1832412"
          payment="مدفوعات الخادم لشهر أبريل"
          newCard="تمت إضافة بطاقة جديدة للطلب #4395133"
          unlockPackages="فتح الحزم من أجل التطوير"
          newOrder2="طلب جديد #9583120"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Card from "@/examples/Cards/Card.vue";
import DeveloperCard from "./components/DeveloperCard.vue";
import RocketCard from "./components/RocketCard.vue";
import ActiveUsersChart from "@/examples/Charts/ActiveUsersChart.vue";
import GradientLineChart from "@/examples/Charts/GradientLineChart.vue";
import OrdersCard from "./components/OrdersCard.vue";
import ProjectCard from "./components/ProjectCard.vue";

export default {
  name: "rtl-page",
  components: {
    Card,
    DeveloperCard,
    RocketCard,
    ActiveUsersChart,
    GradientLineChart,
    OrdersCard,
    ProjectCard,
  },  
  beforeMount() {
    this.$store.state.isRTL = true;
    document.querySelector("html").setAttribute("lang", "ar");
    document.querySelector("html").setAttribute("dir", "rtl");
    document.querySelector("#app").classList.add("rtl");
  },
  beforeUnmount() {
    this.$store.state.isRTL = false;
    document.querySelector("html").removeAttribute("lang");
    document.querySelector("html").removeAttribute("dir");
    document.querySelector("#app").classList.remove("rtl");
  },
};
</script>

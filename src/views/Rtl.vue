<script setup>
import { onBeforeMount, onBeforeUnmount } from "vue";
import { useStore } from "vuex";
import MiniStatisticsCard from "@/examples/Cards/MiniStatisticsCard.vue";
import DeveloperCard from "./components/DeveloperCard.vue";
import RocketCard from "./components/RocketCard.vue";
import ActiveUsersChart from "@/examples/Charts/ActiveUsersChart.vue";
import GradientLineChart from "@/examples/Charts/GradientLineChart.vue";
import TimelineList from "@/examples/Cards/TimelineList.vue";
import TimelineItem from "@/examples/Cards/TimelineItem.vue";
import ProjectCard from "./components/ProjectCard.vue";

import logoXD from "@/assets/img/small-logos/logo-xd.svg";
import logoAtlassian from "@/assets/img/small-logos/logo-atlassian.svg";
import logoSlack from "@/assets/img/small-logos/logo-slack.svg";
import logoSpotify from "@/assets/img/small-logos/logo-spotify.svg";
import logoJira from "@/assets/img/small-logos/logo-jira.svg";
import logoInvision from "@/assets/img/small-logos/logo-invision.svg";

import team1 from "@/assets/img/team-1.jpg";
import team2 from "@/assets/img/team-2.jpg";
import team3 from "@/assets/img/team-3.jpg";
import team4 from "@/assets/img/team-4.jpg";

const store = useStore();

onBeforeMount(() => {
  store.state.isRTL = true;
  document.querySelector("html").setAttribute("lang", "ar");
  document.querySelector("html").setAttribute("dir", "rtl");
  document.querySelector("#app").classList.add("rtl");
});
onBeforeUnmount(() => {
  store.state.isRTL = false;
  document.querySelector("html").removeAttribute("lang");
  document.querySelector("html").removeAttribute("dir");
  document.querySelector("#app").classList.remove("rtl");
});
</script>
<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-3 col-sm-6 mb-lg-0">
        <mini-statistics-card
          class="p-0 bg-white"
          :title="{ text: 'أموال اليوم', color: 'text-dark opacity-7' }"
          :value="{ text: '$53,000', color: 'text-dark' }"
          :icon="{
            component: 'ni ni-money-coins text-white',
            background: 'bg-gradient-success ',
          }"
          :percentage="{ value: '55%+', color: 'text-success' }"
        />
      </div>
      <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
        <mini-statistics-card
          class="p-0 bg-white"
          :title="{ text: 'مستخدمو اليوم', color: 'text-dark opacity-7' }"
          :value="{ text: '2,300', color: 'text-dark' }"
          :icon="{
            component: 'text-white ni ni-world',
            background: 'bg-gradient-success ',
          }"
          :percentage="{ value: '+33%', color: 'text-success' }"
        />
      </div>
      <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
        <mini-statistics-card
          class="p-0 bg-white"
          :title="{ text: 'عملاء جدد', color: 'text-dark opacity-7' }"
          :value="{ text: '+3,462', color: 'text-dark' }"
          :icon="{
            component: 'ni ni-paper-diploma text-white',
            background: 'bg-gradient-success ',
          }"
          :percentage="{ value: '-2%', color: 'text-danger' }"
        />
      </div>
      <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
        <mini-statistics-card
          class="p-0 bg-white"
          :title="{ text: 'مبيعات', color: 'text-dark opacity-7' }"
          :value="{ text: '$103,430', color: 'text-dark' }"
          :icon="{
            component: 'ni ni-cart text-white',
            background: 'bg-gradient-success ',
          }"
          :percentage="{ value: '+5%', color: 'text-success' }"
          rowReverse
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
          id="chart-line-rtl"
          title="نظرة عامة على المبيعات"
          description="<i class='fa fa-arrow-up text-success'></i>
      <span class='font-weight-bold'>4% أكثر</span> في عام 2021"
          :chart="{
            labels: [
              'Apr',
              'May',
              'Jun',
              'Jul',
              'Aug',
              'Sep',
              'Oct',
              'Nov',
              'Dec',
            ],
            datasets: [
              {
                label: 'Mobile Apps',
                data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
              },
            ],
          }"
        />
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
        <project-card
          title="المشاريع"
          description='<i class="fa fa-check text-info" aria-hidden="true"></i>
            <span class="font-weight-bold ms-1">انتهى30</span>
            هذا الشهر'
          :headings="['المشروع', 'أعضاء', 'ميزانية', 'إكمال']"
          :rows="[
            {
              logo: logoXD,
              tool: 'Material XD الإصدار',
              teamMembers: [team1, team2, team3, team4],
              price: '$14,000',
              progress: 60,
            },
            {
              logo: logoAtlassian,
              tool: 'أضف مسار التقدم إلى التطبيق الداخلي',
              teamMembers: [team1, team2],
              price: '$3,000',
              progress: 10,
            },
            {
              logo: logoSlack,
              tool: 'إصلاح أخطاء النظام الأساسي',
              teamMembers: [team2, team4],
              price: 'غير مضبوط',
              progress: 100,
            },
            {
              logo: logoSpotify,
              tool: 'إطلاق تطبيق الهاتف المحمول الخاص بنا',
              teamMembers: [team1, team2, team3, team4],
              price: '$20,500',
              progress: 60,
            },
            {
              logo: logoJira,
              tool: 'أضف صفحة التسعير الجديدة',
              teamMembers: [team1],
              price: '$500',
              progress: 25,
            },
            {
              logo: logoInvision,
              tool: 'إعادة تصميم متجر جديد على الإنترنت',
              teamMembers: [team1, team4],
              price: '$2,000',
              progress: 40,
            },
          ]"
          :action="[
            {
              route: 'javascript:;',
              label: 'عمل',
            },
            {
              route: 'javascript:;',
              label: 'عمل اخر',
            },
            {
              route: 'javascript:;',
              label: 'شی اخر هنا',
            },
          ]"
        />
      </div>
      <div class="col-lg-4 col-md-6">
        <timeline-list
          class="h-100"
          title="نظرة عامة على الطلبات"
          description="<i class='fa fa-arrow-up text-success' aria-hidden='true'></i>
        <span class='font-weight-bold'>24%</span>هذا الشهر"
        >
          <timeline-item
            :icon="{ component: 'ni ni-bell-55', color: 'success' }"
            title="$2400, تغييرات في التصميم"
            date-time="22 ديسمبر 7:20 مساءً"
          />
          <TimelineItem
            :icon="{ component: 'ni ni-html5', color: 'danger' }"
            title="طلب جديد # 1832412"
            date-time="21 ديسمبر 11 م"
          />
          <TimelineItem
            :icon="{ component: 'ni ni-cart', color: 'info' }"
            title="مدفوعات الخادم لشهر أبريل"
            date-time="21 ديسمبر 9:34 مساءً"
          />
          <TimelineItem
            :icon="{ component: 'ni ni-credit-card', color: 'warning' }"
            title="تمت إضافة بطاقة جديدة للأمر رقم 4395133"
            date-time="20 ديسمبر 2:20 صباحًا"
          />
          <TimelineItem
            :icon="{ component: 'ni ni-key-25', color: 'primary' }"
            title="فتح الحزم من أجل التطوير"
            date-time="18 ديسمبر ، 4:54 صباحًا"
          />
          <TimelineItem
            :icon="{ component: 'ni ni-money-coins', color: 'dark' }"
            title="طلب جديد # 9583120"
            date-time="17 ديسمبر"
          />
        </timeline-list>
      </div>
    </div>
  </div>
</template>
